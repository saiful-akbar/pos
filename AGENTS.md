# Project Context & Agent Guidelines

## 1. Project Overview

This project is a **Point of Sale (POS) Application** built with a robust, scalable, and maintainable structure. The AI Agent must adhere strictly to the technology stack, architectural patterns, and coding standards defined below.

## 2. Technology Stack

### Backend (Core)

- **Framework:** Laravel 12.x
- **Database:** MySQL 8.0
- **Architecture:** Hybrid MVC and Domain-Driven Design (DDD)

### Backend Packages & Dependencies

- **DataTables Server-Side:** `yajra/laravel-datatables-oracle` (v12.0)
- **Excel Processing:** `maatwebsite/excel` (v4.0)
- **PDF Generation:** `dompdf/dompdf` via `barryvdh/laravel-dompdf`

### Frontend

- **Styling Framework:** Bootstrap 5.x
- **JavaScript Library:** jQuery 4.x
- **Data Grids:** DataTables.net (Vanilla / Bootstrap 5 integration)
- **Modals & Alerts:** Bootbox.js (for programmatic dialogs/alerts)

---

## 3. Architectural Rules & Directory Structure

We implement a hybrid of **MVC** and **Domain-Driven Design (DDD)**. Code must be highly decoupled, organized into domain layers, and separated by business logic.

### Layer Guidelines:

1. **Domain Layer:** Contains Core Business Logic, Entities, Value Objects, and Domain Events. It must be decoupled from framework-specific code.
2. **Application / Service Layer:** Coordinates use cases, DTOs (Data Transfer Objects), and orchestrates services.
3. **Infrastructure Layer:** Handles database persistence (Repositories), file storage, third-party integrations (Excel/PDF services), and heavy Eloquent configurations.
4. **Presentation Layer (MVC):** Laravel Controllers handle HTTP requests/responses, passing data via DTOs to Services, and returning Bootstrap 5 views.

### Rules for the Agent:

- **No Business Logic in Controllers:** Controllers should only handle request validation, call the appropriate application service, and return a view or JSON response.
- **Thin Models, Rich Services:** Do not clutter Eloquent models with complex business operations. Delegate them to Domain Services or Action classes.
- **Server-Side Rendering:** Always use `yajra/laravel-datatables-oracle` queries for fetching tabular data. Never load large collections directly into JavaScript.

---

## 4. Coding & Implementation Standards

### Datatables Implementation (Yajra + jQuery 4)

- **Backend:** Leverage Yajra DataTables v12 query scopes or service classes. Ensure proper indexing in MySQL for columns handled by global searching or sorting.
- **Frontend:** Use jQuery 4 syntax to initialize `datatables.net`. Ensure all AJAX requests include CSRF tokens securely.

### Export Services (Excel & PDF)

- **Excel (`maatwebsite/excel 4.0`):** Implement exports using Queueable sheets if data exceeds 500 rows. Use explicitly defined mapping classes.
- **PDF (`dompdf`):** Keep HTML structures clean, table-based where necessary, and use inline CSS compatible with Dompdf. Avoid advanced CSS Flexbox/Grid in PDF views.

### UI Interaction (Bootbox & Bootstrap 5)

- Replace standard browser alerts (`alert()`, `confirm()`) with **Bootbox.js** dialogs styled via Bootstrap 5.
- All responsive elements must strictly use Bootstrap 5 utility classes.

### Code Style

- **PHP:** Strictly adhere to PSR-12 coding standards and modern PHP 8.3+ features supported by Laravel 12 (e.g., constructor property promotion, match expressions, typed properties).
- **Naming Conventions:**
    - Services/Actions: PascalCase (e.g., `ProcessCheckoutAction.php`)
    - Repositories: PascalCase ending with Repository (e.g., `OrderRepository.php`)

---

## 5. Instructions for AI Agent

- Read this file before generating any backend services, database migrations, or frontend scripts.
- Ensure all code generated is fully compatible with **Laravel 12** and **jQuery 4**.
- Do not introduce alternative state management or design patterns unless explicitly requested.
