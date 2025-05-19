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
        document.getElementById('dias_trabajo').value = data.data.dias_trabajo;
        document.getElementById('fec_inicio').value = data.data.fecIni;
        document.getElementById('id_user').value  = data.data.id_user;
        document.getElementById('id_tarea_tipo').value  = data.data.id_tarea_tipo;
        document.getElementById('id_tarea_estado').value  = data.data.id_tarea_estado;
        document.getElementById('fec_fin').value = data.data.fec_fin;
    })
    .catch(error => {
        Swal.showValidationMessage(`Error: ${error.message}`);
    });
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
                                    <th class="px-4 py-2">Material</th>
                                    <th class="px-4 py-2">Cantidad</th>
                                    <th class="px-4 py-2">Valor Unitario</th>
                                    <th class="px-4 py-2">Tipo</th>
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
        despacho.items.forEach(item => {
            const row = document.createElement('tr');
            row.className = 'bg-white border-b';
            row.innerHTML = `
                <td class="px-4 py-2">${item.nombre_material}</td>
                <td class="px-4 py-2">${item.cantidad}</td>
                <td class="px-4 py-2">$${item.valor_unidad.toLocaleString()}</td>
                <td class="px-4 py-2 tipo-container">${item.spanTipo}</td>
            `;
            tbody.appendChild(row);
        });
    });
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

async function listaBalance(id) {
    try {
        const response = await fetch(`listaBalance/?id=${id}`, {
            method: "GET",
            headers: {
                "X-CSRF-TOKEN": csrfToken,
                "Content-Type": "application/json"
            }
        });
        const data = await response.json();
        await displayBalanceData(data.data);
    } catch (error) {
        Swal.fire("Error", "No se pudo consultar la data.", "error");
    }
}

async function displayBalanceData(balanceData) {
    // Carpintería
    const carpinteria = balanceData.carpinteria;
    document.querySelector('#carpinteria-presupuesto').textContent = `PRESUPUESTO: ${formatCurrency(carpinteria.presupuesto)}`;
    document.querySelector('#carpinteria-gastos-dinero').textContent = `GASTOS: ${formatCurrency(carpinteria.gastosDinero)}`;
    document.querySelector('#carpinteria-disponible-dinero').textContent = `DISPONIBLE: ${formatCurrency(carpinteria.presupuesto - carpinteria.gastosDinero)}`;
    
    document.querySelector('#carpinteria-presupuesto-material').textContent = `PRESUPUESTO: ${formatCurrency(carpinteria.presupuestoMaterial)}`;
    document.querySelector('#carpinteria-gastos-material').textContent = `GASTOS: ${formatCurrency(carpinteria.gastosMaterial)}`;
    document.querySelector('#carpinteria-disponible-material').textContent = `DISPONIBLE: ${formatCurrency(carpinteria.presupuestoMaterial - carpinteria.gastosMaterial)}`;

    // Obra Blanca
    const obrablanca = balanceData.obrablanca;
    document.querySelector('#obrablanca-presupuesto').textContent = `PRESUPUESTO: ${formatCurrency(obrablanca.presupuesto)}`;
    document.querySelector('#obrablanca-gastos-dinero').textContent = `GASTOS: ${formatCurrency(obrablanca.gastosDinero)}`;
    document.querySelector('#obrablanca-disponible-dinero').textContent = `DISPONIBLE: ${formatCurrency(obrablanca.presupuesto - obrablanca.gastosDinero)}`;
    
    document.querySelector('#obrablanca-presupuesto-material').textContent = `PRESUPUESTO: ${formatCurrency(obrablanca.presupuestoMaterial)}`;
    document.querySelector('#obrablanca-gastos-material').textContent = `GASTOS: ${formatCurrency(obrablanca.gastosMaterial)}`;
    document.querySelector('#obrablanca-disponible-material').textContent = `DISPONIBLE: ${formatCurrency(obrablanca.presupuestoMaterial - obrablanca.gastosMaterial)}`;

    // Otros
    const otros = balanceData.otros;
    document.querySelector('#otros-presupuesto').textContent = `PRESUPUESTO: ${formatCurrency(otros.presupuesto)}`;
    document.querySelector('#otros-gastos').textContent = `GASTOS PAGOS: ${formatCurrency(otros.gastosDinero)}`;
    document.querySelector('#otros-gastos-material').textContent = `GASTOS MATERIAL: ${formatCurrency(otros.gastosMaterial)}`;
    document.querySelector('#otros-disponible').textContent = `DISPONIBLE: ${formatCurrency(otros.presupuesto - otros.gastosDinero - otros.gastosMaterial)}`;

    // Global
    const global = balanceData.global;
    document.querySelector('#global-presupuesto').textContent = `PRESUPUESTO TOTAL: ${formatCurrency(global.presupuesto)}`;
    document.querySelector('#global-abonos').textContent = `ABONOS TOTALES: ${formatCurrency(global.abonos)}`;
    document.querySelector('#global-gastos').textContent = `GASTOS TOTALES: ${formatCurrency(global.gastos)}`;
    document.querySelector('#global-rentabilidad').textContent = `RENTABILIDAD: ${formatCurrency(global.ganancia)}`;
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