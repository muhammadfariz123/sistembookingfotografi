import './bootstrap';
import '@hotwired/turbo';

import Alpine from 'alpinejs';
import * as lucide from 'lucide';
import Swal from 'sweetalert2';

window.Alpine = Alpine;
window.lucide = lucide;
window.Swal = Swal;

Alpine.start();
