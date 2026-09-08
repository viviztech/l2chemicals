# L2 Chemicals — Core PHP Website

Phase 1 establishes a clean Core PHP 8.2+ and MySQL 8+ foundation for the new L2 Chemicals B2B website. Public content is read from MySQL; no retail pricing, cart, checkout, payment, demo products, or WordPress content is included.

## Requirements

- Apache with `mod_rewrite`
- PHP 8.2+ with PDO MySQL and mbstring
- MySQL 8+
- HTTPS in production

## Local or cPanel setup

1. Point the domain document root at this project directory. If cPanel requires `public_html`, upload the complete project there; `.htaccess` blocks direct access to application and database files.
2. Create an empty MySQL database and a database user with privileges on that database.
3. Import `database/schema.sql`, then `database/seed.sql` in that order.
4. Copy `.env.example` to `.env` and enter the database credentials and the final HTTPS `APP_URL` without a trailing slash.
5. Ensure Apache allows overrides so the root `.htaccess` rewrite rules work.
6. Give PHP write access only to `uploads/` and `storage/logs/`. Typical cPanel permissions are `755` for folders and `644` for files; use `775` only when required by the host's PHP user/group configuration. Never use `777`.
7. Confirm the PDO MySQL extension is enabled and select PHP 8.2 or newer in cPanel.

Example production configuration:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://l2chemicals.com
APP_TIMEZONE=Asia/Kolkata
DB_HOST=localhost
DB_PORT=3306
DB_NAME=cpanel_database
DB_USER=cpanel_user
DB_PASS=a-long-random-database-password
```

Never commit `.env`. Production errors are logged to `storage/logs/app.log` without exposing credentials, SQL, stack traces, or server paths to visitors.

## Database and initial content

The schema uses InnoDB, `utf8mb4`, foreign keys, indexes, and timestamps. Seed data contains only business categories, product-family names, industries, and positioning supported by the supplied brief and public L2 Chemicals website. Empty contact/settings fields must be completed with verified company data.

No administrator password is seeded or hard-coded. A one-time, token-protected first-admin installer and the authentication/authorization layer belong to Phase 2; do not insert a plaintext password manually. If an administrator must be created before Phase 2 is implemented, generate the hash with PHP's `password_hash()` and insert only that hash into `users.password_hash` over a secured database connection.

## Uploads

The upload directories are pre-created for products, categories, industries, blog, banners, documents, and general media. Executable extensions and PHP handlers are denied within `uploads/`. The Phase 3/7 upload service will additionally validate extension, detected MIME type, size, destination, and generated random filename before saving media.

## SMTP, SSL, and deployment

SMTP environment placeholders are included but mail delivery is intentionally not connected in Phase 1. RFQ persistence will remain independent of email delivery when that service is added. Install a valid SSL certificate before launch; secure session cookies are enabled automatically under HTTPS.

After deployment, verify clean URLs, writable log/upload directories, PHP error logs, HTTPS redirects at the hosting layer, and that `.env`, `app/`, and `database/` cannot be fetched publicly.

## Phase status

- Phase 1: architecture, configuration, PDO, router, helpers, public layout, schema and seeds — implemented.
- Admin panel: secure login, role enforcement, dashboard, product/category CRUD, content management and RFQ management — implemented at `/admin/login`.
- Product and category image uploads accept verified JPG, PNG and WEBP images up to 5 MB.
- Remaining extended modules from the original roadmap include industry CRUD, blog article CRUD, media library, advanced SEO screens and SMTP notifications.

For an existing Phase 1 database, apply the SQL files in `database/migrations/` in filename order. Fresh databases should import the current `schema.sql` and `seed.sql` only. Create administrator passwords with PHP `password_hash()` and never store a plaintext password in SQL or source files.
