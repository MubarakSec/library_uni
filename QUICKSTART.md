# 🚀 Quick Start Guide - Library_Uni

## خطوات سريعة للبدء (5 دقائق)

### 1️⃣ Import Database

فتح Command Prompt وتشغيل:

```bash
cd D:\Github\library_uni
mysql -u root -p < database.sql
```

سيطلب منك كلمة مرور MySQL. أدخلها واضغط Enter.

---

### 2️⃣ Configure .env File

الملف `.env` موجود لكن يحتاج تعديل كلمة المرور:

**افتح الملف:** `D:\Github\library_uni\.env`

**عدل هذا السطر:**
```
DB_PASS=
```

**ضع كلمة مرور MySQL:**
```
DB_PASS=yourpassword
```

> **ملاحظة**: إذا كنت تستخدم XAMPP/WAMP وليس لديك كلمة مرور، اتركها فارغة

---

### 3️⃣ Test Database Connection

```bash
cd D:\Github\library_uni
php tests\test-db-connection.php
```

يجب أن ترى:
```
✓ .env file found
✓ Environment variables loaded
✓ Database connection successful!
✓ Table 'users' exists
✓ Table 'books' exists
✓ Table 'book_requests' exists
✓ Table 'book_reviews' exists
```

---

### 4️⃣ Start PHP Server

```bash
cd D:\Github
php -S localhost:8000
```

يجب أن ترى:
```
PHP 8.x.x Development Server (http://localhost:8000) started
```

---

### 5️⃣ Open in Browser

افتح المتصفح:
```
http://localhost:8000/library_uni/front-end/pages/login.html
```

---

## 🔑 Test Accounts

| Email | Password | Role |
|-------|----------|------|
| admin@library.uni | Admin123! | Admin |
| student@library.uni | Student123! | Student |
| assistant@library.uni | Assistant123! | Assistant |

---

## ❌ Troubleshooting

### Problem: "Access denied for user 'root'@'localhost'"

**Solution:**
1. افتح `.env`
2. تأكد من `DB_PASS` صحيحة
3. OR استخدم user مختلف:
   ```
   DB_USER=your_username
   DB_PASS=your_password
   ```

### Problem: "Database 'library_uni' doesn't exist"

**Solution:**
```bash
mysql -u root -p < database.sql
```

### Problem: "Can't find .env file"

**Solution:**
```bash
copy .env.example .env
# ثم عدل .env
```

### Problem: Tables not found

**Solution:**
Drop and recreate database:
```bash
mysql -u root -p
```
```sql
DROP DATABASE IF EXISTS library_uni;
exit;
```
```bash
mysql -u root -p < database.sql
```

---

## ✅ Verification Checklist

- [ ] MySQL is running
- [ ] Database imported successfully
- [ ] `.env` file configured with correct password
- [ ] `php tests\test-db-connection.php` passes
- [ ] PHP server started successfully
- [ ] Can login with test accounts

---

## 📞 Next Steps

Once everything works:

1. **Test Reviews**: Login → Browse books → Add a review
2. **Test Admin**: Login as admin → Access dashboard API
3. **Test Search**: Try advanced search with filters
4. **Upload Books**: Login as assistant/admin → Upload PDFs

---

**Need help?** Check [INSTALL.md](INSTALL.md) for detailed instructions!
