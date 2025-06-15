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

/*if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/sw.js')
      .then(reg => console.log('Service Worker registrado', reg))
      .catch(err => console.error('Error al registrar SW:', err));
  });
}*/

// Registro y limpieza del Service Worker
if ('serviceWorker' in navigator) {
    window.addEventListener('load', async () => {
        try {
            // Elimina SW antiguos si hay
            const registrations = await navigator.serviceWorker.getRegistrations();
            for (const reg of registrations) {
                await reg.unregister();
            }

            // Registra el nuevo
            const registration = await navigator.serviceWorker.register('/sw.js');
            console.log('✅ Service Worker registrado:', registration);
        } catch (error) {
            console.error('❌ Error al registrar el Service Worker:', error);
        }
    });
}