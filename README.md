# Bismillah Mobile & Laptop Shop

A full-stack e-commerce catalog and business management platform for a local electronics shop, built as a university final year project. Includes a public storefront with an AI shopping assistant, and a secure admin panel for managing products, services, gallery, reviews, and customer communication.

## Business Overview

Bismillah Mobile & Laptop Shop is a local electronics business in Khuzdar, Balochistan, selling mobile phones, laptops, tablets, and accessories, alongside repair and technical services.

## Business Problem

Local electronics shops like this typically face: no online presence customers can browse before visiting, manual and error-prone stock tracking, no structured way to receive or track customer inquiries, and no way to showcase reviews or build trust with new customers online.

## Proposed Solution

A complete web platform giving the shop:
- A searchable, filterable, professionally designed online catalog
- An AI assistant that answers real customer questions using live shop data
- A review system that builds trust with genuine customer feedback
- A secure admin panel with real authentication, analytics, and full content control
- Direct WhatsApp and contact-form channels for customer inquiries

## Features

**Public Storefront**
- Responsive 8-page site: Home, About, Products, Services, Gallery, Contact, Product Details, Service Details
- Product search, category filtering, price range filtering, and 4 sort options (newest, price low/high, name)
- Multi-photo galleries with a swipeable slider and full-screen click-to-zoom lightbox on every product, service, and gallery item
- Star-rating customer reviews with an admin moderation queue
- Optional sale pricing with automatic discount badges and a homepage "On Sale Now" section
- "You Might Also Like" related products/services on every detail page
- Breadcrumb navigation throughout
- AI shopping assistant (chat widget) that answers questions about products, prices, and stock using live database data — session-aware and rate-limited
- WhatsApp click-to-chat button with context-aware pre-filled messages
- Custom-branded 404 page and per-page SEO meta tags

**Admin Panel**
- Secure login with hashed passwords, session authentication, and automatic lockout after 5 failed attempts
- Real email-based Forgot Password flow with expiring, single-use reset links (Gmail SMTP via PHPMailer)
- Admin Profile page to change name, email, and password
- Analytics dashboard: 5 color-coded stat cards, low-stock warning widget, and live charts (messages per week, top 5 priced products)
- Full CRUD for Products, Services, and Gallery, each with multi-photo upload/delete
- Contact message inbox with read/unread tracking
- Review moderation (approve/delete)
- Logo/branding upload
- One-click image optimization tool for compressing existing uploads

**Technical**
- Automatic image compression and resizing on every upload (GD library)
- Prepared statements throughout (SQL injection protection)
- Output escaping throughout (XSS protection)
- `.htaccess` protection blocking script execution inside upload folders
- Mobile-responsive design tested across all pages, public and admin

## Technology Stack

- **Frontend:** HTML5, custom CSS design system (CSS variables, no framework), JavaScript
- **Backend:** PHP 8
- **Database:** MySQL (via PDO with prepared statements)
- **AI:** Groq API (`openai/gpt-oss-20b`) for the shopping assistant
- **Email:** PHPMailer + Gmail SMTP
- **Charts:** Chart.js
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
6. Visit `http://localhost/bismillah-shop/`

## Admin Login

- URL: `http://localhost/bismillah-shop/admin/login.php`
- Email: `admin@bismillahshop.com`
- Password: `admin123`

*(Change this password via the Profile page before any real deployment.)*

## Folder Structure