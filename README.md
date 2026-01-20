# Kedai-Djanggo — QR-Based Food Ordering System

A Laravel-based QR ordering system designed for small food businesses (UMKM). Customers scan a QR code, browse the menu, add items to cart, and pay via Midtrans — all without account registration.

---

## Table of Contents

1. [Project Overview](#project-overview)
2. [Tech Stack](#tech-stack)
3. [System Architecture](#system-architecture)
4. [Payment Flow](#payment-flow)
5. [API & Routes Overview](#api--routes-overview)
6. [Database Structure](#database-structure)
7. [Testing Strategy](#testing-strategy)
8. [Setup Guide](#setup-guide)
9. [Important Notes & Warnings](#important-notes--warnings)
10. [Default Credentials](#default-credentials)
11. [Credits](#credits)

---

## Project Overview

### What is Kedai-Djanggo?

Kedai-Djanggo is a web-based food ordering system built for small coffee shops and food stalls (UMKM). It enables customers to place orders through a mobile-friendly interface accessed via QR code scanning, eliminating the need for traditional account registration.

### Problem Statement

Small food businesses face challenges in:
- Managing manual orders during peak hours
- Tracking payment status across multiple customers
- Providing digital payment options without expensive POS infrastructure

### Solution

Kedai-Djanggo provides:
- **QR Code Entry**: Customers scan a table QR code to start ordering
- **Session-Based Identity**: No account required — just name and phone number
- **Digital Payment**: Integrated Midtrans payment gateway
- **Real-Time Order Tracking**: Customers can monitor order status
- **Admin Dashboard**: Business owners manage orders, menus, and view reports

### Key Features

**Customer Side:**
- QR code-based menu access
- Category-filtered menu browsing
- Floating cart with quantity management
- Midtrans Snap payment (e-wallet, bank transfer, QRIS)
- Order status tracking
- Digital receipt

**Admin Side:**
- Authentication-protected dashboard
- Order management (assign, complete, fail)
- Menu CRUD operations with image upload
- Financial reporting with charts
- Revenue analytics by date range

---

## Tech Stack

| Layer | Technology |
|-------|------------|
| **Backend Framework** | Laravel 12 (PHP 8.2+) |
| **Architecture Pattern** | MVC (Model-View-Controller) |
| **ORM** | Eloquent ORM (Active Record Pattern) |
| **Frontend Templating** | Blade Templates |
| **Frontend Interactivity** | Alpine.js 3 |
| **CSS Framework** | Tailwind CSS |
| **Charts** | Chart.js |
| **Database** | MySQL (manually created, no migrations used) |
| **Payment Gateway** | Midtrans Snap API + Server Webhook |
| **Image Processing** | Intervention Image v3 |
| **Session Management** | Laravel Server-Side Session (File Driver) |
| **Build Tool** | Vite |

---

## System Architecture

### MVC Request Flow

```
HTTP Request
    ↓
Routes (routes/web.php)
    ↓
Middleware (auth, CSRF verification)
    ↓
Controller (business logic, validation)
    ↓
Model (Eloquent ORM ↔ MySQL)
    ↓
View (Blade template rendering)
    ↓
HTTP Response
```

### Session-Based Customer Identity

Unlike traditional authentication, Kedai-Djanggo uses **session tokens** to identify customers:

1. Customer submits name and phone number
2. System generates a unique **UUID token** (`customer_token`)
3. Token is stored in both:
   - Database (`customers.customer_token`)
   - Server session (`session('customer_token')`)
4. All subsequent requests validate against this token
5. Orders are linked to the token, ensuring session isolation

**Why UUID tokens?**
- Privacy: Even if the same phone number is used, each session is isolated
- Security: Tokens are cryptographically random, preventing enumeration
- Simplicity: No password management required

### Order Lifecycle State Machine

Orders follow a strict state transition:

```
┌─────────────────────────────────────────────────────┐
│                                                     │
│   ┌─────────┐     ┌─────────┐     ┌───────────┐    │
│   │ pending │────►│  paid   │────►│   done    │    │
│   └────┬────┘     └─────────┘     └───────────┘    │
│        │                                            │
│        ▼                                            │
│   ┌─────────┐                                       │
│   │ failed  │                                       │
│   └─────────┘                                       │
│                                                     │
└─────────────────────────────────────────────────────┘
```

| State | Description | Triggered By |
|-------|-------------|--------------|
| `pending` | Order created, awaiting payment | Checkout process |
| `paid` | Payment confirmed | Midtrans webhook (authoritative) |
| `done` | Order fulfilled | Admin action |
| `failed` | Payment failed or cancelled | Webhook or customer cancellation |

### Frontend Callback vs Webhook

**Frontend Callback (JavaScript):**
- Triggered by Midtrans Snap.js in the browser
- Used for UX feedback (redirects, success messages)
- **NEVER updates database** — unreliable (browser may close)

**Server Webhook:**
- Triggered by Midtrans server via HTTP POST
- Exempted from CSRF protection
- Signature-verified for authenticity
- **Single source of truth** for payment status updates

---

## Payment Flow

### Step-by-Step Process

1. **Order Creation**
   - Customer completes checkout
   - System creates `Order` with status `pending`
   - System creates associated `OrderItem` records
   - Unique `midtrans_order_id` generated (format: `KDJ-{timestamp}-{random}`)

2. **Snap Token Generation**
   - System calls Midtrans API with order details
   - Midtrans returns a `snap_token`
   - Token stored in `orders.snap_token`

3. **Frontend Payment Interface**
   - JavaScript calls `snap.pay(snapToken, {...})`
   - Midtrans Snap modal opens
   - Customer selects payment method and completes payment

4. **Frontend Callback (UX Only)**
   ```javascript
   snap.pay(snapToken, {
       onSuccess: function(result) {
           // Redirect to order status page
           // DO NOT update database here
       },
       onPending: function(result) {
           // Show pending message
       },
       onError: function(result) {
           // Show error message
       }
   });
   ```

5. **Webhook Notification (Authoritative)**
   - Midtrans sends POST to `/midtrans/notification`
   - System verifies signature via `Midtrans\Notification` class
   - Database updated based on `transaction_status`:
     - `settlement` → `paid`
     - `capture` (fraud_status=accept) → `paid`
     - `deny`, `expire`, `cancel` → `failed`

6. **Idempotent Handling**
   - Webhook may be called multiple times
   - System checks current status before updating
   - Already `paid` orders are not reverted to `pending`

### Critical Security Rule

> ⚠️ **NEVER trust frontend callback for payment status updates.**
> The webhook is the ONLY authoritative source for payment confirmation.

---

## API & Routes Overview

### Public Routes (No Authentication)

| Method | Route | Controller Method | Description |
|--------|-------|-------------------|-------------|
| GET | `/` | `OrderController@showForm` | Customer identity form |
| POST | `/submit-identity` | `OrderController@submitIdentity` | Submit name & phone |
| GET | `/menu` | `MenuController@index` | Browse menu items |
| GET | `/checkout` | `OrderController@checkout` | Checkout page |
| POST | `/checkout/process` | `OrderController@processCheckout` | Create order + get snap token |
| GET | `/order/status/{id}` | `OrderController@status` | Order status page |
| GET | `/orders` | `OrderController@orders` | Customer's order history |

### AJAX JSON Endpoints

| Method | Route | Description | Response |
|--------|-------|-------------|----------|
| POST | `/add-to-cart` | Add item to session cart | `{ cart_count, cart }` |
| POST | `/update-quantity` | Increment/decrement quantity | `{ cart_count, cart }` |
| POST | `/clear-cart` | Remove all cart items | `{ cart_count: 0 }` |
| GET | `/cart` | Get current cart contents | `{ cart, total }` |

### Admin Routes (Auth Protected)

All routes prefixed with `/admin` and protected by `auth` middleware:

| Method | Route | Description |
|--------|-------|-------------|
| GET | `/admin/dashboard` | Dashboard with analytics |
| GET | `/admin/orders` | Order management list |
| POST | `/admin/order/{id}/complete` | Mark order as done |
| POST | `/admin/order/{id}/fail` | Mark order as failed |
| GET | `/admin/menu` | Menu list |
| POST | `/admin/menu` | Create new menu |
| PUT | `/admin/menu/{id}` | Update menu item |
| DELETE | `/admin/menu/{id}` | Delete menu item |
| GET | `/admin/financial` | Financial reports |

### Webhook Endpoint

| Method | Route | CSRF | Description |
|--------|-------|------|-------------|
| POST | `/midtrans/notification` | **Excluded** | Midtrans payment webhook |

The webhook is excluded from CSRF verification in `VerifyCsrfToken.php`:
```php
protected $except = [
    'midtrans/notification',
];
```

---

## Database Structure

> ⚠️ **Note**: This project does NOT use Laravel migrations. The database schema was created manually in MySQL.

### Tables Overview

| Table | Purpose |
|-------|---------|
| `users` | Admin accounts (Laravel default auth) |
| `customers` | Customer identity records |
| `menus` | Menu items (food & beverages) |
| `orders` | Order headers with payment info |
| `order_items` | Order line items (quantity, subtotal) |

### Entity-Relationship

```
customers (1) ────< orders (N)
                      │
                      ├────< order_items (N) >──── menus (1)
                      │
                      └────> users (1) [admin who completed]
```

### Key Columns Explained

**customers table:**
- `customer_token` (VARCHAR, UUID): Unique session identifier
- Purpose: Enables session isolation without traditional authentication

**orders table:**
- `customer_token`: Links order to specific session (not just customer_id)
- `midtrans_order_id`: Unique ID sent to Midtrans (format: `KDJ-{timestamp}-{random}`)
- `snap_token`: Midtrans payment token
- `status`: ENUM (`pending`, `paid`, `done`, `failed`)

**order_items table:**
- `jumlah`: Quantity ordered
- `subtotal`: Price × quantity (snapshot at order time)

### Column Naming Note

The database uses Indonesian naming conventions in some places:
- `jumlah` = quantity
- `harga` = price
- `total_harga` = total price
- `nama_menu` = menu name
- `kategori_menu` = menu category

---

## Testing Strategy

### Blackbox Testing

Functional testing from the user's perspective:

- **Customer Flow**: QR scan → identity → menu → cart → checkout → payment → status
- **Admin Flow**: Login → dashboard → order management → menu CRUD → reports
- **Edge Cases**: Empty cart checkout, duplicate pending orders, session expiry

### Whitebox Testing

Internal logic and code path verification:

| Test Area | What Was Tested |
|-----------|-----------------|
| Session Isolation | Different sessions with same phone get different orders |
| Webhook Idempotency | Multiple webhook calls don't duplicate updates |
| State Transitions | Only valid transitions allowed (pending→paid, paid→done) |
| Input Validation | SQL injection, XSS, invalid data types |
| Authorization | Non-owners cannot access others' receipts |
| CSRF Protection | All forms protected except webhook |

### Testing Limitations

> Due to time constraints, automated unit tests (PHPUnit) were not implemented. Testing was performed manually following documented test cases.

---

## Setup Guide

### Prerequisites

- PHP 8.2+
- Composer
- Node.js 18+ & npm
- MySQL 8.0+
- Laragon / XAMPP / similar local server
- Midtrans Sandbox Account

### Local Development Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/kedai-djanggo.git
   cd kedai-djanggo
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies and build assets**
   ```bash
   npm install
   npm run build
   ```

4. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Edit `.env` file**
   ```env
   APP_URL=http://127.0.0.1:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=kedai_djanggo
   DB_USERNAME=root
   DB_PASSWORD=

   MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxx
   MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxx
   MIDTRANS_IS_PRODUCTION=false
   ```

6. **Create database manually** (no migrations used)
   - Import the database schema from your database dump
   - Or create tables manually following the schema documentation

7. **Create storage symlink**
   ```bash
   php artisan storage:link
   ```

8. **Start the development server**
   ```bash
   php artisan serve
   ```

9. **Access the application**
   - Customer: http://127.0.0.1:8000
   - Admin: http://127.0.0.1:8000/login

### Ngrok Setup (For Webhook Testing)

Midtrans webhooks require a publicly accessible URL. Use ngrok for local testing:

1. **Install ngrok** and authenticate
   ```bash
   ngrok authtoken YOUR_AUTH_TOKEN
   ```

2. **Start ngrok tunnel**
   ```bash
   ngrok http 8000
   ```

3. **Update `.env`** with ngrok URL
   ```env
   APP_URL=https://xxxx-xxx-xxx.ngrok-free.app
   ```

4. **Build assets for production** (important!)
   ```bash
   npm run build
   ```
   > Do NOT use `npm run dev` with ngrok — Vite's hot reload won't work correctly.

5. **Configure Midtrans Dashboard**
   - Go to Midtrans Sandbox Dashboard
   - Settings → Configuration
   - Set Payment Notification URL to:
     ```
     https://xxxx-xxx-xxx.ngrok-free.app/midtrans/notification
     ```

6. **Restart Laravel server** after `.env` changes
   ```bash
   php artisan serve
   ```

---

## Important Notes & Warnings

### Payment Security

> ⚠️ **CRITICAL**: Frontend JavaScript callbacks must NEVER update payment status in the database. The Midtrans webhook is the only authoritative source for payment confirmation.

### Session Configuration

- `APP_URL` must match the actual URL being accessed
- When using ngrok, `APP_URL` must be the ngrok HTTPS URL
- Session cookies require matching domains to work correctly

### Asset Building

- For local development: `npm run dev` (with hot reload)
- For ngrok/production testing: `npm run build` (compiled assets)
- Never mix these — regenerate assets when switching modes

### Database

- This project does NOT use Laravel migrations
- Schema changes must be applied manually to MySQL
- Always backup before modifying production data

### Common Issues

| Issue | Solution |
|-------|----------|
| CSRF token mismatch | Ensure `APP_URL` matches actual URL |
| Session not persisting | Check cookie domain and HTTPS settings |
| Webhook not received | Verify ngrok URL in Midtrans dashboard |
| Images not showing | Run `php artisan storage:link` |

---

## Default Credentials

**Admin Account:**
- Email: `admin@kedai.com`
- Password: `admin123`

> Change these credentials immediately in production.

---

## Credits

**Project:** Kedai-Djanggo - QR-Based Food Ordering System

**Purpose:** Academic Final Project / Thesis

**Tech Stack:** Laravel 12, Alpine.js, Tailwind CSS, Midtrans

**Author:** [Your Name]

**Institution:** [Your University]

**Year:** 2026

---

## License

This project is developed for academic purposes. All rights reserved.

---

*For technical questions or issues, please refer to the system documentation or contact the project author.*
