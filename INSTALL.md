# Library_Uni Installation Guide

Complete installation and setup guide for the University Library web application.

---

## 📋 Prerequisites

Before starting, ensure you have:

- **PHP 8.0+** installed
  - Windows: Download from [php.net](https://www.php.net/downloads)
  - Verify: `php -v`
- **MySQL 8.0+** installed and running
  - Windows: Download from [MySQL downloads](https://dev.mysql.com/downloads/installer/)
  - Verify: `mysql --version`
- A web browser (Chrome, Firefox, Edge, etc.)

---

## 🔧 Step 1: Project Setup

### 1.1. Download/Clone the Project

Place the project in a suitable directory, for example:
```
C:\projects\library_uni\
```

Your directory structure should look like:
```
C:\projects\library_uni\
├── .env
├── .env.example
├── database.sql
├── back-end/
│   ├── auth/
│   ├── books/
│   ├── config/
│   ├── helpers/
│   └── middleware/
└── front-end/
    ├── pages/
    ├── assets/
    └── uploads/
```

---

## 🗄️ Step 2: Database Configuration

### 2.1. Create MySQL User (Optional but Recommended)

Open MySQL command line or MySQL Workbench and run:

```sql
-- Create a dedicated user for the application
CREATE USER 'library_user'@'localhost' IDENTIFIED BY 'YourStrongPassword123!';

-- Grant privileges
GRANT ALL PRIVILEGES ON library_uni.* TO 'library_user'@'localhost';
FLUSH PRIVILEGES;
```

### 2.2. Import Database Schema

Import the `database.sql` file:

**Option A: Using MySQL Command Line**
```bash
mysql -u root -p < database.sql
```

**Option B: Using MySQL Workbench**
1. Open MySQL Workbench
2. Connect to your MySQL server
3. Go to: `Server` → `Data Import`
4. Select `Import from Self-Contained File`
5. Choose `database.sql`
6. Click `Start Import`

**Option C: Using phpMyAdmin**
1. Open phpMyAdmin
2. Click `Import` tab
3. Choose `database.sql` file
4. Click `Go`

This will create:
- Database: `library_uni`
- Tables: `users`, `books`, `book_requests`
- Sample data including admin user

---

## ⚙️ Step 3: Environment Configuration

### 3.1. Configure .env File

Copy `.env.example` to `.env`:
```bash
copy .env.example .env
```

Edit `.env` with your database credentials:

```env
# Database Configuration
DB_HOST=localhost
DB_NAME=library_uni
DB_USER=library_user
DB_PASS=YourStrongPassword123!

# Session Configuration
SESSION_LIFETIME=7200

# Application Environment
APP_ENV=development
APP_DEBUG=true

# Security (set to true in production with HTTPS)
SECURE_COOKIES=false
```

**Important Notes:**
- Replace `DB_PASS` with your actual MySQL password
- Keep `APP_ENV=development` for local testing
- Set `SECURE_COOKIES=true` only in production with HTTPS

---

## 🚀 Step 4: Running the Application

### 4.1. Start PHP Built-in Server

**On Windows:**

1. Open Command Prompt or PowerShell
2. Navigate to the parent directory of your project:
   ```bash
   cd C:\projects
   ```
3. Start PHP server:
   ```bash
   php -S localhost:8000
   ```

You should see:
```
PHP 8.x.x Development Server (http://localhost:8000) started
```

### 4.2. Access the Application

Open your web browser and navigate to:

**Homepage:**
```
http://localhost:8000/library_uni/front-end/pages/index.html
```

**Login Page:**
```
http://localhost:8000/library_uni/front-end/pages/login.html
```

**Register Page:**
```
http://localhost:8000/library_uni/front-end/pages/register.html
```

---

## 👥 Step 5: Test Accounts

The database includes these test accounts:

### Admin Account
- **Email:** `admin@library.uni`
- **Password:** `Admin123!`
- **Role:** Admin (can upload books, manage content)

### Student Account
- **Email:** `student@library.uni`
- **Password:** `Student123!`
- **Role:** Student (can browse, request books)

### Assistant Account
- **Email:** `assistant@library.uni`
- **Password:** `Assistant123!`
- **Role:** Assistant (can upload books)

**Note:** These are sample passwords. Change them immediately in production!

---

## 📁 Step 6: File Uploads Setup

### 6.1. Create Uploads Directory

Ensure the uploads directory exists and is writable:

```bash
mkdir front-end\uploads\books
```

**On Windows, set folder permissions:**
1. Right-click on `front-end\uploads\books`
2. Properties → Security
3. Edit → Add → Everyone
4. Allow: Full Control (for development only)

---

## ✅ Step 7: Verify Installation

### 7.1. Test Database Connection

Visit:
```
http://localhost:8000/library_uni/back-end/books/list.php
```

You should see JSON output with sample books.

### 7.2. Test Login

1. Go to login page
2. Use admin credentials:
   - Email: `admin@library.uni`
   - Password: `Admin123!`
3. You should be redirected to the homepage

### 7.3. Test Book Upload (Admin/Assistant Only)

1. Login as admin or assistant
2. Navigate to upload book page
3. Try uploading a PDF file (max 10MB)
4. Verify it appears in the books list

---

## 🔒 Security Checklist

Before deploying to production:

- [ ] Change all default passwords
- [ ] Set strong `DB_PASS` in `.env`
- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Set `SECURE_COOKIES=true` in `.env`
- [ ] Use HTTPS (required for secure cookies)
- [ ] Restrict file permissions on `.env`
- [ ] Review and update CORS settings if needed
- [ ] Configure proper backup strategy
- [ ] Set up error logging

---

## 🐛 Troubleshooting

### Database Connection Failed

**Error:** "Database connection failed"

**Solutions:**
1. Check MySQL is running: `mysql -u root -p`
2. Verify credentials in `.env`
3. Ensure database `library_uni` exists
4. Check user permissions

### File Upload Errors

**Error:** "تعذر إنشاء مجلد الرفع"

**Solutions:**
1. Create directory manually: `mkdir front-end\uploads\books`
2. Set write permissions on uploads folder
3. Check PHP `upload_max_filesize` in php.ini

### Page Not Found (404)

**Solutions:**
1. Ensure PHP server is running from correct directory (`C:\projects`)
2. Check URL includes `/library_uni/` prefix
3. Verify file paths are correct

### Session Issues

**Error:** Sessions not persisting

**Solutions:**
1. Check `session.save_path` in php.ini
2. Ensure cookies are enabled in browser
3. Clear browser cache and cookies

---

## 📞 Support

For issues or questions:
- Check the code comments for inline documentation
- Review `database.sql` for schema details
- Inspect browser console for JavaScript errors
- Check PHP error logs for backend issues

---

## 🎯 Next Steps

After successful installation:

1. **Customize**: Update branding, colors, and content
2. **Test**: Try all features (register, login, upload, search)
3. **Backup**: Set up regular database backups
4. **Monitor**: Check logs regularly for errors
5. **Deploy**: When ready, deploy to production server

---

**Congratulations! 🎉** Your University Library system is now ready to use!
