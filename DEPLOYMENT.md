# Invento SaaS - Deployment & Walkthrough Guide

Invento is a production-ready, multi-tenant retail management SaaS built using a custom, zero-dependency PHP MVC framework. It is designed to be highly portable and easily deployed on any standard LAMP/LEMP stack (from free hosts like InfinityFree to dedicated VPS servers).

---

## 1. System Requirements

- **PHP:** 8.0 or higher
- **Database:** MySQL 5.7+ or MariaDB 10.2+
- **Extensions:** `pdo_mysql`, `mbstring`, `zip`, `libxml` (for native XLSX parsing)
- **Web Server:** Apache (with `mod_rewrite` enabled) or Nginx.

---

## 2. Deployment Process

### Step 1: File Upload
1. Zip the entire `invento` folder.
2. Upload the zip file to your hosting provider's web root (typically `public_html`, `htdocs`, or `/var/www/html`).
3. Extract the contents. Ensure the `.htaccess` files in the root and `public/` directory are preserved.

### Step 2: Database Setup
1. Log into your host's database management tool (e.g., phpMyAdmin).
2. Create a new MySQL database (e.g., `invento_db`).
3. Import the `database/schema.sql` file to create the necessary tables, relationships, and the default Super Admin account.

### Step 3: Environment Configuration
1. In the root of your application, rename `.env.example` to `.env`.
2. Edit the `.env` file with your production details:

```ini
APP_ENV=production
APP_URL=https://yourdomain.com
DB_HOST=localhost
DB_NAME=invento_db
DB_USER=your_database_username
DB_PASS=your_database_password
```
*(Note: If deploying to a subfolder, adjust APP_URL accordingly, e.g., `https://yourdomain.com/invento`)*

### Step 4: Security Verification
1. Ensure your host correctly interprets the root `.htaccess`. It should redirect all incoming traffic into the `public/` folder, protecting the `app/`, `core/`, and `database/` directories from direct URL access.
2. Ensure you have an active SSL certificate (Let's Encrypt or similar) and force HTTPS traffic to secure the session cookies.

---

## 3. End-to-End Walkthrough

### Part A: Super Admin Provisioning
1. Navigate to: `https://yourdomain.com/admin/login`
2. Log in using the default Super Admin credentials (check `schema.sql` for the hashed default, usually `admin / password` or create one manually via SQL).
3. **Action:** The Super Admin dashboard will appear. From here, you can view registered businesses, suspend them, or artificially extend their subscriptions.

### Part B: Business Registration & Setup
*(Currently, businesses can be manually seeded by the Super Admin, or via a future public registration form.)*
1. As a new Business Owner, navigate to: `https://yourdomain.com/login`
2. **Dashboard:** The owner sees the main dashboard (Zero metrics on day one).
3. **Store Creation:** Go to **Stores**. The owner creates their branch locations (e.g., "Indore Branch", "Bhopal Branch").
4. **Staff Creation:** Go to **Staff**. The owner creates Cashier/Manager accounts and strictly assigns them to the specific stores created above.

### Part C: Operational Flow
1. **Catalog:** The owner goes to **Products -> Add Product** and adds their global catalog (e.g., "UltraTech Cement", "Asian Paints"). *Alternatively, use the Settings -> Excel Import feature.*
2. **Inventory In:** A manager logs in, switches to their assigned store, goes to **Inventory -> Stock In**, and adds 500 units of Cement. This creates a specific `inventory_batches` record with a timestamp (critical for FIFO).
3. **POS Billing:**
   - Go to **Billing**.
   - Search for "Cement". The system dynamically verifies that *this specific store* has stock available.
   - Add to cart. GST is calculated based on inclusive/exclusive rules.
   - Enter payment details. If paid partially, the system flags the invoice as `due` and creates a Credit Ledger entry automatically.
   - Generate Bill. The system deducts stock exactly following FIFO logic across the oldest available batches.
4. **Credit Management:** Go to **Credit**. View outstanding customer balances. Click to generate a pre-filled WhatsApp reminder.
5. **Reporting:** The owner logs back in, views the **Dashboard**, and sees consolidated Sales, Live Stock Valuation, Outstanding Credit, and Dead Stock (items unsold for 90+ days).

---

## 4. Subscriptions & PWA

- **Read-Only Mode:** When a business subscription reaches its expiry date, the application automatically locks the owner and staff out of `POST` requests. They can view old bills and export data, but cannot modify the database until the Super Admin extends the plan.
- **PWA Installation:** On mobile devices (Android/iOS), users can click "Add to Home Screen" in their browser. The application will install like a native app, complete with caching strategies (Network-First) managed by the Service Worker for lightning-fast UI loading while ensuring financial data remains perfectly accurate.
