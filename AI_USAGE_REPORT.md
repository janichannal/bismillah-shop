# AI Usage Report

## AI Tool Used

Claude (Anthropic) was used as a coding assistant and collaborative planning partner throughout the full development of this project, across many sessions.

## What AI Was Used For

- **Architecture and database design:** Planning the full folder structure, database schema, and table relationships (12 tables total, including later additions for multi-photo galleries, reviews, and password resets)
- **Code generation:** Writing all PHP, CSS, and JavaScript for both the public site and admin panel
- **Debugging:** Diagnosing and fixing real errors during development, including a subtle authentication bug where a relative redirect path broke when included from admin subfolders, a missing GD library causing image compression to fail, and several missing-file issues caused by editor save mistakes
- **Security implementation:** Password hashing, prepared statements, file upload validation with real MIME-type checks, login lockout after repeated failures, and expiring single-use password reset tokens
- **Building the in-app AI feature itself:** Designing and implementing the AI shopping assistant (Groq API), including its database-grounded system prompt and admin-vs-public data boundaries
- **UX/UI research and design system:** Researching current ecommerce UX best practices (Baymard Institute and industry sources) and applying findings — image zoom, breadcrumbs, price sorting/filtering, stock urgency signaling, and image compression for page speed
- **Performance optimization:** Diagnosing slow page loads via browser DevTools, then implementing automatic image compression on upload and fixing render-blocking font loading
- **Documentation:** Drafting this report, the README, the project report, and the demonstration script

## What Was Manually Reviewed and Tested

Every feature was manually tested in the browser after implementation, including deliberate edge-case testing: invalid product IDs, SQL injection attempts in form fields, XSS attempts via script tags in input, expired/reused password reset links, and repeated failed logins to confirm lockout behavior. Real email delivery was verified by checking actual inboxes, not just success messages on screen.

## Real Issues Found and Fixed During Testing

- An authentication redirect bug that only appeared when accessing admin pages from subfolders, found through deliberate testing rather than assumption
- Admin-only AI assistant data was being correctly retrieved from the database but the AI model was declining to share it with logged-in admins — diagnosed with temporary debug output, fixed by making the system prompt explicit about admin authorization
- Image compression silently failing due to the PHP GD extension being disabled by default in this XAMPP installation — diagnosed via a dedicated PHP capability-check script

## What Was Not AI-Generated

- The decision to build this specific project, business concept, and feature set
- All hands-on testing and verification of every feature described above
- Real content decisions (business details, product data, design preferences)
- Final understanding of how each part of the system works, developed through iterative testing and troubleshooting alongside AI assistance