document.addEventListener('DOMContentLoaded', () => {
    const specializationFilter = document.getElementById('specialization-filter');
    const yearFilter = document.getElementById('year-filter');
    const searchInput = document.getElementById('search-input');
    const booksList = document.getElementById('books-list');
    const placeholder = document.getElementById('books-placeholder');

    let booksData = [];
    let searchDebounce;

    const ensurePlaceholder = () => {
        if (booksList && placeholder && !placeholder.parentNode) {
            booksList.appendChild(placeholder);
        }
    };

    const showPlaceholder = (message) => {
        ensurePlaceholder();
        if (placeholder) {
            placeholder.textContent = message;
            placeholder.classList.remove('hidden');
        }
    };

    const hidePlaceholder = () => {
        if (placeholder) {
            placeholder.classList.add('hidden');
        }
    };

    const CATEGORY_THEMES = {
        'computer-science': 'from-blue-900 to-blue-700',
        'software-engineering': 'from-indigo-900 to-indigo-700',
        'information-technology': 'from-teal-900 to-teal-700',
        'cybersecurity': 'from-red-900 to-red-700',
        'ai': 'from-purple-900 to-purple-700',
        'data-science': 'from-yellow-900 to-yellow-700',
        'networks': 'from-emerald-900 to-emerald-700',
        'web-development': 'from-cyan-900 to-cyan-700',
        'default': 'from-gray-800 to-gray-700'
    };

    const cardGradient = (category) => {
        const key = (category || '').toLowerCase().trim();
        return CATEGORY_THEMES[key] || CATEGORY_THEMES.default;
    };

    const renderBooks = () => {
        if (!booksList) return;

        Array.from(booksList.children).forEach((child) => {
            if (child !== placeholder) {
                child.remove();
            }
        });

        const searchValue = (searchInput?.value || '').trim().toLowerCase();
        const specializationValue = (specializationFilter?.value || 'all').toLowerCase();
        const yearValue = (yearFilter?.value || 'all').toLowerCase();

        const filteredBooks = booksData.filter((book) => {
            const category = (book.category || '').toLowerCase();
            const level = (book.level || '').toLowerCase();
            const yearText = book.year ? String(book.year).toLowerCase() : '';
            const title = (book.title || '').toLowerCase();
            const author = (book.author || '').toLowerCase();

            const matchesSearch =
                !searchValue ||
                title.includes(searchValue) ||
                author.includes(searchValue) ||
                category.includes(searchValue);

            const matchesSpecialization =
                specializationValue === 'all' || category.includes(specializationValue);

            const matchesYear =
                yearValue === 'all' || level.includes(yearValue) || yearText.includes(yearValue);

            return matchesSearch && matchesSpecialization && matchesYear;
        });

        const hasBooks = Boolean(filteredBooks.length);

        if (!hasBooks) {
            showPlaceholder('لا توجد كتب مطابقة للبحث.');
            return;
        }

        hidePlaceholder();

        filteredBooks.forEach((book) => {
            const card = document.createElement('div');
            card.className = 'book-card rounded-xl overflow-hidden bg-gray-800 border border-gray-700 flex flex-col justify-between';
            card.dataset.category = book.category || '';
            card.dataset.level = book.level || '';
            card.dataset.year = book.year ? String(book.year) : '';

            const gradient = cardGradient(book.category);
            const levelLabel = book.level || (book.year ? `السنة ${book.year}` : 'عام');
            const ratingDisplay = book.avg_rating ? `⭐ ${Number(book.avg_rating).toFixed(1)}` : '⭐ جديد';
            const downloadUrl = book.file_path ? `../..${book.file_path}` : '#';

            card.innerHTML = `
                <div>
                    <div class="relative h-48 bg-gradient-to-r ${gradient} flex items-center justify-center">
                        <i class="fas fa-book-open text-8xl text-white opacity-20 floating-icon"></i>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <i class="fas fa-book text-5xl text-white"></i>
                        </div>
                        <span class="absolute top-3 left-3 bg-gray-900/80 text-yellow-400 text-xs px-2.5 py-1 rounded-full font-bold">
                            ${ratingDisplay}
                        </span>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="text-xl font-bold text-white">${book.title || 'كتاب بدون عنوان'}</h3>
                            <span class="bg-blue-900 text-blue-300 text-xs px-3 py-1 rounded-full">${levelLabel}</span>
                        </div>
                        <p class="text-gray-300 mb-4 line-clamp-3">${book.description || 'لا يوجد وصف متاح لهذا الكتاب.'}</p>
                        <div class="flex flex-wrap gap-2 mb-4">
                            ${book.category ? `<span class="course-badge bg-gray-700 text-blue-400 text-xs px-3 py-1 rounded-full">${book.category}</span>` : ''}
                            ${book.author ? `<span class="course-badge bg-gray-700 text-blue-400 text-xs px-3 py-1 rounded-full">${book.author}</span>` : ''}
                        </div>
                    </div>
                </div>
                <div class="p-6 pt-0 border-t border-gray-700/50 mt-auto">
                    <div class="flex justify-between items-center mt-4">
                        <a href="add-review.html?book_id=${book.id}" class="text-xs text-blue-400 hover:text-blue-300 transition">
                            <i class="fas fa-star ml-1"></i> تقييم
                        </a>
                        ${book.file_path ? 
                            `<a href="${downloadUrl}" download class="download-btn bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-1">
                                <i class="fas fa-download"></i> تحميل PDF
                            </a>` : 
                            `<span class="text-xs text-gray-500">غير متوفر للتحميل</span>`
                        }
                    </div>
                </div>
            `;

            booksList.appendChild(card);
        });
    };

    const loadBooks = async (searchQuery = '') => {
        showPlaceholder('جاري تحميل الكتب...');
        try {
            const url = new URL('../../back-end/books/list.php', window.location.href);

            if (searchQuery.trim()) {
                url.searchParams.set('q', searchQuery.trim());
            }

            const response = await fetch(url);
            if (!response.ok) {
                throw new Error('Unable to load books');
            }

            const resData = await response.json();
            booksData = Array.isArray(resData) ? resData : (Array.isArray(resData?.data) ? resData.data : []);

            if (!booksData.length) {
                showPlaceholder('لا توجد كتب في المكتبة حتى الآن.');
            } else {
                hidePlaceholder();
                renderBooks();
            }
        } catch (error) {
            console.error(error);
            showPlaceholder('حدث خطأ أثناء تحميل الكتب.');
        }
    };

    specializationFilter?.addEventListener('change', renderBooks);
    yearFilter?.addEventListener('change', renderBooks);
    searchInput?.addEventListener('input', (event) => {
        const value = event.target.value || '';

        clearTimeout(searchDebounce);
        searchDebounce = setTimeout(() => loadBooks(value), 300);
    });

    loadBooks();
});
