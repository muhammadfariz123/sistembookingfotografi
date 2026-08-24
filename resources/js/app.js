import './bootstrap';

// Turbo dimatikan agar navigasi langsung diproses oleh browser tanpa delay SPA
// import * as Turbo from '@hotwired/turbo';
import Alpine from 'alpinejs';
import { createIcons, icons } from 'lucide';
import Swal from 'sweetalert2';

window.Alpine = Alpine;
window.lucide = {
    createIcons: (options = {}) => createIcons({ icons, ...options })
};
window.Swal = Swal;

Alpine.start();
