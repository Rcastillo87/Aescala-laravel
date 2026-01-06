function cambiarEstado(userId, estadoActual) {
    let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    Swal.fire({
        title: "¿Estás seguro?",
        text: (estadoActual==1) ? "¿Desea Desactivarlo?" : "¿Desea Activarlo?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, cambiar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`editStatus/${userId}`, {
                method: "get",
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    "Content-Type": "application/json"
                }
            })
            .then(response => response.json())
            .then(data => {
                Swal.fire("¡Éxito!", "Estado actualizado.", "success");
                location.reload();
            })
            .catch(error => {
                Swal.fire("Error", "No se pudo cambiar el estado.", "error");
            });
        }
    });
}

document.getElementById('download-search-excel').addEventListener('click', function () {

    Swal.fire({
        title: 'Generando Excel',
        text: 'Por favor espere...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading()
        }
    });

    // Tomar los filtros actuales de la URL
    const params = new URLSearchParams(window.location.search);
    params.set('export', 1);

    const url = `index?${params.toString()}`;

    // Crear descarga sin recargar la página
    const link = document.createElement('a');
    link.href = url;
    link.download = 'inventario_materiales.xls';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    // Cerrar spinner luego de un momento
    setTimeout(() => {
        Swal.close();
    }, 1500);
});

