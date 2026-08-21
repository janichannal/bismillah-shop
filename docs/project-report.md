# Bismillah Mobile & Laptop Shop — Project Report

**Course:** Final Year Project
**Student:** [Your Name]
**Batch:** 2022, Department of CSE&S
**Institution:** BUET Khuzdar

---

## 1. Business Problem

Local electronics businesses like Bismillah Mobile & Laptop Shop typically operate with manual, in-person-only processes: no online presence for customers to browse or buy from before visiting, no structured stock or pricing records, no organized channel for customer inquiries, and no way to build trust with new customers through visible reviews.

## 2. Proposed Solution

This project delivers a complete web platform addressing each problem directly, including a genuine online ordering system appropriate for a small local business without access to formal payment-gateway infrastructure:

- **Discoverability:** A searchable, filterable, category-organized catalog with multi-photo galleries and zoomable images
- **Purchasing:** A real "Buy Now" flow where customers pay via bank transfer or JazzCash/EasyPaisa and upload payment proof, tracked with a private reference number
- **Trust:** A genuine customer review system, moderated before going public
- **Communication:** A validated contact form with its own reference-number status tracking, a WhatsApp quick-chat button, and an AI assistant answering product, price, stock, and payment questions using the shop's real, live data
- **Responsiveness:** Instant email alerts to the shop for every new message, review, and order — sent in the background so the customer's page never waits on the email server
- **Management:** A secure admin panel with full CRUD control, order management, an analytics dashboard, and real authentication including forgotten-password email recovery

## 3. Key Features Beyond Baseline Requirements

- An AI shopping assistant grounded in the shop's actual MySQL data, aware of the real payment process, with different data access for public visitors versus authenticated admins
- A real order-placement and payment-confirmation system, deliberately built around locally-accessible payment methods (bank transfer, JazzCash/EasyPaisa) rather than a fake card-payment simulation
- Dual reference-number tracking systems (orders and general enquiries) letting customers self-serve status checks without contacting the shop
- Background-processed email notifications, so customer-facing pages stay fast regardless of email server response time
- Multi-photo product/service/gallery support with a shared slider and full-screen zoom viewer site-wide
- A moderated customer review system with average star ratings shown throughout the catalog
- A full analytics dashboard with live charts and low-stock warnings
- Real email-based password recovery and brute-force login protection
- Automatic image compression on upload, plus a one-time optimization tool for legacy images
- Genuine mobile responsiveness, validated on a real physical device, not just simulated

## 4. AI Tools Used

Claude (Anthropic) was used throughout development for architecture planning, code generation, debugging, security implementation, UX research, and documentation. Full detail is in `AI_USAGE_REPORT.md`.

## 5. Challenges Faced

**Authentication across nested folders:** A redirect bug in the shared authentication check used a relative path that broke specifically when included from admin subfolders, only caught through deliberate testing of every admin page while logged out.

**Designing an honest payment system:** Real payment gateways (Stripe, PayPal) require business registration unavailable to a student project. Rather than simulating a fake card-payment form — which would be actively misleading — the system was designed around what small Pakistani businesses genuinely use: bank transfer and mobile wallet payments with manually-verified proof of payment. This required building a full order lifecycle (placement, proof upload, admin review, status tracking, automatic confirmation email) rather than a single "checkout" button.

**Image and email performance:** As the catalog grew with real camera photos, page loads slowed noticeably; browser DevTools revealed several-megabyte uncompressed images as the cause, requiring PHP's GD extension (disabled by default) and custom compression logic. Separately, form submissions felt slow because the page waited on a live connection to Gmail's mail server before responding — solved by running email sending as a background process instead.

**Bugs only visible on real devices:** Two significant layout bugs — a CSS flexbox overflow issue on wide desktop screens, and button-spacing issues on mobile — were invisible in the browser's built-in device simulator and were only discovered by testing the live site on an actual laptop screen and an actual phone (via ngrok tunneling over the local network) side by side. This highlighted a real limitation of simulated responsive testing versus genuine multi-device testing.

**Balancing AI assistant permissions and knowledge:** The AI assistant needed explicit instruction to correctly share internal admin data (unread messages, low stock, pending orders) only with authenticated admins, and later needed its knowledge base updated to accurately explain the new payment process — a reminder that an AI feature is only as current as the information it's given, not something that updates itself automatically when the underlying business logic changes.

**Database evolution:** The schema grew from an initial 6 tables to 14 across the project, as multi-photo galleries, reviews, password resets, and orders were added, each requiring carefully planned foreign key relationships and `ON DELETE CASCADE` behavior to preserve data integrity.

## 6. Conclusion

The completed system goes well beyond a basic catalog-and-admin-panel requirement, combining a genuine AI-powered customer experience, a real (if manually-verified) payment and ordering system appropriate to local market realities, and production-grade security, performance, and responsive design — while remaining fully understood, tested, and maintainable by a single developer.