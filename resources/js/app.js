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
