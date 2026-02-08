let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function cambiarEstado(itemId, estadoActual) {
    let selectedEstado = estadoActual;

    Swal.fire({
        title: '⚠️ Cambiar Estado',
        text: "Selecciona un nuevo estado para el proyecto.",
        icon: 'warning',
        input: 'select',
        inputOptions: window.estadosProyecto,
        inputValue: estadoActual,
        showCancelButton: true,
        confirmButtonText: 'Guardar',
        cancelButtonText: 'Cancelar',
        didOpen: () => {
            const swalContainer = Swal.getPopup();

            const label = document.createElement('label');
            label.textContent = 'Ingrese fecha de entrega del Proyecto';
            label.className = 'swal2-label mt-2 text-center';
            label.style.display = 'none';

            const fechaInput = document.createElement('input');
            fechaInput.type = 'date';
            fechaInput.id = 'fechaDua';
            fechaInput.className = 'swal2-input mt-1';
            fechaInput.style.display = 'none';

            swalContainer.appendChild(label);
            swalContainer.appendChild(fechaInput);

            const select = swalContainer.querySelector('select');
            select.addEventListener('change', (e) => {
                selectedEstado = parseInt(e.target.value);
                const mostrar = selectedEstado === 3;
                label.style.display = mostrar ? 'block' : 'none';
                fechaInput.style.display = mostrar ? 'block' : 'none';
            });
        },
        preConfirm: async () => {
            const fechaDua = document.getElementById('fechaDua')?.value;

            if (selectedEstado === 3 && !fechaDua) {
                Swal.showValidationMessage('Debes ingresar una fecha de entrega.');
                return false;
            }

            // 🔥 NUEVA VALIDACIÓN: Confirmación adicional para estado 6
            if (selectedEstado === 6) {
                const confirmDelete = await Swal.fire({
                    title: '¿Eliminar firma del contrato?',
                    text: "Este proceso eliminará la firma asociada al proyecto. ¿Seguro que desea continuar?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, borrar firma',
                    cancelButtonText: 'Cancelar'
                });

                if (!confirmDelete.isConfirmed) {
                    return false; // Evita enviar
                }
            }

            return fetch(`editStatus/${itemId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    estado: selectedEstado,
                    fecha_dua: selectedEstado === 3 ? fechaDua : null
                })
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

document.addEventListener('DOMContentLoaded', function() {
    // Elementos del DOM
    const conFechaFin = document.getElementById('checkboxFecha');
    const fechaFinContainer = document.getElementById('fechaFinBegin');
    const diasTrabajoContainer = document.getElementById('diasTrabajoBegin');
    const fechaFinInput = document.getElementById('fec_fin_estimado_b');
    const diasTrabajoInput = document.getElementById('dias_trabajo_begin');
    const conFechaFinInput = document.getElementById('conFechaFin_b');

    const conFechaDiseInput = document.getElementById('conFechaDise');
    const checkboxFechaDise = document.getElementById('checkboxFechaDise');
    const fechaDiseDiv = document.getElementById('fechaDiseDiv');
    const fechaDiseInput = document.getElementById('fec_ini_dise');

    // Función para alternar visibilidad
    function toggleFields() {
        if (conFechaFin.checked) {
            conFechaFinInput.value = 1;
            fechaFinContainer.classList.remove('hidden');
            diasTrabajoContainer.classList.add('hidden');
            fechaFinInput.required = true;
            diasTrabajoInput.required = false;
            if(diasTrabajoInput.value < '1'){
                diasTrabajoInput.value = '1';
            }
        } else {
            conFechaFinInput.value = 0;
            fechaFinContainer.classList.add('hidden');
            diasTrabajoContainer.classList.remove('hidden');
            fechaFinInput.required = false;
            diasTrabajoInput.required = true;
        }
    }

    function toggleFields2() {
        if (checkboxFechaDise.checked) {
            conFechaDiseInput.value = 1;
            fechaDiseDiv.classList.remove('hidden');
            fechaDiseInput.required = true;
            fechaDiseInput.disabled = false;
        } else {
            conFechaDiseInput.value = 0;
            fechaDiseDiv.classList.add('hidden');
            fechaDiseInput.required = false;
            fechaDiseInput.disabled = true;
        }
    }

    // Event listener para el checkbox
    conFechaFin.addEventListener('change', toggleFields);
    checkboxFechaDise.addEventListener('change', toggleFields2);


    // Inicializar el estado
    toggleFields();
    toggleFields2();

    // Inicializar el datepicker de Flowbite
    if (typeof window.Datepicker !== 'undefined') {
        new Datepicker(fechaFinInput, {
            format: 'yyyy-mm-dd',
            autohide: true
        });
        new Datepicker(fechaDiseInput, {
            format: 'yyyy-mm-dd',
            autohide: true
        });
    }
});

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
                <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">${formatCurrency(service.valor) }</td>
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
                <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">${formatCurrency(service.valor_unidad )}</td>
                <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">${formatCurrency(service.subtotal)}</td>
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
            totalCotizacion.textContent = `Total : ${formatCurrency(total)  }`;
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

async function listaDespachos(id) {
    try {
        const response = await fetch(`listaDespachos/?id=${id}`, {
            method: "GET",
            headers: {
                "X-CSRF-TOKEN": csrfToken,
                "Content-Type": "application/json"
            }
        });
        const data = await response.json();
        await renderDespachos(data.data, id);
        
    } catch (error) {
        Swal.fire("Error", "No se pudo consultar la data.", "error");
    }
}

document.getElementById('botonDescarga').addEventListener('click', function() {
    const id = this.getAttribute('data-id');
    const url = `pdfDespachos?id=${id}`;
    window.open(url, '_blank');
});

async function renderDespachos(despachos, id) {

    const container = document.getElementById('listaDespachos');
    container.innerHTML= '';   

    const boton = document.getElementById('botonDescarga');

    // Manejo cuando no hay despachos
    if (!despachos || despachos.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8 col-span-1 md:col-span-2">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-2 text-lg font-medium text-gray-900">No hay despachos registrados</h3>
                <p class="mt-1 text-gray-500">No se encontraron despachos para mostrar.</p>
            </div>
        `;
        boton.removeAttribute('data-id');
        boton.classList.add('hidden');
        return;
    }
    boton.setAttribute('data-id', id);
    boton.classList.remove('hidden');
    
    let total_fact = 0;
    despachos.forEach(despacho => {
        const card = document.createElement('div');
        card.className = 'w-full max-w-full';
        
        card.innerHTML = `
            <div class="bg-white border border-gray-200 rounded-lg shadow p-2">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h2 class="text-lg font-semibold">Código: ${despacho.codigo}</h2>
                        <p class="text-gray-500 text-sm">${formatDate(despacho.createdAt)}</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="pdfDespacho?codigo=${despacho.codigo}&id=${id}" class="mt-2 tooltip">
                            <svg class="w-8 h-8" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 15v2a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-2m-8 1V4m0 12-4-4m4 4 4-4"></path>
                            </svg>
                            <span class="tooltiptext">Descarga Despacho</span>
                        </a>
                        <div>
                            <p class="text-sm font-semibold">${despacho.nombre_completo}</p>
                            <div class="estado-container">${despacho.spanEstado}</div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-1">
                    <h3 class="font-medium mb-2">Materiales:</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2">Material</th>
                                    <th class="px-3 py-2">Cantidad</th>
                                    <th class="px-3 py-2">Valor Unitario</th>
                                    <th class="px-3 py-2">Se Cobra</th>
                                    <th class="px-3 py-2">Tipo</th>
                                </tr>
                            </thead>
                            <tbody id="items-${despacho.codigo}">
                                <!-- Items se insertarán aquí -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            `;
        
        container.appendChild(card);
        
        // Renderizar items
        const tbody = document.getElementById(`items-${despacho.codigo}`);
        let suma = 0;
        despacho.items.forEach(item => {
            const row = document.createElement('tr');
            row.className = 'bg-white border-b';
            row.innerHTML = `
                <td class="px-3 py-2">${item.nombre_material}</td>
                <td class="px-3 py-2">${item.cantidad}</td>
                <td class="px-3 py-2">$ ${item.valor_unidad.toLocaleString()}</td>
                <td class="px-3 py-2 tipo-container">${item.isCobro}</td>
                <td class="px-3 py-2 tipo-container">${item.spanTipo}</td>
            `;
            suma += item.cantidad * item.valor_unidad;
            tbody.appendChild(row);
        });
        total_fact += suma;
        const row = document.createElement('tr');
        row.className = 'bg-white border-b font-bold text-md';
        row.innerHTML = `
            <td class="px-3 py-2 text-red-500">Total: </td>
            <td colspan="4" class="px-3 py-2">$ ${suma.toLocaleString()}</td>
        `;
        tbody.appendChild(row);
    });

    const txtotalFacturado = document.getElementById('totalFacturado');
    const htmlfacturado = `
        <div class="bg-white border border-gray-200 rounded-lg shadow p-3">
            <h2 class="text-red-500 text-lg font-semibold">Total Facturado en Despachos: <span class="text-black">$ ${total_fact.toLocaleString()}</span></h2>
        </div>
    `;
    txtotalFacturado.innerHTML = htmlfacturado;
}

document.querySelectorAll('[data-accordion-target]').forEach(button => {
    const targetId = button.getAttribute('data-accordion-target');
    const target = document.querySelector(targetId);
    const icon = button.querySelector('[data-accordion-icon]');
    
    button.addEventListener('click', () => {
        // Alternar visibilidad del contenido
        target.classList.toggle('hidden');
        
        // Alternar atributo aria-expanded
        const isExpanded = button.getAttribute('aria-expanded') === 'true';
        button.setAttribute('aria-expanded', !isExpanded);
        
        // Rotar el ícono
        icon.classList.toggle('rotate-180');
    });
});

async function listComparativo(id) {
    try {
        const response = await fetch(`listComparativo/?id=${id}`, {
            method: "GET",
            headers: {
                "X-CSRF-TOKEN": csrfToken,
                "Content-Type": "application/json"
            }
        });
        const data = await response.json();

        if (data.data.length === 0) {
            window.dataGrafica = '';
            document.getElementById('listaComparativoEmpy').classList.remove('hidden'); 
            document.getElementById('listaComparativo').classList.add('hidden'); 
        } else {
            window.dataGrafica = data.data;
            tableComparativo(data.data);
            renderGraficaComparativa(data.data, 1);
            document.getElementById('listaComparativoEmpy').classList.add('hidden'); 
            document.getElementById('listaComparativo').classList.remove('hidden'); 
        }

    } catch (error) {
        Swal.fire("Error", "No se pudo consultar la data.", "error");
    }
}

function tableComparativo(data) {
    const serviceList = document.getElementById('tableComparativo');
    const noDataMessage = document.getElementById('noDataMessageComparativo');

    serviceList.innerHTML = '';
    let total = 0;
    if (data && data.length > 0) {
        data.forEach(service => {
            total += service.subtotal;
            const fila = document.createElement("tr");
            fila.innerHTML = `
                <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">${service.id_material}</td>
                <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">${service.nombre_material.toLowerCase()}</td>
                <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">${service.desp_cantidad}</td>
                <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">${formatCurrency(service.desp_valor)}</td>
                <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">${service.cot_cantidad}</td>
                <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">${formatCurrency(service.cot_valor)}</td>
            `;
            serviceList.appendChild(fila);
        });

        noDataMessage.classList.add('hidden');
    } else {
        noDataMessage.classList.remove('hidden');
    }
}

//Renderizar gráfica
function renderGraficaComparativa(data, tipo) {
    const labels = data.map(item => item.nombre_material);
    const cantidadDespachada = data.map(item => parseFloat(item.desp_cantidad) || 0);
    const cantidadCotizada = data.map(item => parseFloat(item.cot_cantidad) || 0);
    const valorDespachado = data.map(item => parseFloat(item.desp_valor) || 0);
    const valorCotizado = data.map(item => parseFloat(item.cot_valor) || 0);
  
    const datosEntrega = tipo == 1 ? cantidadDespachada : valorDespachado;
    const datosCotizados = tipo == 1 ? cantidadCotizada : valorCotizado;
    const titulo = tipo == 1 ? 'Materiales x Cantidades' : 'Materiales x Valor';
  
    if (chart) chart.destroy();
  
    const ctx = document.getElementById('graficaComparativa').getContext('2d');
    chart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [
          {
            label: 'Material Entregado',
            data: datosEntrega,
            borderColor: 'rgba(250, 128, 114, 2)',
            backgroundColor: 'rgba(250, 128, 114, 0.4)',
            stack: 'combined',
            type: 'bar'
          },
          {
            label: 'Material Cotizado',
            data: datosCotizados,
            borderColor: 'rgba(124, 252, 0, 0.8)',
            backgroundColor: 'rgba(124, 252, 0, 2)',
            stack: 'combined'
          }
        ]
      },
      options: {
        responsive: true,
        devicePixelRatio: 2,
        plugins: {
          title: {
            display: true,
            text: titulo
          }
        },
        scales: {
          y: {
            stacked: false
          }
        }
      }
    });
  
}

let chart;

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('tipoGrafico').addEventListener('change', function () {
      if (window.dataGrafica) {
        renderGraficaComparativa(window.dataGrafica, this.value);
      }
    });
});

document.addEventListener("DOMContentLoaded", () => {
    const btnbeginProyec = document.querySelectorAll(".beginProyec");
    const form = document.getElementById("formBeginProyec");


    btnbeginProyec.forEach(btn => {
        btn.addEventListener("click", () => {
            const proyecto = JSON.parse(btn.getAttribute("data-beginProyec"));
            console.log(proyecto);
            let departamentos = window.departamentos;
            let txCui = departamentos[proyecto.departamento]['departamento'] + ' - ' + departamentos[proyecto.departamento]['ciudades'][proyecto.ciudad];
            document.getElementById("txNombreProyec").textContent = proyecto.nombre_proyecto ?? '';
            document.getElementById("txUbicacion").textContent = txCui;
            document.getElementById("txDireccion").textContent = proyecto.direccion ?? '';
            document.getElementById("txContacto").textContent = proyecto.nombre_cliente ?? '';
            document.getElementById("txDocumento").textContent = 
                (window.tipoDoc?.[proyecto.tipo_doc_cliente] ?? "N/A") + 
                ": " + 
                (proyecto.cedula_cliente ?? "N/A");
            document.getElementById("txTelefono").textContent = proyecto.telefono_cliente ?? '';
            document.getElementById("txAreaPrivada").textContent = proyecto.area_privada ?? '';
            document.getElementById("txDiasProyecto").textContent = proyecto.dias_trabajo;

            document.getElementById("id_user_proy").value = proyecto.id_user;
            document.getElementById("id_user_obra_blanca").value = proyecto.id_user_obra_blanca;
            document.getElementById("id_user_carpinteria").value = proyecto.id_user_carpinteria;

            document.getElementById("dias_trabajo_begin").value = proyecto.dias_trabajo ?? 1;
            document.getElementById("observacion").textContent = proyecto.observacion ?? '';

            document.getElementById("fec_inicio_begin").value = proyecto.fec_inicio?.split('T')[0] ?? '';
            document.getElementById("fec_fin_estimado_b").value = proyecto.fec_fin_estimado?.split('T')[0] ?? '';

            // 🔹 Campos ocultos obligatorios
            document.getElementById("id_proyecto_begin").value = proyecto.id ?? '';
        });
    });

    form.addEventListener("submit", function(e) {
        e.preventDefault();

        Swal.fire({
            title: '¿Iniciar proyecto?',
            text: "Se guardará la información del formulario.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, iniciar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});

async function descargarExcelDespachos(id = '') {
    const btn = event.currentTarget;
    btn.disabled = true;

    Swal.fire({
        title: 'Generando reporte',
        html: `
            <div class="flex flex-col items-center gap-3">
                <svg class="animate-spin h-8 w-8 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10"
                        stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span class="text-sm text-gray-600">Por favor espera…</span>
            </div>
        `,
        allowOutsideClick: false,
        showConfirmButton: false
    });

    try {
        
        const route = id ? `excelDespachoProyecto/${id}` : 'excelDespachosGeneral';
        const response = await fetch(route, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        if (!response.ok) {
            const error = await response.json();
            throw new Error(error.message || 'Error al generar el archivo');
        }

        const blob = await response.blob();

        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = id ? 'excelDespachosProyecto.xlsx' : 'excelDespachosGeneral.xlsx';
        document.body.appendChild(a);
        a.click();
        a.remove();
        window.URL.revokeObjectURL(url);

        Swal.fire({
            icon: 'success',
            title: 'Reporte generado',
            timer: 1500,
            showConfirmButton: false
        });

    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message
        });
    } finally {
        btn.disabled = false;
    }
}