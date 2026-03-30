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
                        <p class="text-gray-500 text-sm">${despacho.createdAt}</p>
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
            document.getElementById("ubicacion").value = proyecto.ubicacion ?? '';

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

/**
 * calendario-proyecto.js
 * Modal de calendario por proyecto — formato mensual.
 */

// ─── Fix TomSelect: proteger inicialización ────────────────────────────
// Evita el error "Cannot read properties of null (reading 'tomselect')"
// cuando el elemento no existe en la página actual.
document.addEventListener('DOMContentLoaded', function () {
    if (typeof TomSelect !== 'undefined') {
        const origTomSelect = TomSelect;
        window.TomSelect = function (selector, options) {
            const el = typeof selector === 'string'
                ? document.querySelector(selector)
                : selector;
            if (!el) return;
            return new origTomSelect(el, options);
        };
        Object.assign(window.TomSelect, origTomSelect);
        window.TomSelect.prototype = origTomSelect.prototype;
    }
});

// ─── Estado ────────────────────────────────────────────────────────────
let _cpIdProy    = null;
let _cpModalOpen = false;

// ─── Abrir modal ───────────────────────────────────────────────────────
function abrirCalendarioProy(idProyecto) {
    _cpIdProy    = idProyecto;
    _cpModalOpen = true;

    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'calendario-modal' }));
    _cpMostrarLoader(true);
    _cpLimpiarContenido();

    fetch(`calendarioProyecto?id_proyecto=${idProyecto}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => _cpRenderizar(data))
    .catch(() => {
        Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudieron cargar los datos del proyecto.' });
        _cpCerrarModal();
    })
    .finally(() => _cpMostrarLoader(false));
}

// ─── Cerrar modal ──────────────────────────────────────────────────────
function _cpCerrarModal() {
    window.dispatchEvent(new CustomEvent('close-modal', { detail: 'calendario-modal' }));
    _cpModalOpen = false;
    _cpIdProy    = null;
}

// ─── Sincronizar estado cuando Alpine cierra con Esc ──────────────────
window.addEventListener('close-modal', (e) => {
    if (e.detail !== 'calendario-modal') return;
    _cpModalOpen = false;
    _cpIdProy    = null;
});

// ─── Renderizar completo ───────────────────────────────────────────────
function _cpRenderizar(data) {
    document.getElementById('cp-nombre-proy').textContent  = data.nombre;
    document.getElementById('cp-cliente-proy').textContent = data.cliente ? `Cliente: ${data.cliente}` : '';
    document.getElementById('cp-fec-inicio').textContent   = _cpFechaLegible(data.fec_inicio);
    document.getElementById('cp-fec-fin-est').textContent  = _cpFechaLegible(data.fec_fin_est);
    document.getElementById('cp-dias-trabajo').textContent = data.dias_trabajo;

    document.getElementById('cp-leyenda').classList.remove('hidden');

    const contenedor = document.getElementById('cp-meses-contenedor');
    contenedor.innerHTML = '';

    // Conteo global para resumen
    const conteoGlobal = { laborable: 0, sabado: 0, domingo: 0, festivo: 0, no_laboral_global: 0, no_laborado: 0 };

    data.meses.forEach(mes => {
        conteoGlobal.laborable       += mes.dias.filter(d => d.tipo === 'laborable').length;
        conteoGlobal.sabado          += mes.dias.filter(d => d.tipo === 'sabado').length;
        conteoGlobal.domingo         += mes.dias.filter(d => d.tipo === 'domingo').length;
        conteoGlobal.festivo         += mes.dias.filter(d => d.tipo === 'festivo').length;
        conteoGlobal.no_laboral_global += mes.dias.filter(d => d.tipo === 'no_laboral_global').length;
        conteoGlobal.no_laborado     += mes.dias.filter(d => d.tipo === 'no_laborado').length;

        contenedor.appendChild(_cpRenderMes(mes));
    });

    _cpActualizarResumen(conteoGlobal);
    document.getElementById('cp-resumen').classList.remove('hidden');
}

// ─── Renderizar un mes ─────────────────────────────────────────────────
function _cpRenderMes(mes) {
    const wrapper = document.createElement('div');
    wrapper.className = 'cp-mes-wrapper';

    // Cabecera del mes
    const header = document.createElement('div');
    header.className   = 'cp-mes-header';
    header.textContent = mes.nombre;
    wrapper.appendChild(header);

    // Cabecera días semana
    const diasSemana = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];
    const headerGrid = document.createElement('div');
    headerGrid.className = 'cp-semana-header';
    diasSemana.forEach(d => {
        const span = document.createElement('div');
        span.className   = 'cp-header-day';
        span.textContent = d;
        headerGrid.appendChild(span);
    });
    wrapper.appendChild(headerGrid);

    // Grilla
    const grid = document.createElement('div');
    grid.className = 'cp-grid';

    // Celdas vacías de offset
    for (let i = 0; i < mes.offset; i++) {
        const empty = document.createElement('div');
        empty.className = 'cp-day cp-empty';
        grid.appendChild(empty);
    }

    mes.dias.forEach(d => {
        grid.appendChild(_cpCrearCelda(d));
    });

    wrapper.appendChild(grid);
    return wrapper;
}

// ─── Crear celda de día ────────────────────────────────────────────────
function _cpCrearCelda(d) {
    const cell = document.createElement('div');

    // Clases base: tipo + estado
    cell.className = `cp-day cp-tipo-${d.tipo} cp-estado-${d.estado}`;

    // Número
    const num = document.createElement('span');
    num.className   = 'cp-day-num';
    num.textContent = d.day;
    cell.appendChild(num);

    // Nombre del día
    const nombre = document.createElement('span');
    nombre.className   = 'cp-day-name';
    nombre.textContent = d.dayName;
    cell.appendChild(nombre);

    // Etiqueta descriptiva
    const etiqueta = d.noLaboradoDetalle || d.festivoName || null;
    if (etiqueta) {
        const lbl = document.createElement('span');
        lbl.className   = 'cp-day-label';
        lbl.textContent = etiqueta;
        cell.appendChild(lbl);
    }

    // Botón marcar no laborado (pasado o hoy, editable, no ya marcado)
    if (d.editable && !d.noLaborado) {
        const btn = document.createElement('button');
        btn.type        = 'button';
        btn.className   = 'cp-btn-marcar';
        btn.textContent = '＋ No laborado';
        btn.onclick     = (e) => {
            e.stopPropagation();
            _cpConfirmarNoLaborado(d.date, d.dayName);
        };
        cell.appendChild(btn);
    }

    return cell;
}

// ─── Resumen pie ───────────────────────────────────────────────────────
function _cpActualizarResumen(c) {
    const sabMedia = c.sabado > 0 ? (c.sabado / 2) : 0;
    document.getElementById('cp-res-laborable').textContent     = c.laborable + (sabMedia > 0 ? ` + ${sabMedia}` : '');
    document.getElementById('cp-res-sabado').textContent        = c.sabado;
    document.getElementById('cp-res-domingo').textContent       = c.domingo;
    document.getElementById('cp-res-festivo').textContent       = c.festivo;
    document.getElementById('cp-res-no-lab-global').textContent = c.no_laboral_global;
    document.getElementById('cp-res-no-laborado').textContent   = c.no_laborado;

    document.getElementById('cp-row-no-lab-global').classList.toggle('hidden', c.no_laboral_global === 0);
    document.getElementById('cp-row-no-laborado').classList.toggle('hidden', c.no_laborado === 0);
}

// ─── Confirmar y guardar día no laborado ──────────────────────────────
function _cpConfirmarNoLaborado(fecha, nombreDia) {
    Swal.fire({
        title: 'Día no laborado',
        html: `
            <p style="margin-bottom:10px;font-size:.9rem;color:#4b5563;">
                <b>${_cpFechaLegible(fecha)}</b> (${nombreDia})<br>
                <span style="font-size:.8rem;color:#6b7280;">¿Por qué no se trabajó este día?</span>
            </p>
            <textarea
                id="cp-swal-detalle"
                maxlength="500"
                autocomplete="off"
                placeholder="Describe el motivo (obligatorio)"
                style="width:100%;min-height:110px;padding:10px 12px;border:1px solid #d1d5db;
                       border-radius:8px;font-size:.9rem;resize:vertical;outline:none;
                       box-sizing:border-box;font-family:inherit;transition:border-color .15s;"
                onfocus="this.style.borderColor='#d97706'"
                onblur="this.style.borderColor='#d1d5db'"
            ></textarea>
            <p style="text-align:right;font-size:.72rem;color:#9ca3af;margin-top:4px;">
                <span id="cp-char-count">0</span>/500
            </p>
            <script>
                document.getElementById('cp-swal-detalle')
                    .addEventListener('input', function() {
                        document.getElementById('cp-char-count').textContent = this.value.length;
                    });
            <\/script>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Guardar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#d97706',
        cancelButtonColor: '#9ca3af',
        focusConfirm: false,
        preConfirm: () => {
            const detalle = document.getElementById('cp-swal-detalle').value.trim();
            if (!detalle) {
                Swal.showValidationMessage('El motivo es obligatorio.');
                return false;
            }
            return detalle;
        },
    }).then(result => {
        if (!result.isConfirmed) return;

        Swal.fire({ title: 'Guardando...', allowOutsideClick: false, allowEscapeKey: false, didOpen: () => Swal.showLoading() });

        fetch('saveDiaNoLaborado', {
            method: 'POST',
            headers: {
                'Content-Type':     'application/json',
                'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ id_proyecto: _cpIdProy, dia: fecha, detalle: result.value }),
        })
        .then(async r => {
            const json = await r.json();
            if (!r.ok) throw new Error(json.error || 'Error al guardar.');
            return json;
        })
        .then(json => {
            Swal.fire({
                icon: 'success',
                title: '¡Guardado!',
                html: `Día registrado.<br><small>Nueva fecha fin estimada: <b>${_cpFechaLegible(json.nueva_fecha_fin)}</b></small>`,
                timer: 2500,
                showConfirmButton: false,
            }).then(() => window.location.reload());
        })
        .catch(err => Swal.fire({ icon: 'error', title: 'Error', text: err.message }));
    });
}

// ─── Helpers ───────────────────────────────────────────────────────────
function _cpMostrarLoader(show) {
    const el = document.getElementById('cp-loading');
    if (el) el.style.display = show ? 'flex' : 'none';
}

function _cpLimpiarContenido() {
    const c = document.getElementById('cp-meses-contenedor');
    if (c) c.innerHTML = '';
    document.getElementById('cp-leyenda')?.classList.add('hidden');
    document.getElementById('cp-resumen')?.classList.add('hidden');
}

const _MESES_CP = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio',
                    'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];

function _cpFechaLegible(dateStr) {
    if (!dateStr) return '—';
    const [y, m, d] = dateStr.split('-').map(Number);
    return `${d} de ${_MESES_CP[m]} de ${y}`;
}
