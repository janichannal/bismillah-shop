<?php
session_start();
header('Content-Type: application/json');

require 'config/database.php';
require 'config/ai_config.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Invalid request method.']);
    exit;
}

// Read the JSON sent from the chat widget
$input = json_decode(file_get_contents('php://input'), true);
$userMessage = trim($input['message'] ?? '');
$history = is_array($input['history'] ?? null) ? $input['history'] : [];

if ($userMessage === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Please type a question.']);
    exit;
}

// ---- Simple rate limiting (protects your free Groq quota from abuse) ----
if (!isset($_SESSION['ai_requests'])) {
    $_SESSION['ai_requests'] = [];
}
// Keep only requests from the last 10 minutes
$_SESSION['ai_requests'] = array_filter($_SESSION['ai_requests'], function ($t) {
    return $t > time() - 600;
});
if (count($_SESSION['ai_requests']) >= 20) {
    http_response_code(429);
    echo json_encode(['error' => 'You are asking a lot of questions! Please wait a few minutes and try again.']);
    exit;
}
$_SESSION['ai_requests'][] = time();

// ---- Build live shop data from the database ----
$products = $pdo->query("SELECT p.*, c.category_name FROM products p 
                          JOIN categories c ON p.category_id = c.category_id 
                          ORDER BY p.category_id")->fetchAll();

$services = $pdo->query("SELECT * FROM services ORDER BY service_name")->fetchAll();

$catalogText = "PRODUCTS:\n";
foreach ($products as $p) {
    $stockStatus = $p['stock_quantity'] > 0 ? $p['stock_quantity'] . ' in stock' : 'OUT OF STOCK';
    $catalogText .= "- {$p['product_name']} ({$p['brand']}, {$p['category_name']}), Condition: {$p['condition']}, "
                  . "Price: Rs. " . number_format($p['price']) . ", $stockStatus\n";
}

$catalogText .= "\nSERVICES:\n";
foreach ($services as $s) {
    $catalogText .= "- {$s['service_name']}: starting Rs. " . number_format($s['price']) . " — {$s['description']}\n";
}

$shopInfo = "SHOP INFO:\n"
          . "Name: Bismillah Mobile & Laptop Shop\n"
          . "Address: Main Bazaar Azadii chowk Mashke Road, Khuzdar, Balochistan\n"
          . "Phone: 03379788440\n"
          . "Email: info@bismillahshop.com\n"
          . "Hours: Saturday-Thursday 9:00 AM - 11:00 PM, Friday 9:00 AM - 3:00 PM\n";

// ---- If this is a logged-in admin, add extra business data ----
$isAdmin = isset($_SESSION['admin_id']);
$adminContext = '';

if ($isAdmin) {
    $unreadCount = $pdo->query("SELECT COUNT(*) FROM messages WHERE status = 'unread'")->fetchColumn();
    $lowStock = $pdo->query("SELECT product_name, stock_quantity FROM products WHERE stock_quantity < 5 ORDER BY stock_quantity ASC")->fetchAll();

   $adminContext = "\nINTERNAL BUSINESS DATA — you are talking to a logged-in shop ADMIN right now, so you may share this freely when asked:\n";
    $adminContext .= "Unread customer messages: $unreadCount\n";
    $adminContext .= "Low stock products (below 5 units in stock):\n";
    if (count($lowStock) === 0) {
        $adminContext .= "(none currently)\n";
    }
    foreach ($lowStock as $item) {
        $adminContext .= "- {$item['product_name']}: {$item['stock_quantity']} left\n";
    }}

// ---- Build the system prompt ----
$adminInstruction = $isAdmin
    ? "The current user is a LOGGED-IN ADMIN. If they ask about unread messages, low stock, or other internal figures, "
      . "answer directly and confidently using the INTERNAL BUSINESS DATA section below — never say you don't have it.\n\n"
    : '';

$systemPrompt = "You are the friendly AI assistant for Bismillah Mobile & Laptop Shop, a local electronics shop in Khuzdar. "
              . "Answer ONLY using the shop data given below. Never invent products, prices, or stock numbers that aren't listed. "
              . "If a customer asks about something not in this data, politely say you don't have that information and suggest "
              . "they contact the shop directly. Keep answers short, friendly, and in Pakistani Rupees (Rs.). "
              . "Do not use markdown formatting, just plain conversational text.\n\n"
              . $adminInstruction
              . $shopInfo . "\n" . $catalogText . $adminContext;

// ---- Build the conversation (system + recent history + new message) ----
$messages = [['role' => 'system', 'content' => $systemPrompt]];

// Only keep the last 8 messages of history to control cost/length
$recentHistory = array_slice($history, -8);
foreach ($recentHistory as $h) {
    if (isset($h['role'], $h['content']) && in_array($h['role'], ['user', 'assistant'])) {
        $messages[] = ['role' => $h['role'], 'content' => (string) $h['content']];
    }
}
$messages[] = ['role' => 'user', 'content' => $userMessage];

// ---- Call Groq ----
$payload = [
    'model' => GROQ_MODEL,
    'messages' => $messages,
    'temperature' => 0.4,
    'max_tokens' => 400,
];

$ch = curl_init(GROQ_API_URL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . GROQ_API_KEY,
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 20);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response === false || $httpCode !== 200) {
    error_log('Groq API error: ' . $response);
    http_response_code(502);
    echo json_encode(['error' => "Sorry, I'm having trouble answering right now. Please try again shortly."]);
    exit;
}

$data = json_decode($response, true);
$reply = $data['choices'][0]['message']['content'] ?? "Sorry, I couldn't come up with an answer. Please try again.";

echo json_encode(['reply' => $reply]);
exit;
?>