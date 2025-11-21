async function loadPartial(targetId, url) {
    const container = document.getElementById(targetId);
    if (!container) return null;

    try {
        const res = await fetch(url);
        if (!res.ok) {
            container.innerHTML = '';
            return null;
        }
        const html = await res.text();
        container.innerHTML = html;

        // Mark active nav link based on the current page
        if (targetId === 'site-header') {
            const currentPath = window.location.pathname.split('/').pop() || 'index.html';
            const navLinks = container.querySelectorAll('a[href]');
            navLinks.forEach(link => {
                const linkPath = link.getAttribute('href');
                if (linkPath === currentPath) {
                    link.classList.add('text-blue-400', 'font-semibold');
                }
            });
        }
        return container;
    } catch (err) {
        console.error('Error loading partial:', url, err);
        return null;
    }
}

async function hydrateHeaderAuthState() {
    try {
        const res = await fetch('../../back-end/auth/me.php', { credentials: 'include' });
        if (!res.ok) return;

        const data = await res.json();
        const guest = document.getElementById('header-auth-guest');
        const user = document.getElementById('header-auth-user');
        const nameEl = document.getElementById('header-username');
        const uploadLink = document.getElementById('header-upload-link');

        if (!guest || !user) return;

        if (!data.logged_in) {
            guest.classList.remove('hidden');
            user.classList.add('hidden');
            return;
        }

        guest.classList.add('hidden');
        user.classList.remove('hidden');

        if (nameEl && data.name) {
            nameEl.textContent = `مرحباً، ${data.name}`;
        }

        if (uploadLink) {
            if (data.role === 'assistant' || data.role === 'admin') {
                uploadLink.classList.remove('hidden');
            } else {
                uploadLink.classList.add('hidden');
            }
        }
    } catch (e) {
        console.error('Failed to hydrate header auth state', e);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    loadPartial('site-header', '../partials/header.html').then(hydrateHeaderAuthState);
    loadPartial('site-footer', '../partials/footer.html');
});
