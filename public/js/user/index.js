function cambiarEstado(userId, estadoActual) {
    Swal.fire({
        title: "¿Estás seguro?",
        text: (estadoActual==1) ? "El usuario será Desactivado" : "El usuario será Activado",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, cambiar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`editStatus/${userId}`, {
                method: "get",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
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
