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

    const cardGradient = (category) => {
        const gradients = [
            'from-blue-900 to-blue-700',
            'from-purple-900 to-purple-700',
            'from-green-900 to-green-700',
            'from-indigo-900 to-indigo-700',
            'from-teal-900 to-teal-700',
        ];
        const index = Math.abs(category?.length || 0) % gradients.length;
        return gradients[index];
    };

    const renderBooks = (data) => {
        if (!booksList) return;

        Array.from(booksList.children).forEach((child) => {
            if (child !== placeholder) {
                child.remove();
            }
        });

        if (!data.length) {
            showPlaceholder('لا توجد كتب مطابقة للبحث.');
            return;
        }

        hidePlaceholder();

        data.forEach((book) => {
            const card = document.createElement('div');
            card.className = 'book-card rounded-xl overflow-hidden bg-gray-800 border border-gray-700';
            card.dataset.category = book.category || '';
            card.dataset.level = book.level || '';
            card.dataset.year = book.year ? String(book.year) : '';

            const gradient = cardGradient(book.category);
            const availability = Number(book.available_copies || 0) > 0 ? 'متاح' : 'غير متاح';
            const levelLabel = book.level || (book.year ? `السنة ${book.year}` : '');

            card.innerHTML = `
                <div class="relative h-48 bg-gradient-to-r ${gradient} flex items-center justify-center">
                    <i class="fas fa-book-open text-8xl text-white opacity-20 floating-icon"></i>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fas fa-book text-5xl text-white"></i>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-start mb-3">
                        <h3 class="text-xl font-bold text-white">${book.title || 'كتاب بدون عنوان'}</h3>
                        <span class="bg-blue-900 text-blue-300 text-xs px-3 py-1 rounded-full">${levelLabel || 'عام'}</span>
                    </div>
                    <p class="text-gray-300 mb-4">${book.description || 'لا يوجد وصف متاح لهذا الكتاب.'}</p>
                    <div class="flex flex-wrap gap-2 mb-4">
                        ${book.category ? `<span class="course-badge bg-gray-700 text-blue-400 text-xs px-3 py-1 rounded-full">${book.category}</span>` : ''}
                        ${book.author ? `<span class="course-badge bg-gray-700 text-blue-400 text-xs px-3 py-1 rounded-full">${book.author}</span>` : ''}
                    </div>
                    <div class="flex justify-between items-center">
                        <div class="flex items-center text-sm text-gray-400">
                            <i class="fas fa-user mr-1"></i>
                            <span>${book.author || 'مؤلف غير معروف'}</span>
                        </div>
                        <span class="download-btn bg-blue-600 text-white px-4 py-2 rounded-lg text-sm flex items-center">${availability}</span>
                    </div>
                </div>
            `;

            booksList.appendChild(card);
        });
    };

    const loadBooks = async () => {
        showPlaceholder('جاري تحميل الكتب...');
        try {
            const url = new URL('../../back-end/books/list.php', window.location.href);
            const searchValue = (searchInput?.value || '').trim();
            const specializationValue = specializationFilter?.value || '';
            const yearValue = yearFilter?.value || '';

            if (searchValue) {
                url.searchParams.set('q', searchValue);
            }

            if (specializationValue && specializationValue !== 'all') {
                url.searchParams.set('category', specializationValue);
            }

            const normalizedYear = (() => {
                switch (yearValue) {
                    case 'first-year':
                        return 1;
                    case 'second-year':
                        return 2;
                    case 'third-year':
                        return 3;
                    case 'fourth-year':
                        return 4;
                    case 'graduate':
                        return 5;
                    default:
                        return '';
                }
            })();

            if (normalizedYear !== '') {
                url.searchParams.set('year', normalizedYear);
            }

            const response = await fetch(url);
            if (!response.ok) {
                throw new Error('Unable to load books');
            }

            const data = await response.json();
            booksData = Array.isArray(data) ? data : [];

            if (!booksData.length) {
                showPlaceholder('لا توجد كتب في المكتبة حتى الآن.');
            } else {
                hidePlaceholder();
                renderBooks(booksData);
            }
        } catch (error) {
            console.error(error);
            showPlaceholder('حدث خطأ أثناء تحميل الكتب.');
        }
    };

    specializationFilter?.addEventListener('change', loadBooks);
    yearFilter?.addEventListener('change', loadBooks);
    searchInput?.addEventListener('input', () => {
        clearTimeout(searchDebounce);
        searchDebounce = setTimeout(() => loadBooks(), 300);
    });

    loadBooks();
});
