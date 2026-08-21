# AI Usage Report

## AI Tool Used

Claude (Anthropic) was used as a coding assistant and collaborative planning partner throughout the full development of this project, across many sessions from initial architecture through final polish.

## What AI Was Used For

- **Architecture and database design:** Planning the full folder structure and database schema, which grew from an initial 6 tables to 14 as features were added (multi-photo galleries, reviews, password resets, orders)
- **Code generation:** Writing all PHP, CSS, and JavaScript for the public site and admin panel
- **Debugging real, encountered errors**, including:
  - An authentication redirect bug that only broke when accessed from admin subfolders
  - A missing PHP GD extension causing image compression to silently fail
  - A missing `includes/functions.php` require causing a fatal error that only appeared after `display_errors` was turned off for production
  - A CSS layout bug (flexbox refusing to shrink inside a grid column) that only appeared on wide desktop screens, not caught until testing on an actual laptop screen
  - Mobile layout issues (buttons touching without gaps, forced two-column layouts not stacking) that were only caught through testing on a real physical phone over the local network via ngrok — browser-based mobile simulation had not revealed them
- **Security implementation:** Password hashing, prepared statements, file upload validation, login lockout, expiring single-use password reset tokens, and secure random reference-number generation for order/enquiry tracking
- **Building the in-app AI shopping assistant:** Designing its database-grounded system prompt, admin-vs-public data boundaries, and later updating it to accurately explain the real payment process when customers ask how to pay
- **Designing the payment/order system honestly:** Researching what payment methods are actually accessible to a small unregistered local business in Pakistan, and explicitly declining to build a fake card-payment form, in favor of a real bank-transfer/mobile-wallet-with-proof-upload flow matching genuine local business practice
- **Performance optimization:** Diagnosing slow page loads via browser DevTools (uncompressed images, render-blocking font loading), then implementing automatic image compression and moving notification email sending to a background process so form submissions no longer wait on the email server
- **UX/UI research:** Researching ecommerce UX best practices (Baymard Institute and industry sources) and applying findings — image zoom, breadcrumbs, price sorting/filtering, stock urgency signaling
- **Documentation:** Drafting this report, the README, the project report, and the demonstration script

## What Was Manually Reviewed and Tested

Every feature was manually tested in the browser after implementation. This included deliberate edge-case testing (invalid IDs, SQL injection and XSS attempts, expired/reused reset tokens, ordering more than available stock, repeated failed logins) and, importantly, testing on a real physical Android phone connected via ngrok over the local network — this caught several real layout bugs that desktop browser DevTools' mobile simulator had not revealed, demonstrating the limits of simulated testing versus real-device testing.

## Real Issues Found and Fixed During Testing

- An authentication redirect bug only reproducible from admin subfolders
- Admin-only AI assistant data being correctly retrieved but the AI declining to share it with authenticated admins, fixed by making the system prompt explicit
- Image compression silently failing due to a disabled PHP extension
- A missing file require causing a page to fail completely once error display was turned off for production — found by checking the Apache error log directly rather than guessing
- A CSS layout bug specific to wide desktop screens, invisible on mobile, requiring both a laptop and phone side by side to catch
- Mobile-only button spacing and layout bugs only visible on a genuine physical device, not a simulator
- Form submissions feeling slow because the page was waiting on a live email-server connection before responding — fixed by moving email sending to a background process

## What Was Not AI-Generated

- The decision to build this specific project, business concept, and feature set
- All hands-on testing and verification, including real-device mobile testing via ngrok
- Real content decisions (business details, product data, real payment account information)
- The judgment to reject a fake payment-gateway approach in favor of an honest, locally-appropriate solution
- Final understanding of how each part of the system works, developed through iterative testing and troubleshooting alongside AI assistance