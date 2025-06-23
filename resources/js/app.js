import './bootstrap';
import 'flowbite/dist/flowbite';
import Swal from 'sweetalert2';

import 'tom-select/dist/css/tom-select.default.css';
import TomSelect from 'tom-select';
window.TomSelect = TomSelect;

window.Swal = Swal;

import Alpine from 'alpinejs';
window.Alpine = Alpine;

Alpine.start();

if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/sw.js')
      .then(reg => console.log('Service Worker registrado', reg))
      .catch(err => console.error('Error al registrar SW:', err));
  });
}