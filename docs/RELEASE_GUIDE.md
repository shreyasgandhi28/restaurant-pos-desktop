# Release & Update Guide

## How the Auto-Update Works
Your application now has a **Smart Update System**.
-   When the app starts, it checks the **Version** inside `electron/package.json`.
-   It compares this with the version installed in the user's `AppData`.
-   **If the versions are different**, it automatically:
    1.  Backs up the database and storage.
    2.  Replaces the old code with the new code.
    3.  Restores the database and storage.
    4.  Runs migrations and clears caches.

## Workflow for New Updates

When you have fixed a bug or added a feature, follow these exact steps to release it to your client:

### 1. Make Your Changes
Edit your Laravel code or Electron code as usual.

### 2. Update Version (CRITICAL)
You **MUST** update the version number, otherwise the client's app will think it's already up to date and skip the update.

1.  Open `electron/package.json`.
2.  Find the `"version"` line (e.g., `"1.0.1"`).
3.  Increment it (e.g., to `"1.0.2"`).

```json
{
  "name": "restaurant-pos-desktop",
  "version": "1.0.2",  <-- CHANGE THIS
  ...
}
```

### 3. Build the EXE
Run your build command to generate the new installer/executable.
```bash
cd electron
npm run dist
```

### 4. Send to Client
Give the new EXE to your client.
-   When they run it, the app will detect "Version 1.0.2" is newer than "Version 1.0.1".
-   It will perform the update automatically.
-   Their data (bills, menu items, etc.) will be preserved.

---

## Development Mode
**Note:** You do NOT need to change versions or build the EXE while you are developing on your own machine.
-   Just run `npm start` inside the `electron` folder.
-   The app will run in **Dev Mode** (loading directly from your source files).
-   Changes are visible immediately after refreshing (Ctrl+R).
