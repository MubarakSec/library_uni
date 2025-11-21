document.addEventListener('DOMContentLoaded', () => {
    const specializationFilter = document.getElementById('specialization-filter');
    const yearFilter = document.getElementById('year-filter');
    const searchInput = document.getElementById('search-input');
    const booksList = document.getElementById('books-list');
    const placeholder = document.getElementById('books-placeholder');

    let booksData = [];

    const setPlaceholder = (message) => {
        if (placeholder) {
            placeholder.textContent = message;
        }
    };

    const clearPlaceholder = () => {
        if (placeholder) {
            placeholder.remove();
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

    const renderBooks = () => {
        if (!booksList) return;

        booksList.innerHTML = '';

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

        if (!filteredBooks.length) {
            booksList.innerHTML = '<div class="col-span-full text-center text-gray-400">لا توجد كتب مطابقة للبحث.</div>';
            return;
        }

        filteredBooks.forEach((book) => {
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
        setPlaceholder('جاري تحميل الكتب...');
        try {
            const response = await fetch('../../back-end/books/list.php');
            if (!response.ok) {
                throw new Error('Unable to load books');
            }

            const data = await response.json();
            booksData = Array.isArray(data) ? data : [];
            clearPlaceholder();
            renderBooks();
        } catch (error) {
            console.error(error);
            setPlaceholder('حدث خطأ أثناء تحميل الكتب.');
        }
    };

    specializationFilter?.addEventListener('change', renderBooks);
    yearFilter?.addEventListener('change', renderBooks);
    searchInput?.addEventListener('input', renderBooks);

    loadBooks();
});
