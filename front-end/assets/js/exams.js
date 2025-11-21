document.addEventListener('DOMContentLoaded', function () {
    // تصفية حسب السنة
    const yearButtons = document.querySelectorAll('.year-btn');
    const examCards = document.querySelectorAll('.exam-card');

    yearButtons.forEach(button => {
        button.addEventListener('click', function () {
            // إزالة التنشيط من جميع الأزرار
            yearButtons.forEach(btn => btn.classList.remove('active'));

            // تنشيط الزر الحالي
            this.classList.add('active');

            const selectedYear = this.dataset.year;

            // تصفية البطاقات
            examCards.forEach(card => {
                if (selectedYear === 'all' || card.dataset.year === selectedYear) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    // نظام البحث
    const searchInput = document.getElementById('exam-search');

    searchInput.addEventListener('input', function () {
        const searchTerm = this.value.toLowerCase();

        examCards.forEach(card => {
            const subject = card.dataset.subject.toLowerCase();
            const title = card.querySelector('h3').textContent.toLowerCase();

            if (subject.includes(searchTerm) || title.includes(searchTerm)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    });

    // معاينة الملفات (يمكن تطويرها لاحقاً)
    const previewButtons = document.querySelectorAll('.preview-btn');

    previewButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            alert('سيتم تطوير خاصية المعاينة قريباً!');
        });
    });
});

// دالة تفعيل الوضع الداكن
function initDarkMode() {
    const darkModeToggle = document.getElementById('dark-mode-switch');
    const savedMode = localStorage.getItem('darkMode');

    // تطبيق الوضع المحفوظ
    if (savedMode === 'dark') {
        document.body.classList.add('dark-mode');
        darkModeToggle.checked = true;
    }

    // حدث تغيير الوضع
    darkModeToggle.addEventListener('change', function () {
        if (this.checked) {
            document.body.classList.add('dark-mode');
            localStorage.setItem('darkMode', 'dark');
        } else {
            document.body.classList.remove('dark-mode');
            localStorage.setItem('darkMode', 'light');
        }
    });
}

// بدء التشغيل عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function () {
    initDarkMode();

    // بقية الكود الخاص بالاختبارات...
    const yearButtons = document.querySelectorAll('.year-btn');
    // ... إلخ
});