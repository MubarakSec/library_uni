document.addEventListener('DOMContentLoaded', function () {
    // تبديل الوضع الليلي
    const modeToggle = document.getElementById('modeToggle');
    const body = document.body;

    // تحقق من التفضيل المحفوظ
    const savedMode = localStorage.getItem('mode');
    if (savedMode === 'dark') {
        body.classList.add('dark-mode');
        modeToggle.querySelector('.fa-moon').classList.add('hidden');
        modeToggle.querySelector('.fa-sun').classList.remove('hidden');
    }

    modeToggle.addEventListener('click', function () {
        body.classList.toggle('dark-mode');

        const isDarkMode = body.classList.contains('dark-mode');
        localStorage.setItem('mode', isDarkMode ? 'dark' : 'light');

        modeToggle.querySelector('.fa-moon').classList.toggle('hidden');
        modeToggle.querySelector('.fa-sun').classList.toggle('hidden');
    });

    // القائمة الجانبية للموبايل
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
    const mobileMenu = document.getElementById('mobileMenu');
    const closeMenuBtn = document.getElementById('closeMenuBtn');

    mobileMenuBtn.addEventListener('click', function () {
        mobileMenu.classList.add('active');
        mobileMenuOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    });

    closeMenuBtn.addEventListener('click', function () {
        mobileMenu.classList.remove('active');
        mobileMenuOverlay.classList.remove('active');
        document.body.style.overflow = '';
    });

    mobileMenuOverlay.addEventListener('click', function () {
        mobileMenu.classList.remove('active');
        mobileMenuOverlay.classList.remove('active');
        document.body.style.overflow = '';
    });

    // تأثير التمرير السلس للروابط
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();

            const targetId = this.getAttribute('href');
            if (targetId === '#') return;

            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth'
                });

                // إغلاق القائمة الجانبية إذا كانت مفتوحة
                mobileMenu.classList.remove('active');
                mobileMenuOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    });

    // تأثير التحميل للبطاقات
    const cards = document.querySelectorAll('.book-card, .summary-card, .challenge-card');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });

    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = `opacity 0.5s ease ${index * 0.1}s, transform 0.5s ease ${index * 0.1}s`;
        observer.observe(card);
    });

    // تأثير التمرير للشريط العلوي
    let lastScroll = 0;
    const header = document.querySelector('.main-header');

    window.addEventListener('scroll', function () {
        const currentScroll = window.pageYOffset;

        if (currentScroll <= 0) {
            header.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)';
            if (body.classList.contains('dark-mode')) {
                header.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.3)';
            }
            return;
        }

        if (currentScroll > lastScroll) {
            // التمرير لأسفل
            header.style.transform = 'translateY(-100%)';
        } else {
            // التمرير لأعلى
            header.style.transform = 'translateY(0)';
            header.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)';
            if (body.classList.contains('dark-mode')) {
                header.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.3)';
            }
        }

        lastScroll = currentScroll;
    });

    // عرض تنبيه عند الضغط على زر الاشتراك في النشرة البريدية
    const newsletterForm = document.querySelector('.newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function (e) {
            e.preventDefault();
            alert('شكرًا لك على رايك في المكتبة الجامعية!');
            this.reset();
        });
    }

    // عرض تنبيه عند الضغط على أي زر تحميل
    document.querySelectorAll('.book-actions .btn, .summary-actions .btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            if (this.querySelector('.fa-download')) {
                e.preventDefault();
                alert('سيبدأ تحميل الملف قريبًا!');
            }
        });
    });
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