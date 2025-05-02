import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
// Configurer le token CSRF pour toutes les requêtes axios
const token = document.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

// Configurer le token CSRF pour jQuery
if (token && window.jQuery) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': token.getAttribute('content')
        }
    });
}
// Fonction utilitaire pour fetch avec CSRF
window.csrfFetch = async (url, options = {}) => {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!token) {
        console.error('CSRF token not found');
        throw new Error('CSRF token not found');
    }

    const defaultOptions = {
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
        },
    };

    const mergedOptions = {
        ...defaultOptions,
        ...options,
        headers: {
            ...defaultOptions.headers,
            ...options.headers,
        },
    };

    return fetch(url, mergedOptions);
};
