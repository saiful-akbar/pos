# POS (Point Of Sales)

## 1. Project Overview

This project is a Point of Sales (POS) application with integrated inventory management, built with PHP 8.3, Laravel 12, and MySQL 8.0.

The application supports master data management, purchasing, sales, sales returns, stock opname, and operational reporting.

### Architectural Approach

The application combines:

- **Laravel MVC** for HTTP request handling and presentation.
- **Domain-Driven Design (DDD)** for domain modeling and business rules.
- **Use Case-Based Application Architecture** for orchestrating business operations.
- **Repository Pattern** for abstracting data access and persistence.

Claude Code must follow the existing implementation and maintain clear separation of responsibilities between these architectural components.

### Core principles

- Understand the existing codebase before making changes.
- Preserve existing interfaces, namespaces, and business rules.
- Keep controllers thin and delegate business operations to use cases.
- Keep persistence logic inside repository implementations.
- Represent business concepts through entities and value objects where appropriate.
- Maintain inventory integrity and financial accuracy.
- Prefer small, testable, maintainable changes over unnecessary refactoring.

---

## 2. Technology Stack

### Backend

- PHP 8.3
- Laravel 12
- `barryvdh/laravel-dompdf` 3.1 — PDF generation
- `maatwebsite/excel` 4.0 — Excel import and export
- `yajra/laravel-datatables-oracle` 12.0 — server-side DataTables

### Frontend

- Bootstrap CSS 5
- jQuery 4
- DataTables 3.0.2
- Bootbox 6.0.4
- Laravel Blade

### Database

- MySQL 8.0

Verify installed package versions using the project's dependency manifests and lockfiles before relying on specific APIs. Do not assume that APIs from other versions are available.

---

## 3. Business Modules

### 3.1 Master Data

- Users
- User roles and permissions
- Suppliers
- Customers
- Product categories
- Product units
- Products

### 3.2 Purchasing

- Create and manage purchase transactions.
- Associate purchases with suppliers.
- Record purchased products, units, quantities, and prices.
- Calculate purchase totals according to business rules.
- Update stock when a purchase reaches the appropriate finalized state.

### 3.3 Sales

- Create sales transactions.
- Associate sales with users and optional customers.
- Record products, units, quantities, and selling prices.
- Calculate transaction totals and discounts.
- Validate stock availability.
- Decrease stock when a sale is finalized.
- Generate invoice numbers.
- Print sales receipts.

Invoice number format example: `INV2610-00001`.

The year and month must be represented in the prefix. The sequential number must reset when the month changes. Invoice generation must remain unique and safe under concurrent requests.

### 3.4 Sales Returns

- Associate returns with original sales.
- Validate eligible return quantities.
- Prevent cumulative returns from exceeding eligible sold quantities.
- Calculate return amounts according to established business rules.
- Restore stock when appropriate.
- Preserve transaction history and relationships.

### 3.5 Stock Opname

- Support daily and monthly stock opname.
- Record system quantities and physical counts.
- Calculate inventory discrepancies.
- Finalize stock adjustments.
- Prevent duplicate adjustments.
- Preserve an audit trail of stock changes.

### 3.6 Reports

- Purchase reports.
- Sales reports.
- Sales return reports.
- Inventory and stock reports.

Use appropriate date filters, database-level aggregation, and consistent calculation rules.

---

## 4. Architecture: Laravel MVC + DDD + Use Case-Based Repository Pattern

This architecture is mandatory for new features and modifications unless the user explicitly authorizes a different approach.

Follow the existing directory structure and namespaces. The structure below illustrates responsibilities; it does not require moving existing classes.

### 4.1 Architectural flow

```text
HTTP Request
    |
    v
Route
    |
    v
Controller
    |
    | Validate request and prepare input
    v
Application Use Case
    |
    | Coordinate business operation
    v
Domain Entities / Value Objects
    |
    | Persistence through abstractions
    v
Repository Interface
    ^
    | implements
Repository Implementation
    |
    v
Eloquent Model
    |
    v
MySQL Database
```

The dependency direction must remain consistent with the existing architecture: application and domain logic should depend on abstractions rather than concrete persistence implementations.

The controller receives the result from the use case and returns an appropriate HTTP response, JSON response, redirect, or Blade view.

### 4.2 Laravel MVC responsibilities

**Model**

Use Eloquent models to represent persistence and database relationships.

Models may define relationships, casts, scopes, and persistence-related behavior. Do not turn Eloquent models into containers for the entire business workflow.

**View**

Use Blade templates, Bootstrap 5, jQuery, DataTables, and Bootbox for the user interface.

Views must not contain complex business rules or perform direct database operations.

**Controller**

Controllers handle HTTP concerns, including:

- Receiving requests.
- Applying request validation through Form Requests or the established validation approach.
- Enforcing authentication and authorization.
- Passing validated input to the appropriate use case.
- Translating results into HTTP responses.
- Returning views, redirects, JSON, PDF, or Excel responses.

Controllers must remain thin.

Do not implement stock calculations, invoice number generation algorithms, or multi-step transaction workflows directly in controllers.

### 4.3 Domain-Driven Design responsibilities

The Domain layer represents business concepts and invariants.

It may contain:

- Entities.
- Value objects.
- Repository interfaces.
- Domain rules and domain services when necessary.

Domain classes must not depend on HTTP requests, Blade views, or Eloquent models.

Use domain-specific value objects when required by the established domain model. Do not replace them with primitive values merely for convenience.

### 4.4 Use Case-Based Application layer

Use cases represent individual application operations.

Examples include:

- `CreateSaleUseCase`
- `GetSaleByIdUseCase`
- `GetSalesByDateRangeUseCase`
- `DeleteSaleUseCase`
- `CreateSaleDetailUseCase`
- `CreatePurchaseUseCase`
- `ProcessSalesReturnUseCase`
- `FinalizeStockOpnameUseCase`

These names are illustrative. Before creating a class, inspect the project's existing naming conventions and use case implementations.

Each use case should have a clear business purpose and an explicit input/output contract.

Responsibilities:

- Coordinate the execution of a business operation.
- Accept validated input or an appropriate DTO.
- Invoke repositories through interfaces.
- Coordinate domain entities and value objects.
- Enforce application-level business workflow rules.
- Manage transaction boundaries where appropriate.
- Return a clearly defined result.

Rules:

- Keep each use case focused on one application operation.
- Do not place HTTP response construction in use cases.
- Do not access Eloquent models directly when the established architecture requires repository interfaces.
- Do not duplicate domain rules already implemented in domain entities or domain services.
- Avoid creating a generic use case that handles unrelated operations.
- Do not introduce unnecessary service classes when an existing use case is sufficient.

### 4.5 Repository Pattern responsibilities

Repository interfaces define the data access contract used by application and domain logic.

Repository implementations handle persistence using Eloquent and MySQL.

Responsibilities include:

- Finding records.
- Creating records from DTOs or domain data.
- Updating records where supported by the contract.
- Deleting records according to business constraints.
- Querying date ranges and other required filters.
- Converting database records into domain entities.
- Handling persistence errors using the established logging convention.

Repository interfaces must not depend on their concrete implementations.

Use dependency injection and the project's service container bindings to resolve implementations.

### 4.6 Infrastructure responsibilities

Infrastructure contains persistence implementations and framework integrations.

Examples:

- Repository implementations.
- Eloquent model integration.
- Database-specific queries.
- External package integrations where appropriate.

Follow the actual project namespaces. Do not create a new infrastructure directory solely because it appears in this document if the existing project uses another established location.

---

## 5. Request-to-Response Workflow

For typical CRUD operations, follow this flow:

1. The route directs the request to the appropriate controller.
2. The controller validates input and enforces access control.
3. The controller creates or passes the appropriate DTO or input object.
4. The controller invokes the relevant use case.
5. The use case coordinates domain behavior and repository interfaces.
6. The repository implementation performs the persistence operation.
7. The result is returned to the use case and controller.
8. The controller returns the appropriate response.

For financial or inventory operations, the use case must coordinate the complete business operation and its transaction boundaries.

Do not bypass the use case layer by having controllers call repository implementations directly when the project's architecture requires use case-based execution.

Read-only operations must follow the same architectural conventions, even when they require little business logic.

---

## 6. PHP Coding Standards

- Follow PSR-12.
- Use explicit namespaces and imports.
- Define parameter and return types.
- Use nullable types only where null is valid.
- Prefer immutable DTOs where appropriate.
- Use constructor property promotion when it improves clarity.
- Apply `declare(strict_types=1);` where consistent with the existing codebase.
- Avoid unexplained magic values.
- Avoid unnecessary abstractions and duplicated code.
- Follow the project's existing naming conventions.

### 6.1 Entities

Entities represent domain concepts and their identity.

Examples:

- `SaleEntity`
- `SaleDetailEntity`

Use the established base entity, `App\Base\Entity`, where applicable.

Entities must not perform direct database queries or handle HTTP responses.

### 6.2 DTOs

DTOs carry data between application components.

Examples:

- `SaleDTO`
- `SaleDetailDTO`

Use `App\Base\DTO` where applicable.

DTOs should have typed constructor properties and remain focused on data transfer.

Match their properties to the actual domain contracts and database schema. Do not assume that DTOs must duplicate every database column.

### 6.3 Value Objects

Use the project's existing value objects when defined.

Examples:

- `CustomerSaleValue`
- `UserSaleValue`
- `ProductSaleValue`
- `UnitSaleValue`

Preserve their namespaces, construction rules, and validation behavior.

### 6.4 Use cases

Use case classes must have a clear name and responsibility.

Where consistent with the existing codebase, prefer an explicit public entry-point method such as `execute()`.

Example signature only:

```php
public function execute(SaleDTO $dto): SaleEntity;
```

The actual signature must reflect the operation, existing contracts, and required return type.

Inject dependencies through the constructor. Prefer repository interfaces over concrete repository implementations.

### 6.5 Repository interfaces and implementations

Preserve the separation between repository interfaces and implementations.

Examples:

- `SaleRepositoryInterface`
- `SaleDetailRepositoryInterface`
- `SaleRepository`
- `SaleDetailRepository`

Use existing base repository conventions, including `App\Base\Repository`, where applicable.

Rules:

- Implement every required interface method.
- Match parameter and return types exactly.
- Return domain entities when required by the contract.
- Keep database operations inside repository implementations.
- Follow established model-to-entity conversion conventions.
- Do not change public signatures without reviewing affected callers.

### 6.6 Eloquent models

Inspect migrations and the actual schema before defining model properties, relationships, casts, or queries.

Some identifiers use `CHAR(26)`. Do not assume every primary key is an auto-incrementing integer.

Preserve existing key generation, timestamps, decimal precision, foreign keys, and mass-assignment protections.

---

## 7. Error Handling and Repository Logging

Repository implementations must use the existing `errorLog()` method inside `try-catch` blocks for operations that can fail, following the established project convention.

Example:

```php
try {
    // Perform the repository operation.
} catch (\Throwable $e) {
    $this->errorLog(__METHOD__, $e->getMessage());

    throw $e;
}
```

Rules:

- Use `__METHOD__` to identify the failing method.
- Record errors through the established logging mechanism.
- Rethrow the original exception unless the existing contract explicitly requires another strategy.
- Do not silently swallow exceptions.
- Do not return fabricated success results after failed operations.
- Avoid exposing internal exception details to users.
- Do not log credentials, access tokens, or other sensitive information.

Use cases must not duplicate repository error logging indiscriminately. Follow the existing exception-handling responsibilities at each architectural layer.

---

## 8. Database and Transaction Integrity

### 8.1 Database transactions

Use `DB::transaction()` or the established transaction abstraction for operations that modify multiple related records.

Examples:

- Creating a sale and its details.
- Creating a purchase and its details.
- Finalizing a sales return.
- Applying stock opname adjustments.

The operation must either complete all required changes or roll them back.

Transaction ownership should be clear. Avoid unnecessary nested transactions or splitting a single atomic business operation across unrelated transaction boundaries.

### 8.2 Inventory integrity

- Validate stock before finalizing sales.
- Prevent negative stock unless explicitly permitted.
- Apply stock changes atomically.
- Handle concurrent requests using appropriate locking or atomic conditional updates.
- Prevent duplicate stock movements on retries.
- Preserve consistency between stock balances and stock movement records where both are maintained.
- Ensure stock opname adjustments are applied only once.

### 8.3 Financial calculations

- Respect the precision and scale of database decimal columns.
- Avoid relying on binary floating-point arithmetic for exact financial calculations.
- Follow the project's established decimal arithmetic strategy.
- Define rounding rules consistently.
- Recalculate authoritative totals on the server.
- Keep persisted totals consistent with transaction details.

### 8.4 Invoice numbers

Invoice numbers must follow the established format, such as `INV2610-00001`.

Generation must account for the year and month and reset the sequence at the start of a new month.

Use a concurrency-safe strategy backed by suitable database constraints. Do not rely exclusively on an unprotected `MAX()` query.

### 8.5 Schema changes

Before changing the schema:

1. Inspect existing migrations and models.
2. Identify affected entities, DTOs, use cases, repository contracts, and implementations.
3. Review relationships and foreign keys.
4. Create migrations following Laravel conventions.
5. Consider indexes and unique constraints.
6. Preserve existing data.
7. Test the relevant operation and rollback behavior.

Never perform destructive production database operations without explicit authorization.

---

## 9. Frontend Standards

### Blade and Bootstrap

- Use Blade for server-rendered views.
- Use Bootstrap 5 components and utilities.
- Reuse established layouts and partials.
- Escape user-generated content by default.
- Keep business logic out of views.

### jQuery

- Use the installed jQuery 4 API.
- Follow existing initialization and event delegation conventions.
- Prevent duplicate event handlers.
- Handle dynamic form validation and server responses consistently.

### DataTables

Use Yajra DataTables for server-side processing where appropriate.

- Apply filtering, sorting, pagination, and searching at the database level.
- Validate filter inputs and allowed sort columns.
- Avoid loading the full dataset into memory.
- Escape user-generated content.
- Follow established JSON response contracts.

### Bootbox

Use Bootbox 6.0.4 for alerts and confirmation dialogs.

Use confirmations for consequential operations where appropriate. Client-side confirmation does not replace server-side validation or authorization.

---

## 10. PDF and Excel

### PDF

Use `barryvdh/laravel-dompdf` 3.1 for receipts and reports.

- Prepare data in the appropriate application or presentation workflow.
- Keep complex database queries out of Blade PDF templates.
- Follow established page sizes, margins, and formatting.
- Test empty datasets and long content.

### Excel

Use `maatwebsite/excel` 4.0 for import and export.

- Follow the installed package API.
- Separate validation and transformation from controller logic.
- Validate imported rows.
- Provide meaningful validation errors.
- Use chunking or queued processing when appropriate.
- Consider atomicity for bulk imports.
- Prevent spreadsheet formula injection for untrusted exported values.

---

## 11. Security

- Enforce authentication and authorization on the server.
- Validate all submitted data.
- Protect state-changing requests against CSRF.
- Prevent mass-assignment vulnerabilities.
- Use Eloquent or parameterized queries.
- Never trust client-calculated prices, totals, or stock quantities.
- Validate access to individual transaction records.
- Do not expose internal exceptions in production responses.
- Keep credentials and secrets out of source control.

Follow the existing authorization and permission architecture.

---

## 12. Testing

Add or update tests when business behavior changes.

Prioritize:

- Entity and DTO construction.
- Use case execution and dependency behavior.
- Repository persistence and retrieval.
- Dependency injection and interface bindings.
- Invoice generation across month changes.
- Duplicate and concurrent invoice generation where feasible.
- Purchase and sales stock movements.
- Insufficient stock validation.
- Sales return limits.
- Stock opname adjustments.
- Transaction rollback.
- Authorization and validation.
- Report filters and totals.
- PDF and Excel generation.

Use the project's existing testing framework, factories, and test conventions.

Run relevant tests and configured quality checks before reporting completion. Never claim a test passed unless it was actually executed successfully.

---

## 13. Mandatory Workflow for Claude Code

For every task, follow these steps.

### Step 1: Understand the requirement

Identify the expected behavior, affected modules, and business rules.

Ask for clarification when an ambiguity could affect financial calculations, inventory behavior, or public interfaces.

### Step 2: Inspect the existing implementation

Review the relevant:

- Routes and controllers.
- Form Requests.
- Use cases and application services.
- Entities, DTOs, and value objects.
- Repository interfaces and implementations.
- Eloquent models and migrations.
- Service provider bindings.
- Blade templates and JavaScript.
- Tests and dependency versions.

Do not create new architectural conventions before checking whether an equivalent implementation already exists.

### Step 3: Determine the affected layers

Identify the minimum set of components that need modification.

For a typical feature, consider the controller, use case, domain contracts, repository implementation, persistence model, and tests as appropriate.

Not every operation requires changes to every layer.

### Step 4: Implement

- Follow MVC, DDD, and the use case-based repository architecture.
- Keep controllers focused on HTTP concerns.
- Keep business workflows in use cases and domain logic.
- Access persistence through repository interfaces when required.
- Apply appropriate transaction boundaries.
- Preserve established naming and type contracts.
- Add or update tests.

### Step 5: Verify

Run applicable tests, formatting, and static analysis checks.

Inspect the final diff and check for unrelated modifications.

### Step 6: Report

Summarize:

- Files created or modified.
- Responsibilities of the new or updated components.
- Important business rules.
- Tests and checks actually executed.
- Remaining assumptions or limitations.

---

## 14. Prohibited Practices

Claude Code must not:

- Bypass the use case layer when the established architecture requires it.
- Put complex business logic in controllers or Blade templates.
- Let domain entities depend directly on Eloquent or HTTP requests.
- Put database persistence logic in DTOs.
- Couple repository interfaces to concrete implementations.
- Invent schema fields or relationships.
- Modify inventory outside the appropriate business workflow.
- Trust client-calculated monetary values.
- Use unsafe invoice number generation.
- Swallow exceptions or fabricate successful results.
- Change dependencies without authorization.
- Refactor unrelated modules unnecessarily.
- Execute destructive commands without explicit authorization.
- Claim that tests or commands succeeded when they were not executed.

---

## 15. Existing Project Conventions

Preserve these conventions where they exist in the codebase:

- Base DTO: `App\Base\DTO`
- Base Entity: `App\Base\Entity`
- Base Repository: `App\Base\Repository`
- Repository error logging through `errorLog()`
- Domain entities separated from application DTOs.
- Repository interfaces separated from implementations.
- Use cases coordinating application operations.
- Dependency injection for use case and repository dependencies.
- Typed constructors and immutable DTOs where appropriate.
- `DateTimeInterface` for date/time properties when required.
- Domain value objects for customer, user, product, and unit references.
- Existing conversion conventions such as `fromModel()` and `toArray()`.

Treat the actual class definitions and tests as authoritative contracts.

---

## 16. Definition of Done

A task is complete when all applicable conditions are satisfied:

- [ ] The requested functionality works as specified.
- [ ] MVC responsibilities are preserved.
- [ ] Application workflows follow the use case-based architecture.
- [ ] Domain and infrastructure responsibilities remain separated.
- [ ] Repository interfaces and implementations are consistent.
- [ ] Database and inventory integrity are preserved.
- [ ] Financial calculations follow established rules.
- [ ] Validation, authentication, and authorization are enforced.
- [ ] Exceptions are handled appropriately.
- [ ] Relevant tests are added or updated.
- [ ] Applicable checks have been executed.
- [ ] No unrelated changes or unauthorized destructive operations were introduced.
- [ ] Remaining limitations are reported.

---

## 17. Final Instructions

This document defines the general development conventions for the project.

The priority order is:

1. Explicit requirements from the user.
2. Existing database schema and confirmed business rules.
3. Existing public interfaces, tests, and architectural contracts.
4. The MVC, DDD, use case, and repository conventions defined here.
5. General Laravel and PHP best practices.

Inspect the codebase before making architectural decisions. If an existing implementation conflicts with this document, identify the discrepancy before making significant changes.

**Always prioritize business correctness, inventory integrity, financial accuracy, security, and maintainability.**
