function cambiarEstado(itemId, estadoActual) {
    let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    Swal.fire({
        title: '⚠️ Cambiar Estado',
        text: "Selecciona un nuevo estado para el proyecto.",
        icon: 'warning', // 🔥 Icono de advertencia
        input: 'select',
        inputOptions: window.estadosProyecto,
        inputValue: estadoActual,
        showCancelButton: true,
        confirmButtonText: 'Guardar',
        cancelButtonText: 'Cancelar',
        customClass: {
            popup: 'custom-swal', // Clase para personalizar la alerta
            confirmButton: 'custom-confirm-button',
            cancelButton: 'custom-cancel-button',
            input: 'border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1' // Clase para personalizar el select
        },
        preConfirm: (nuevoEstado) => {
            return fetch(`{{ route('editStatus', '') }}/${itemId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ estado: nuevoEstado })
            })
            .then(response => {
                if (!response.ok) throw new Error('Error en la solicitud');
                return response.json();
            })
            .catch(error => {
                Swal.showValidationMessage(`Error: ${error.message}`);
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: '¡Estado actualizado!',
                text: 'El estado se cambió correctamente.',
                icon: 'success',
                confirmButtonText: 'Aceptar'
            }).then(() => location.reload()); 
        }
    });
}
