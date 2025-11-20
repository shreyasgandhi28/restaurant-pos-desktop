# 🚀 Quick Start Guide - Restaurant POS System

## Immediate Setup (Already Done!)

The system is already set up and ready to use! Here's what's been configured:

✅ Laravel 12 installed with all dependencies
✅ Database migrated with sample data
✅ TailwindCSS compiled and ready
✅ Storage linked for image uploads
✅ User roles configured (Admin & Staff)

## Start the Application

```bash
cd restaurant-pos
php artisan serve
```

Then open your browser to: **http://localhost:8000**

## Login Credentials

### Admin Account (Full Access)
- **Email**: `admin@restaurant.com`
- **Password**: `password`

### Staff Account (Orders & Billing Only)
- **Email**: `staff@restaurant.com`
- **Password**: `password`

## Sample Data Included

The system comes pre-loaded with:
- ✅ **4 Categories**: Starters, Main Course, Desserts, Beverages
- ✅ **9 Menu Items**: Various dishes with prices
- ✅ **12 Tables**: T01 through T12 with different capacities
- ✅ **2 Users**: Admin and Staff accounts

## Quick Workflow Test

### 1. View Tables
- Login as admin or staff
- Navigate to "Tables" from the menu
- You'll see a visual grid of all 12 tables

### 2. Create an Order
- Click on any available (green) table
- Click "Start Order"
- Browse menu items by category
- Click items to add them to the cart
- Adjust quantities with +/- buttons
- Add special instructions if needed
- Click "Place Order"

### 3. Generate Bill
- From the order view, click "Generate Bill"
- Apply discount if needed (optional)
- Click "Generate Bill"

### 4. Process Payment
- Select payment method (Cash/Card/UPI/Other)
- Change status to "Paid"
- Click "Update Payment Status"
- Click "Print PDF" to download receipt

### 5. Admin Features (Admin Only)
- **Menu Management**: Add/edit/delete menu items with images
- **Category Management**: Organize menu categories
- **Table Management**: Add/edit/delete tables

## API Testing

### Get API Token
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@restaurant.com","password":"password"}'
```

### Get Menu Items
```bash
curl http://localhost:8000/api/menu \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Get Tables
```bash
curl http://localhost:8000/api/tables \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

## Key Features Overview

### 📋 Menu Management
- Upload item photos (supports JPG, PNG, etc.)
- Set prices, descriptions, preparation time
- Mark items as available/unavailable
- Feature special items
- Organize by categories

### 🪑 Table Management
- Visual grid layout with color-coded status
- **Green** = Available
- **Red** = Occupied
- **Yellow** = Reserved
- Quick actions from table cards

### 📝 Order System
- Real-time order creation
- Category filtering for easy browsing
- Shopping cart with quantity controls
- Order status tracking:
  - Pending → Preparing → Ready → Served
- Special instructions support

### 💰 Billing System
- Automatic tax calculation (10%)
- Service charge (5%)
- Flexible discount options (% or fixed amount)
- Multiple payment methods
- PDF receipt generation
- Print-friendly format

### 🔐 Security & Roles
- **Admin**: Full system access
  - Manage menu items
  - Manage categories
  - Manage tables
  - Process orders & bills
  
- **Staff**: Operational access
  - View menu
  - Create & manage orders
  - Generate & process bills
  - Cannot modify menu or tables

### 📱 REST API
Complete API for mobile app integration:
- Authentication (login/register/logout)
- Menu browsing
- Table management
- Order creation & tracking
- All endpoints use Bearer token auth

## File Structure

```
restaurant-pos/
├── app/
│   ├── Http/Controllers/
│   │   ├── DashboardController.php
│   │   ├── MenuItemController.php
│   │   ├── RestaurantTableController.php
│   │   ├── OrderController.php
│   │   ├── BillController.php
│   │   ├── CategoryController.php
│   │   └── Api/
│   │       ├── AuthController.php
│   │       ├── MenuController.php
│   │       ├── TableController.php
│   │       └── OrderController.php
│   └── Models/
│       ├── Category.php
│       ├── MenuItem.php
│       ├── RestaurantTable.php
│       ├── Order.php
│       ├── OrderItem.php
│       └── Bill.php
├── resources/views/
│   ├── layouts/
│   │   ├── app.blade.php
│   │   └── guest.blade.php
│   ├── auth/
│   ├── dashboard.blade.php
│   ├── tables/
│   ├── orders/
│   ├── bills/
│   ├── menu-items/
│   └── categories/
└── routes/
    ├── web.php
    └── api.php
```

## Customization Tips

### Change Tax Rate
Edit `app/Http/Controllers/OrderController.php` and `BillController.php`:
```php
$tax = $subtotal * 0.10; // Change 0.10 to your rate
```

### Change Service Charge
```php
$serviceCharge = $subtotal * 0.05; // Change 0.05 to your rate
```

### Add More Tables
Use the admin interface or run:
```php
php artisan tinker
>>> RestaurantTable::create(['table_number' => 'T13', 'capacity' => 4, 'status' => 'available']);
```

### Upload Menu Item Images
1. Login as admin
2. Go to Menu → Add Menu Item
3. Fill in details and upload image
4. Images are stored in `storage/app/public/menu-items/`

## Troubleshooting

### Can't Upload Images?
```bash
php artisan storage:link
chmod -R 775 storage
```

### CSS Not Loading?
```bash
npm run build
```

### Database Issues?
```bash
php artisan migrate:fresh --seed
```

### Clear All Cache
```bash
php artisan optimize:clear
```

## Next Steps

1. **Customize Menu**: Add your restaurant's actual menu items
2. **Configure Tables**: Set up your actual table layout
3. **Test Workflow**: Run through a complete order-to-payment cycle
4. **Explore API**: Test API endpoints for mobile integration
5. **Customize Branding**: Update colors, logo, and restaurant name

## Support & Documentation

- Full README: See `README.md` for detailed documentation
- Laravel Docs: https://laravel.com/docs
- TailwindCSS: https://tailwindcss.com/docs

## System Requirements

- PHP 8.2+
- Composer
- Node.js & NPM
- SQLite (or MySQL/PostgreSQL)

---

**Built with ❤️ using Laravel & TailwindCSS**

Enjoy your Restaurant POS System! 🍽️
