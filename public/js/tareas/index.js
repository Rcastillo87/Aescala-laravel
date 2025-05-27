let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

async function editTarea(id) {
    document.getElementById('idFecFinReal').classList.add('hidden');
    document.getElementById('fec_fin_real').value = '';
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
        document.getElementById('dias_trabajo').value = data.data.dias_trabajo;
        document.getElementById('fec_inicio').value = data.data.fecIni;
        document.getElementById('id_user').value  = data.data.id_user;
        document.getElementById('id_tarea_tipo').value  = data.data.id_tarea_tipo;
        document.getElementById('id_tarea_estado').value  = data.data.id_tarea_estado;
        document.getElementById('fec_fin').value = data.data.fec_fin;
        if( (data.data.id_tarea_estado == 3) && (data.data.fec_fin_real)){
            document.getElementById('idFecFinReal').classList.remove('hidden');
            document.getElementById('fec_fin_real').value = data.data.fec_fin_real.split(' ')[0];
        }

    })
    .catch(error => {
        Swal.showValidationMessage(`Error: ${error.message}`);
    });
}

async function formIdProyecto() {
    document.getElementById('id').value = null;
    document.getElementById('TextModalTarea').textContent = 'Crear Tarea';
}

document.addEventListener('DOMContentLoaded', function() {
    // Elementos del DOM
    const conFechaFin = document.getElementById('conFechaFin');
    const fechaFinContainer = document.getElementById('fechaFinContainer');
    const diasTrabajoContainer = document.getElementById('diasTrabajoContainer');
    const fechaFinInput = document.getElementById('fec_fin');
    const diasTrabajoInput = document.getElementById('dias_trabajo');

    // Función para alternar visibilidad
    function toggleFields() {
        if (conFechaFin.checked) {
            fechaFinContainer.classList.remove('hidden');
            diasTrabajoContainer.classList.add('hidden');
            fechaFinInput.required = true;
            diasTrabajoInput.required = false;
            if(diasTrabajoInput.value < '1'){
                diasTrabajoInput.value = '1';
            }
        } else {
            fechaFinContainer.classList.add('hidden');
            diasTrabajoContainer.classList.remove('hidden');
            fechaFinInput.required = false;
            diasTrabajoInput.required = true;
        }
    }

    // Event listener para el checkbox
    conFechaFin.addEventListener('change', toggleFields);

    // Inicializar el estado
    toggleFields();

    // Inicializar el datepicker de Flowbite
    if (typeof window.Datepicker !== 'undefined') {
        new Datepicker(fechaFinInput, {
            format: 'yyyy-mm-dd',
            autohide: true
        });
    }
});

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
                    <button onclick="confirmDelete(${service.id})" class="px-3 py-1 text-white bg-red-500 rounded-lg hover:bg-red-600 cursor-pointer">
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

function initSortable() {
    document.querySelectorAll('.task-list, .task-end').forEach(list => {
        new Sortable(list, {
            group: 'shared-tasks',
            animation: 150,
            scroll: true,
            scrollSensitivity: 30,
            scrollSpeed: 10,
            onEnd: async function (evt) {
                const proyectoId = evt.item.getAttribute('data-id');
                const tipo = evt.item.getAttribute('data-tipo');
                const destino = evt.to.closest('[data-id][data-name]');
                const newTareaTipoId = destino.getAttribute('data-id');
                const estadoNombre = destino.getAttribute('data-name');

                // Revertir si algo está mal
                if (!proyectoId || !newTareaTipoId) return;

                // Si ya está en el mismo tipo, no hacer nada
                if (tipo == newTareaTipoId) return;

                // Si es Finalizado
                if (estadoNombre.toLowerCase() === 'finalizado') {
                    const result = await Swal.fire({
                        title: '¿Desea Finalizar el Proyecto?',
                        html: `
                            <div class="text-left">
                                <label for="fecha_fin" class="block mb-2 text-sm font-semibold text-gray-800 tracking-wide">
                                    📅 Fecha de finalización:
                                </label>
                                <input
                                    type="date"
                                    id="fecha_fin"
                                    class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm text-gray-700"
                                />
                            </div>
                        `,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, finalizar',
                        cancelButtonText: 'Cancelar',
                        preConfirm: () => {
                            const fecha = document.getElementById('fecha_fin').value;
                            if (!fecha) {
                                Swal.showValidationMessage('Debes seleccionar una fecha');
                                return false;
                            }
                            return { fecha };
                        }
                    });


                    if (!result.isConfirmed) {
                        if (evt.from !== evt.to) {
                            evt.from.insertBefore(evt.item, evt.from.children[evt.oldIndex]);
                        }
                        return;
                    }

                    const fechaSeleccionada = result.value.fecha;
                    console.log('Fecha seleccionada:', fechaSeleccionada);

                    try {
                        const response = await fetch('finTarea', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ proyecto_id: proyectoId, fecha_fin: fechaSeleccionada})
                        });

                        const data = await response.json();

                        if (response.ok && data.status) {
                            await Swal.fire('Finalizado', data.message, 'success');
                            location.reload();
                        } else {
                            throw new Error(data.message || 'No se pudo finalizar el proyecto.');
                        }

                    } catch (error) {
                        console.error('Error al finalizar:', error);
                        await Swal.fire('Error', error.message, 'error');
                        location.reload();
                    }
                    return;
                }

                // Para otros cambios de tipo
                const result = await Swal.fire({
                    title: '¿Mover proyecto?',
                    text: `¿Deseas mover este proyecto al estado "${estadoNombre}"?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, mover',
                    cancelButtonText: 'Cancelar',
                });

                if (result.isConfirmed) {
                    try {
                        const response = await fetch('moverTarea', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                proyecto_id: proyectoId,
                                tarea_tipo_id: newTareaTipoId
                            })
                        });

                        const data = await response.json();
                        if (response.ok && data.status) {
                            await Swal.fire({
                                title: '¡Éxito!',
                                text: data.message || 'Tarea movida correctamente.',
                                icon: 'success'
                            });
                            location.reload();
                        } else {
                            await Swal.fire({
                                title: 'Error',
                                text: data.message || 'No se pudo mover la tarea.',
                                icon: 'error'
                            });
                            if (evt.from !== evt.to) {
                                evt.from.insertBefore(evt.item, evt.from.children[evt.oldIndex]);
                            }
                        }

                    } catch (error) {
                        console.error('Error al mover tarea:', error);
                        Swal.fire('Error', 'Ocurrió un error al mover el proyecto.', 'error');
                    }
                } else {
                    if (evt.from !== evt.to) {
                        evt.from.insertBefore(evt.item, evt.from.children[evt.oldIndex]);
                    }
                }
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', initSortable);

const container = document.getElementById('scrollContainer');
let isDragging = false;

const edgeThreshold = 500; // Área cerca del borde donde se activa el scroll
const scrollSpeed = 30;    // Velocidad de scroll

document.querySelectorAll('.draggable').forEach(el => {
el.addEventListener('dragstart', () => isDragging = true);
el.addEventListener('dragend', () => isDragging = false);
});

container.addEventListener('dragover', (e) => {
if (!isDragging) return;

const containerRect = container.getBoundingClientRect();
const mouseX = e.clientX;

// Verifica si el cursor está dentro del área de acción
if (mouseX < containerRect.left + edgeThreshold) {
    container.scrollLeft -= scrollSpeed;
} else if (mouseX > containerRect.right - edgeThreshold) {
    container.scrollLeft += scrollSpeed;
}
});
