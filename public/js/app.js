function formatFecha(fecha) {
    if (!fecha) return "Fecha no disponible";

    const date = new Date(fecha);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, "0"); // Mes empieza en 0
    const day = String(date.getDate()).padStart(2, "0");
    const hours = String(date.getHours()).padStart(2, "0");
    const minutes = String(date.getMinutes()).padStart(2, "0");
    const seconds = String(date.getSeconds()).padStart(2, "0");

    return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
}

document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.querySelector('[data-drawer-toggle="logo-sidebar"]');
    const sidebar = document.getElementById('logo-sidebar');

    sidebarToggle.addEventListener('click', function() {
      const isHidden = sidebar.classList.contains('-translate-x-full');
      const backdrop = document.querySelector('[drawer-backdrop]');

      if (isHidden) {
        // Abrir sidebar
        sidebar.classList.remove('-translate-x-full');
        if (backdrop) backdrop.classList.remove('hidden');
      } else {
        // Cerrar sidebar
        sidebar.classList.add('-translate-x-full');
        if (backdrop) backdrop.classList.add('hidden');
      }
    });

    // Cerrar al hacer clic en el fondo
    document.addEventListener('click', function(event) {
      const backdrop = document.querySelector('[drawer-backdrop]');
      if (!backdrop) return;

      const isBackdrop = event.target.hasAttribute('drawer-backdrop');
      const isSidebar = event.target.closest('#logo-sidebar');
      const isToggle = event.target.closest('[data-drawer-toggle="logo-sidebar"]');

      if (isBackdrop && !isSidebar && !isToggle) {
        sidebar.classList.add('-translate-x-full');
        backdrop.classList.add('hidden');
      }
    });
});

function formatCurrency(value) {
  const num = typeof value === 'number' ? value : parseFloat(value) || 0;

  return new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP'
  }).format(num);
}

document.addEventListener('input', function (e) {
    if (!e.target.classList.contains('moneda-cop')) return;

    const input = e.target;
    const value = parseFloat(input.value) || 0;

    let span = input.parentNode.querySelector('.formatted-span');

    if (!span) {
        span = document.createElement('span');
        span.className =
            'formatted-span right-2 top-9 text-sm font-semibold text-green-600 pointer-events-none';
            //'formatted-span absolute right-2 top-9 text-sm font-semibold text-green-600 pointer-events-none';
        input.parentNode.appendChild(span);
    }

    span.textContent = value ? formatCurrency(value) : '';
});

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

setTimeout(() => {
    location.reload();
}, 30 * 60 * 1000);


/*function mostrarErrores(errors) {
    document.querySelectorAll(".error-msg").forEach(e => e.remove());

    Object.keys(errors).forEach(campo => {
        const campoForm = campo.replace(/\./g, "][");
        const input = document.querySelector(`[name="${campoForm}"]`)
            || document.querySelector(`[name="${campoForm}]"]`);

        if (!input) return;
        const div = document.createElement("div");
        div.className = "error-msg text-red-600 mt-1 text-sm";
        div.innerText = errors[campo][0];
        input.insertAdjacentElement("afterend", div);
    });
}

function mostrarErrores(errors) {
    Object.entries(errors).forEach(([field, messages]) => {

        const input = document.querySelector(`[name="${field}"]`);
        const errorDiv = document.querySelector(`[data-error-for="${field}"]`);

        if (input) {
            input.classList.add('border-red-500');
        }

        if (errorDiv) {
            errorDiv.textContent = messages[0];
            errorDiv.classList.remove('hidden');
        }
    });
}*/

function mostrarErrores(errors) {
    limpiarErrores();
    Object.keys(errors).forEach(campo => {
        const campoForm = campo.replace(/\./g, "][");
        const input = document.querySelector(`[name="${campoForm}"]`) || document.querySelector(`[name="${campoForm}]"]`);
        if (!input) return;

        // 🔴 borde rojo
        input.classList.add('border-red-500');

        // 📝 mensaje
        const div = document.createElement("div");
        div.className = "error-msg text-red-600 mt-1 text-sm";
        div.innerText = errors[campo][0];
        input.insertAdjacentElement("afterend", div);
    });
}

function limpiarErrores() {
    document.querySelectorAll('[data-error-for]').forEach(el => {
        el.textContent = '';
        el.classList.add('hidden');
    });

    document.querySelectorAll('.border-red-500').forEach(el => {
        el.classList.remove('border-red-500');
    });
}
