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

    const proyecto = document.getElementById('id_proyecto');

    // todos los inputs que quieres controlar
    const campos = [
        document.getElementById('concepto'),
        document.getElementById('fecha_pago'),
        document.getElementById('fv'),
        document.getElementById('valor_pagado'),
        document.getElementById('comentario'),
    ];

    function toggleCampos() {
        const tieneProyecto = proyecto.value && proyecto.value !== '';

        if (!tieneProyecto) {
            document.getElementById('divCartera').innerHTML = '';
        } else {
            loadCartera(proyecto.value);
        }

        campos.forEach(campo => {
            if (!campo) return;

            if (!tieneProyecto) {
                campo.setAttribute('disabled', true);
                campo.classList.add('bg-gray-100', 'cursor-not-allowed');
            } else {
                campo.removeAttribute('disabled');
                campo.classList.remove('bg-gray-100', 'cursor-not-allowed');
            }
        });
    }

    // ejecutar al cargar
    toggleCampos();

    // escuchar cambios
    proyecto.addEventListener('change', toggleCampos);

 });


function renderSelectConcepto(data) {
    const selectElement = document.getElementById('concepto');
    if (!selectElement) return;
    selectElement.innerHTML = '<option value="">Seleccione un concepto</option>';

    if (data && Array.isArray(data)) {
        data.forEach(item => {
            const option = document.createElement('option');
            option.value = item.campo ?? '';
            option.textContent = item.msg;
            // Guardamos el tipo de pago en el dataset
            option.dataset.tipoPago = item.tipo_pago ?? '';
            selectElement.appendChild(option);
        });
    }

    selectElement.onchange = function () {
        const selectedOption = this.options[this.selectedIndex];
        const tipoPago = selectedOption ? (selectedOption.dataset.tipoPago || '') : '';
        const inputTipoPago = document.getElementById('tipo');
        if (inputTipoPago) {
            inputTipoPago.value = tipoPago;
        }
    };
}

async function loadCartera(id) {
    try {
        Swal.fire({
            title: 'Cargando...',
            text: 'Obteniendo información de la cartera',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const res = await fetch(`pagosProyecto/${id}`);
        const response = await res.json();

        Swal.close();

        if (!response.status) {
            throw new Error(response.message || 'Error al consultar');
        }

        const data = response.data;
        const div = document.getElementById('divCartera');
        renderSelectConcepto(data.select);
        div.innerHTML = '';

        /*
        =====================================
        TABLA DE PAGOS REALIZADOS
        =====================================
        */

        let pagosHTML = `
            <div class="mb-6">
                <h2 class="text-lg font-semibold mb-3">
                    Lista de Pagos Realizados
                </h2>

                <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-md bg-white">
                    <table class="w-full text-sm text-center">
                        <thead class="bg-green-700 text-white uppercase text-xs">
                            <tr>
                                <th class="p-3">Concepto</th>
                                <th class="p-3">Valor Pago</th>
                                <th class="p-3">Factura</th>
                                <th class="p-3">Fecha</th>
                                <th class="p-3">Comentario</th>
                                <th class="p-3">Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
        `;

        if (data.pagos.length > 0) {
            data.pagos.forEach(item => {
                pagosHTML += `
                    <tr class="border-t hover:bg-gray-50">
                        <td class="p-3">${item.concepto ?? '--'}</td>
                        <td class="p-3">$${formatMoney(item.valor_pago)}</td>
                        <td class="p-3">${item.factura ?? '--'}</td>
                        <td class="p-3">${item.fecha_pago ?? '--'}</td>
                        <td class="p-3">${item.comentarios ?? '--'}</td>
                        <td class="p-3">
                            <button
                                onclick="deletePago(${item.id})"
                                class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-600 hover:bg-red-700 text-white transition"
                            >
                                ✕
                            </button>
                        </td>
                    </tr>
                `;
            });

            pagosHTML += `
                <tr class="bg-gray-100 font-semibold">
                    <td colspan="6" class="p-3 text-right">
                        Total Pagado:
                        <span class="text-red-600 ml-2">
                            $${formatMoney(data.total_pagos)}
                        </span>
                    </td>
                </tr>
            `;
        } else {
            pagosHTML += `
                <tr>
                    <td colspan="6" class="p-4 text-center">
                        No hay registros
                    </td>
                </tr>
            `;
        }

        pagosHTML += `
                        </tbody>
                    </table>
                </div>
            </div>
        `;

        /*
        =====================================
        RESUMEN GENERAL
        =====================================
        */

        let resumenHTML = `
            <div class="grid md:grid-cols-2 gap-5 mb-6">
                <div class="rounded-xl border border-gray-200 shadow-md bg-white p-4">
                    <h2 class="text-lg font-semibold mb-3">
                        Resumen Total
                    </h2>

                    <table class="w-full text-sm text-center">
                        <thead class="bg-green-700 text-white text-xs uppercase">
                            <tr>
                                <th class="p-3">Concepto</th>
                                <th class="p-3">Valor</th>
                            </tr>
                        </thead>
                        <tbody>
        `;

        if (data.resumen.length > 0) {
            data.resumen.forEach(item => {
                resumenHTML += `
                    <tr class="border-t">
                        <td class="p-3">${item.concepto}</td>
                        <td class="p-3">$${formatMoney(item.valor_total)}</td>
                    </tr>
                `;
            });
        }

        const saldoPendiente =
            (Number(data.total_proyecto) || 0) -
            (Number(data.total_pagado) || 0);

        resumenHTML += `
                    <tr class="bg-gray-100 font-semibold">
                        <td class="p-3">Total Proyecto</td>
                        <td class="p-3">$${formatMoney(data.total_proyecto)}</td>
                    </tr>

                    <tr class="bg-gray-100 font-semibold">
                        <td class="p-3">Total Pagado</td>
                        <td class="p-3">$${formatMoney(data.total_pagado)}</td>
                    </tr>

                    <tr class="bg-gray-100 font-semibold text-red-600">
                        <td class="p-3">Saldo Pendiente</td>
                        <td class="p-3">$${formatMoney(saldoPendiente)}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        `;

        /*
        =====================================
        RELACIÓN DE PAGOS
        =====================================
        */

        let relacionHTML = `
            <div class="rounded-xl border border-gray-200 shadow-md bg-white p-4 overflow-x-auto">
                <h2 class="text-lg font-semibold mb-3">
                    Relación de Pagos
                </h2>

                <table class="w-full text-sm text-center">
                    <thead class="bg-green-700 text-white text-xs uppercase">
                        <tr>
                            <th class="p-3">Concepto</th>
        `;

        // encabezados porcentajes contrato
        if (Array.isArray(data.porcentajes)) {
            data.porcentajes.forEach(porcentaje => {
                relacionHTML += `
                    <th class="p-3">
                        ${porcentaje ? porcentaje + '%' : ''}
                    </th>
                `;
            });
        }

        relacionHTML += `
                        </tr>
                    </thead>
                    <tbody>
        `;

        if (Array.isArray(data.relacion_pagos) && data.relacion_pagos.length > 0) {
            data.relacion_pagos.forEach(item => {
                relacionHTML += `<tr class="border-t">`;

                item.forEach((valor, index) => {
                    if (index === 0) {
                        relacionHTML += `
                            <td class="p-3 font-medium">
                                ${valor}
                            </td>
                        `;
                    } else {
                        relacionHTML += `
                            <td class="p-3">
                                $${formatMoney(valor)}
                            </td>
                        `;
                    }
                });

                relacionHTML += `</tr>`;
            });
        } else {
            relacionHTML += `
                <tr>
                    <td colspan="7" class="p-4 text-center">
                        No hay relación de pagos
                    </td>
                </tr>
            `;
        }

        relacionHTML += `
                    </tbody>
                </table>
            </div>
        </div>
        `;

        div.innerHTML = pagosHTML + resumenHTML + relacionHTML;

    } catch (error) {
        console.error(error);

        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo cargar la información de cartera'
        });
    }
}

function formatMoney(value) {
    return new Intl.NumberFormat('es-CO').format(
        Number(value) || 0
    );
}


document.getElementById('formPago').addEventListener('submit', async function (e) {
    e.preventDefault();
    limpiarErrores();

    const formData = new FormData(this);

    try {
        Swal.fire({
            title: 'Guardando pago...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        const response = await fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        const result = await response.json();
        Swal.close();

        if (result.status) {
            Swal.fire('Éxito', result.message, 'success');
            setTimeout(() => window.location.reload(), 1200);
        } else {
            mostrarErrores(result.errors ?? {});
            Swal.fire('Error', result.message, 'error');
        }

    } catch (error) {
        Swal.close();
        Swal.fire('Error', 'No se pudo guardar el pago.', 'error');
        console.error(error);
    }
});

deletePago = async (id) => {
    try {
        const result = await Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción eliminará el pago seleccionado.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        });

        if (result.isConfirmed) {
            Swal.fire({
                title: 'Eliminando pago...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            const response = await fetch(`deleetePago/${id}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const resData = await response.json();
            Swal.close();

            if (resData.status) {
                Swal.fire('Éxito', resData.message, 'success');
                setTimeout(() => window.location.reload(), 1200);
            } else {
                Swal.fire('Error', resData.message, 'error');
            }
        }
    } catch (error) {
        Swal.close();
        Swal.fire('Error', 'No se pudo eliminar el pago.', 'error');
        console.error(error);
    }
}

