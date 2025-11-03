# ☕ Kedai Djanggo - POS System

Modern Point of Sale system untuk kedai/warung kopi dengan fitur:
- 🛒 Customer ordering system dengan floating cart
- 💳 Payment gateway (Midtrans)
- 📊 Admin dashboard dengan analytics
- 🍽️ Menu management (CRUD)
- 💰 Financial reporting
- 👥 Customer management

## 🛠️ Tech Stack
- Laravel 12
- Alpine.js 3
- Tailwind CSS
- Chart.js
- Midtrans Payment Gateway

## ⚙️ Installation

1. Clone repo
```bash
git clone https://github.com/yourusername/kedai-djanggo.git
cd kedai-djanggo
```

2. Install dependencies
```bash
composer install
npm install && npm run build
```

3. Setup environment
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure database & Midtrans credentials in `.env`

5. Start server
```bash
php artisan serve
npm run dev
```

6. Access the app at:
```bash
👉 User: [http://127.0.0.1:8000](http://127.0.0.1:8000)
👉 Admin: [http://127.0.0.1:8000/login](http://127.0.0.1:8000/login)
```

## Default Credentials

**🧑‍💻 Admin:**
- Email: admin@kedai.com
- Password: admin123

## 🚀 Features

### Customer Side
- Identity form untuk ordering
- Menu browsing dengan kategori
- Floating cart (GoFood-style)
- Midtrans payment integration
- Order status tracking
- Order history

### Admin Side
- Dashboard dengan real-time stats
- Order management
- Menu CRUD
- Financial reports dengan charts
- Customer list
- Receipt/Struk generator

## License
MIT
