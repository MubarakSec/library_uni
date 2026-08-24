document.addEventListener('DOMContentLoaded', () => {
    // Floating icons animation variation
    const floatingIcons = document.querySelectorAll('.floating-icon');
    floatingIcons.forEach(icon => {
        const delay = Math.random() * 2;
        icon.style.animationDelay = `${delay}s`;
    });

    // Difficulty filter
    const difficultyFilter = document.getElementById('difficulty-filter');
    const cards = document.querySelectorAll('.pathway-card');

    if (difficultyFilter && cards.length) {
        difficultyFilter.addEventListener('change', () => {
            const selected = difficultyFilter.value;
            cards.forEach(card => {
                const badge = card.querySelector('.rounded-full')?.textContent || '';
                if (selected === 'all') {
                    card.style.display = 'block';
                } else if (selected === 'beginner' && (badge.includes('مبتدئ') || badge.includes('أساسيات'))) {
                    card.style.display = 'block';
                } else if (selected === 'intermediate' && badge.includes('متوسط')) {
                    card.style.display = 'block';
                } else if (selected === 'advanced' && badge.includes('متقدم')) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }

    // Modal or feedback for learning paths
    const startButtons = document.querySelectorAll('.pathway-card button');
    startButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            const card = btn.closest('.pathway-card');
            const title = card?.querySelector('h3')?.textContent || 'المسار';
            console.log(`بدء التعلم في: ${title}`);
        });
    });
});
