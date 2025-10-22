document.getElementById('plantilla_otro_si').addEventListener('change', async function(e) {
    const file = e.target.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('file', file);

    Swal.fire({
        title: 'Validando plantilla...',
        text: 'Por favor espera mientras se valida el archivo.',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    try {
        const response = await fetch('valiPlantilla', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        });

        const result = await response.json();
        Swal.close();

        if (response.ok && result.status === 'ok') {
            Swal.fire({
                icon: 'success',
                title: 'Archivo válido',
                text: result.message || 'La estructura del Excel es correcta.'
            });
            console.log(result);
        } else if (response.status === 422) {
            // Errores de validación desde backend
            let msg = '';

            if (result.missing_columns) {
                msg += `Faltan columnas: ${result.missing_columns.join(', ')}\n`;
            }

            if (result.errors && Array.isArray(result.errors)) {
                msg += `\nErrores de filas:\n`;
                result.errors.forEach(err => {
                    msg += `- Fila ${err.fila}: ${err.detalle}\n`;
                });
            }

            Swal.fire({
                icon: 'error',
                title: 'Error de validación',
                text: msg.trim() || result.message || 'Error desconocido en la validación.'
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error inesperado',
                text: result.message || 'Ocurrió un problema procesando el archivo.'
            });
        }
    } catch (error) {
        Swal.close();
        Swal.fire({
            icon: 'error',
            title: 'Error del servidor',
            text: 'No se pudo conectar al servidor o el archivo es inválido.'
        });
        console.error('Error:', error);
    }
});
