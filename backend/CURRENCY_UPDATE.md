# Currency Update - Indian Rupees (₹)

The entire application has been updated to use **Indian Rupees (₹)** as the currency.

## Changes Made

### 1. **Sample Menu Items Updated**
All menu items now have Indian-themed dishes with prices in INR:
- **Paneer Tikka** - ₹250
- **Garlic Bread** - ₹150
- **Butter Chicken** - ₹350
- **Dal Makhani** - ₹280
- **Biryani** - ₹320
- **Gulab Jamun** - ₹120
- **Ice Cream** - ₹100
- **Masala Chai** - ₹50
- **Fresh Lime Soda** - ₹80

### 2. **Helper Functions Created**
Created `app/helpers.php` with currency formatting functions:
```php
format_currency($amount)  // Returns: ₹1,234.56
currency_symbol()         // Returns: ₹
```

### 3. **All Views Updated**
Replaced all `$` symbols with `₹` in:
- Dashboard
- Menu Items
- Tables
- Orders (creation, listing, details)
- Bills (creation, display, PDF)
- All JavaScript calculations

### 4. **Database Seeded**
Fresh database with Indian menu items and INR pricing.

## Tax & Service Charge

The system applies:
- **GST (Tax)**: 10% (configurable)
- **Service Charge**: 5% (configurable)

These can be adjusted in:
- `app/Http/Controllers/OrderController.php`
- `app/Http/Controllers/BillController.php`

## Usage

All currency displays now show:
- ₹250.00 (instead of $250.00)
- Proper Indian Rupee formatting
- Consistent across web interface and PDF receipts

## Login Credentials

Same as before:
- **Admin**: admin@restaurant.com / password
- **Staff**: staff@restaurant.com / password

---

**The application is now fully configured for Indian restaurants! 🇮🇳**
