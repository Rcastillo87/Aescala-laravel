document.addEventListener("DOMContentLoaded", function(event) {
    new TomSelect("#id_proyecto",{
        create: true,
        dropdownParent: 'body',
        sortField: {
            field: "text",
            direction: "asc"
        },
        onInitialize: function() {
            this.wrapper.classList.add("tom-select-custom");
        }
    });

    FormManager.init('#formNuevaNovedad', {
        autoRedirect: true,
        confirmText: '¿Desea guardar estas novedades Novedad?'
    });

});

// Token CSRF global para fetch
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

// 1. Ver novedades y cambiar estado SOLO al cerrar el SweetAlert
function verNovedades(id, estadoActual, buttonElement) {
    // Extrae el texto directamente del atributo data del botón presionado de forma segura
    const textoNovedades = buttonElement.getAttribute('data-novedades') || '';

    let listaHtml = textoNovedades.split('***').map(item => `<li class="text-left mb-2">🔹 ${item.trim()}</li>`).join('');

    Swal.fire({
        title: 'Detalle de Novedades',
        html: `<ul class="list-none p-2 bg-gray-50 rounded-lg max-h-60 overflow-y-auto">${listaHtml}</ul>`,
        icon: 'info',
        confirmButtonText: 'Cerrar'
    }).then((result) => {
        if (estadoActual === 1) {
            fetch(`/novedades/notificadoUpd/${id}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    location.reload();
                }
            });
        }
    });
}

// 2. Abrir modal para añadir comentario
function abrirModalComentario(id) {
    document.getElementById('novedad_id').value = id;
    document.getElementById('formComentario').reset();
    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'modal-comentario' }));
}

// Enviar formulario del modal mediante Fetch y recargar al confirmar
function enviarComentario(event) {
    event.preventDefault();
    let id = document.getElementById('novedad_id').value;
    
    let estado = document.getElementById('select_estado_nuevo').value;
    let comentario = document.getElementById('textarea_comentario').value;

    fetch(`/novedades/saveNotificado/${id}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            estado: estado,
            comentario: comentario
        })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw err; });
        }
        return response.json();
    })
    .then(data => {
        if(data.success) {
            // Corrección aquí: pasamos el nombre del modal correctamente al evento de Alpine.js
            window.dispatchEvent(new CustomEvent('close-modal', { detail: 'modal-comentario' }));
            
            Swal.fire({
                title: '¡Éxito!',
                text: data.message,
                icon: 'success',
                confirmButtonText: 'Aceptar'
            }).then(() => {
                location.reload();
            });
        }
    })
    .catch(error => {
        let mensaje = error.message || 'Ocurrió un error inesperado.';
        if (error.errors) {
            let primerError = Object.values(error.errors)[0][0];
            mensaje = primerError;
        }
        Swal.fire('Error', mensaje, 'error');
    });
}

// 3. Ver comentario guardado
function verComentario(comentario, estadoNombre) {
    Swal.fire({
        title: `Comentario`,
        text: comentario,
        icon: 'info',
        confirmButtonText: 'Entendido'
    });
}

// 4. Eliminar novedad con confirmación (Solo estado 1)
function eliminarNovedad(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "¡No podrás revertir esta acción!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/novedades/delete/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    Swal.fire('¡Eliminado!', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            });
        }
    });
}

// Abrir modal de nueva novedad y resetear formulario (dejando 1 solo textarea)
function abrirModalNuevaNovedad() {
    let form = document.getElementById('formNuevaNovedad');
    form.reset();
    
    // Dejar únicamente el primer textarea por defecto
    let contenedor = document.getElementById('contenedor-novedades');
    contenedor.innerHTML = `
        <div class="flex items-start gap-2 novedad-item">
            <textarea name="novedades_array[]" rows="2" required
                class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm"
                placeholder="Escribe la novedad..."></textarea>
            <button type="button" onclick="eliminarTextareaNovedad(this)"
                class="px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700" title="Eliminar campo">
                🗑️
            </button>
        </div>
    `;

    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'modal-nueva-novedad' }));
}

// Añadir dinámicamente un nuevo textarea
function agregarTextareaNovedad() {
    let contenedor = document.getElementById('contenedor-novedades');
    let nuevoItem = document.createElement('div');
    nuevoItem.className = 'flex items-start gap-2 novedad-item';
    nuevoItem.innerHTML = `
        <textarea name="novedades_array[]" rows="2" required
            class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm"
            placeholder="Escribe la novedad..."></textarea>
        <button type="button" onclick="eliminarTextareaNovedad(this)"
            class="px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700" title="Eliminar campo">
            🗑️
        </button>
    `;
    contenedor.appendChild(nuevoItem);
}

// Eliminar textarea con validación de mínimo 1 y confirmación por SweetAlert
function eliminarTextareaNovedad(button) {
    let contenedor = document.getElementById('contenedor-novedades');
    let items = contenedor.querySelectorAll('.novedad-item');

    // Validar que siempre quede mínimo una novedad
    if (items.length <= 1) {
        Swal.fire({
            title: 'Atención',
            text: 'Debe haber al menos una novedad registrada.',
            icon: 'warning',
            confirmButtonText: 'Entendido'
        });
        return;
    }

    // Confirmación con SweetAlert al intentar quitar un campo
    Swal.fire({
        title: '¿Eliminar campo?',
        text: "¿Estás seguro de quitar esta novedad?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            button.closest('.novedad-item').remove();
        }
    });
}
