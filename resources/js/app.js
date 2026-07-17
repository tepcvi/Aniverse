// ============================================================
// AniVerse — Frontend JavaScript
// ============================================================

import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// ------- Dark Mode Toggle -------
function initTheme() {
    const saved = localStorage.getItem('aniverse-theme');
    if (saved === 'light') {
        document.documentElement.classList.add('light');
        document.documentElement.classList.remove('dark');
    } else {
        document.documentElement.classList.add('dark');
        document.documentElement.classList.remove('light');
    }
}
initTheme();

window.toggleTheme = function () {
    const isLight = document.documentElement.classList.contains('light');
    if (isLight) {
        document.documentElement.classList.remove('light');
        document.documentElement.classList.add('dark');
        localStorage.setItem('aniverse-theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        document.documentElement.classList.add('light');
        localStorage.setItem('aniverse-theme', 'light');
    }
    updateThemeIcon();
};

function updateThemeIcon() {
    const sunIcon = document.getElementById('sun-icon');
    const moonIcon = document.getElementById('moon-icon');
    if (!sunIcon || !moonIcon) return;
    const isLight = document.documentElement.classList.contains('light');
    sunIcon.classList.toggle('hidden', !isLight);
    moonIcon.classList.toggle('hidden', isLight);
}

document.addEventListener('DOMContentLoaded', updateThemeIcon);

// ------- Search Autocomplete -------
function initAutocomplete() {
    const input = document.getElementById('search-autocomplete');
    const dropdown = document.getElementById('autocomplete-dropdown');
    if (!input || !dropdown) return;

    let debounceTimer;

    input.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        const query = this.value.trim();

        if (query.length < 2) {
            dropdown.classList.add('hidden');
            dropdown.innerHTML = '';
            return;
        }

        debounceTimer = setTimeout(async () => {
            try {
                const response = await fetch(`/api/autocomplete?q=${encodeURIComponent(query)}`);
                const results = await response.json();

                if (results.length === 0) {
                    dropdown.classList.add('hidden');
                    return;
                }

                dropdown.innerHTML = results.map(anime => `
                    <a href="/anime/${anime.id}" class="flex items-center gap-3 px-4 py-3 hover:bg-dark-700/50 light:hover:bg-dark-100 transition-colors">
                        <img src="${anime.coverImage?.medium || ''}" alt="" class="w-10 h-14 object-cover rounded" loading="lazy">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-white light:text-dark-900 truncate">${anime.title?.english || anime.title?.romaji || 'Unknown'}</p>
                            <p class="text-xs text-dark-400 light:text-dark-500">${anime.format || ''} ${anime.seasonYear ? '• ' + anime.seasonYear : ''} ${anime.averageScore ? '• ★ ' + anime.averageScore + '%' : ''}</p>
                        </div>
                    </a>
                `).join('');
                dropdown.classList.remove('hidden');
            } catch (e) {
                console.error('Autocomplete error:', e);
            }
        }, 300);
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (!input.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });

    // Navigate to search on Enter
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            const query = input.value.trim();
            if (query) {
                window.location.href = `/search?query=${encodeURIComponent(query)}`;
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', initAutocomplete);

// ------- Lazy Loading Images -------
function initLazyLoad() {
    const images = document.querySelectorAll('img[data-src]');
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    img.classList.add('animate-fade-in');
                    observer.unobserve(img);
                }
            });
        }, { rootMargin: '50px' });

        images.forEach(img => observer.observe(img));
    } else {
        // Fallback
        images.forEach(img => {
            img.src = img.dataset.src;
            img.removeAttribute('data-src');
        });
    }
}

document.addEventListener('DOMContentLoaded', initLazyLoad);

// ------- Toast Notifications -------
window.showToast = function (message, type = 'info', duration = 3000) {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const colors = {
        info: 'from-primary-600 to-primary-700',
        success: 'from-emerald-600 to-emerald-700',
        error: 'from-red-600 to-red-700',
        warning: 'from-amber-600 to-amber-700',
    };

    const toast = document.createElement('div');
    toast.className = `flex items-center gap-3 px-5 py-3 rounded-xl bg-gradient-to-r ${colors[type] || colors.info} text-white shadow-2xl animate-slide-up`;
    toast.innerHTML = `
        <span class="text-sm font-medium">${message}</span>
        <button onclick="this.parentElement.remove()" class="ml-auto text-white/70 hover:text-white transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    `;

    container.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-10px)';
        toast.style.transition = 'all 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, duration);
};

// ------- Scroll Animations -------
function initScrollAnimations() {
    const elements = document.querySelectorAll('[data-animate]');
    if (!elements.length) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in');
                entry.target.style.opacity = '1';
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    elements.forEach(el => {
        el.style.opacity = '0';
        observer.observe(el);
    });
}

document.addEventListener('DOMContentLoaded', initScrollAnimations);

// ------- Mobile Menu Toggle -------
window.toggleMobileMenu = function () {
    const menu = document.getElementById('mobile-menu');
    if (menu) {
        menu.classList.toggle('hidden');
    }
};

// ------- Hero Banner Carousel -------
function initHeroCarousel() {
    const slides = document.querySelectorAll('[data-hero-slide]');
    const dots = document.querySelectorAll('[data-hero-dot]');
    if (slides.length <= 1) return;

    let current = 0;
    const total = slides.length;

    function showSlide(index) {
        slides.forEach((s, i) => {
            s.classList.toggle('opacity-100', i === index);
            s.classList.toggle('opacity-0', i !== index);
            s.classList.toggle('z-10', i === index);
            s.classList.toggle('z-0', i !== index);
        });
        dots.forEach((d, i) => {
            d.classList.toggle('bg-white', i === index);
            d.classList.toggle('bg-white/40', i !== index);
            d.classList.toggle('w-8', i === index);
            d.classList.toggle('w-3', i !== index);
        });
    }

    setInterval(() => {
        current = (current + 1) % total;
        showSlide(current);
    }, 6000);

    dots.forEach((dot, i) => {
        dot.addEventListener('click', () => {
            current = i;
            showSlide(current);
        });
    });
}

document.addEventListener('DOMContentLoaded', initHeroCarousel);
