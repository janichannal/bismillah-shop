document.addEventListener('DOMContentLoaded', function () {
    // Build the widget HTML and add it to the page
    const widget = document.createElement('div');
    widget.innerHTML = `
        <button class="ai-chat-fab" id="aiChatToggle" aria-label="Open shop assistant">💬</button>
        <div class="ai-chat-panel hidden" id="aiChatPanel">
            <div class="ai-chat-header">
                <div>
                    <strong>Shop Assistant</strong>
                    <small>Ask about products, prices, stock</small>
                </div>
                <button class="ai-chat-close" id="aiChatClose" aria-label="Close">&times;</button>
            </div>
            <div class="ai-chat-messages" id="aiChatMessages"></div>
            <div class="ai-chat-input-row">
                <input type="text" id="aiChatInput" placeholder="Type your question..." maxlength="300">
                <button id="aiChatSend">Send</button>
            </div>
            <div class="ai-chat-footer-note">Powered by AI &middot; answers based on our live catalog</div>
        </div>
    `;
    document.body.appendChild(widget);

    const toggleBtn = document.getElementById('aiChatToggle');
    const closeBtn = document.getElementById('aiChatClose');
    const panel = document.getElementById('aiChatPanel');
    const messagesEl = document.getElementById('aiChatMessages');
    const input = document.getElementById('aiChatInput');
    const sendBtn = document.getElementById('aiChatSend');

    let history = [];
    let hasOpenedBefore = false;

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function addMessage(text, sender) {
        const bubble = document.createElement('div');
        bubble.className = sender === 'user' ? 'ai-msg ai-msg-user' : 'ai-msg ai-msg-bot';
        bubble.innerHTML = escapeHtml(text).replace(/\n/g, '<br>');
        messagesEl.appendChild(bubble);
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function showTyping() {
        const typing = document.createElement('div');
        typing.className = 'ai-msg-typing';
        typing.id = 'aiChatTyping';
        typing.textContent = 'Typing...';
        messagesEl.appendChild(typing);
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function hideTyping() {
        const typing = document.getElementById('aiChatTyping');
        if (typing) typing.remove();
    }

    toggleBtn.addEventListener('click', function () {
        panel.classList.toggle('hidden');
        if (!panel.classList.contains('hidden')) {
            input.focus();
            if (!hasOpenedBefore) {
                hasOpenedBefore = true;
                addMessage("Hi! I'm the Bismillah Shop Assistant. Ask me about products, prices, stock, or our services!", 'bot');
            }
        }
    });

    closeBtn.addEventListener('click', function () {
        panel.classList.add('hidden');
    });

    async function sendMessage() {
        const text = input.value.trim();
        if (text === '') return;

        addMessage(text, 'user');
        input.value = '';
        sendBtn.disabled = true;
        showTyping();

        try {
            const response = await fetch('/bismillah-shop/ai-assistant.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: text, history: history.slice(-8) })
            });
            const data = await response.json();

            hideTyping();

            if (data.reply) {
                addMessage(data.reply, 'bot');
                history.push({ role: 'user', content: text });
                history.push({ role: 'assistant', content: data.reply });
            } else {
                addMessage(data.error || "Sorry, something went wrong. Please try again.", 'bot');
            }
        } catch (err) {
            hideTyping();
            addMessage("Sorry, I couldn't connect. Please check your internet and try again.", 'bot');
        }

        sendBtn.disabled = false;
    }

    sendBtn.addEventListener('click', sendMessage);
    input.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') sendMessage();
    });
});