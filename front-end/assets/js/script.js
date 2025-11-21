document.addEventListener('DOMContentLoaded', function () {
    // بيانات وهمية للكتب والملخصات
    const booksData = [
        {
            id: 1,
            title: "مقدمة في الذكاء الاصطناعي",
            author: "د. أحمد محمد",
            year: "2023",
            category: "engineering",
            type: "book",
            downloads: 245,
            rating: 4.5,
            cover: "https://via.placeholder.com/150x200",
            badge: "جديد"
        },
        {
            id: 2,
            title: "أساسيات البرمجة بلغة بايثون",
            author: "د. سارة عبدالله",
            year: "2022",
            category: "engineering",
            type: "book",
            downloads: 512,
            rating: 4.8,
            cover: "https://via.placeholder.com/150x200",
            badge: "الأكثر تحميلاً"
        },
        {
            id: 3,
            title: "التحليل العددي وتطبيقاته",
            author: "د. خالد حسن",
            year: "2021",
            category: "science",
            type: "book",
            downloads: 187,
            rating: 4.2,
            cover: "https://via.placeholder.com/150x200"
        },
        {
            id: 4,
            title: "قواعد البيانات المتقدمة",
            author: "د. علي محمود",
            year: "2023",
            category: "engineering",
            type: "book",
            downloads: 321,
            rating: 4.6,
            cover: "https://via.placeholder.com/150x200",
            badge: "موصى به"
        },
        {
            id: 5,
            title: "ملخص مقرر الذكاء الاصطناعي",
            author: "الطالب: محمد علي",
            year: "2023",
            category: "engineering",
            type: "summary",
            downloads: 124,
            rating: 4.3,
            cover: "https://via.placeholder.com/150x200"
        },
        {
            id: 6,
            title: "ملخص شامل لمادة قواعد البيانات",
            author: "الطالب: سارة أحمد",
            year: "2022",
            category: "engineering",
            type: "summary",
            downloads: 215,
            rating: 4.7,
            cover: "https://via.placeholder.com/150x200"
        },
        {
            id: 7,
            title: "علم الأدوية الأساسي",
            author: "د. نورة سالم",
            year: "2023",
            category: "medicine",
            type: "book",
            downloads: 178,
            rating: 4.4,
            cover: "https://via.placeholder.com/150x200"
        },
        {
            id: 8,
            title: "اختبارات سابقة - مبادئ الاقتصاد",
            author: "قسم إدارة الأعمال",
            year: "2022",
            category: "business",
            type: "exam",
            downloads: 298,
            rating: 4.1,
            cover: "https://via.placeholder.com/150x200"
        }
    ];

    // عناصر DOM
    const resultsContainer = document.getElementById('resultsContainer');
    const resultsCount = document.getElementById('resultsCount').querySelector('span');
    const dynamicSearch = document.getElementById('dynamicSearch');
    const categoryFilter = document.getElementById('categoryFilter');
    const typeFilter = document.getElementById('typeFilter');
    const yearFilter = document.getElementById('yearFilter');
    const sortFilter = document.getElementById('sortFilter');
    const resetFilters = document.getElementById('resetFilters');
    const viewOptions = document.querySelectorAll('.view-option');
    const prevPageBtn = document.getElementById('prevPage');
    const nextPageBtn = document.getElementById('nextPage');
    const pageNumbers = document.getElementById('pageNumbers');

    // متغيرات البحث
    let currentSearch = '';
    let currentCategory = 'all';
    let currentType = 'all';
    let currentYear = 'all';
    let currentSort = 'newest';
    let currentView = 'grid';
    let currentPage = 1;
    const itemsPerPage = 8;

    // تهيئة الصفحة
    function initPage() {
        renderBooks();
        setupEventListeners();
    }

    // إعداد مستمعي الأحداث
    function setupEventListeners() {
        dynamicSearch.addEventListener('input', function () {
            currentSearch = this.value.toLowerCase();
            currentPage = 1;
            renderBooks();
        });

        categoryFilter.addEventListener('change', function () {
            currentCategory = this.value;
            currentPage = 1;
            renderBooks();
        });

        typeFilter.addEventListener('change', function () {
            currentType = this.value;
            currentPage = 1;
            renderBooks();
        });

        yearFilter.addEventListener('change', function () {
            currentYear = this.value;
            currentPage = 1;
            renderBooks();
        });

        sortFilter.addEventListener('change', function () {
            currentSort = this.value;
            renderBooks();
        });

        resetFilters.addEventListener('click', function () {
            dynamicSearch.value = '';
            categoryFilter.value = 'all';
            typeFilter.value = 'all';
            yearFilter.value = 'all';
            sortFilter.value = 'newest';

            currentSearch = '';
            currentCategory = 'all';
            currentType = 'all';
            currentYear = 'all';
            currentSort = 'newest';
            currentPage = 1;

            renderBooks();
        });

        viewOptions.forEach(option => {
            option.addEventListener('click', function () {
                viewOptions.forEach(opt => opt.classList.remove('active'));
                this.classList.add('active');
                currentView = this.dataset.view;
                resultsContainer.classList.toggle('list-view', currentView === 'list');
            });
        });

        prevPageBtn.addEventListener('click', function () {
            if (currentPage > 1) {
                currentPage--;
                renderBooks();
            }
        });

        nextPageBtn.addEventListener('click', function () {
            const totalPages = Math.ceil(filterBooks().length / itemsPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                renderBooks();
            }
        });
    }

    // تصفية الكتب حسب معايير البحث
    function filterBooks() {
        return booksData.filter(book => {
            // تطابق البحث النصي
            const matchesSearch = book.title.toLowerCase().includes(currentSearch) ||
                book.author.toLowerCase().includes(currentSearch);

            // تطابق التصنيف
            const matchesCategory = currentCategory === 'all' || book.category === currentCategory;

            // تطابق النوع
            const matchesType = currentType === 'all' || book.type === currentType;

            // تطابق السنة
            const matchesYear = currentYear === 'all' || book.year === currentYear;

            return matchesSearch && matchesCategory && matchesType && matchesYear;
        });
    }

    // ترتيب الكتب
    function sortBooks(books) {
        switch (currentSort) {
            case 'newest':
                return books.sort((a, b) => b.year - a.year);
            case 'oldest':
                return books.sort((a, b) => a.year - b.year);
            case 'popular':
                return books.sort((a, b) => b.downloads - a.downloads);
            case 'rating':
                return books.sort((a, b) => b.rating - a.rating);
            default:
                return books;
        }
    }

    // عرض الكتب في الصفحة
    function renderBooks() {
        const filteredBooks = filterBooks();
        const sortedBooks = sortBooks(filteredBooks);

        // التقسيم إلى صفحات
        const startIndex = (currentPage - 1) * itemsPerPage;
        const paginatedBooks = sortedBooks.slice(startIndex, startIndex + itemsPerPage);

        // تحديث عدد النتائج
        resultsCount.textContent = filteredBooks.length;

        // مسح المحتوى الحالي
        resultsContainer.innerHTML = '';

        // عرض النتائج أو رسالة عدم وجود نتائج
        if (paginatedBooks.length === 0) {
            resultsContainer.innerHTML = `
                <div class="no-results">
                    <i class="fas fa-search"></i>
                    <h4>لا توجد نتائج مطابقة</h4>
                    <p>حاول تغيير معايير البحث أو إعادة تعيين الفلاتر</p>
                </div>
            `;
        } else {
            paginatedBooks.forEach(book => {
                const bookCard = document.createElement('div');
                bookCard.className = 'book-card';
                bookCard.innerHTML = `
                    <div class="book-cover">
                        <img src="${book.cover}" alt="${book.title}">
                        ${book.badge ? `<div class="book-badge">${book.badge}</div>` : ''}
                    </div>
                    <div class="book-info">
                        <h4>${book.title}</h4>
                        <div class="book-meta">
                            <span><i class="fas fa-user"></i> ${book.author}</span>
                            <span><i class="fas fa-calendar-alt"></i> ${book.year}</span>
                            <span><i class="fas fa-download"></i> ${book.downloads}</span>
                            <span><i class="fas fa-star"></i> ${book.rating}</span>
                        </div>
                        <div class="book-actions">
                            <button class="btn small-btn"><i class="fas fa-download"></i> تحميل</button>
                            <button class="btn small-btn"><i class="fas fa-eye"></i> معاينة</button>
                        </div>
                    </div>
                `;
                resultsContainer.appendChild(bookCard);
            });
        }

        // تحديث أرقام الصفحات
        updatePagination(filteredBooks.length);
    }

    // تحديث الترقيم
    function updatePagination(totalItems) {
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        pageNumbers.innerHTML = '';

        for (let i = 1; i <= totalPages; i++) {
            const pageBtn = document.createElement('button');
            pageBtn.textContent = i;
            if (i === currentPage) {
                pageBtn.classList.add('active');
            }
            pageBtn.addEventListener('click', function () {
                currentPage = i;
                renderBooks();
            });
            pageNumbers.appendChild(pageBtn);
        }

        // تعطيل/تمكين أزرار الصفحة السابقة/التالية
        prevPageBtn.disabled = currentPage === 1;
        nextPageBtn.disabled = currentPage === totalPages;
    }

    // بدء التطبيق
    initPage();
});

// Global App Functionality
document.addEventListener('DOMContentLoaded', function () {
    // Mobile Menu Toggle
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const sidebar = document.getElementById('sidebar');

    if (mobileMenuBtn && sidebar) {
        mobileMenuBtn.addEventListener('click', function () {
            sidebar.classList.toggle('active');
        });
    }

    // Dark Mode Toggle
    const darkModeToggle = document.getElementById('darkModeToggle');
    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', function () {
            document.body.classList.toggle('dark-mode');
            localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
        });

        // Check for saved user preference
        if (localStorage.getItem('darkMode') === 'true') {
            document.body.classList.add('dark-mode');
        }
    }

    // User Authentication Check
    const loginBtn = document.getElementById('loginBtn');
    if (loginBtn) {
        loginBtn.addEventListener('click', function () {
            // Check if user is logged in
            const isLoggedIn = false; // This would come from your auth system

            if (!isLoggedIn) {
                window.location.href = 'login.html';
            }
        });
    }
});