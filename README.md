# Restaurant POS System

A modern Point of Sale (POS) system built with Laravel (backend) and Electron (desktop application). This application helps manage restaurant operations including order management, billing, and inventory.

## 🌟 Features

- **Order Management**: Create, view, and manage orders
- **Billing System**: Generate bills with tax and discount support
- **Table Management**: Track table status and reservations
- **Menu Management**: Manage food items, categories, and pricing
- **User Authentication**: Secure login system with role-based access
- **Reporting**: Sales reports and analytics
- **Desktop Application**: Cross-platform desktop app built with Electron

## 🛠 Tech Stack

### Backend (Laravel 10.x)
- PHP 8.1+
- Laravel 10.x
- MySQL/PostgreSQL/SQLite
- Laravel Sanctum for API Authentication
- Laravel Livewire for dynamic interfaces

### Frontend
- HTML5, CSS3, JavaScript (ES6+)
- Tailwind CSS 3.x
- Alpine.js
- Livewire

### Desktop Application
- Electron 25.x
- Node.js 18.x
- PHP Desktop (PHP 8.1+)

## 🚀 Prerequisites

### For Development
- PHP 8.1 or higher
- Composer 2.x
- Node.js 18.x or higher
- NPM 8.x or higher
- MySQL 8.0+ / PostgreSQL 13+ / SQLite 3.35+
- Git

### For Production
- Web server (Nginx/Apache)
- SSL Certificate (for HTTPS)
- PHP 8.1+
- Database server

## 🛠 Installation

### 1. Clone the Repository
```bash
git clone https://github.com/shreyasgandhi28/restaurant-pos-desktop.git
cd restaurant-pos-desktop
```

### 2. Backend Setup
```bash
# Navigate to backend directory
cd backend

# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure your .env file with database credentials
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=restaurant_pos
# DB_USERNAME=your_username
# DB_PASSWORD=your_password

# Run database migrations and seeders
php artisan migrate --seed

# Generate storage link
php artisan storage:link

# Clear configuration cache
php artisan config:clear
php artisan cache:clear
```

### 3. Frontend Setup
```bash
# Install Node.js dependencies
npm install

# Build assets for production
npm run build

# Or for development
npm run dev
```

### 4. Desktop Application Setup
```bash
# Navigate to electron directory
cd electron

# Install dependencies
npm install

# For development
npm start

# To build the application
npm run dist
```

## 🏃‍♂️ Running the Application

### Web Version
```bash
# Start the development server
php artisan serve

# Access the application at: http://127.0.0.1:8000
```

### Desktop Version
```bash
# In the electron directory
npm start
```

## 🔧 Configuration

### Environment Variables
Create a `.env` file in the `backend` directory and configure the following variables:

```env
APP_NAME="Restaurant POS"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=restaurant_pos
DB_USERNAME=root
DB_PASSWORD=

# For production
# SESSION_SECURE_COOKIE=true
# SESSION_DOMAIN=yourdomain.com
# SANCTUM_STATEFUL_DOMAINS=yourdomain.com
```

## 📦 Building for Production

### Web Version
```bash
# Optimize the application
php artisan optimize

# Cache routes and config
php artisan route:cache
php artisan config:cache

# Build assets for production
npm run build
```

### Desktop Version
```bash
# Build the application
npm run dist

# The built application will be in the 'dist' directory
```

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👨‍💻 Author

- [Shreyas Gandhi](https://github.com/shreyasgandhi28)

## 🙏 Acknowledgments

- Laravel Community
- Electron Team
- All contributors

## 📝 Changelog

See [CHANGELOG.md](CHANGELOG.md) for more information on what has changed recently.

## 🔒 Security

If you discover any security related issues, please email your-email@example.com instead of using the issue tracker.

## 📧 Contact

For any queries, please contact at your-email@example.com
