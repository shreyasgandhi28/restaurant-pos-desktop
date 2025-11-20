# Settings Page - Profile & Application Configuration

## Overview

A comprehensive settings page has been added to manage user profiles and application-wide configurations including GST and service charge rates.

## Features

### 1. **Profile Management**
- Update name and email
- View current role
- Account information display
- Account creation date and last update

### 2. **Password Management**
- Change password securely
- Current password verification
- Password confirmation
- Minimum 8 characters requirement

### 3. **Application Settings** (Admin Only)
- **Restaurant Name** - Customize your restaurant name
- **GST/Tax Rate** - Adjustable tax percentage (0-100%)
- **Service Charge** - Adjustable service charge percentage (0-100%)
- Real-time rate display
- Important notice about rate changes

## How It Works

### Database Structure
New `settings` table with key-value pairs:
- `key` - Setting identifier (unique)
- `value` - Setting value
- `type` - Data type (string, number, boolean)
- `description` - Setting description

### Default Settings
- **tax_rate**: 10% (GST)
- **service_charge_rate**: 5%
- **restaurant_name**: "Restaurant POS"
- **currency_symbol**: ₹

### Dynamic Rate Application
All controllers now use dynamic rates from settings:
- `POSController` - Uses settings for new orders
- `OrderController` - Uses settings for order calculations
- `BillController` - Uses settings for bill generation

## Access

### For All Users
- Profile Information
- Password Change
- Account Information

### For Admin Only
- Application Settings
- Tax Rate Configuration
- Service Charge Configuration
- Restaurant Name

## Usage

### Update Profile
1. Go to Settings (top right menu)
2. Update Name or Email
3. Click "Update Profile"

### Change Password
1. Go to Settings
2. Enter current password
3. Enter new password (min 8 characters)
4. Confirm new password
5. Click "Change Password"

### Update Tax/Service Rates (Admin Only)
1. Go to Settings
2. Scroll to "Application Settings"
3. Modify GST/Tax Rate or Service Charge
4. Click "Save Application Settings"
5. New rates apply to all future orders

## Important Notes

⚠️ **Rate Changes**
- Changing rates affects **only new orders**
- Existing orders and bills are **not modified**
- Changes are applied immediately

✅ **Benefits**
- No code changes needed to adjust rates
- Flexible pricing structure
- Easy compliance with tax changes
- Per-restaurant customization

## Files Created/Modified

### New Files:
- `database/migrations/2025_10_13_105606_create_settings_table.php`
- `app/Models/Setting.php`
- `app/Http/Controllers/SettingsController.php`
- `resources/views/settings/index.blade.php`
- `database/seeders/SettingsSeeder.php`

### Modified Files:
- `routes/web.php` - Added settings routes
- `resources/views/layouts/app.blade.php` - Added Settings link
- `app/Http/Controllers/POSController.php` - Uses dynamic rates
- `app/Http/Controllers/OrderController.php` - Uses dynamic rates
- `app/Http/Controllers/BillController.php` - Uses dynamic rates
- `database/seeders/DatabaseSeeder.php` - Added settings seeding

## Routes

- **GET** `/settings` - Settings page
- **PUT** `/settings/profile` - Update profile
- **PUT** `/settings/password` - Change password
- **PUT** `/settings/app` - Update app settings (Admin only)

## Setting Model Helper Methods

```php
// Get a setting value
Setting::get('tax_rate', 10); // Returns 10 if not found

// Set a setting value
Setting::set('tax_rate', 12, 'number', 'GST/Tax percentage');
```

## Example: Changing Tax Rate

**Before:**
```php
$tax = $subtotal * 0.10; // Hardcoded 10%
```

**After:**
```php
$taxRate = Setting::get('tax_rate', 10) / 100;
$tax = $subtotal * $taxRate; // Dynamic from settings
```

## Security

- Profile updates require authentication
- Password change requires current password
- Application settings restricted to Admin role
- Email uniqueness validation
- CSRF protection on all forms

---

**Settings page is now live and fully functional! ⚙️**

Access it from the top-right menu next to your name.
