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