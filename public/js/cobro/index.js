deleteCobro = async (id) => {
    try {
        const result = await Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción eliminará el cobro seleccionado.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        });

        if (result.isConfirmed) {
            Swal.fire({
                title: 'Eliminando cobro...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            const response = await fetch(`/cobro/deleteCobro/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const resData = await response.json();
            Swal.close();

            if (resData.status) {
                Swal.fire('Éxito', resData.message, 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1200);
            } else {
                Swal.fire('Error', resData.message, 'error');
            }
        }
    } catch (error) {
        Swal.close();
        Swal.fire('Error', 'No se pudo eliminar el cobro.', 'error');
        console.error(error);
    }
}

function abrirAcuerdoPago(id, nombreProyecto) {
    // 1. Obtener la fecha de hoy en formato local YYYY-MM-DD
    const hoy = new Date();
    const año = hoy.getFullYear();
    const mes = String(hoy.getMonth() + 1).padStart(2, '0');
    const dia = String(hoy.getDate()).padStart(2, '0');
    const fechaMinima = `${año}-${mes}-${dia}`;

    // 2. Disparar el SweetAlert personalizando el texto con el nombre del proyecto
    Swal.fire({
        title: 'Acuerdo de Pago',
        html: `
            <p class="text-gray-600 mb-4 text-lg">Selecciona la fecha acordada para el proyecto:</p>
            <h5 class="text-gray-800 font-bold text-xl mb-2">${nombreProyecto}</h5>
        `,
        icon: 'calendar',
        input: 'date',
        inputAttributes: {
            min: fechaMinima 
        },
        // Añadimos clases personalizadas de Tailwind al input y botones
        customClass: {
            input: 'border border-gray-400 rounded-xl px-4 py-3 text-lg w-50 max-w-md mx-auto focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none text-center shadow-xs',
            confirmButton: 'bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-3 rounded-xl mx-2 shadow-sm transition-colors text-base',
            cancelButton: 'bg-red-500 hover:bg-red-600 text-white font-medium px-6 py-3 rounded-xl mx-2 shadow-sm transition-colors text-base'
        },
        buttonsStyling: false, // Desactivar estilos por defecto de Swal para usar 100% Tailwind
        showCancelButton: true,
        confirmButtonText: 'Guardar Fecha',
        cancelButtonText: 'Cancelar',
        
        preConfirm: (dateValue) => {
            if (!dateValue) {
                Swal.showValidationMessage('Por favor, selecciona una fecha válida.');
                return false;
            }

            const fechaSeleccionada = new Date(dateValue + 'T00:00:00');
            const fechaHoyClon = new Date(fechaMinima + 'T00:00:00');

            if (fechaSeleccionada < fechaHoyClon) {
                Swal.showValidationMessage('La fecha debe ser igual o posterior al día de hoy.');
                return false;
            }

            return dateValue;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Actualizando...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            // 3. Envío de datos a Laravel
            fetch('/cobro/sendAcuerdoPago', { 
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    id: id,
                    fecha_acuerdo_pago: result.value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire(
                        '¡Guardado!',
                        'La fecha de acuerdo de pago se registró con éxito.',
                        'success'
                    ).then(() => {
                        window.location.reload(); 
                    });
                } else {
                    Swal.fire('Error', data.message || 'No se pudo guardar la fecha.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error de red', 'Hubo un fallo en la conexión con el servidor.', 'error');
            });
        }
    });
}

// Asegúrate de que los 5 parámetros estén aquí
function openNotificacionModal(id, nombre, telefono, proyecto, valor_pendiente) {
    
    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'notificacion-modal' }));
    
    window.dispatchEvent(new CustomEvent('set-notificacion-data', { 
        detail: { 
            id: id, 
            nombre: nombre, 
            telefono: telefono, 
            proyecto: proyecto, 
            valor_pendiente: valor_pendiente 
        } 
    }));
}

function whatsappData() {
    return {
        id: null,
        telefono: '',
        mensaje: '',
        valor_pendiente: 0,
        init() {
            window.addEventListener('set-notificacion-data', (e) => {
                const d = e.detail;
                this.id = d.id;
                this.telefono = d.telefono;
                this.valor_pendiente = d.valor_pendiente;
                
                // Usamos la función global aquí
                this.mensaje = `*Estimado ${d.nombre}:*\n\nNos alegra comunicarte que tu proyecto *${d.proyecto}* continúa en avance; para lo cual solicitamos el pago de ${formatCurrency(d.valor_pendiente)}, agradecemos realizar el pago a la mayor brevedad posible para evitar retrasos.`;
            });
        },
        enviar() {
            // 1. Limpiar el número de teléfono: solo números y el prefijo de país
            // Esto asegura que +56 9 6282 8665 se convierta en 56962828665
            let numeroLimpio = this.telefono.replace(/\D/g, ''); 

            // 2. Guardar en BD vía AJAX (tu código actual)
            fetch('/cobro/sendNotificacion', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ 
                    id: this.id, 
                    fecha_notificacion: new Date().toISOString().slice(0, 10) 
                })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    Swal.fire('¡Éxito!', 'Fecha registrada', 'success');
                    
                    // 3. Abrir WhatsApp correctamente
                    const url = `https://wa.me/${numeroLimpio}?text=${encodeURIComponent(this.mensaje)}`;
                    window.open(url, '_blank');
                    
                    this.$dispatch('close-modal', 'notificacion-modal');
                }
            });
        }
    }
}