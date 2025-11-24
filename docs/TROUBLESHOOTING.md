# Troubleshooting Guide

## Common Issues & Fixes

### 1. "419 Page Expired" Error (Login Fails)
This error means the security token (CSRF) or your session has expired or is invalid.

**Why it happens:**
-   **Session Configuration**: The app thinks it's on a secure (HTTPS) domain but it's actually running locally (HTTP), so it rejects the cookie.
-   **Old Cache**: Old session files in `AppData` are conflicting with new code.
-   **Time Sync**: Your computer's clock is out of sync.

**How we fixed it permanently:**
We updated `electron/main.js` to force these settings every time the app starts:
-   `SESSION_DRIVER=file` (Stores sessions in files, not cookies/DB)
-   `SESSION_SECURE_COOKIE=false` (Allows HTTP sessions)
-   `SESSION_DOMAIN=null` (Accepts localhost)

**If it happens again:**
1.  **Check Time**: Ensure your computer's date and time are correct.
2.  **Clear AppData**:
    -   Close the app.
    -   Go to `%AppData%\RestaurantPOS`.
    -   Delete the `backend/storage/framework/sessions` folder content.
    -   Restart the app.

### 2. App Not Updating
If you send a new EXE but the client says they don't see the changes.

**Fix:**
-   Did you update the `version` in `package.json`?
-   The update only triggers if the **new version is higher** than the installed version.

### 3. "PHP not found" Error
-   This means the `runtime/php` folder is missing from the build.
-   Ensure you are building with `npm run dist` and that the `runtime` folder exists in your project root.
