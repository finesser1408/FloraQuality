# PRAZ Flower Checklist System — Technical Documentation

**Version:** 1.0.0  
**Stack:** Laravel 12 · Livewire 4 · Tailwind CSS v4 · Alpine.js · MySQL / SQLite  
**Prepared by:** Development Team  
**Last Updated:** June 2026

---

## Table of Contents

1. [System Overview](#1-system-overview)
2. [Technology Stack](#2-technology-stack)
3. [Application Architecture](#3-application-architecture)
4. [Database Design](#4-database-design)
5. [Authentication System](#5-authentication-system)
6. [Role-Based Access Control](#6-role-based-access-control)
7. [The Digital Signature System](#7-the-digital-signature-system)
8. [Livewire Components](#8-livewire-components)
9. [Audit Logging System](#9-audit-logging-system)
10. [Dashboard and Analytics](#10-dashboard-and-analytics)
11. [Reporting and Exports](#11-reporting-and-exports)
12. [Asset Pipeline](#12-asset-pipeline)
13. [PRAZ Design System](#13-praz-design-system)
14. [Dark Mode Implementation](#14-dark-mode-implementation)
15. [Security Hardening](#15-security-hardening)
16. [Deployment Architecture](#16-deployment-architecture)
17. [Known Limitations and Future Work](#17-known-limitations-and-future-work)

---

## 1. System Overview

The **PRAZ Flower Checklist System** is a web-based internal quality control and inspection management application built for the Procurement Regulatory Authority of Zimbabwe (PRAZ). It enables authorized staff to:

- Record daily flower quality inspections with a structured condition rating (Good, Average, Bad).
- Capture legally-admissible digital signatures from both the inspecting staff member and the flower supplier.
- Store all records in a secure, queryable database with full audit trails.
- Generate, filter, and export inspection reports in CSV and print-friendly PDF format.
- Manage users, assign roles, and enforce hierarchical access control.

The system is designed to replace paper-based quality checklists, reducing data loss and improving traceability across inspection cycles.

---

## 2. Technology Stack

### Why Each Technology Was Chosen

#### PHP / Laravel 12
Laravel is the server-side framework that handles all routing, business logic, database interaction, authentication, and security. Laravel 12 was chosen for the following reasons:

- **Eloquent ORM**: Provides an elegant, safe way to interact with the database without writing raw SQL, preventing SQL injection by design.
- **Built-in Security**: CSRF protection, session management, password hashing, and rate limiting are all handled by the framework.
- **Blade Templating Engine**: Allows server-side rendering of HTML with conditional logic, loops, and component reuse.
- **Artisan CLI**: Provides commands for database migration, seeding, caching, and maintenance without direct database access.
- **Policy-based Authorization**: A formal, testable way to define who can perform which actions.

#### Livewire 4
Livewire is a full-stack framework for Laravel that allows building dynamic, reactive user interfaces without writing custom JavaScript for every interaction. It works by:

- Rendering a Blade component on the server and sending the HTML to the browser.
- When a user interacts (e.g., types in a search box), Livewire sends a small AJAX request to the server with the updated state.
- The server re-renders only the changed portion of the component and sends a diff back.
- The browser patches the DOM with the change without a full page reload.

This is why the search, filters, sorting, and modals in the Inspections and User Management pages respond instantly without navigating away. Livewire was chosen over Vue.js or React because it allows writing all logic in PHP, keeping the codebase in a single language and dramatically reducing complexity.

#### Tailwind CSS v4
Tailwind is a utility-first CSS framework. Instead of writing named CSS classes (e.g., `.btn-large`), you compose styles directly in your HTML using small, single-purpose classes (e.g., `flex items-center gap-3 px-4 py-2`). Tailwind v4 was used because:

- **Zero runtime overhead**: Tailwind scans your source files at build time and generates only the CSS classes you actually used. The final CSS file contains no unused styles.
- **Consistent design**: Every spacing, colour, and size decision comes from a defined scale, making the UI naturally consistent.
- **Dark mode**: Tailwind's dark mode variant (`dark:`) enables automatic styling for dark themes.

#### Alpine.js
Alpine.js provides lightweight JavaScript reactivity for DOM interactions that are purely client-side and do not need to talk to the server. In this system it handles:

- **Dropdown menus** (user profile menu in the top bar)
- **Theme toggling** (dark/light mode switch)
- **Sidebar collapse** state
- **Flash message auto-dismiss** (disappears after 4.5 seconds)
- **Password visibility toggle** (eye icon on the login form)

Alpine was chosen because it has a tiny footprint (~15KB) and its syntax lives directly in HTML attributes, making it easy to read and maintain without separate JavaScript files.

#### MySQL (Production) / SQLite (Development)
MySQL is used as the production database on Railway because it is the industry standard for relational data and handles concurrent writes from multiple users reliably. SQLite is used locally for development because it requires zero setup — it is a single file on disk.

The codebase handles both databases transparently, with one exception: the `DATE_FORMAT()` function used in the monthly inspection trend query. MySQL uses `DATE_FORMAT(check_date, '%m-%Y')` while SQLite uses `strftime('%m-%Y', check_date)`. The `Dashboard.php` component detects the active database driver at runtime and chooses the correct SQL function:

```php
$driver  = \Illuminate\Support\Facades\DB::getDriverName();
$dateSql = $driver === 'sqlite'
    ? "strftime('%m-%Y', check_date)"
    : "DATE_FORMAT(check_date, '%m-%Y')";
```

This is a deliberate design decision to keep both environments working without separate code paths.

#### Vite
Vite is the build tool that compiles CSS and JavaScript for production. It replaces the old Laravel Mix (webpack-based) system. Vite was chosen because it is significantly faster during development (it uses native ES modules and skips bundling during development) and produces highly optimized, content-hashed output files for production.

---

## 3. Application Architecture

The system follows the **Model-View-Controller (MVC)** pattern with Livewire acting as the controller and view in a single component class.

```
┌─────────────────────────────────────────────────────┐
│                     Browser                         │
│  HTML + CSS (Tailwind) + JS (Alpine.js + Livewire)  │
└──────────────────────┬──────────────────────────────┘
                       │ HTTP / AJAX (Livewire wire calls)
┌──────────────────────▼──────────────────────────────┐
│                  Laravel Router                      │
│         routes/web.php + routes/auth.php            │
└──────────────────────┬──────────────────────────────┘
                       │
          ┌────────────▼────────────┐
          │   Middleware Stack      │
          │  auth, verified,        │
          │  EnforcePasswordChange, │
          │  TrustProxies           │
          └────────────┬────────────┘
                       │
        ┌──────────────▼──────────────┐
        │  Livewire Components        │
        │  (Dashboard, ChecklistForm, │
        │   ChecklistTable, etc.)     │
        │                             │
        │  OR                         │
        │                             │
        │  Standard Controllers       │
        │  (Auth, Profile, Password)  │
        └──────────────┬──────────────┘
                       │
        ┌──────────────▼──────────────┐
        │  Services                   │
        │  AuditService               │
        │  SignatureService           │
        └──────────────┬──────────────┘
                       │
        ┌──────────────▼──────────────┐
        │  Eloquent Models            │
        │  User, FlowerChecklist,     │
        │  ActivityLog                │
        └──────────────┬──────────────┘
                       │
        ┌──────────────▼──────────────┐
        │  Database                   │
        │  MySQL (Production)         │
        │  SQLite (Development)       │
        └─────────────────────────────┘
```

### Key Directories

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/           ← Login, logout, password reset
│   │   └── ProfileController.php
│   ├── Livewire/           ← All interactive page components
│   ├── Middleware/
│   │   └── EnforcePasswordChange.php
│   └── Requests/           ← Form validation (ProfileUpdateRequest, LoginRequest)
├── Models/
│   ├── User.php
│   ├── FlowerChecklist.php
│   └── ActivityLog.php
├── Policies/
│   ├── ChecklistPolicy.php
│   └── UserPolicy.php
├── Providers/
│   └── AppServiceProvider.php   ← Registers policies, Livewire components
├── Services/
│   ├── AuditService.php
│   └── SignatureService.php
└── Listeners/
    ├── LogSuccessfulLogin.php
    └── LogSuccessfulLogout.php

resources/
├── css/app.css              ← PRAZ design system
├── js/app.js                ← Alpine.js + Axios bootstrap
└── views/
    ├── auth/                ← Login, password reset pages
    ├── components/          ← Reusable Blade components
    ├── layouts/             ← Sidebar, navigation, app shell
    ├── livewire/            ← Blade templates for Livewire components
    └── profile/             ← Profile settings pages
```

---

## 4. Database Design

The system uses four tables. Three are custom, and one is the standard Laravel sessions table.

### Table: `users`

Stores all system accounts. Extended beyond the Laravel default with PRAZ-specific columns.

| Column | Type | Description |
| :--- | :--- | :--- |
| `id` | bigint, PK | Auto-incrementing primary key |
| `name` | varchar(255) | Full display name |
| `email` | varchar(255), unique | Login email address |
| `password` | varchar(255) | Bcrypt-hashed password (never stored in plaintext) |
| `role` | varchar | One of: `super_admin`, `admin`, `staff` |
| `phone_number` | varchar, nullable | Optional contact number |
| `status` | varchar | `active` or `inactive` |
| `last_login_at` | timestamp, nullable | Populated by the login event listener |
| `require_password_change` | boolean | Forces the user to change password on next login |
| `email_verified_at` | timestamp, nullable | Laravel email verification (pre-verified for seeded users) |
| `remember_token` | varchar | For "Remember Me" login sessions |
| `created_at`, `updated_at` | timestamps | Automatic Laravel timestamps |

### Table: `flower_checklists`

The core inspection record table. Each row is one quality inspection.

| Column | Type | Description |
| :--- | :--- | :--- |
| `id` | bigint, PK | Auto-incrementing primary key |
| `check_date` | date | The date the inspection took place |
| `check_time` | time | The time the inspection took place |
| `condition` | enum | One of: `good`, `average`, `bad` |
| `remarks` | text, nullable | Free-text observations from the inspector |
| `staff_signature` | varchar, nullable | **File path** to the staff member's PNG signature |
| `supplier_signature` | varchar, nullable | **File path** to the supplier's PNG signature |
| `user_id` | bigint, FK | References `users.id` — who logged the record |
| `created_at`, `updated_at` | timestamps | Automatic |

**Why store file paths, not the image data?**
Storing image binary data inside a database column is extremely inefficient. It bloats the database, makes backups large, and slows down every query that touches the table. Instead, the PNG file is saved to the server's filesystem (the `storage/app/public/signatures/` directory) and only the path (e.g., `signatures/staff/uuid.png`) is stored in the database. When the image needs to be displayed, Laravel's `Storage::url($path)` resolves the path to the full public URL.

### Table: `activity_logs`

Every significant user action is recorded here for full auditability.

| Column | Type | Description |
| :--- | :--- | :--- |
| `id` | bigint, PK | Auto-incrementing primary key |
| `user_id` | bigint, FK | Who performed the action |
| `action` | varchar | The type of action (e.g., `login`, `created`, `updated`, `deleted`, `exported`) |
| `model_type` | varchar | What entity was acted on (e.g., `FlowerChecklist`, `User`) |
| `model_id` | bigint, nullable | The ID of the entity (null for system events) |
| `description` | text | Human-readable summary of what happened |
| `ip_address` | varchar(45) | The IP address of the request (supports IPv6) |
| `user_agent` | varchar | The browser and device string |
| `created_at`, `updated_at` | timestamps | Automatic |

### Database Indexes

Indexes are defined in the migrations to ensure fast queries even with large datasets:

- `flower_checklists`: composite index on `(check_date, condition)` for fast date-range and condition filtering; index on `user_id` for role-scoped queries.
- `activity_logs`: composite index on `(model_type, model_id)` for polymorphic lookups; index on `user_id`.

---

## 5. Authentication System

Authentication was built on top of **Laravel Breeze**, which provided the initial scaffolding for login, logout, password reset, and email verification flows. The Breeze views were then completely replaced with the PRAZ custom interface.

### Login Flow

1. The user submits the login form to `POST /login`.
2. **Rate limiting**: The `LoginRequest` enforces a maximum of **5 failed attempts** per email+IP combination within 1 minute. On the 6th attempt, the account is locked for 60 seconds. This prevents brute-force attacks.
3. On success, Laravel regenerates the session ID to prevent session fixation attacks.
4. The `LogSuccessfulLogin` event listener fires, updating `last_login_at` and writing an entry to `activity_logs`.
5. If `require_password_change` is `true` on the user's account, the `EnforcePasswordChange` middleware intercepts every subsequent request and redirects to the profile settings page until the password is changed.

### Password Security

Passwords are hashed using **bcrypt** via Laravel's `Hash::make()` function. Bcrypt is a deliberately slow hashing algorithm, making it computationally expensive to crack even if the database is compromised. The hash includes an auto-generated salt so two users with the same password will have different hash values.

Passwords are **never stored in plaintext, never logged, and never appear in any output.**

### The `EnforcePasswordChange` Middleware

```
Flower System/app/Http/Middleware/EnforcePasswordChange.php
```

This middleware runs on every web request after authentication. If the authenticated user has `require_password_change = true`, the middleware allows only:

- `GET /profile` — so the user can see the form
- `PUT /password` — so the user can actually submit the new password
- `POST /logout` — so the user can exit if needed

All other routes redirect to the profile page with a warning banner. Once the user submits a new password through `PasswordController@update`, the controller sets `require_password_change = false` and redirects to the dashboard with a success message.

---

## 6. Role-Based Access Control

The system uses Laravel's **Policy** system to enforce authorization. Policies are PHP classes that define a set of methods, each returning a boolean indicating whether a given user can perform a given action.

### Roles

| Role | String Value | Capabilities |
| :--- | :--- | :--- |
| Super Administrator | `super_admin` | Full access to everything including user management, audit logs |
| Administrator | `admin` | Full inspection management and reporting; cannot manage users |
| Staff / Inspector | `staff` | Can create inspections and view only their own records |

### ChecklistPolicy

```
Flower System/app/Policies/ChecklistPolicy.php
```

| Method | Who Can? |
| :--- | :--- |
| `view` | Super Admin, Admin, or the Staff member who created the checklist |
| `create` | Any authenticated user |
| `update` | Super Admin, Admin, or the Staff member who created it |
| `delete` | Super Admin or Admin (Admins cannot delete checklists created by Super Admins) |
| `export` | Super Admin and Admin only |

### UserPolicy

```
Flower System/app/Policies/UserPolicy.php
```

| Method | Who Can? |
| :--- | :--- |
| `create` | Super Admin only |
| `update` | Super Admin only |
| `delete` | Super Admin only; cannot delete self or another Super Admin |
| `manageSystem` | Super Admin only (Audit Logs access) |

### How Policies are Enforced

Policies are registered in `AppServiceProvider.php`:

```php
Gate::policy(FlowerChecklist::class, ChecklistPolicy::class);
Gate::policy(User::class, UserPolicy::class);
```

In Livewire components, the `$this->authorize('action', $model)` call is used. If the current user fails the policy check, Laravel automatically throws a `403 Forbidden` exception, which renders the custom `errors/403.blade.php` page.

In Blade templates, the `@can` directive shows or hides UI elements:

```blade
@can('delete', $checklist)
    <button wire:click="confirmDelete({{ $checklist->id }})">Delete</button>
@endcan
```

This means the Delete button is not even rendered in the HTML for users who do not have permission — it is not just hidden with CSS.

---

## 7. The Digital Signature System

This is the most technically interesting part of the application. The signature system involves three distinct layers: the browser, the server, and the filesystem.

### How It Works — End to End

```
┌────────────────────────────────────────────────────────────────┐
│  STEP 1: The browser draws                                     │
│                                                                │
│  A <canvas> HTML element is rendered on the inspection form.   │
│  The JavaScript library "Signature Pad" (by Szymon Nowak)      │
│  intercepts mouse/touch events on the canvas and draws        │
│  smooth Bezier curves using the Canvas 2D API.                │
│                                                                │
│  Pen colour: PRAZ Blue (#003580)                              │
│  Min stroke width: 1.5px                                      │
│  Max stroke width: 2.8px (mimics real pen pressure)          │
└───────────────────────────────┬────────────────────────────────┘
                                │
┌───────────────────────────────▼────────────────────────────────┐
│  STEP 2: Convert to base64                                     │
│                                                                │
│  When the user lifts their pen (the "endStroke" event fires), │
│  the library calls canvas.toDataURL('image/png'), which        │
│  encodes the entire canvas pixel data as a base64 string.     │
│                                                                │
│  The string starts with: "data:image/png;base64,iVBORw0KGgo..." │
│  and can be several thousand characters long.                 │
│                                                                │
│  This string is sent to the Livewire PHP component via a      │
│  wire call: @this.updateStaffSignature(base64String)          │
└───────────────────────────────┬────────────────────────────────┘
                                │ Livewire AJAX (JSON payload)
┌───────────────────────────────▼────────────────────────────────┐
│  STEP 3: Livewire stores the data in PHP memory               │
│                                                                │
│  The ChecklistForm component has two public string properties: │
│  $staffSignatureData and $supplierSignatureData               │
│                                                                │
│  These hold the raw base64 strings temporarily.               │
│  They are NOT saved to the database yet.                      │
└───────────────────────────────┬────────────────────────────────┘
                                │ On form submit (wire:submit)
┌───────────────────────────────▼────────────────────────────────┐
│  STEP 4: SignatureService processes and saves the file        │
│                                                                │
│  SignatureService::store() is called with the base64 string.  │
│                                                                │
│  1. Validates that the string starts with                     │
│     "data:image/png;base64," (rejects non-PNG data)          │
│  2. Strips the data URL prefix, leaving only the base64 data  │
│  3. Decodes the base64 to raw binary PNG data                 │
│  4. Generates a UUID filename (e.g., "a3f9c1b2-...uuid.png") │
│  5. Writes the binary to:                                     │
│     storage/app/public/signatures/staff/<uuid>.png            │
│  6. Returns the relative path: "signatures/staff/<uuid>.png"  │
│                                                                │
│  This path is then stored in the flower_checklists table.     │
└───────────────────────────────┬────────────────────────────────┘
                                │
┌───────────────────────────────▼────────────────────────────────┐
│  STEP 5: Displaying the signature                             │
│                                                                │
│  When viewing an inspection record, the path from the         │
│  database is passed to Storage::url($path), which converts   │
│  it to a publicly accessible URL:                             │
│                                                                │
│  /storage/signatures/staff/<uuid>.png                         │
│                                                                │
│  This works because "php artisan storage:link" creates a      │
│  symbolic link from public/storage → storage/app/public,     │
│  making the files accessible via HTTP.                        │
└────────────────────────────────────────────────────────────────┘
```

### Why UUID Filenames?

Each signature is saved with a UUID (Universally Unique Identifier) as its filename. This is intentional:

1. **No collisions**: Two inspections done at the same second by different users will never overwrite each other's signature files.
2. **No enumeration**: An attacker cannot guess signature URLs by incrementing a number. URLs like `/storage/signatures/staff/a3f9c1b2-4d2a-4f1e-b3c7-9e8d2a1f0c5b.png` are effectively unguessable.
3. **No filenames from user input**: The signature name is never derived from user-supplied data, eliminating a whole class of path traversal vulnerabilities.

### Editing an Existing Inspection

When a user edits an existing inspection, the existing signature paths are loaded into `$existingStaffSig` and `$existingSupplierSig`. If the user draws a new signature, the old file is replaced and the new path is stored. If the user does not draw on the pad, the existing path is kept. This logic lives in `ChecklistForm@save`:

```php
$staffSig = $this->sigService->store($this->staffSignatureData ?: null, 'staff')
            ?? $this->existingStaffSig;
```

The `??` (null coalescing) operator means: "use the newly stored path, or fall back to the existing path if no new signature was provided."

---

## 8. Livewire Components

All interactive pages in the system are Livewire components. Each component is a PHP class that:

1. Holds the current state (public properties).
2. Handles user actions (public methods).
3. Returns a rendered Blade view via its `render()` method.

| Component | File | Purpose |
| :--- | :--- | :--- |
| `Dashboard` | `app/Http/Livewire/Dashboard.php` | KPI cards, chart data, recent inspections |
| `ChecklistForm` | `app/Http/Livewire/ChecklistForm.php` | Create and edit inspections with signature capture |
| `ChecklistTable` | `app/Http/Livewire/ChecklistTable.php` | Searchable, sortable, paginated inspection list |
| `ChecklistView` | `app/Http/Livewire/ChecklistView.php` | Read-only inspection detail view |
| `ReportGenerator` | `app/Http/Livewire/ReportGenerator.php` | Report filtering and CSV export |
| `UserManager` | `app/Http/Livewire/UserManager.php` | Full CRUD for user accounts |
| `AuditLogs` | `app/Http/Livewire/AuditLogs.php` | Searchable, filtered audit log viewer |

### Computed Properties

The `Dashboard` component uses Livewire's computed properties pattern. Instead of querying the database in `render()`, each stat is a method prefixed with `get` and suffixed with `Property`:

```php
public function getTotalProperty(): int
{
    return FlowerChecklist::count();
}
```

This can be accessed in the view as `$total` or `$this->total`. Computed properties are evaluated lazily — they only run if the view actually uses them.

### The `WithPagination` Trait

The `ChecklistTable`, `UserManager`, and `AuditLogs` components use Livewire's `WithPagination` trait. This integrates with Laravel's built-in pagination but re-resets to page 1 automatically when any filter changes. This is wired using the `updatingX()` lifecycle hooks:

```php
public function updatingSearch(): void { $this->resetPage(); }
public function updatingCondition(): void { $this->resetPage(); }
```

### Query String Synchronization

Filters are persisted to the URL query string using `$queryString`. This means if a user filters inspections by "bad" condition and then copies the URL, anyone opening that URL will see the same filtered view. It also means the browser's back button correctly restores filters.

```php
protected $queryString = [
    'search'    => ['except' => ''],
    'condition' => ['except' => ''],
    'dateFrom'  => ['except' => ''],
    'dateTo'    => ['except' => ''],
];
```

The `'except' => ''` means the query string parameter is omitted from the URL when the value is empty (keeps URLs clean).

---

## 9. Audit Logging System

Every critical action in the system is recorded in the `activity_logs` table. This is implemented using two mechanisms that work together.

### Mechanism 1: AuditService

```
Flower System/app/Services/AuditService.php
```

A static service class that provides a single `log()` method called directly in controllers and Livewire components after any state-changing operation:

```php
AuditService::log(
    'created',           // action
    'FlowerChecklist',   // model_type
    $checklist->id,      // model_id
    "Created checklist #{$checklist->id}" // description
);
```

The service automatically captures the current user's ID, the request IP address, and the user agent (browser/device string) using Laravel's `Request` facade.

### Mechanism 2: Event Listeners

Login and logout events cannot be captured in a Livewire component or controller because they are dispatched by the framework itself. Event listeners solve this:

- `LogSuccessfulLogin` listens for `Illuminate\Auth\Events\Login` and records the login event plus updates `last_login_at`.
- `LogSuccessfulLogout` listens for `Illuminate\Auth\Events\Logout` and records the logout event.

Both listeners are registered in `AppServiceProvider::boot()`:

```php
Event::listen(
    \Illuminate\Auth\Events\Login::class,
    \App\Listeners\LogSuccessfulLogin::class
);
```

### Actions That Are Logged

| Action | Trigger |
| :--- | :--- |
| `login` | Successful authentication |
| `logout` | Session destruction |
| `created` | New inspection, new user account |
| `updated` | Checklist edit, profile change, password change, role change |
| `deleted` | Checklist deletion, user account deletion |
| `exported` | CSV report downloaded |

---

## 10. Dashboard and Analytics

### KPI Cards

The five statistic cards (Total, Good, Average, Bad, Today) are each simple `COUNT` queries on the `flower_checklists` table, executed using Eloquent query scopes defined in the `FlowerChecklist` model:

```php
public function scopeByCondition(Builder $query, string $condition): Builder
{
    return $query->where('condition', $condition);
}

public function scopeToday(Builder $query): Builder
{
    return $query->whereDate('check_date', today());
}
```

These are deliberately separate queries rather than one aggregate query because the data changes infrequently and the queries are sub-millisecond on the expected data volumes.

### Monthly Trend Chart (Chart.js)

The 6-month inspection trend is rendered using **Chart.js**, a JavaScript charting library loaded from a CDN. The data is passed from PHP to JavaScript using Laravel's `@json()` Blade directive, which safely encodes PHP arrays to JSON:

```blade
const labels = @json(array_keys($monthlyStats));
const data   = @json(array_values($monthlyStats));
```

The underlying query uses a single database call that groups inspection counts by month, which is significantly more efficient than running six separate queries (one per month):

```php
FlowerChecklist::selectRaw("DATE_FORMAT(check_date, '%m-%Y') as month_year, count(*) as total")
    ->where('check_date', '>=', $sixMonthsAgo)
    ->groupBy('month_year')
    ->get()
    ->pluck('total', 'month_year');
```

The result is then mapped into a consistent 6-element array (filling in 0 for months with no inspections) so the chart always shows exactly 6 months even if some are empty.

---

## 11. Reporting and Exports

### CSV Export

The `ReportGenerator` component produces CSV files using PHP's native `fputcsv()` function. The export is streamed directly to the browser without writing a temporary file to disk, using Laravel's `Response::streamDownload()`:

```php
return Response::streamDownload(function () use ($data) {
    $handle = fopen('php://output', 'w');
    fputcsv($handle, ['ID', 'Date', 'Time', 'Condition', 'Remarks', 'Staff', 'Created At']);
    foreach ($data as $row) {
        fputcsv($handle, [...]);
    }
    fclose($handle);
}, $filename, ['Content-Type' => 'text/csv']);
```

`php://output` is a PHP stream that writes directly to the HTTP response body. This approach handles arbitrarily large datasets without memory issues because the rows are written and flushed one at a time.

### Print-to-PDF

Rather than generating a PDF server-side (which would require a library like Dompdf or wkhtmltopdf), the system uses the browser's native print functionality (`window.print()`). The CSS includes a dedicated `@media print` block that:

- Hides the sidebar, top bar, and all buttons.
- Removes left margin (so content fills the full page width).
- Adds a PRAZ-branded print header and footer (normally hidden with `display:none`).
- Resets card borders and shadows to clean black lines suitable for printing.

Users can use "Save as PDF" from their browser's print dialog to generate a PDF. This approach requires no server-side dependencies and produces pixel-perfect output because the browser's print engine handles the layout.

---

## 12. Asset Pipeline

### Vite Configuration

```
Flower System/vite.config.js
```

Vite is configured with two plugins:

1. **`laravel-vite-plugin`**: Integrates Vite with Laravel's asset versioning system. During development, it runs a hot-module-replacement (HMR) server. For production, it generates content-hashed filenames (e.g., `app-DppWYQO0.css`) and writes a `manifest.json` that maps source paths to their compiled output paths.

2. **`@tailwindcss/vite`**: The official Tailwind CSS v4 Vite plugin. It scans all Blade and JS files during the build and generates only the CSS classes that are actually used in the source files (defined by `@source` directives in `app.css`).

### The `@vite` Blade Directive

In every HTML layout file, assets are loaded with:

```blade
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

In development, this injects a script tag pointing to the Vite dev server (with HMR enabled). In production, it reads `public/build/manifest.json` and generates the correct `<link>` and `<script>` tags pointing to the content-hashed files. This ensures browsers always load the latest files after a deployment (because the filename changes whenever the content changes).

### Why `public/build` is Committed to Git

For Railway deployment, the `public/build` folder is committed to GitHub (the `.gitignore` line `/public/build` was removed). This is because:

- Railway runs `npm install && npm run build` during its build phase, but the build environment may not have the exact same Node.js version, which can produce different output.
- By committing the pre-built assets, Railway can serve the verified, locally-tested CSS immediately even if the npm build step produces slightly different output.
- The `nixpacks.toml` configuration also runs `npm run build` on Railway, so the assets are always refreshed on each deployment regardless.

---

## 13. PRAZ Design System

The entire visual language of the application is defined in `resources/css/app.css` using CSS Custom Properties (CSS variables). This approach means the entire colour scheme of the application can be changed by updating just the `:root` block.

### Design Tokens

```css
:root {
    --color-primary:   #003580;   /* PRAZ Deep Government Blue */
    --color-secondary: #c9a227;   /* PRAZ Gold */
    --surface-0:       #ffffff;   /* Card / Input backgrounds */
    --surface-1:       #f4f6f9;   /* Page background */
    --surface-2:       #e8edf5;   /* Alternate row / section backgrounds */
    --surface-border:  #d0d8e8;   /* All borders */
    --text-primary:    #0f1c2e;   /* Headings and primary text */
    --text-secondary:  #4a5568;   /* Labels and secondary text */
    --text-tertiary:   #718096;   /* Placeholders and captions */
}
```

### Why CSS Variables Instead of Tailwind Config?

Using raw CSS variables allows:
1. **Runtime theming**: Dark mode is implemented by overriding the variables in the `.dark` class selector. The entire colour scheme flips with a single class toggle on the `<html>` element.
2. **Inline style access**: In Blade templates, `style="color:var(--text-primary)"` lets you reference design tokens directly in inline styles without adding Tailwind classes.
3. **Consistency**: All custom component styles (`.card`, `.btn-primary`, `.form-input`, etc.) use these variables, so they automatically adapt to theme changes.

### The Sidebar Design

The sidebar is permanently deep navy blue (`#003580` in light mode, `#0a1628` in dark mode). This is intentional — in a government system, the sidebar is the primary navigation chrome and should feel stable and authoritative regardless of the user's preferred content theme. The gold active indicator (`border-left: 3px solid #c9a227`) clearly marks the current page without being distracting.

---

## 14. Dark Mode Implementation

Dark mode is implemented entirely on the client side using Alpine.js and CSS variables. There is no server-side preference storage.

### How the Toggle Works

1. The `<html>` element has `x-data="appShell()"` from the `appShell()` Alpine component defined at the bottom of the layout file.
2. When `toggleDark()` is called (by clicking the moon icon in the top bar), the `isDark` property flips.
3. The `:class="{ 'dark': isDark }"` binding on the `<html>` tag adds or removes the `dark` class.
4. CSS selectors like `.dark .badge-good { ... }` and the `.dark { ... }` variable overrides in `app.css` take effect immediately across the entire page.
5. `this.$watch('isDark', v => localStorage.setItem('praz_dark', v))` saves the preference to `localStorage` so it persists across page loads and browser sessions.

### Restoring Preference on Load

The layout includes an inline `<script>` tag in the `<head>` that runs **before** the page renders:

```html
<script>
    if (localStorage.getItem('praz_dark') === 'true' ||
        (!('praz_dark' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    }
</script>
```

This prevents the "flash of unstyled content" where the page briefly appears in light mode before Alpine.js loads and applies the preference. The script is intentionally inline (not deferred) so it executes synchronously before any HTML is painted.

If no preference is stored, the system respects the operating system's dark mode setting via `window.matchMedia('(prefers-color-scheme: dark)')`.

---

## 15. Security Hardening

### CSRF Protection

Every form in the system includes `@csrf`, which outputs a hidden `_token` field. Laravel validates this token on every `POST`, `PUT`, `PATCH`, and `DELETE` request. If the token is missing or invalid, Laravel returns a `419 Page Expired` response. This prevents Cross-Site Request Forgery attacks where a malicious website tricks a logged-in user into submitting a form to this system.

Livewire handles its own CSRF automatically for all wire calls.

### SQL Injection Prevention

The system never constructs raw SQL using string concatenation. All database queries use Laravel's Eloquent ORM or the Query Builder, both of which use PDO prepared statements under the hood. Query parameters are always bound separately from the query text, which is the fundamental defense against SQL injection.

The one exception is the `selectRaw()` call in the dashboard chart query. This uses a hard-coded format string (not user input), so it is safe.

### XSS Prevention

Blade templates automatically escape all output using `{{ $variable }}`, which converts characters like `<`, `>`, and `"` to their HTML entity equivalents. This prevents Cross-Site Scripting attacks where user-supplied text could contain HTML or JavaScript that would execute in the browser.

Raw unescaped output (`{!! $variable !!}`) is used only in two places:
1. SVG icon paths in Blade components (developer-controlled, not user data).
2. The signature pad JavaScript template — this is a `@json()` output, which properly encodes the data.

### File Upload Validation

The signature system validates that the uploaded data string starts with `data:image/png;base64,` before processing. This ensures that only PNG image data is accepted and stored. Non-image data cannot be injected through the signature pad.

### Trusting the Railway Proxy

Railway's infrastructure terminates HTTPS at a load balancer and forwards requests to the application over HTTP internally. Without telling Laravel to trust the proxy, it would generate `http://` URLs even though the user accessed the site over `https://`, causing Mixed Content browser errors.

The `bootstrap/app.php` file configures proxy trust:

```php
$middleware->trustProxies(
    at: '*',
    headers: Request::HEADER_X_FORWARDED_FOR |
             Request::HEADER_X_FORWARDED_HOST |
             Request::HEADER_X_FORWARDED_PORT |
             Request::HEADER_X_FORWARDED_PROTO |
             Request::HEADER_X_FORWARDED_AWS_ELB
);
```

`at: '*'` means trust any upstream proxy (appropriate for Railway's managed infrastructure). The `headers` parameter tells Laravel which `X-Forwarded-*` headers to read when reconstructing the original request URL.

---

## 16. Deployment Architecture

### Railway Platform

The application is deployed on Railway, a Platform-as-a-Service (PaaS) that handles infrastructure provisioning, SSL, and deployment automation.

```
GitHub Repository
       │
       │  git push main
       ▼
Railway Build Server
  1. composer install --no-dev --optimize-autoloader
  2. npm install
  3. npm run build  ←  compiles Tailwind CSS + JS
       │
       ▼
Railway Container Starts
  1. php artisan config:cache   ←  runs WITH env vars
  2. php artisan route:cache
  3. php artisan migrate --force
  4. php artisan storage:link
  5. php artisan serve --host=0.0.0.0 --port=$PORT
```

**Why `config:cache` runs at start time, not build time**: Railway injects environment variables (APP_KEY, DB_HOST, etc.) into the running container but not into the build environment. If `config:cache` runs during the build, it caches the configuration with empty values, breaking the live application. Running it at container start time ensures it captures the correct live values.

### Nixpacks Configuration

The `nixpacks.toml` file tells Railway exactly how to build the application:

```toml
[phases.install]
cmds = ["composer install --no-dev --optimize-autoloader", "npm install"]

[phases.build]
cmds = ["npm run build"]

[start]
cmd = "php artisan config:cache && php artisan route:cache && php artisan migrate --force && php artisan storage:link && php artisan serve --host=0.0.0.0 --port=$PORT"
```

### Environment Variables

| Variable | Purpose |
| :--- | :--- |
| `APP_KEY` | 256-bit encryption key for sessions, cookies, and encrypted fields |
| `APP_URL` | The full `https://` URL used to generate correct asset and redirect URLs |
| `APP_ENV` | Set to `production` to disable debug output |
| `APP_DEBUG` | Set to `false` in production so errors are logged, not displayed |
| `DB_CONNECTION` | `mysql` for Railway production |
| `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | MySQL connection credentials (referenced from the Railway MySQL service) |
| `LOG_CHANNEL` | `stderr` — logs go to Railway's log stream rather than a file |

---

## 17. Known Limitations and Future Work

### Current Limitations

1. **No real-time notifications**: There is no WebSocket or Server-Sent Events integration. Users must refresh the page to see new inspections created by other users.
2. **Signature storage is local**: Signatures are stored on the Railway container's volume. If the volume is not attached or is reset, signatures would be lost. A future improvement would be to upload signatures to a dedicated object storage service (e.g., AWS S3 or Cloudflare R2).
3. **No native PDF generation**: The print-to-PDF approach is convenient but cannot be automated or scheduled. Server-side PDF generation with a library like DOMPDF would enable emailed reports.
4. **No email notifications**: The password reset flow uses Laravel's built-in email system but requires a configured mail provider (SMTP, Mailgun, etc.) which is not set up in the current deployment.
5. **Single-server deployment**: The application uses file-based session storage. A multi-server (horizontally scaled) deployment would require switching to database or Redis sessions.

### Recommended Future Enhancements

- **S3 / Cloud storage** for signature images and future file uploads.
- **Server-side PDF generation** using DOMPDF for automated monthly reports.
- **Email notifications** for new inspection submissions or system alerts.
- **Two-factor authentication** for administrator accounts.
- **Dashboard date range picker** to filter KPI cards by custom date ranges.
- **Offline support** using a Progressive Web App (PWA) service worker for use in areas with unreliable internet connectivity during inspections.

---

*This document reflects the system as it exists at version 1.0.0. It should be updated whenever architectural decisions change.*
