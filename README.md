# Bismillah Mobile & Laptop Shop

A full-stack e-commerce platform for a local electronics shop, built as a university final year project. Includes a public storefront with an AI shopping assistant and real order placement, plus a secure admin panel for managing the entire business.

## Business Overview

Bismillah Mobile & Laptop Shop is a local electronics business in Khuzdar, Balochistan, selling mobile phones, laptops, tablets, and accessories, alongside repair and technical services.

## Business Problem

Local electronics shops typically face: no online presence customers can browse before visiting, no way to actually place and pay for an order online, manual stock tracking, no structured way to receive or track customer inquiries, and no way to build trust with new customers through visible reviews.

## Proposed Solution

A complete web platform giving the shop:
- A searchable, filterable, professionally designed online catalog
- A real "Buy Now" ordering system with manual bank/mobile-wallet payment confirmation — appropriate for a market where card payment gateways aren't accessible to small local businesses
- An AI assistant that answers real customer questions, including how to pay, using live shop data
- Automatic email alerts to the shop the moment a customer messages, reviews, or orders
- A review system that builds trust with genuine customer feedback
- A secure admin panel with full content control, order management, and analytics

## Features

**Public Storefront**
- Responsive 11-page site: Home, About, Products, Services, Gallery, Contact, Checkout, Track Order, Enquiry Status, Product Details, Service Details
- Product search, category filtering, price range filtering, and 4 sort options
- Multi-photo galleries with a swipeable slider and full-screen click-to-zoom lightbox everywhere
- Star-rating customer reviews with an admin moderation queue
- Optional sale pricing with automatic discount badges
- "You Might Also Like" related products/services
- Breadcrumb navigation throughout
- **Real order placement ("Buy Now")** — customer selects quantity, delivery address, and pays via Bank Transfer or JazzCash/EasyPaisa, with payment screenshot upload
- **Order tracking** — customers check order status and upload payment proof anytime using a private reference number (e.g. `ORD-7F3K9A`)
- **Enquiry status tracking** — same reference-number system for general contact messages (e.g. `ENQ-7F3K9A`)
- AI shopping assistant that answers questions about products, prices, stock, and how to pay, using live database data — session-aware and rate-limited
- WhatsApp click-to-chat button with context-aware pre-filled messages
- Embedded Google Map with directions link on the Contact page
- Custom-branded 404 page and per-page SEO meta tags

**Admin Panel**
- Secure login with hashed passwords, session authentication, automatic lockout after 5 failed attempts, and a real emailed password-reset flow
- Admin Profile page to change name, email, and password
- **Order management** — view every order, see uploaded payment proof, and update status (Awaiting Payment → Payment Review → Confirmed → Processing → Completed); confirming payment automatically emails the customer
- **Automatic email notifications** — the shop is emailed instantly (in the background, without slowing the page down) whenever a customer sends a message, submits a review, or places an order
- Analytics dashboard: 6 color-coded stat cards, low-stock warning widget, and live charts
- Full CRUD for Products, Services, and Gallery, each with multi-photo upload/delete
- Contact message inbox with read/unread tracking and per-message status control
- Review moderation (approve/delete)
- Logo/branding upload
- One-click image optimization tool

**Technical**
- Automatic image compression and resizing on every upload (GD library)
- Background email sending — pages respond instantly to the visitor while notification emails send separately, without blocking the request
- Prepared statements and output escaping throughout (SQL injection / XSS protection)
- `.htaccess` protection blocking script execution inside every upload folder
- True mobile-first responsive design, verified on a real physical device (not just browser simulation) via local network testing, with specific fixes for two-column layouts and button spacing on small screens

## Technology Stack

- **Frontend:** HTML5, custom CSS design system (CSS variables, no framework), JavaScript
- **Backend:** PHP 8
- **Database:** MySQL (via PDO with prepared statements)
- **AI:** Groq API (`openai/gpt-oss-20b`) for the shopping assistant
- **Email:** PHPMailer + Gmail SMTP, sent via a background CLI process
- **Charts:** Chart.js
- **Maps:** Google Maps embed
- **Environment:** XAMPP (Apache, MySQL, phpMyAdmin)

## Requirements

- XAMPP with PHP 8.x, MySQL, and the GD extension enabled
- A free Groq API key (for the AI assistant)
- A Gmail account with an App Password (for email sending)

## Installation

1. Clone or download this repository into `C:\xampp\htdocs\bismillah-shop`
2. Start **Apache** and **MySQL** in XAMPP Control Panel
3. Open `http://localhost/phpmyadmin`, click **Import**, choose `database/bismillah_shop.sql`, click **Go**
4. Copy `config/ai_config.example.php` to `config/ai_config.php` and add a free Groq API key from `console.groq.com/keys`
5. Copy `config/mail_config.example.php` to `config/mail_config.php` and add a Gmail address + App Password
6. Open `config/constants.php` and replace the placeholder bank/JazzCash/EasyPaisa account details with real ones before accepting real payments
7. Visit `http://localhost/bismillah-shop/`

## Admin Login

- URL: `http://localhost/bismillah-shop/admin/login.php`
- Email: `admin@bismillahshop.com`
- Password: `admin@123`

*(Change this password via the Profile page before any real deployment.)*

## Folder Structure
bismillah-shop/
├── index.php, about.php, products.php, product-details.php
├── services.php, service-details.php, gallery.php, contact.php
├── checkout.php, track-order.php, enquiry-status.php, 404.php
├── config/ - database, AI, mail, and site/payment constants (secrets gitignored)
├── includes/ - shared header, footer, functions, mailer, PHPMailer
├── assets/ - CSS, JS, images
├── uploads/ - product/service/gallery/logo/payment-proof images
├── admin/ - admin panel: dashboard, orders, CRUD modules, auth, profile
├── database/ - SQL schema + data
└── docs/ - screenshots and demo script

## Testing

Manually tested: all CRUD operations, multi-photo upload/delete and compression, admin authentication and lockout, forgot-password email flow, order placement with both payment methods, payment proof upload and admin confirmation flow, SQL injection and XSS resistance, search/sort/filter combinations, AI assistant (public and admin-aware responses, including payment questions), background email delivery, and responsive layout tested on a real physical mobile device over the local network (not just browser simulation), which surfaced and led to fixing several layout bugs simulation testing alone had missed.

## AI Usage

Extensive AI assistance was used throughout development. See `AI_USAGE_REPORT.md` for full details.

## A Note on Payments

This project intentionally does not integrate a real payment gateway (e.g. Stripe), since that requires business verification and legal merchant agreements not available for a student project. Instead, it implements the payment model genuinely used by small local businesses in Pakistan: the customer pays via bank transfer or JazzCash/EasyPaisa directly to the shop's account, uploads proof of payment, and the shop manually confirms it. This is an honest, real-world-accurate approach rather than a simulated checkout.

## Future Improvements

- Real payment gateway integration if/when the business is formally registered
- Order history for returning customers (would require customer accounts)
- Admin bulk actions (bulk approve reviews, bulk order status updates)