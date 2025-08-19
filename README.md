# School Management System (Laravel)

Features included:
- Bootstrap Authentication (via Laravel UI) — setup steps below
- School Profile (name, logo, academic year, holidays)
- User Management (Admins, Teachers, Students, Parents, Staff)
- Role-Based Access Control (RBAC)
- Notifications (Email, SMS, In‑App/Database, Push)

## Requirements
- PHP and Composer
- Node.js and NPM
- A database (MySQL/MariaDB, SQLite, etc.)

## Setup
1. Copy .env and set DB credentials
   - cp .env.example .env (or copy manually on Windows)
   - Update DB_ settings
2. Install dependencies
   - composer install
   - npm install
3. Authentication
   - This repo includes minimal Bootstrap-based Login and Register pages out of the box (no extra packages required).
   - Alternatively, you can install Laravel UI Bootstrap scaffolding if you prefer its structure:
     - composer require laravel/ui
     - php artisan ui bootstrap --auth
     - npm run build (or npm run dev)
4. Generate app key
   - php artisan key:generate
5. Migrate and seed
   - php artisan migrate --seed
6. Serve
   - php artisan serve

You should now have login/register pages (Bootstrap-based) and the initial Admin user.

Per-user-type authentication:
- Login URLs:
  - Generic selector: /login
  - Type-specific (legacy): /login/{type} where {type} is one of admin, teacher, student, parent, staff
  - Base-path per user type (preferred):
    - Admin: /admin/login
    - Teachers: /teachers/login
    - Students: /students/login
    - Parents: /parents/login
    - Staff: /staff/login
- Registration URLs:
  - Generic selector: /register
  - Type-specific (legacy): /register/{type}
  - Base-path per user type (preferred):
    - Admin: /admin/register
    - Teachers: /teachers/register
    - Students: /students/register
    - Parents: /parents/register
    - Staff: /staff/register
- New registrations are marked as pending and inactive until approved by an Admin. They cannot log in until approved + active.
- After approval, users are redirected to their role-specific dashboards: /{type}/dashboard (base shortcuts: /admin, /teachers, /students, /parents, /staff).

Base URLs per user type (role-protected):
- Admin: /admin (redirects to /admin/dashboard)
- Teachers: /teachers (redirects to /teachers/dashboard)
- Students: /students (redirects to /students/dashboard)
- Parents: /parents (redirects to /parents/dashboard)
- Staff: /staff (redirects to /staff/dashboard)

Note: These base URLs are protected by the role middleware. If you are logged in as one type (e.g., admin), you cannot access another type’s base pages (e.g., /teachers, /students, etc.).

Admin credentials (seeded):
- Email: admin@example.com
- Password: password

## Admin Panel (Dashboard)
- URL: /admin/dashboard (requires admin role)
- Features:
  - Overview cards: Total users, Pending approvals, Active, Inactive
  - Pending registrations table with Approve/Delete
  - All users table with: Assign role, Revoke role, Activate/Deactivate, Delete
- The panel uses Bootstrap 5 and calls existing admin endpoints via AJAX. Ensure you are logged in as the seeded admin to use it.

## Quick Start on Windows (Laragon)
1. Open Laragon and ensure MySQL is running. Create database `schoolmanagementsystem` (Menu > MySQL > phpMyAdmin or TablePlus).
2. Clone or copy this project into `C:\laragon\www\schoolmanagementsystem`.
3. In a Terminal (Laragon Terminal or PowerShell) from the project folder:
   - Copy env: `copy .env.example .env` (if .env doesn’t exist) and set these values:
     - DB_HOST=127.0.0.1
     - DB_PORT=3306
     - DB_DATABASE=schoolmanagementsystem
     - DB_USERNAME=root
     - DB_PASSWORD= (empty if default Laragon)
     - For easiest local use: set `QUEUE_CONNECTION=sync` (see Troubleshooting below)
   - Install backend deps: `composer install`
   - Install frontend deps: `npm install`
   - Scaffold auth UI: `composer require laravel/ui` then `php artisan ui bootstrap --auth`
   - Build assets: `npm run dev` (keeps Vite running) or `npm run build`
   - Generate key: `php artisan key:generate`
   - Run migrations and seed: `php artisan migrate --seed`
4. Start the app:
   - Option A: `php artisan serve` (http://127.0.0.1:8000)
   - Option B: Use Laragon’s auto-virtual host (e.g., http://schoolmanagementsystem.test) if enabled.
5. Log in with seeded admin:
   - Email: admin@example.com
   - Password: password

## Core Endpoints (RBAC protected)
- GET /school/profile — view school profile (admin)
- POST /school/profile — create/update profile (admin)
- GET /users — list users with roles (admin)
- POST /users/{user}/approve — approve a pending user (admin)
- POST /users/{user}/activate — activate user (admin)
- POST /users/{user}/deactivate — deactivate user (admin)
- PUT /users/{user} — update user basic info (admin)
- DELETE /users/{user} — delete user (admin)
- POST /users/{user}/assign-role — assign a role (admin)
  - Body: { "role": "teacher|student|parent|staff|admin" }
- POST /users/{user}/revoke-role — revoke a role (admin)
  - Body: { "role": "..." }
- POST /users/{user}/notify — send a notification (admin)
  - Body: { "title": "...", "message": "...", "extra": { ... } }

## Notifications
- Email and Database notifications are enabled by default.
- SMS and Push are stubbed via logging (storage/logs/laravel.log) using custom channels.

## RBAC
- Roles are stored in roles table with a role_user pivot.
- Middleware `role:` enforces access to routes.
- Seeded roles: admin, teacher, student, parent, staff.

## Notes
- SchoolProfile stores holidays as JSON array.
- You can customize views after running Laravel UI auth scaffolding. This project adds the backend pieces; UI scaffolding is installed via commands above.

## Troubleshooting & Tips
- Queues: Notifications are queued. For a simple local setup, set `QUEUE_CONNECTION=sync` in `.env`. If you want to use database queues instead:
  1) `php artisan queue:table`
  2) `php artisan migrate`
  3) Run a worker: `php artisan queue:work`
- Storage symlink for file uploads (e.g., logos): `php artisan storage:link`
- Vite: Use `npm run dev` while developing (auto-refresh). For production, use `npm run build`.
- Session driver is `database` by default and the `sessions` table is created by migrations.
- If login/register pages are missing, re-run: `composer require laravel/ui` and `php artisan ui bootstrap --auth`, then rebuild assets with NPM.

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
