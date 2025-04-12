let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function cambiarEstado(itemId, estadoActual) {
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
            return fetch(`editStatus/${itemId}`, {
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

async function formIdProyecto(id) {
    document.getElementById('id_proyecto').value = id;
    document.getElementById('id').value = null;
    document.getElementById('TextModalTarea').textContent = 'Crear Tarea';
}

async function editTarea(id) {
    return fetch(`editTarea/${id}`, {
        method: 'get',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('Error en la solicitud');
        return response.json();
    })
    .then(data => {
        console.log('Datos recibidos:', data.data); // Verifica los datos

        // Abre el modal
        window.dispatchEvent(new CustomEvent('open-modal', { detail: 'my-modal' }));

        // Establece los valores de los campos
        document.getElementById('TextModalTarea').textContent = 'Editar Tarea';
        document.getElementById('id_proyecto').value = data.data.id_proyecto;
        document.getElementById('id').value = data.data.id;
        document.getElementById('descripccion').value = data.data.descripccion;
        document.getElementById('fec_fin').value = data.data.fechaFin;
        document.getElementById('fec_inicio').value = data.data.fecIni;
        document.getElementById('id_user').value  = data.data.id_user;
        document.getElementById('id_tarea_tipo').value  = data.data.id_tarea_tipo;
        document.getElementById('id_tarea_estado').value  = data.data.id_tarea_estado;
    })
    .catch(error => {
        Swal.showValidationMessage(`Error: ${error.message}`);
    });
}

async function listFinanzas(page = 1, id) {
    try {
        const response = await fetch(`listFinanzas/?page=${page}&id=${id}`, {
            method: "GET",
            headers: {
                "X-CSRF-TOKEN": csrfToken,
                "Content-Type": "application/json"
            }
        });
        const data = await response.json();
        document.getElementById('id_proyecto_finanza').value = id;
        tableFinanzas(data.data);
    } catch (error) {
        Swal.fire("Error", "No se pudo consultar la data.", "error");
    }
}

function tableFinanzas(data) {
    const serviceList = document.getElementById('serviceFinanzas');
    const pagination = document.getElementById('pagination');
    const noDataMessage = document.getElementById('noDataMessage');

    serviceList.innerHTML = '';
    pagination.innerHTML = '';

    if (data.data && data.data.length > 0) {
        data.data.forEach(service => {
            const fila = document.createElement("tr");
            fila.innerHTML = `
                <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">${service.spanTipo}</td>
                <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">${service.concepto.toLowerCase() || 'N/A'}</td>
                <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">${service.valor}</td>
                <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">${formatFecha(service.createdAt)}</td>
            `;
            serviceList.appendChild(fila);
        });

        const paginationLinks = data.links.map(link => {
            if (link.url) {
                const page = new URL(link.url).searchParams.get('page') || 1;
                return `<a href="#" onclick="listFinanzas(${page}, ${id})" class="px-4 py-2 mx-1 text-blue-500 rounded-lg">${link.label}</a>`;
            }
            return `<span class="px-4 py-2 mx-1 text-blue-500 rounded-lg">${link.label}</span>`;
        }).join('');
        pagination.innerHTML = paginationLinks;

        noDataMessage.classList.add('hidden');
    } else {
        noDataMessage.classList.remove('hidden');
    }
}

async function openAvance(page = 1, id) {
    try {
        const response = await fetch(`listAvances/?page=${page}&id=${id}`, {
            method: "GET",
            headers: {
                "X-CSRF-TOKEN": csrfToken,
                "Content-Type": "application/json"
            }
        });
        const data = await response.json();
        document.getElementById('id_tarea_avance').value = id;
        tableAvances(data.data);
    } catch (error) {
        Swal.fire("Error", "No se pudo consultar la data.", "error");
    }
}

function tableAvances(data) {
    const serviceList = document.getElementById('avanceList');
    const pagination = document.getElementById('paginationAvance');
    const noDataMessage = document.getElementById('noDataMessageAvance');

    serviceList.innerHTML = '';
    pagination.innerHTML = '';

    if (data.data && data.data.length > 0) {
        data.data.forEach(service => {
            const fila = document.createElement("tr");
            fila.innerHTML = `
                <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">${service.avance}</td>
                <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">${service.fec_avance}</td>
                <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">${formatFecha(service.createdAt)}</td>
                <td class="py-2 text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                    <button onclick="confirmDelete(${service.id})" class="px-3 py-1 text-white bg-red-500 rounded-lg hover:bg-red-600">
                        Eliminar
                    </button>
                </td>
            `;
            serviceList.appendChild(fila);
        });

        const paginationLinks = data.links.map(link => {
            if (link.url) {
                const page = new URL(link.url).searchParams.get('page') || 1;
                return `<a href="#" onclick="listAvances(${page}, ${id})" class="px-4 py-2 mx-1 text-blue-500 rounded-lg">${link.label}</a>`;
            }
            return `<span class="px-4 py-2 mx-1 text-blue-500 rounded-lg">${link.label}</span>`;
        }).join('');
        pagination.innerHTML = paginationLinks;

        noDataMessage.classList.add('hidden');
    } else {
        noDataMessage.classList.remove('hidden');
    }
}

function confirmDelete(id) {
    Swal.fire({
        title: "¿Estás seguro?",
        text: "Esta acción no se puede deshacer.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const response = await fetch(`deleteAvance/?id=${id}`, {
                    method: "delete",
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                        "Content-Type": "application/json"
                    }
                });
                const data = await response.json();
                if( data.status){
                    window.dispatchEvent(new CustomEvent('close-modal', { detail: 'avance-modal' }));
                    Swal.fire({
                        title: "Eliminado",
                        text: "El avance ha sido eliminado correctamente.",
                        icon: "success",
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire("Error", "No se pudo eliminar la data.", "error");
                } 
            } catch (error) {
                Swal.fire("Error", "No se pudo eliminar la data.", "error");
            }
        }
    });
}

async function listaCotizacion(page = 1, id) {
    try {
        const response = await fetch(`listaCotizacion/?page=${page}&id=${id}`, {
            method: "GET",
            headers: {
                "X-CSRF-TOKEN": csrfToken,
                "Content-Type": "application/json"
            }
        });
        const data = await response.json();
        document.getElementById('id_proyecto_cotizacion').value = id;
        tableCotizacion(data.data);
    } catch (error) {
        Swal.fire("Error", "No se pudo consultar la data.", "error");
    }
}

function tableCotizacion(data) {
    const serviceList = document.getElementById('listaCotizacion');
    const pagination = document.getElementById('paginationCotizacion');
    const noDataMessage = document.getElementById('noDataMessageCotizacion');
    const totalCotizacion = document.getElementById('totalCotizacion');
    totalCotizacion.classList.add('hidden');

    serviceList.innerHTML = '';
    pagination.innerHTML = '';
    let total = 0;
    if (data.data && data.data.length > 0) {
        data.data.forEach(service => {
            total += service.subtotal;
            const fila = document.createElement("tr");
            fila.innerHTML = `
                <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">${service.createdAt}</td>
                <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">${service.nombre_material.toLowerCase()}</td>
                <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">${service.cantidad}</td>
                <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">${service.valor_unidad}</td>
                <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">${service.subtotal}</td>
                <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                <button onclick="deleteCotizacion(${service.id})" class="px-3 py-1 text-white bg-red-500 rounded-lg hover:bg-red-600 btn-eliminar-cotizacion">
                        Eliminar
                    </button>
                </td>
            `;
            serviceList.appendChild(fila);
        });
        if(total>0){
            totalCotizacion.classList.remove('hidden');
            totalCotizacion.textContent = `Total : $${total}`;
        } else {
            totalCotizacion.classList.add('hidden');
        }

        const paginationLinks = data.links.map(link => {
            if (link.url) {
                const page = new URL(link.url).searchParams.get('page') || 1;
                return `<a href="#" onclick="listaCotizacion(${page}, ${id})" class="px-4 py-2 mx-1 text-blue-500 rounded-lg">${link.label}</a>`;
            }
            return `<span class="px-4 py-2 mx-1 text-blue-500 rounded-lg">${link.label}</span>`;
        }).join('');
        pagination.innerHTML = paginationLinks;

        noDataMessage.classList.add('hidden');
    } else {
        noDataMessage.classList.remove('hidden');
    }
}

document.getElementById('formCotizacion').addEventListener('submit', async function (e) {
    e.preventDefault(); // Evita envío tradicional

    const form = e.target;
    const url = form.action;
    const formData = new FormData(form);

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            },
            body: formData
        });

        const data = await response.json();

        if (!response.ok || data.success === false) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Ocurrió un error al guardar los datos.'
            });
        } else {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: data.message || 'Cotización guardada correctamente.',
                confirmButtonText: 'Aceptar'
            }).then(() => {
                // redireccionar si todo salió bien
                window.location.reload();
            });
        }

    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo procesar la solicitud.'
        });
    }
});

async function deleteCotizacion(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "¿Deseas eliminar este elemento?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'No, cancelar',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const response = await fetch(`deleteCotizacion/?id=${id}`, {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                        "Content-Type": "application/json"
                    }
                });

                const data = await response.json();

                if (data.status) {
                    window.dispatchEvent(new CustomEvent('close-modal', { detail: 'cotizacion-modal' }));
                    Swal.fire({
                        title: "Eliminado",
                        text: "Item eliminado correctamente.",
                        icon: "success",
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire("Error", data.message || "No se pudo eliminar la data.", "error");
                }
            } catch (error) {
                Swal.fire("Error", "Ocurrió un problema al procesar la solicitud.", "error");
            }
        }
    });
}