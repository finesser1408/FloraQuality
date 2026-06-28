# Flower Quality Checklist System — Documentation

## Introduction
The Flower Quality Checklist System is a professional, enterprise-grade application built with Laravel 12, Livewire, and Tailwind CSS. It is designed to streamline the inspection process for flower quality, allowing staff to record conditions, capture digital signatures, and generate comprehensive reports.

---

## Installation Guide

### Requirements
- PHP 8.2 or higher
- MySQL 8.0 or higher
- Composer
- Node.js & NPM

### Installation Steps
1. **Clone the repository**:
   ```bash
   git clone <repository-url>
   cd flower-system
   ```
2. **Install PHP dependencies**:
   ```bash
   composer install
   ```
3. **Install JS dependencies**:
   ```bash
   npm install
   ```
4. **Environment Configuration**:
   - Copy `.env.example` to `.env`.
   - Update database credentials and other settings.
5. **Generate Application Key**:
   ```bash
   php artisan key:generate
   ```
6. **Run Migrations & Seeders**:
   ```bash
   php artisan migrate --seed
   ```
7. **Create Storage Link**:
   ```bash
   php artisan storage:link
   ```
8. **Build Assets**:
   ```bash
   npm run build
   ```
9. **Start the Application**:
   ```bash
   php artisan serve
   ```

---

## User Manual

### Logging In
- Access the login page at `/login`.
- Enter your email and password.
- If it's your first time logging in, you may be required to change your password for security.

### Managing Inspections
- **View Inspections**: Navigate to "Inspections" to see a list of all records.
- **Create Inspection**: Click "New Inspection", select the date/time, choose the condition, add remarks, and collect digital signatures from both staff and supplier.
- **Edit/Delete**: Use the actions menu on the inspections list (permissions apply).

### Digital Signatures
- Signatures are captured using touch-enabled signature pads.
- Click "Clear" to reset a signature.
- Signatures are securely stored and included in inspection summaries.

### Dashboard & Analytics
- The Dashboard provides a real-time overview of inspection stats.
- View total, good, average, and bad inspection counts.
- Monitor inspection trends over the last 6 months via the interactive chart.

---

## Administrator Guide

### User Management
- Administrators can manage user accounts via the "Users" section.
- Create new users, assign roles (Super Admin, Admin, Staff), and deactivate accounts.
- Password changes can be enforced for any user.

### Roles & Permissions
- **Super Admin**: Full access to everything, including audit logs and user management.
- **Admin**: Full access to inspections and reports, but restricted from some system-level settings.
- **Staff**: Can create and view their own inspections.

### Audit Logs
- Every critical action (login, logout, CRUD, exports) is recorded in the Audit Logs.
- Logs include the user, action, description, IP address, and timestamp.

---

## Deployment Checklist
- [ ] Set `APP_DEBUG=false` in `.env`.
- [ ] Ensure `APP_ENV=production`.
- [ ] Configure mail server for password resets.
- [ ] Run `php artisan optimize` (caches config, routes, and views).
- [ ] Ensure storage permissions are correctly set for `storage/` and `bootstrap/cache/`.
- [ ] Set up a task scheduler for any automated tasks if added.
