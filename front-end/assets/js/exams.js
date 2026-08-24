document.addEventListener('DOMContentLoaded', () => {
    const tabs = document.querySelectorAll('.year-tab');
    const contents = document.querySelectorAll('.year-content');
    const downloadToast = document.getElementById('download-toast');
    const searchInput = document.getElementById('exam-search');

    // Year Tabs Switching
    tabs.forEach(tab => {
        tab.addEventListener('click', function () {
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            contents.forEach(content => {
                content.classList.add('hidden');
            });

            const year = this.dataset.year;
            const targetSection = document.getElementById(`${year}-year`);
            if (targetSection) {
                targetSection.classList.remove('hidden');
            }
        });
    });

    // Download handlers
    document.querySelectorAll('.download-exam').forEach(btn => {
        btn.addEventListener('click', function () {
            const examTitle = this.dataset.exam || this.closest('.exam-card')?.querySelector('h3')?.textContent || 'النموذج';
            
            if (downloadToast) {
                const textEl = downloadToast.querySelector('.notification-text');
                if (textEl) {
                    textEl.innerHTML = `<strong>جاري التحميل</strong><br>تم بدء تحميل: ${examTitle}`;
                }
                downloadToast.classList.add('show');
                setTimeout(() => {
                    downloadToast.classList.remove('show');
                }, 4000);
            }
        });
    });

    // Realtime search
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();
            const activeYearSection = document.querySelector('.year-content:not(.hidden)');
            const cards = (activeYearSection || document).querySelectorAll('.exam-card');

            cards.forEach(card => {
                const title = card.querySelector('h3')?.textContent.toLowerCase() || '';
                const desc = card.querySelector('p')?.textContent.toLowerCase() || '';
                const tags = Array.from(card.querySelectorAll('.rounded-full')).map(el => el.textContent.toLowerCase()).join(' ');

                const matches = !query || title.includes(query) || desc.includes(query) || tags.includes(query);
                card.style.display = matches ? 'block' : 'none';
            });
        });
    }
});
