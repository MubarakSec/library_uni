# 🔧 Manual Database Setup Instructions

Since automated import didn't work, follow these manual steps:

---

## Option 1: Command Line (Recommended)

### Step 1: Find MySQL

MySQL is usually installed at one of these locations:
- **XAMPP**: `C:\xampp\mysql\bin\mysql.exe`
- **WAMP**: `C:\wamp64\bin\mysql\mysql8.0.x\bin\mysql.exe`
- **Standalone**: `C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe`

### Step 2: Add MySQL to PATH (Optional but easier)

1. Copy the path where `mysql.exe` is located
2. Search Windows for "Environment Variables"
3. Edit "Path" variable
4. Add the MySQL bin directory
5. Restart Command Prompt

### Step 3: Import Database

**Open Command Prompt** and run:

```bash
cd D:\Github\library_uni

# If MySQL is in PATH:
mysql -u root -p < database.sql

# OR use full path (XAMPP example):
C:\xampp\mysql\bin\mysql.exe -u root -p < database.sql
```

Enter MySQL password when prompted.

---

## Option 2: Use the Batch Script

**Double-click**: `import-database.bat`

The script will:
- ✓ Find MySQL automatically
- ✓ Import database.sql
- ✓ Show success/error message

---

## Option 3: MySQL Workbench (GUI)

1. Open **MySQL Workbench**
2. Connect to your MySQL server
3. Go to: **Server** → **Data Import**
4. Select: **Import from Self-Contained File**
5. Choose: `D:\Github\library_uni\database.sql`
6. Click: **Start Import**

---

## Option 4: phpMyAdmin (XAMPP/WAMP)

1. Open: `http://localhost/phpmyadmin`
2. Click: **Import** tab
3. Click: **Choose File**
4. Select: `D:\Github\library_uni\database.sql`
5. Click: **Go**

---

## ✅ Verify Import Success

After importing, run:

```bash
cd D:\Github\library_uni
php tests\test-db-connection.php
```

**Expected output**:
```
✓ .env file found
✓ Environment variables loaded
✓ Database connection successful!
✓ Connected to database: library_uni
✓ Table 'users' exists
  └─ Records: 3
✓ Table 'books' exists
  └─ Records: 3
✓ Table 'book_requests' exists
  └─ Records: 0
✓ Table 'book_reviews' exists
  └─ Records: 2
```

---

## ❌ Troubleshooting

### Problem: "mysql: command not found"

**Solution**: Use full path to mysql.exe (see Option 1, Step 1)

### Problem: "Access denied"

**Solutions**:
1. Check password is correct
2. Update `.env` file with correct `DB_PASS`
3. Try different user: `mysql -u your_username -p`

### Problem: "Can't connect to MySQL server"

**Solutions**:
1. Start MySQL service:
   - **XAMPP**: Open XAMPP Control Panel → Start MySQL
   - **WAMP**: Open WAMP → Start All Services
   - **Windows Service**: Services → MySQL → Start
2. Check if MySQL is running: `tasklist | findstr mysql`

---

## 🎯 Next Steps

Once database is imported successfully:

1. ✅ Verify with: `php tests\test-db-connection.php`
2. ✅ Start server: `php -S localhost:8000` (from `D:\Github`)
3. ✅ Open browser: `http://localhost:8000/library_uni/front-end/pages/login.html`
4. ✅ Login with: `student@library.uni` / `Student123!`

---

**Need help?** Check [INSTALL.md](INSTALL.md) or [QUICKSTART.md](QUICKSTART.md)
