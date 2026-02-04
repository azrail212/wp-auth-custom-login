# WordPress Custom Auth Login (Reference Implementation)

## Overview

This repository contains a **reference WordPress plugin** demonstrating how to implement:

- A custom login UI
- Google OAuth login
- Standard username/password login
- Safe redirection away from `wp-login.php` **without breaking WordPress core authentication**

This is **not a production-ready plugin**.  
It is an **architectural reference** intended for learning, portfolio demonstration, and future extension (e.g. headless or mobile authentication).

---

## Why This Exists

Many tutorials and plugins:

- Block or rename `wp-login.php`
- Break logout and password reset flows
- Rely on brittle redirect logic
- Treat WordPress authentication as replaceable

This project demonstrates the **correct approach**:

> **Redirect the login interface, not the login process.**

WordPress remains the source of truth for:
- Users
- Sessions
- Cookies
- Roles and capabilities

---

## What This Plugin Does

- Adds Google OAuth login (using Google Identity)
- Supports standard WordPress username/password login
- Provides a custom login page template
- Redirects human access away from the default `wp-login.php` UI
- Preserves WordPress core authentication flows

---

## What This Plugin Intentionally Does NOT Do

- Does NOT rename or disable `wp-login.php`
- Does NOT replace WordPress session handling
- Does NOT store OAuth access tokens
- Does NOT include UI styling or branding
- Does NOT implement MFA, CAPTCHA, or rate limiting

These are **deliberate non-goals**.

---

## Key Technical Decisions

- Uses `login_form_login` instead of `login_init`
- Avoids `is_user_logged_in()` checks in auth hooks
- Allows WordPress to manage cookies and redirects
- Uses Google OAuth strictly as an identity provider
- Separates UI concerns from authentication logic

---

## File Structure

```
google-login.php        # Core plugin logic
login-page.php          # Custom login page template
register-page.php       # Custom registration page template
README.md               # Documentation
```

---

## Intended Audience

- WordPress developers
- Developers building custom authentication flows
- Developers preparing WordPress for headless or mobile usage
- Portfolio reviewers evaluating backend and architecture skills

---

## Security Notes

- Google Client ID and Secret must be stored securely
- HTTPS is required
- Additional hardening (rate limiting, MFA) depends on project context

---

## Disclaimer

This code is provided **for educational and reference purposes only**.

Authentication requirements vary by project, jurisdiction, and threat model.
Use appropriate security reviews before deploying to production.

---

## Author

BalkanGameHub  
Senior WordPress / Backend Architecture Reference


---

## How the Custom Login & Registration Pages Are Implemented

This project replaces the **default WordPress login UI** with custom `/login` and `/register` pages, while **preserving WordPress’ native authentication engine**.

### Custom Routes via Pages (Not Rewrites)

Instead of rewriting or renaming `wp-login.php`, the plugin uses **standard WordPress pages**:

- `/login`
- `/register`

These pages are created in the WordPress admin and assigned **custom page templates** provided by the plugin.

This approach ensures:
- Compatibility with WordPress core
- Compatibility with themes and page builders
- No interference with internal auth flows

---

### Page Templates

The plugin includes two page templates:

- `login-page.php`
- `register-page.php`

Each template:

- Renders a custom authentication UI
- Submits to WordPress’ native authentication endpoints
- Does **not** replace or override WordPress authentication logic

The login form posts directly to `wp-login.php`, allowing WordPress to:

- Validate credentials
- Set authentication cookies
- Handle redirects and sessions

---

### Why This Approach Was Chosen

This implementation deliberately avoids:

- Blocking or renaming `wp-login.php`
- Custom session handling
- Replacing WordPress cookies

Instead, it follows a clear separation of responsibilities:

- **UI layer** → custom pages (`/login`, `/register`)
- **Auth engine** → WordPress core
- **OAuth provider** → Google (identity only)

This makes the system:
- Stable
- Predictable
- Compatible with future headless or mobile authentication (JWT)

---

### Redirecting the Default Login UI (Safely)

To guide users toward the custom login page, the plugin redirects **only the visual login form** at `wp-login.php` using the `login_form_login` hook.

This ensures that:

- Logout still works
- Password reset still works
- Admin access remains intact
- WordPress internal flows are not disrupted

---

### Key Takeaway

> The goal is not to replace WordPress authentication, but to **wrap it with a custom UI**.

This pattern is widely used in professional WordPress projects and scales cleanly to API- and mobile-based authentication in the future.


## Screenshots

### Custom Login Page
![Custom Login Page](docs/screenshots/login-page.png)

### Registration Page
![Registration Page](docs/screenshots/register-page.png)

---

## Disclaimer

This code is provided **for educational and reference purposes only**.

Authentication requirements vary by project, jurisdiction, and threat model.
Use appropriate security reviews before deploying to production.

---


