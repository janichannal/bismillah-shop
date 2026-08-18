# Bismillah Mobile & Laptop Shop — Project Report

**Course:** Final Year Project
**Student:** [Your Name]
**Batch:** 2022, Department of CSE&S
**Institution:** BUET Khuzdar

---

## 1. Business Problem

Local electronics businesses like Bismillah Mobile & Laptop Shop typically operate with manual, in-person-only processes: no online presence for customers to browse before visiting, no structured stock or pricing records, no organized channel for customer inquiries, and no way to build trust with new customers through visible reviews or credentials.

## 2. Proposed Solution

This project delivers a complete web platform addressing each problem directly:

- **Discoverability:** A searchable, filterable, category-organized product and service catalog with multi-photo galleries
- **Trust:** A genuine customer review system with star ratings, moderated by the shop before going public
- **Communication:** A validated contact form, a WhatsApp quick-chat button, and an AI assistant that instantly answers product, price, and stock questions using the shop's real, live data
- **Management:** A secure admin panel with full CRUD control, an analytics dashboard, and real authentication (including a genuine forgotten-password email flow)

## 3. Key Features Beyond Baseline Requirements

Beyond the standard requirements of a catalog site with an admin panel, this project includes:

- An AI shopping assistant grounded in the shop's actual MySQL data (not a generic chatbot), with different data access for public visitors versus authenticated admins
- Multi-photo product/service/gallery support with a shared slider and full-screen zoom viewer across the entire site
- A moderated customer review system with average star ratings shown throughout the catalog
- Optional sale pricing with automatic discount calculation and display
- A full analytics dashboard with live charts and a low-stock warning system
- Real email-based password recovery and brute-force login protection
- Automatic image compression on upload, plus a one-time optimization tool for existing images
- Search, sort, and price-range filtering that all combine together on the product catalog

## 4. AI Tools Used

Claude (Anthropic) was used throughout development for architecture planning, code generation, debugging, security implementation, UX research, and documentation. Full detail is in `AI_USAGE_REPORT.md`.

## 5. Challenges Faced

**Authentication across nested folders:** A redirect bug in the shared authentication check used a relative path that broke specifically when included from admin subfolders (Products, Services, etc.), producing a confusing "page not found" instead of a login redirect. This was only caught through deliberate testing of every admin page while logged out, not assumed to work from a single test.

**Image performance:** As the catalog grew with real, full-size camera photos, page load times increased noticeably. Diagnosing this required using browser developer tools to inspect actual network transfer sizes, which revealed several-megabyte images loading uncompressed. The fix required enabling PHP's GD extension (disabled by default in this XAMPP install) and writing automatic resize/compression logic for both new uploads and a one-time cleanup pass over existing files.

**Balancing AI assistant permissions:** Getting the AI assistant to correctly share internal business data (unread message counts, low stock) with authenticated admins while still refusing that same data to public visitors required explicit, carefully worded instructions in the system prompt — simply providing the data wasn't enough, as the AI model needed to be told plainly that the current user was authorized.

**Database evolution:** The schema grew significantly over the project's development, from an initial 6 tables to 12, as features like multi-photo galleries, reviews, and password resets were added. Each new table's foreign key relationships needed to be planned carefully to preserve data integrity (e.g. deleting a product automatically removes its extra photos and reviews via `ON DELETE CASCADE`).

**Security without breaking usability:** Adding login lockout protection required balancing real brute-force protection against locking out a legitimate admin who simply mistyped a password a few times — a 5-attempt threshold with a 15-minute cooldown was chosen as a reasonable middle ground.

## 6. Conclusion

The completed system goes well beyond a basic catalog-and-admin-panel requirement, combining a genuine AI-powered customer experience with production-grade security, performance, and design practices — while remaining fully understandable, tested, and maintainable by a single developer.