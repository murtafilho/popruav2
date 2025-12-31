import './bootstrap';

// Leaflet
import L from 'leaflet';
import 'leaflet.markercluster';

// Fix Leaflet default marker icon paths
delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl: new URL('leaflet/dist/images/marker-icon-2x.png', import.meta.url).href,
    iconUrl: new URL('leaflet/dist/images/marker-icon.png', import.meta.url).href,
    shadowUrl: new URL('leaflet/dist/images/marker-shadow.png', import.meta.url).href,
});

// Export Leaflet globally
window.L = L;

// Alpine.js (usado pelo Breeze)
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// Dark Mode Toggle
function toggleDarkMode() {
    const html = document.documentElement;
    const isDark = html.classList.contains('dark');
    
    if (isDark) {
        html.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    } else {
        html.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    }
    
    // Atualizar ícone do botão se existir
    updateDarkModeIcon();
}

// Exportar para window para uso global (disponível imediatamente)
window.toggleDarkMode = toggleDarkMode;

window.getDarkMode = function() {
    return document.documentElement.classList.contains('dark');
};

function updateDarkModeIcon() {
    const isDark = window.getDarkMode();
    const darkIcons = document.querySelectorAll('[data-dark-icon]');
    const lightIcons = document.querySelectorAll('[data-light-icon]');
    
    darkIcons.forEach(icon => {
        if (isDark) {
            icon.classList.remove('hidden');
        } else {
            icon.classList.add('hidden');
        }
    });
    
    lightIcons.forEach(icon => {
        if (isDark) {
            icon.classList.add('hidden');
        } else {
            icon.classList.remove('hidden');
        }
    });
}

// Atualizar ícones quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    updateDarkModeIcon();
    
    // Sobrescrever a função inline com a versão completa
    window.toggleDarkMode = toggleDarkMode;
});
