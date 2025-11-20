# 🍽️ Restaurant POS System

A modern, full-featured Point-of-Sale (POS) system for restaurants built with Laravel and TailwindCSS.

## Features

### Core Functionality
- **Menu Management** - Add, edit, delete menu items with photos, prices, and categories
- **Table Management** - Visual grid display of restaurant tables with status indicators
- **Order Management** - Create and manage orders with real-time status updates
- **Billing System** - Generate bills with tax, service charge, and discount support
- **PDF Receipts** - Print-ready PDF invoices for customers
- **User Roles** - Admin (full access) and Staff (order management only)

### Technical Features
- **Modern UI/UX** - Built with TailwindCSS for a clean, responsive design
- **RESTful API** - Complete API for mobile app integration
- **Image Upload** - Support for menu item photos
- **Authentication** - Secure login system with role-based access control
- **Database** - SQLite (easily switchable to MySQL/PostgreSQL)

## Tech Stack

- **Backend**: Laravel 12.x (PHP 8.2+)
- **Frontend**: TailwindCSS, Blade Templates
- **Database**: SQLite (default), MySQL/PostgreSQL compatible
- **PDF Generation**: DomPDF
- **Image Processing**: Intervention Image
- **Authentication**: Laravel Sanctum (API tokens)
- **Permissions**: Spatie Laravel Permission

## Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js & NPM
- SQLite (or MySQL/PostgreSQL)

### Setup Instructions

1. **Clone the repository**
```bash
cd restaurant-pos
```

2. **Install PHP dependencies**
```bash
composer install
```

3. **Install NPM dependencies**
```bash
npm install
```

4. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Database setup**
The project uses SQLite by default. The database file is already created at `database/database.sqlite`.

If you want to use MySQL/PostgreSQL, update your `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=restaurant_pos
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

6. **Run migrations and seed data**
```bash
php artisan migrate --seed
```

7. **Create storage link**
```bash
php artisan storage:link
```

8. **Build frontend assets**
```bash
npm run build
```

9. **Start the development server**
```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## Default Credentials

### Admin Account
- **Email**: admin@restaurant.com
- **Password**: password

### Staff Account
- **Email**: staff@restaurant.com
- **Password**: password

## Usage Guide

### For Admin Users

1. **Menu Management**
   - Navigate to "Menu" to add/edit menu items
   - Upload item photos, set prices, and assign categories
   - Mark items as available/unavailable or featured

2. **Category Management**
   - Create and organize menu categories
   - Set sort order for display

3. **Table Management**
   - Add new tables with capacity
   - View all tables in visual grid layout

### For All Users

1. **Taking Orders**
   - Go to "Tables" and select an available table
   - Click "Start Order"
   - Add menu items to the order
   - Submit the order

2. **Managing Orders**
   - View all orders in "Orders"
   - Update order status (Pending → Preparing → Ready → Served)
   - View order details and items

3. **Billing**
   - From an active order, click "Generate Bill"
   - Apply discounts if needed
   - Select payment method and mark as paid
   - Print PDF receipt

## API Documentation

The system includes a complete REST API for mobile app integration.

### Authentication
```bash
POST /api/login
POST /api/register
POST /api/logout
```

### Menu
```bash
GET /api/menu
GET /api/menu/{id}
GET /api/categories
```

### Tables
```bash
GET /api/tables
GET /api/tables/{id}
PUT /api/tables/{id}/status
```

### Orders
```bash
GET /api/orders
POST /api/orders
GET /api/orders/{id}
PUT /api/orders/{id}
DELETE /api/orders/{id}
```

All protected endpoints require Bearer token authentication:
```bash
Authorization: Bearer {your-token}
```

## Project Structure

```
restaurant-pos/
├── app/
│   ├── Http/Controllers/      # Web & API controllers
│   └── Models/                # Eloquent models
├── database/
│   ├── migrations/            # Database migrations
│   └── seeders/               # Database seeders
├── resources/
│   ├── views/                 # Blade templates
│   └── css/                   # Stylesheets
├── routes/
│   ├── web.php               # Web routes
│   └── api.php               # API routes
└── public/                    # Public assets
```

## Customization

### Tax & Service Charge
Edit the rates in `app/Http/Controllers/OrderController.php`:
```php
$tax = $subtotal * 0.10;           // 10% tax
$serviceCharge = $subtotal * 0.05; // 5% service charge
```

### Adding New Features
The codebase is modular and follows Laravel best practices:
- Models use Eloquent relationships
- Controllers follow RESTful conventions
- Views use Blade components
- Clean separation of concerns

## Future Enhancements

Easily extendable to include:
- **Inventory Management** - Track stock levels
- **Sales Reports** - Daily/weekly/monthly analytics
- **Table Reservations** - Advance booking system
- **Kitchen Display System** - Real-time order display for kitchen
- **Multi-location Support** - Manage multiple restaurant branches
- **Customer Management** - Track customer preferences and history

## Troubleshooting

### Storage Permission Issues
```bash
chmod -R 775 storage bootstrap/cache
```

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Rebuild Assets
```bash
npm run build
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
