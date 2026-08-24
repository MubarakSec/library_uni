# AI Agent & Developer Guidelines (`AGENTS.md`)

## 1. Architectural Philosophy & Principles

To prevent **if/else spaghetti hell**, monolithic multi-thousand-line files, and unmaintainable code patterns, all contributions to this project must follow strict architectural boundaries.

### Core Tenets
1. **Single Responsibility Principle (SRP):**
   - Keep files small and focused (target: `< 200 lines` per file).
   - Backend endpoints should act only as thin controllers (validate input -> call service/helper -> return response).
   - Frontend modules should isolate DOM manipulation, state, and API communication into dedicated handlers.

2. **No `if/else` Ladder Hell:**
   - **Early Return Pattern / Guard Clauses:** Return or bail out immediately upon failed validations or errors instead of deeply nesting conditionals.
   - **Lookup Dictionaries / Mapping Objects / Enums:** Replace multi-branch `switch` or `if/else` statements for role checking, status mappings, or category styling with lookup tables / associative arrays.
   - **Polymorphism & Handler Maps:** For complex multi-action endpoints or route handlers, dispatch to registered action handlers rather than chaining `if ($action === '...')`.

3. **Separation of Concerns:**
   - **Database Logic:** Use PDO with prepared statements, extracted into reusable helper/repository functions.
   - **Validation:** Separate input sanitization and validation from core business logic.
   - **HTTP Responses:** Use uniform response helpers (`Response::json(...)`, `Response::error(...)`, `Response::success(...)`).

---

## 2. Directory & Component Structure

```
library_uni/
├── back-end/
│   ├── admin/          # Admin APIs (dashboard metrics, approvals)
│   ├── auth/           # Authentication endpoints (login, register, logout, me)
│   ├── books/          # Book catalog, reviews, search, and upload endpoints
│   ├── config/         # Environment, PDO database connection, and session handling
│   ├── helpers/        # Uniform JSON response utilities & sanitization
│   └── middleware/     # Auth checks, RBAC (Role-Based Access Control)
├── front-end/
│   ├── assets/
│   │   ├── css/        # Modular CSS stylesheets
│   │   └── js/         # Client-side JavaScript modules (kept modular & event-driven)
│   ├── pages/          # HTML view pages
│   ├── partials/       # Reusable layout partials (header, footer, nav)
│   └── uploads/        # Storage directory for uploaded assets (e.g., book PDFs)
├── tests/              # Database scripts and test suites
├── database.sql        # Core database schema and initial seed data
└── AGENTS.md           # This governance and architectural manual
```

---

## 3. Backend (PHP) Best Practices

- **Strict Strictness & Errors:**
  - Always use prepared statements (`$pdo->prepare(...)` + `$stmt->execute(...)`). Never concatenate raw SQL queries.
  - Return standardized JSON payloads via `back-end/helpers/response.php`.
- **Session & Auth Middleware:**
  - Secure sessions via `back-end/config/session.php`.
  - Guard protected routes with `require_login()` or `require_role([...])`.
- **Anti-Spaghetti Rule for Controllers:**
  ```php
  // ❌ Bad: Deeply nested if/else hell
  if ($user) {
      if ($isValid) {
          if ($hasPermission) {
              // Action
          } else {
              echo "No permission";
          }
      } else {
          echo "Invalid";
      }
  } else {
      echo "No user";
  }

  // ✅ Good: Guard clauses & early returns
  if (!$user) {
      Response::unauthorized("User is not authenticated");
  }
  if (!$isValid) {
      Response::badRequest("Invalid input parameters");
  }
  if (!$hasPermission) {
      Response::forbidden("Insufficient privileges");
  }

  // Pure execution logic here
  Response::success("Operation completed successfully", $data);
  ```

---

## 4. Frontend (JavaScript & HTML) Best Practices

- **Avoid Monolithic JS Files:**
  - Group page-specific logic into dedicated files (`books.js`, `dashboard.js`, `auth.js`).
  - Shared functionality (e.g. `layout.js`, `dark-mode.js`) must remain decoupled and pluggable.
- **Avoid Giant Switch/Case UI Styling:**
  ```javascript
  // ❌ Bad
  if (category === 'math') { color = 'blue'; }
  else if (category === 'cs') { color = 'purple'; }
  else if (category === 'engineering') { color = 'green'; }

  // ✅ Good: Mapping object / Lookup map
  const CATEGORY_THEMES = {
      math: 'from-blue-900 to-blue-700',
      cs: 'from-purple-900 to-purple-700',
      engineering: 'from-green-900 to-green-700',
      default: 'from-gray-900 to-gray-700'
  };
  const theme = CATEGORY_THEMES[category] || CATEGORY_THEMES.default;
  ```
- **Async/Await & Error Handling:**
  - Wrap API fetch calls in `try/catch` with clear UI notifications and fallback states.
  - Avoid inline HTML `onclick` handlers; prefer `addEventListener`.

---

## 5. Security & Validation Directives

1. **Input Sanitization:** All incoming requests (`$_POST`, `$_GET`, `php://input`) must be validated and sanitized.
2. **Password Security:** Use `password_hash($pass, PASSWORD_BCRYPT)` and `password_verify()`.
3. **File Upload Hardening:**
   - Whitelist file MIME types (e.g. `application/pdf`).
   - Validate file sizes and store uploaded files outside executable public script paths or sanitize filenames.
4. **Environment Variables:** Never commit secrets, passwords, or API keys. Always rely on `.env` loaded through `back-end/config/env.php`.
