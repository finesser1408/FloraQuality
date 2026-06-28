# 🌸 Flower Quality Checklist System

A professional, enterprise-grade inspection and quality control system built for the floral industry. This system allows inspectors to record flower conditions, capture digital signatures, and generate real-time analytics.

![Version](https://img.shields.io/badge/version-1.0.0-emerald)
![Laravel](https://img.shields.io/badge/Laravel-12.x-red)
![Livewire](https://img.shields.io/badge/Livewire-4.x-blueviolet)
![Tailwind](https://img.shields.io/badge/Tailwind-CSS-38bdf8)

---

## ✨ Key Features

- **Quality Inspections**: Record detailed flower conditions (Good, Average, Bad) with remarks.
- **Digital Signatures**: Touch-enabled signature pads for both Staff and Suppliers.
- **Dynamic Dashboard**: Real-time KPI cards and interactive 6-month inspection trends.
- **Audit Logging**: Comprehensive tracking of all critical system actions for security.
- **User Management**: Multi-role system (Super Admin, Admin, Staff) with enforced security policies.
- **Dark Mode**: Fully responsive, theme-aware interface (Light & Dark).
- **Reporting**: Export data to CSV or generate print-friendly PDF summaries.

---

## 🚀 Installation Guide

### Prerequisites
- **PHP** 8.2 or higher
- **Composer**
- **Node.js & NPM**
- **SQLite** (Local) or **MySQL** (Production)

### 1. Clone & Install
```bash
git clone <your-repo-url>
cd "Flower System"

# Install dependencies
composer install
npm install
```

### 2. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Database Configuration (SQLite)
Create the local database file:
```bash
touch database/database.sqlite
```
Update your `.env`:
```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/your/database/database.sqlite
```

### 4. Finalize Setup
```bash
php artisan migrate --seed
php artisan storage:link
npm run build
```

---

## 💻 Running Locally

To run the application on your local machine, open two terminal windows:

**Window 1: PHP Server**
```bash
php artisan serve
```

**Window 2: Asset Watcher (Vite)**
```bash
npm run dev
```

Visit [http://127.0.0.1:8000](http://127.0.0.1:8000) to access the portal.

---

## 🔐 Default Credentials

| Role | Email | Password |
| :--- | :--- | :--- |
| **Super Admin** | `admin@example.com` | `password` |
| **Admin** | `manager@example.com` | `password` |
| **Staff** | `staff@example.com` | `password` |

---

## ☁️ Deployment (Railway)

1. Connect your GitHub repository to **Railway.app**.
2. Add a **MySQL** service.
3. Add the following **Variables**:
   - `APP_KEY`: (Your generated key)
   - `DB_CONNECTION`: `mysql`
   - `APP_URL`: `${{RAILWAY_STATIC_URL}}`
4. Set the **Start Command**:
   ```bash
   php artisan migrate --force && php artisan storage:link && php artisan serve --host=0.0.0.0 --port=$PORT
   ```
5. **Add a Volume** mounted at `/app/storage/app/public` to persist signature images.

---

## 🛠️ Built With
- **Laravel 12** - The PHP Framework for Web Artisans.
- **Livewire 4** - Full-stack framework for Laravel.
- **Tailwind CSS** - Utility-first CSS framework.
- **Alpine.js** - Lightweight JavaScript framework.
- **Chart.js** - Interactive data visualization.
- **Signature Pad** - Digital ink signature capture.

---

## 📄 Documentation
For more detailed information on system architecture and administrator procedures, refer to the [DOCUMENTATION.md](./DOCUMENTATION.md) file.
