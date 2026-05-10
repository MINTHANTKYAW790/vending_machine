# Vending Machine PHP Application

Production-style PHP + MySQL vending machine system with:
- Product management CRUD
- Inventory tracking and transactional purchasing
- Session authentication with roles (`Admin`, `User`)
- REST API secured by JWT
- PHPUnit unit tests with DI and mocks

## Tech Stack
- PHP 8.1+
- MySQL 8+
- PDO
- PHPUnit
- `firebase/php-jwt`
- `vlucas/phpdotenv`

## 1) Setup
1. Copy environment file:
   - `copy .env.example .env` (Windows)
2. Install dependencies:
   - `composer install`
3. Create database:
   - `vending_machine`
4. Update `.env` DB credentials.
5. Run migration and seed:
   - `php scripts/migrate.php`
   - `php scripts/seed.php`
6. Configure Apache document root to:
   - `public/`

## Default Login
- Username: `admin`
- Password: `admin123`

## Web Routes
- `GET /login`
- `POST /login`
- `POST /logout`
- `GET /products`
- `GET /products/create` (Admin)
- `POST /products` (Admin)
- `GET /products/{id}/edit` (Admin)
- `POST /products/{id}/update` (Admin)
- `POST /products/{id}/delete` (Admin)
- `GET /products/{id}-{slug}`
- `GET /products/{id}/purchase`
- `POST /products/{id}/purchase` (attribute routing on controller)

## API (JWT)
### Auth
- `POST /api/v1/auth/login`
  - Body: `{"username":"admin","password":"admin123"}`
  - Response: `{ "token": "...", "user": {...} }`

### Products
- `GET /api/v1/products` (Bearer token)
- `GET /api/v1/products/{id}` (Bearer token)
- `POST /api/v1/products` (Admin token)
- `PUT /api/v1/products/{id}` (Admin token)
- `DELETE /api/v1/products/{id}` (Admin token)
- `POST /api/v1/products/{id}/purchase` (Bearer token)

## Tests
- Run all tests:
  - `vendor\\bin\\phpunit` (Windows)
  - or `composer test`

## Deployment (Preview Link)
Recommended easiest path:
1. Provision a small VPS/shared host with Apache + PHP 8.1 + MySQL.
2. Upload project (exclude `vendor/` if uploading manually, or upload all and run `composer install --no-dev --optimize-autoloader` on server).
3. Copy `.env.example` to `.env` and update with production values:
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - `APP_URL=https://yourdomain.com`
   - DB credentials for production DB
   - Strong `JWT_SECRET` (generate random string)
4. Create database and run migrations/seeds once:
   - `php scripts/migrate.php`
   - `php scripts/seed.php`
5. Point vhost document root to `public/`.
6. Enable rewrite module (`mod_rewrite`) and allow `.htaccess`.
7. Ensure file permissions: `public/` writable for sessions if needed.
8. Test login, product CRUD, and purchase flow.
### InfinityFree deployment
If you deploy to InfinityFree, upload the project into `htdocs` and use the root `index.php` + `.htaccess` files provided in this repository. This routes requests into `public/index.php` without moving the entire app out of the `public/` folder.
After deployment, use your server domain/IP as the preview link for submission.
