import './bootstrap';

import * as Turbo from '@hotwired/turbo';
import Alpine from 'alpinejs';
import { createIcons, icons } from 'lucide';
import Swal from 'sweetalert2';

window.Alpine = Alpine;
window.lucide = {
    createIcons: (options = {}) => createIcons({ icons, ...options })
};
window.Swal = Swal;

Alpine.start();

// Global Onboarding & Loading Overlay Injector
document.addEventListener('DOMContentLoaded', injectLoaderHTML);
document.addEventListener('turbo:load', injectLoaderHTML);

function injectLoaderHTML() {
    if (document.getElementById('global-loading-overlay')) return;
    
    const overlay = document.createElement('div');
    overlay.id = 'global-loading-overlay';
    overlay.className = 'fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-slate-900/60 backdrop-blur-sm transition-all duration-300 opacity-0 pointer-events-none';
    overlay.innerHTML = `
        <div class="bg-white/95 border border-slate-100 rounded-3xl p-8 shadow-2xl flex flex-col items-center max-w-[280px] w-full mx-4 scale-95 transition-all duration-300" id="global-loading-card">
            <div class="relative w-16 h-16 flex items-center justify-center mb-5">
                <div class="absolute inset-0 rounded-full border-4 border-slate-100 border-t-blue-600 animate-spin"></div>
                <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="animate-pulse"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/><circle cx="12" cy="13" r="3"/></svg>
                </div>
            </div>
            <h3 class="text-slate-900 font-extrabold text-[15px] mb-1.5 text-center">Memproses Data</h3>
            <p class="text-slate-500 text-xs text-center leading-relaxed">Harap tunggu sebentar, sedang mengunggah perubahan ke cloud...</p>
        </div>
    `;
    document.body.appendChild(overlay);
}

function showGlobalLoader() {
    const overlay = document.getElementById('global-loading-overlay');
    const card = document.getElementById('global-loading-card');
    if (overlay && card) {
        overlay.classList.remove('opacity-0', 'pointer-events-none');
        overlay.classList.add('opacity-100');
        card.classList.remove('scale-95');
        card.classList.add('scale-100');
    }
}

function hideGlobalLoader() {
    const overlay = document.getElementById('global-loading-overlay');
    const card = document.getElementById('global-loading-card');
    if (overlay && card) {
        overlay.classList.remove('opacity-100');
        overlay.classList.add('opacity-0', 'pointer-events-none');
        card.classList.remove('scale-100');
        card.classList.add('scale-95');
    }
}

// Intercept form submissions (HTML and AJAX)
document.addEventListener('submit', (event) => {
    if (event.defaultPrevented) return;
    if (event.target.classList.contains('no-loader')) return;
    
    // Gunakan timeout kecil agar jika form dibatalkan oleh validator client-side tidak langsung muncul
    setTimeout(() => {
        if (!event.defaultPrevented) {
            showGlobalLoader();
        }
    }, 50);
});

// Intercept navigasi halaman via Turbo
document.addEventListener('turbo:visit', () => {
    showGlobalLoader();
});

// Intercept submit form via Turbo
document.addEventListener('turbo:submit-start', () => {
    showGlobalLoader();
});

// Sembunyikan loader saat load selesai
document.addEventListener('turbo:load', () => {
    hideGlobalLoader();
});
document.addEventListener('turbo:frame-load', () => {
    hideGlobalLoader();
});
document.addEventListener('turbo:render', () => {
    hideGlobalLoader();
});

// Sembunyikan loader jika kembali halaman dari cache browser
window.addEventListener('pageshow', (event) => {
    if (event.persisted) {
        hideGlobalLoader();
    }
});

// Intercept Fetch API write requests (POST, PUT, DELETE, PATCH)
const originalFetch = window.fetch;
window.fetch = async function (...args) {
    const options = args[1];
    const isWriteRequest = options && ['POST', 'PUT', 'DELETE', 'PATCH'].includes((options.method || '').toUpperCase());
    
    if (isWriteRequest) {
        showGlobalLoader();
    }
    
    try {
        const response = await originalFetch(...args);
        if (isWriteRequest) {
            hideGlobalLoader();
        }
        return response;
    } catch (error) {
        if (isWriteRequest) {
            hideGlobalLoader();
        }
        throw error;
    }
};

// Intercept Axios requests (jika ada)
if (window.axios) {
    window.axios.interceptors.request.use((config) => {
        const method = (config.method || '').toUpperCase();
        if (['POST', 'PUT', 'DELETE', 'PATCH'].includes(method)) {
            showGlobalLoader();
        }
        return config;
    }, (error) => {
        return Promise.reject(error);
    });

    window.axios.interceptors.response.use((response) => {
        hideGlobalLoader();
        return response;
    }, (error) => {
        hideGlobalLoader();
        return Promise.reject(error);
    });
}
