# Library_Uni - University Library Management System

A modern web-based library management system built with PHP and MySQL, featuring book reviews, admin dashboard, and advanced search capabilities.

![Rating](https://img.shields.io/badge/Rating-⭐⭐⭐⭐⭐-yellow)
![PHP](https://img.shields.io/badge/PHP-8.0+-blue)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange)

---

## ✨ Features

### Core Features
- 🔐 **User Authentication** - Secure login/registration with role-based access
- 📚 **Book Management** - Upload, browse, and search books (PDF)
- 🔍 **Advanced Search** - Filter by category, level, rating with sorting
- 📄 **Pagination** - Efficient browsing (20 items per page)

### New Features
- ⭐ **Reviews System** - Rate books (1-5 stars) and write reviews
- 📊 **Analytics Dashboard** - Admin statistics and insights
- 🎯 **Request Management** - Students can request books (max 5/day)
- 🔧 **Admin Panel** - Manage users, books, and requests

### Security Features
- ✅ Environment variables (.env)
- ✅ Prepared statements (SQL injection prevention)
- ✅ Password hashing (bcrypt)
- ✅ Session security (httponly, samesite)
- ✅ File validation (type, size, extension)
- ✅ Rate limiting

---

## 🚀 Quick Start

### Prerequisites
- PHP 8.0+
- MySQL 8.0+

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/YOUR_USERNAME/library_uni.git
   cd library_uni
   ```

2. **Configure environment**
   ```bash
   copy .env.example .env
   # Edit .env with your MySQL credentials
   ```

3. **Import database**
   ```bash
   # Option 1: Using PHP script
   php tests/import-database.php
   
   # Option 2: Using MySQL
   mysql -u root -p < database.sql
   ```

4. **Start server**
   ```bash
   cd ..
   php -S localhost:8000
   ```

5. **Open in browser**
   ```
   http://localhost:8000/library_uni/front-end/pages/login.html
   ```

See [QUICKSTART.md](QUICKSTART.md) for detailed instructions.

---

## 👥 Test Accounts

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@library.uni | Admin123! |
| Student | student@library.uni | Student123! |
| Assistant | assistant@library.uni | Assistant123! |

---

## 📁 Project Structure

```
library_uni/
├── back-end/
│   ├── auth/           # Authentication (login, register, logout)
│   ├── books/          # Books management + reviews + search
│   ├── admin/          # Admin panel APIs
│   ├── config/         # Database + session + env loader
│   ├── middleware/     # Authentication & authorization
│   └── helpers/        # Response helpers
├── front-end/
│   ├── pages/          # HTML pages
│   ├── assets/         # CSS, JS, images
│   └── uploads/        # Uploaded PDFs (not in Git)
├── tests/              # Testing scripts
├── database.sql        # Database schema
└── .env.example        # Environment template
```

---

## 🗄️ Database Schema

- **users** - User accounts with roles
- **books** - Book catalog with ratings
- **book_requests** - Student book requests
- **book_reviews** - User reviews and ratings

See [database.sql](database.sql) for complete schema.

---

## 🔧 API Endpoints

### Public
- `GET /back-end/books/list.php` - List books (paginated)
- `GET /back-end/books/search.php` - Advanced search
- `GET /back-end/books/reviews.php?book_id=1` - Get reviews

### Protected (login required)
- `POST /back-end/books/add-review.php` - Submit review
- `POST /back-end/books/request.php` - Request book

### Admin only
- `GET /back-end/admin/dashboard.php` - Statistics
- `GET /back-end/admin/requests.php` - Manage requests

See [advanced_features_complete.md](advanced_features_complete.md) for API documentation.

---

## 🧪 Testing

```bash
# Test database connection
php tests/test-db-connection.php

# Test API endpoints (server must be running)
php tests/test-api-endpoints.php
```

---

## 📚 Documentation

- [INSTALL.md](INSTALL.md) - Complete installation guide
- [QUICKSTART.md](QUICKSTART.md) - 5-minute quick start
- [DATABASE_SETUP.md](DATABASE_SETUP.md) - Database setup options
- [TESTING.md](TESTING.md) - Testing instructions

---

## 🔒 Security

- Passwords hashed with `password_hash()` (bcrypt)
- SQL injection prevention via prepared statements
- Session fixation prevention (ID regeneration)
- XSS protection (httponly cookies)
- CSRF protection (samesite cookies)
- File upload validation (type, size, extension)

**Important**: Change default passwords in production!

---

## 🛠️ Tech Stack

- **Backend**: PHP 8.0+, PDO
- **Database**: MySQL 8.0+
- **Frontend**: HTML5, Tailwind CSS, Vanilla JS
- **Server**: PHP Built-in Server (development)

---

## 📊 Project Stats

- **Backend APIs**: 15+ endpoints
- **Database Tables**: 4 + 3 views
- **Lines of Code**: ~3000+
- **Features**: 10+ major features
- **Security Level**: High ✅

---

## 🤝 Contributing

Contributions are welcome! Please:
1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Open a Pull Request

---

## 📝 License

This project is open source and available under the [MIT License](LICENSE).

---

## 👨‍💻 Author

**MubarakSec**
- GitHub: [@MubarakSec](https://github.com/MubarakSec)

---

## 🙏 Acknowledgments

- Built with ❤️ for university students
- Inspired by modern library management systems
- Thanks to all contributors and testers

---

## 📞 Support

For issues or questions:
- Check [INSTALL.md](INSTALL.md) troubleshooting section
- Open an issue on GitHub
- Review documentation in `/docs` folder

---

**Made with 💻 and ☕**
