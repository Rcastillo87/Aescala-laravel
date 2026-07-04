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