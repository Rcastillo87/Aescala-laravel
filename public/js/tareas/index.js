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

async function deleteTarea(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "¿Deseas eliminar esta tarea con todos sus avaces?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'No, cancelar',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const response = await fetch(`deleteTarea/?id=${id}`, {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                        "Content-Type": "application/json"
                    }
                });

                const data = await response.json();

                if (data.status) {
                    window.dispatchEvent(new CustomEvent('close-modal', { detail: 'tarea-modal' }));
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
    const contenedor = document.getElementById('avanceList');
    
    if (!data || data.length === 0) {
        contenedor.innerHTML = `
            <div class="p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50">
                No hay datos para mostrar
            </div>
        `;
        return;
    }

    // Mapear colores para cada tarea
    const colores = [
        'bg-blue-500', 'bg-green-500', 'bg-purple-500', 
        'bg-yellow-500', 'bg-red-500', 'bg-indigo-500',
        'bg-pink-500', 'bg-teal-500', 'bg-orange-500',
        'bg-cyan-500', 'bg-lime-500', 'bg-amber-500',
        'bg-emerald-500', 'bg-violet-500', 'bg-fuchsia-500'
    ];

    let html = `
        <div class="max-w-4xl mx-auto">
    `;

    data.forEach((tarea, index) => {
        const color = colores[index % colores.length];
        const colorClaro = color.replace('500', '200');
        const colorMedio = color.replace('500', '400');
        const colorOscuro = color;
        
        // Usar el estado proporcionado por la API
        const estadoHTML = tarea.estado 
            ? tarea.estado.replace('span-yellow', 'px-3 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800')
            : '<span class="px-3 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Estado no definido</span>';

        html += `
            <div class="mb-10">
                <div class="flex flex-wrap items-center mb-4 gap-2">
                    <div class="flex-1 flex items-center gap-2 flex-wrap">
                        <div class="${color} w-4 h-4 rounded-full"></div>
                        <h2 class="text-xl font-semibold text-gray-800">${tarea.nombre_tarea}</h2>
                        <span class="text-sm text-gray-500">${tarea.fech_ini} - ${tarea.fech_fin}</span>
                        ${estadoHTML}
                    </div>
                    <div class="ml-auto">
                        <button onclick="deleteTarea(${tarea.id})" class="px-3 py-1 text-white bg-red-500 rounded-lg hover:bg-red-600 cursor-pointer">
                            Eliminar
                        </button>
                    </div>
                </div>

        `;

        if (tarea.avances && tarea.avances.length > 0) {
            html += `
                <ol class="relative border-l border-gray-200 ml-2">
            `;
            
            tarea.avances.forEach((avance, avanceIndex) => {
                const avanceColor = avanceIndex % 3 === 0 ? colorClaro : 
                                  avanceIndex % 3 === 1 ? colorMedio : colorOscuro;
                
                html += `
                    <li class="mb-4 ml-6 group">
                        <div class="absolute w-3 h-3 ${avanceColor} rounded-full mt-1.5 -left-1.5 border border-white"></div>
                        <div class="flex justify-between items-start">
                            <p class="text-base font-normal text-gray-800 mt-1 flex-1">${avance.avance}</p>

                            <button onclick="confirmDelete(${avance.id})" class="px-3 py-1 text-white bg-red-500 rounded-lg hover:bg-red-600 cursor-pointer">
                                Eliminar
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Actualizado: 
                            ${new Date(avance.updatedAt).getFullYear()}-${String(new Date(avance.updatedAt).getMonth() + 1).padStart(2, '0')}-${String(new Date(avance.updatedAt).getDate()).padStart(2, '0')} ${String(new Date(avance.updatedAt).getHours()).padStart(2, '0')}:${String(new Date(avance.updatedAt).getMinutes()).padStart(2, '0')}
                        </p>
                    </li>
                `;
            });
            
            html += `
                </ol>
            `;
        } else {
            html += `
                <div class="ml-6 p-4 text-sm text-gray-500 italic">
                    No hay avances registrados para esta tarea
                </div>
            `;
        }

        html += `
            </div>
        `;
    });

    html += `
        </div>
    `;

    contenedor.innerHTML = html;
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
                            if (evt.from !== evt.to) {
                                evt.from.insertBefore(evt.item, evt.from.children[evt.oldIndex]);
                            }
                            document.getElementById(newTareaTipoId).innerHTML += data.data;
                            evt.item.remove();
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

// Selecciona el contenedor que tiene scroll horizontal
const container = document.getElementById('scrollContainer');

// Inicializa SortableJS en el contenedor
new Sortable(container, {
  animation: 150,
  ghostClass: 'sortable-ghost',
  // Habilita soporte para dispositivos táctiles (activado por defecto)
  touchStartThreshold: 5,
  // Scroll automático cuando el ítem llega al borde
  scroll: true,
  scrollSensitivity: 30, // Qué tan cerca del borde empieza a hacer scroll
  scrollSpeed: 15,       // Velocidad del scroll
  // Activamos scroll personalizado horizontal
  setScroll: function (scrollContainer, direction) {
    if (direction === 'x') {
      scrollContainer.scrollLeft += scrollContainer.scrollSpeed;
    }
  }
});

