# Student Portal Inventory Management System

**Laboratory Exercise 5 – Implementing MVC Architecture with Laravel**

A complete Inventory Management System themed around a **Student Portal** (textbooks, stationery, lab equipment, uniforms, electronics, sports gear, etc.).

## Features

- Full MVC architecture (Model – View – Controller)
- Laravel Breeze authentication (login / register / logout)
- Product CRUD (Create, Read, Update, Delete)
- Real-time dashboard statistics (total products, low stock, out of stock, inventory value)
- Form validation & custom error messages
- Low-stock badges and alerts
- Responsive Bootstrap 5 UI with custom education-themed color scheme
- Database seeder with 18 realistic student-related products
- Eloquent ORM, Migrations, Blade templating

## Requirements

- PHP 8.2+
- Composer
- MySQL / MariaDB (or SQLite)
- Node.js & npm (for Breeze assets)
- XAMPP / Laravel Herd / Valet / Docker (optional)

## Quick Setup (Follow these steps)

1. **Unzip** the project and open a terminal in the project root.

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Copy environment file**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database** in `.env`:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=student_portal_inventory
   DB_USERNAME=root
   DB_PASSWORD=
   ```
   Create the database in phpMyAdmin or via CLI:
   ```sql
   CREATE DATABASE student_portal_inventory;
   ```

5. **Install Laravel Breeze (Authentication)**
   ```bash
   composer require laravel/breeze --dev
   php artisan breeze:install blade
   npm install && npm run build
   ```

6. **Run migrations & seed sample data**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

7. **Start the development server**
   ```bash
   php artisan serve
   ```

8. Open **http://127.0.0.1:8000** in your browser.

### Default Login Credentials
- **Email:** `admin@studentportal.test`
- **Password:** `password`

## Project Structure (Key Files)

```
app/
├── Http/Controllers/
│   ├── DashboardController.php   # Real statistics from DB
│   └── ProductController.php     # Full CRUD + validation
├── Models/
│   └── Product.php               # Eloquent model + business methods
database/
├── migrations/
│   └── ..._create_products_table.php
└── seeders/
    ├── DatabaseSeeder.php
    └── ProductSeeder.php         # 18 sample student inventory items
resources/views/
├── layouts/app.blade.php         # Master layout + sidebar
├── dashboard.blade.php
└── products/                     # index, create, edit, show, _form
public/css/custom.css             # Education color scheme (navy + teal)
routes/web.php                    # Resource routes + auth middleware
```

## Color Scheme (from Lab 4 theme extension)

- Primary Navy: `#1e3a5f`
- Accent Teal: `#0d9488`
- Success / Warning / Danger badges for stock status

## Testing Checklist (as required by the lab)

- [x] Login page with custom styling
- [x] Dashboard shows real DB statistics
- [x] Product list with low-stock badges
- [x] Add / Edit / Delete products with validation
- [x] Flash success messages
- [x] Responsive layout
- [x] Seeded sample data
- [x] Git-ready (`.gitignore` included)

## Author Notes

This project fulfills all requirements of Laboratory Exercise 5:

- MVC separation of concerns
- Migrations & Seeders
- Eloquent ORM examples
- Blade templating
- Form validation
- Resource routes + middleware
- Connection of Lab 4 dashboard concept to a real backend

Enjoy managing your Student Portal Inventory!

## Laboratory Exercise 7: Procurement and Automated Replenishment

This version extends the inventory app with the requested supplier and procurement workflow:

- Supplier CRUD, search, active status, lead time, minimum order amount, and soft deletion
- Product-to-supplier assignments and purchase order line items with historical unit-price snapshots
- Purchase order drafts, manager/admin approval, sending, rejection, partial receiving, and inventory transactions
- Role values: `admin`, `manager`, `warehouse`, and `staff`. New registrations default to `staff`; the seeded administrator is assigned `admin`.
- Daily reorder checks using 90-day stock-out history. Reorder point is the greater of the configured product reorder level and `(average daily usage × supplier lead time) + 25% safety stock`. Reorder generation creates reviewable **draft** orders and avoids duplicate outstanding quantities.
- Procurement dashboard widgets and authenticated JSON endpoints under `/api/dashboard/`
- Supplier delivery, timeliness, accuracy, and spend reporting
- Search/filtering for products, purchase orders, and inventory transactions
- CSV and PDF exports for products, purchase orders, transactions, suppliers, and supplier performance
- Purchase order and replenishment email templates. The default local mailer writes messages to the configured log; configure `MAIL_*` in `.env` for delivery.

After updating the project files, run:

```bash
composer install
php artisan migrate
php artisan db:seed
php artisan test
```

Before migrating, verify `.env` points to the database intended for this Lab 7 project. Do not run these migrations against a prior laboratory database unless you intentionally want both projects to share the new procurement tables. The automated test suite runs migrations against an isolated in-memory SQLite database.

For automated daily checks, run Laravel's scheduler once per minute from the operating system scheduler:

```cron
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

On Windows, create the equivalent recurring task in Task Scheduler that runs `php artisan schedule:run` from the project directory. To check reorder points manually:

```bash
php artisan inventory:check-reorder-points
```

The supplier setup flow is: create a supplier, assign that supplier to products, and create a purchase order. Staff can create orders; only managers/admins can approve, and warehouse staff/admins can record receipts. PDF export uses `barryvdh/laravel-dompdf`.
