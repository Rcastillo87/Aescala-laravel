function cambiarEstado(userId, estadoActual) {
    let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    Swal.fire({
        title: "¿Estás seguro?",
        text: (estadoActual==1) ? "El Insumo será Desactivado" : "El Insumo será Activado",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, cambiar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`editStatus/${userId}`, {
                method: "get",
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    "Content-Type": "application/json"
                }
            })
            .then(response => response.json())
            .then(data => {
                Swal.fire("¡Éxito!", "Estado actualizado.", "success");
                location.reload();
            })
            .catch(error => {
                Swal.fire("Error", "No se pudo cambiar el estado.", "error");
            });
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('formEntregaInsumo');
    const inputCantidad = document.getElementById('cantidad');
    const selectInsumo = document.getElementById('id_insumo');

    let maxGlobal = 0;

    // 🔹 CAPTURAR STOCK
    selectInsumo.addEventListener('change', function () {
        const option = this.options[this.selectedIndex];
        let datax = option.getAttribute('data-datax');

        if (!datax) return;

        datax = JSON.parse(datax);

        if (datax.cantidad == 0) {
            inputCantidad.value = '';
            inputCantidad.disabled = true;

            Swal.fire({
                icon: 'warning',
                title: 'Sin stock',
                text: 'Este insumo no tiene disponibilidad'
            });

            return;
        }

        maxGlobal = parseInt(datax.cantidad);

        inputCantidad.disabled = false;
        inputCantidad.classList.remove('bg-gray-200');
        inputCantidad.min = 1;
        inputCantidad.max = maxGlobal;
        inputCantidad.value = 1;
    });

    // 🔒 BLOQUEAR EXCESOS
    inputCantidad.addEventListener('input', function () {
        let val = parseInt(this.value);

        if (isNaN(val)) return;

        if (val < 1) this.value = 1;
        if (val > maxGlobal) this.value = maxGlobal;
    });

    // 🚀 ENVÍO CON SWAL
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        limpiarErrores();
        const cantidad = parseInt(inputCantidad.value);
        const max = parseInt(inputCantidad.max);

        // 🔴 VALIDACIÓN FRONT
        if (!cantidad || cantidad < 1 || cantidad > max) {
            mostrarErrores({
                cantidad: ['La cantidad debe estar entre 1 y ' + max]
            });
            return;
        }

        // 🔄 LOADING
        Swal.fire({
            title: 'Procesando...',
            text: 'Enviando información',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            }
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) {
                throw { status: res.status, data };
            }
            return data;
        })
        .then(response => {
            Swal.close();
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: response.message || 'Entrega registrada correctamente'
            }).then(() => {
                location.reload();
            });

        })
        .catch(err => {
            Swal.close();
            if (err.status === 422) {
                mostrarErrores(err.data.errors);

                // 👇 SCROLL AL PRIMER ERROR
                const firstError = document.querySelector('.border-red-500');
                if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });

                return;
            }
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: err.data?.message || 'Error inesperado'
            });
        });
    });

    // 🔁 RESET AL CERRAR MODAL (Alpine)
    document.addEventListener('close-modal', function (e) {
        if (e.detail === 'modalEntregaInsumo-modal') {
            form.reset();

            inputCantidad.value = '';
            inputCantidad.disabled = true;
            inputCantidad.classList.add('bg-gray-200');

            maxGlobal = 0;
        }
    });

});

async function listPrestamos(page = 1, id) {
    let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    window.dispatchEvent(new CustomEvent('open-modal', {
        detail: 'modalHistoryInsumo-modal'
    }));
    await new Promise(resolve => setTimeout(resolve, 100));
    try {
        Swal.fire({
            title: 'Procesando...',
            text: 'Solicitando información',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        const response = await fetch(`historyInsumo?page=${page}&id=${id}`, {
            method: "GET",
            headers: {
                "X-CSRF-TOKEN": csrfToken,
                "Content-Type": "application/json"
            }
        });
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        const json = await response.json();
        tablePrestamos(json.data, id);
        Swal.close();
    } catch (error) {
        Swal.close();
        console.error('listPrestamos error:', error);
        Swal.fire("Error", "No se pudo consultar la data.", "error");
    }
}

function tablePrestamos(paginado, id) {
    const serviceList   = document.getElementById('serviceList');
    const pagination    = document.getElementById('pagination');
    const noDataMessage = document.getElementById('noDataMessage');

    serviceList.innerHTML = '';
    pagination.innerHTML  = '';

    // paginado.data = array de items
    const items = paginado.data;

    if (items && items.length > 0) {
        items.forEach(service => {
            const fila = document.createElement("tr");
            fila.innerHTML = `
                <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                    ${service.insumo.nombre_insumo}
                </td>
                <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                    ${service.area_empresa.nombre_area}
                </td>
                <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                    ${service.cantidad}
                </td>
                <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                    ${formatFecha(service.created_at)}
                </td>
            `;
            serviceList.appendChild(fila);
        });

        // ✅ Paginador robusto — sin usar new URL() que puede explotar
        const paginationLinks = paginado.links.map(link => {
            if (link.url) {
                // ✅ Extraer page del query string manualmente
                const urlParams = new URLSearchParams(link.url.split('?')[1] ?? '');
                const pageNum   = urlParams.get('page') || 1;
                // ✅ Limpiar el label (puede traer &laquo; etc)
                const label     = link.label.replace(/&laquo;|&raquo;/g, '').trim();
                const activeClass = link.active
                    ? 'bg-blue-500 text-white'
                    : 'text-blue-500 hover:bg-blue-100';
                return `<a href="#"
                    onclick="event.preventDefault(); listPrestamos(${pageNum}, ${id})"
                    class="px-4 py-2 mx-1 rounded-lg ${activeClass}">
                    ${label}
                </a>`;
            }
            // Links deshabilitados (Anterior/Siguiente sin url)
            const label = link.label.replace(/&laquo;|&raquo;/g, '').trim();
            return `<span class="px-4 py-2 mx-1 text-gray-400 rounded-lg cursor-not-allowed">
                ${label}
            </span>`;
        }).join('');

        pagination.innerHTML  = paginationLinks;
        noDataMessage.classList.add('hidden');

    } else {
        noDataMessage.classList.remove('hidden');
    }
}
