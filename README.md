
---

# ☕ Kedai Djanggo

A simple **Laravel-based coffee shop ordering system** with user-friendly features for browsing menus, managing carts, and viewing profiles.

---

## 🚀 Features

* **User Authentication** — Login and logout functionality using Laravel Breeze
* **Menu Display** — Browse categories (Kopi, Minuman, Makanan, Cemilan) with popular items
* **Cart Management** — Add, update, and view items in the cart with dynamic pricing
* **Profile Section** — Display customer info and order history
* **Responsive Design** — Mobile-friendly interface with a bottom navigation bar

---

## 🛠️ Technologies

* **Laravel**: v12.33.0 (Backend framework)
* **PHP**: v8.3.16
* **Alpine.js**: v3.x.x (Frontend interactivity)
* **Tailwind CSS**: For styling
* **MySQL**: Database management

---

## ⚙️ Installation

1. **Clone the repository**

   ```bash
   git clone https://github.com/azra-ahmad/kedai-djanggo.git
   cd kedai-djanggo
   ```

2. **Install dependencies**

   ```bash
   composer install
   npm install
   ```

3. **Copy environment file**

   ```bash
   cp .env.example .env
   ```

   Then configure your database settings inside `.env`.

4. **Generate app key**

   ```bash
   php artisan key:generate
   ```

5. **Run migrations and seed data**

   ```bash
   php artisan migrate --seed
   ```

6. **Start the development servers**

   ```bash
   php artisan serve
   npm run dev
   ```

7. Access the app at:
   👉 [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## 💡 Usage

* Fill the form to start ordering
* Navigate using **Home**, **Cart**, and **Profile** tabs
* Add items to cart and proceed to checkout

---
