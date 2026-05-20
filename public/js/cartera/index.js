document.addEventListener('DOMContentLoaded', async () => {
    const id = document.getElementById('id_proyecto').value;
    await loadCartera(id);
});

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

        const res = await fetch(`/cartera/pagosProyecto/${id}`);
        const response = await res.json();

        Swal.close();

        if (!response.status) {
            throw new Error(response.message || 'Error al consultar');
        }

        const data = response.data;
        const div = document.getElementById('divCartera');

        div.innerHTML = '';

        /*
        =====================================
        TABLA DE PAGOS REALIZADOS
        =====================================
        */

        let pagosHTML = `
            <div class="my-4">
                <h2 class="text-lg font-semibold mb-2">
                    Lista de Pagos Realizados
                </h2>

                <div class="overflow-x-auto mb-2 rounded-xl border border-gray-200 shadow-md bg-white">
                    <table class="w-full text-sm text-center mb-2">
                        <thead class="bg-green-700 text-white uppercase text-xs">
                            <tr>
                                <th class="p-3">Valor Pago</th>
                                <th class="p-3">Fecha</th>
                                <th class="p-3">Comentario</th>
                                <th class="p-3">RC</th>
                                <th class="p-3">Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
        `;

        if (data.pagos.length > 0) {
            data.pagos.forEach(item => {
                const tieneSoporte = item.soporte;

                const btnRecivo =
                    `<a data-tooltip-target="tooltip-hover-recivo-${item.id}" data-tooltip-trigger="hover" href="${item.urlRecivo}" target="_blank" class="flex items-center justify-center w-10 h-10 text-white bg-green-700 hover:bg-white hover:text-green-800 border-2 border-green-800 focus:ring-4
                            focus:outline-none focus:ring-green-300 font-medium rounded-full text-sm dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.556 8.5h8m-8 3.5H12m7.111-7H4.89a.896.896 0 0 0-.629.256.868.868 0 0 0-.26.619v9.25c0 .232.094.455.26.619A.896.896 0 0 0 4.89 16H9l3 4 3-4h4.111a.896.896 0 0 0 .629-.256.868.868 0 0 0 .26-.619v-9.25a.868.868 0 0 0-.26-.619.896.896 0 0 0-.63-.256Z"/>
                        </svg>
                    </a>
                    <div id="tooltip-hover-recivo-${item.id}" role="tooltip" class="absolute z-10 inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs tooltip dark:bg-gray-700 opacity-0 invisible" style="position: absolute; inset: auto auto 0px 0px; margin: 0px; transform: translate(849.333px, -113.333px);" data-popper-escaped="" data-popper-placement="top">
                        PDF Recibo
                        <div class="tooltip-arrow" data-popper-arrow="" style="position: absolute; left: 0px; transform: translate(54.6667px, 0px);"></div>
                    </div>`;

                pagosHTML += `
                    <tr class="border-t hover:bg-gray-50">
                        <td class="p-3">${formatCurrency(item.valor_pago)}</td>
                        <td class="p-3">${item.fecha_pago ?? '--'}</td>
                        <td class="p-3">${item.comentarios ?? '--'}</td>
                        <td class="p-3">${item.rc ?? '--'}</td>
                        <td class="p-3">
                            <div class=" flex items-center justify-center space-x-2">
                                <a data-tooltip-target="tooltip-hover-delete-${item.id}" data-tooltip-trigger="hover" onclick="deletePago(${item.id})" class="flex items-center justify-center w-10 h-10 text-white bg-red-700 hover:bg-white hover:text-red-800 border-2 border-red-800 focus:ring-4
                                        focus:outline-none focus:ring-red-300 font-medium rounded-full text-sm dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
                                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/>
                                    </svg>
                                </a>
                                <div id="tooltip-hover-delete-${item.id}" role="tooltip" class="absolute z-10 inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs tooltip dark:bg-gray-700 opacity-0 invisible" style="position: absolute; inset: auto auto 0px 0px; margin: 0px; transform: translate(849.333px, -113.333px);" data-popper-escaped="" data-popper-placement="top">
                                    Eliminar Pago
                                    <div class="tooltip-arrow" data-popper-arrow="" style="position: absolute; left: 0px; transform: translate(54.6667px, 0px);"></div>
                                </div>
                                ${ item.urlRecivo !== '' ? btnRecivo : '' }

                                <button
                                    @click="
                                        const tabla = document.getElementById('id_tabla');
                                        if (tabla) {
                                            tabla.value = 'App\\\\Models\\\\Pagos';
                                        }
                                        $dispatch('documentoModal', {
                                            id_pago: ${item.id},
                                            tiene_archivo: ${tieneSoporte ? 'true' : 'false'},
                                            url_ver: '${item.url_soporte ?? ''}',
                                            nombre_archivo: '${tieneSoporte?.nombre ?? ''}'
                                        })"
                                    data-tooltip-target="tooltip-hover-doc-${item.id}"
                                    data-tooltip-trigger="hover"
                                    class="flex items-center justify-center w-10 h-10 text-white ${tieneSoporte ? 'bg-blue-600 border-blue-800' : 'bg-gray-500 border-gray-700'}
                                    hover:bg-white hover:text-blue-800 border-2 focus:ring-4 focus:outline-none font-medium rounded-full text-sm transition-colors">

                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13">
                                        </path>
                                    </svg>
                                </button>
                                <div id="tooltip-hover-doc-${item.id}" role="tooltip" class="absolute z-10 inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs tooltip dark:bg-gray-700 opacity-0 invisible" style="position: absolute; inset: auto auto 0px 0px; margin: 0px; transform: translate(849.333px, -113.333px);" data-popper-escaped="" data-popper-placement="top">
                                    Comprobante de Pago
                                    <div class="tooltip-arrow" data-popper-arrow="" style="position: absolute; left: 0px; transform: translate(54.6667px, 0px);"></div>
                                </div>

                                <button
                                    @click="
                                        document.getElementById('id_pago_rc').value = ${item.id}??'';
                                        document.getElementById('rc').value = '${item.rc??''}';
                                        $dispatch('open-modal', 'modalRC')"
                                    data-tooltip-target="tooltip-hover-rc-${item.id}"
                                    data-tooltip-trigger="hover"
                                    class="flex items-center justify-center w-10 h-10 text-white bg-orange-600 border-orange-800
                                    hover:bg-white hover:text-orange-800 border-2 focus:ring-4 focus:outline-none font-medium rounded-full text-sm transition-colors">
                                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6.2V5h11v1.2M8 5v14m-3 0h6m2-6.8V11h8v1.2M17 11v8m-1.5 0h3"/>
                                    </svg>
                                </button>
                                <div id="tooltip-hover-rc-${item.id}" role="tooltip" class="absolute z-10 inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs tooltip dark:bg-gray-700 opacity-0 invisible" style="position: absolute; inset: auto auto 0px 0px; margin: 0px; transform: translate(849.333px, -113.333px);" data-popper-escaped="" data-popper-placement="top">
                                    Ingresar RC
                                    <div class="tooltip-arrow" data-popper-arrow="" style="position: absolute; left: 0px; transform: translate(54.6667px, 0px);"></div>
                                </div>

                            </div>
                        </td>
                    </tr>
                `;
            });

            pagosHTML += `
                <tr class="bg-gray-100 font-semibold">
                    <td colspan="5" class="p-3 text-right">
                        Total Pagado:
                        <span class="text-red-600 ml-2">
                            ${formatCurrency(data.total_pagado)}
                        </span>
                    </td>
                </tr>
            `;
        } else {
            pagosHTML += `
                <tr>
                    <td colspan="5" class="p-4 text-center">
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
            <div class="grid lg:grid-cols-2 gap-2 mb-2">
                <div class="rounded-xl border border-gray-200 shadow-md bg-white p-4">
                    <h2 class="text-lg font-semibold mb-3">
                        Resumen Total
                    </h2>

                    <table class="w-full text-sm text-center">
                        <thead class="bg-green-700 text-white text-xs uppercase">
                            <tr>
                                <th class="p-3">Concepto</th>
                                <th class="p-3">Valor</th>
                                <th class="p-3">Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
        `;

        const soporteFact = data.soporteFact;

        if (data.resumen.length > 0) {
            data.resumen.forEach((item, index) => {
                const btnPZ = `<a data-tooltip-target="tooltip-hover-pz-${item.tipo}-${item.id_pago}" data-tooltip-trigger="hover" href="/cartera/certificadoPZPDF/${item.id_pago}/${item.tipo}" target="_blank"
                        class="flex items-center justify-center w-10 h-10 text-white bg-blue-700 hover:bg-white hover:text-blue-800 border-2 border-blue-800 focus:ring-4
                            focus:outline-none focus:ring-blue-300 font-medium rounded-full text-sm dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 12.5l2 2 4-4m5 1.5V7a2 2 0 0 0-2-2h-3.5L12 3 9.5 5H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h6"
                            />
                        </svg>
                    </a>
                    <div id="tooltip-hover-pz-${item.tipo}-${item.id_pago}" role="tooltip" class="absolute z-10 inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs tooltip dark:bg-gray-700 opacity-0 invisible" style="position: absolute; inset: auto auto 0px 0px; margin: 0px; transform: translate(849.333px, -113.333px);" data-popper-escaped="" data-popper-placement="top">
                        PDF Paz y Salvo
                        <div class="tooltip-arrow" data-popper-arrow="" style="position: absolute; left: 0px; transform: translate(54.6667px, 0px);"></div>
                    </div>`;

                resumenHTML += `
                    <tr class="border-t">
                        <td class="p-3">${item.concepto}</td>
                        <td class="p-3">${formatCurrency(item.valor_total)}</td>
                        <td class="p-1">
                            <div class=" flex items-center justify-center space-x-2">
                                <a data-tooltip-target="tooltip-hover-contratoPdf-${index}" data-tooltip-trigger="hover" href="${item.urlContrato}" target="_blank" class="flex items-center justify-center w-10 h-10 text-white bg-slate-700 hover:bg-white hover:text-slate-800 border-2 border-slate-800 focus:ring-4
                                        focus:outline-none focus:ring-slate-300 font-medium rounded-full text-sm dark:bg-slate-600 dark:hover:bg-slate-700 dark:focus:ring-slate-800">
                                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7h1v12a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1V5a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h11.5M7 14h6m-6 3h6m0-10h.5m-.5 3h.5M7 7h3v3H7V7Z"></path>
                                    </svg>
                                </a>
                                <div id="tooltip-hover-contratoPdf-${index}" role="tooltip" class="absolute z-10 inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs tooltip dark:bg-gray-700 opacity-0 invisible" style="position: absolute; inset: auto auto 0px 0px; margin: 0px; transform: translate(849.333px, -113.333px);" data-popper-escaped="" data-popper-placement="top">
                                    PDF Contrato
                                    <div class="tooltip-arrow" data-popper-arrow="" style="position: absolute; left: 0px; transform: translate(54.6667px, 0px);"></div>
                                </div>
                                ${( (item.tipo == 1) && ((item.total_proyecto - item.total_pagado) <= 0)) ? btnPZ : ''}
                            </div>
                        </td>
                    </tr>
                `;
            });
        }

        const saldoPendiente = (Number(data.total_proyecto) || 0) - (Number(data.total_pagado) || 0);

        const divBotones = document.getElementById('idDivBotones');
        divBotones.innerHTML = '';
        if (saldoPendiente <= 0) {
            divBotones.innerHTML = `
                <div class="flex flex-wrap gap-3">
                    <a
                        href="/cartera/certificadoPZPDF/${id}"
                        target="_blank"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-red-700 text-white hover:bg-red-800 transition-all shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        </svg>
                        Paz y Salvo
                    </a>

                    <button
                        @click="
                            const tabla = document.getElementById('id_tabla');
                            if (tabla) {
                                tabla.value = 'App\\\\Models\\\\Proyecto';
                            }
                            $dispatch('documentoModal', {
                                id_pago: ${id},
                                tiene_archivo: ${soporteFact ? 'true' : 'false'},
                                url_ver: '/cartera/viewDocumento/${soporteFact?.id ?? ''}',
                                nombre_archivo: '${soporteFact?.nombre ?? ''}'
                            });"
                        data-tooltip-target="tooltip-hover-fact-${id}"
                        data-tooltip-trigger="hover"
                        class="${soporteFact ? 'bg-yellow-600 border-yellow-800' : 'bg-gray-500 border-gray-700'}
                        inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-white hover:bg-yellow-800 transition-all shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13">
                            </path>
                        </svg>
                        Factura
                    </button>
                    <div id="tooltip-hover-fact-${id}" role="tooltip" class="absolute z-10 inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs tooltip dark:bg-gray-700 opacity-0 invisible" style="position: absolute; inset: auto auto 0px 0px; margin: 0px; transform: translate(849.333px, -113.333px);" data-popper-escaped="" data-popper-placement="top">
                        Factura
                        <div class="tooltip-arrow" data-popper-arrow="" style="position: absolute; left: 0px; transform: translate(54.6667px, 0px);"></div>
                    </div>

                </div>
            `;
        }

        resumenHTML += `
                </tbody>
            </table>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-5">
                <div class="rounded-xl border border-gray-200 shadow-sm bg-white p-4">
                    <p class="text-sm text-gray-500 font-medium">
                        Total Proyecto
                    </p>
                    <p class="text-xl font-bold text-gray-800 mt-2">
                        ${formatCurrency(data.total_proyecto)}
                    </p>
                </div>
                <div class="rounded-xl border border-gray-200 shadow-sm bg-white p-4">
                    <p class="text-sm text-gray-500 font-medium">
                        Total Pagado
                    </p>
                    <p class="text-xl font-bold text-green-600 mt-2">
                        ${formatCurrency(data.total_pagado)}
                    </p>
                </div>
                <div class="rounded-xl border border-red-200 shadow-sm bg-red-50 p-4">
                    <p class="text-sm text-red-500 font-medium">
                        Saldo Pendiente
                    </p>
                    <p class="text-xl font-bold text-red-600 mt-2">
                        ${formatCurrency(saldoPendiente)}
                    </p>
                </div>
            </div>
        </div>`;

        /*
        =====================================
        Balance del Proyecto
        =====================================
        */

        let relacionHTML = `
            <div class="rounded-xl border border-gray-200 shadow-md bg-white p-4 overflow-x-auto">
                <h2 class="text-lg font-semibold mb-3">
                    Balance del Proyecto
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

        if (Array.isArray(data.valance_pro) && data.valance_pro.length > 0) {
            const cantidad = data.valance_pro.length - 1;
            const txPro = ['Costo del Proyecto', 'Costo del Total', 'Saldo Pendiente'];
            data.valance_pro.forEach((item, index0) => {
                let color = '';
                let tx = '';
                const val = cantidad - index0;
                if (index0 === 0) {
                    tx = txPro[0];
                } else if (val === 1) {
                    color = 'text-green-700 font-bold bg-green-50';
                    tx = txPro[1];
                } else if (val === 0) {
                    color = 'text-red-700 font-bold bg-red-50';
                    tx = txPro[2];
                } else if (val > 1) {
                    tx = 'Costo del Otrosi N ' + index0;
                }

                relacionHTML += `<tr class="border-t ${color}"><td class="p-3">${tx}</td>`;
                item.forEach((valor, index) => {
                    if ( valor >= 0 ) {
                        let valorNumerico = parseFloat(valor) || 0;
                        relacionHTML += `<td class="p-3">${formatCurrency(valorNumerico)}</td>`;
                    } else {
                        relacionHTML += `<td class="p-3">
                            <div class=" flex items-center justify-center space-x-2">
                                <button
                                    @click="
                                        const max = ${Math.abs(valor)};
                                        const referencia = ${index};
                                        const numero = ${index0};
                                        document.getElementById('referencia').value = referencia;
                                        document.getElementById('numero').value = numero;
                                        document.getElementById('txTitlePagoRefe').innerHTML = \`Crear Referencia de Pago del Otrosi N \${referencia}\`;
                                        const input = document.getElementById('valor_referecia');
                                        input.max = max;
                                        input.value = max;
                                        let label = formatCurrency(max);
                                        document.getElementById('txlabelValor').innerHTML =
                                            \`<span class='font-medium text-sm text-gray-400'>
                                                Valor Maximo:
                                            </span> \${label}\`;
                                        \$dispatch('open-modal', 'modalOtroSiRefe');"
                                    data-tooltip-target="tooltip-hover-abono-${index}-${index0}" data-tooltip-trigger="hover"
                                    class="beginProyec flex items-center justify-center w-10 h-10 text-white bg-cyan-600 hover:bg-cyan-800 border-2 border-cyan-800 focus:ring-4
                                      focus:outline-none focus:ring-cyan-300 font-medium rounded-full text-sm">
                                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21a9 9 0 1 1 0-18c1.052 0 2.062.18 3 .512M7 9.577l3.923 3.923 8.5-8.5M17 14v6m-3-3h6"/>
                                    </svg>
                                </button>
                                <div id="tooltip-hover-abono-${index}-${index0}" role="tooltip" class="absolute z-10 inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs tooltip dark:bg-gray-700 opacity-0 invisible" style="inset: auto auto 0px 0px; transform: translate(556.25px, -117.5px); position: absolute; margin: 0px;" data-popper-placement="top">
                                    Defina Pago/Abono Otrosi
                                    <div class="tooltip-arrow" data-popper-arrow="" style="left: 0px; transform: translate(51.25px, 0px); position: absolute;"></div>
                                </div>
                            </div>
                        </td>`;
                    }
                });
                relacionHTML += `</tr>`;
            });
        } else {
            relacionHTML += `
                <tr>
                    <td colspan="7" class="p-4 text-center">
                        No hay data del proyecto
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
        initFlowbite();
    } catch (error) {
        console.error(error);

        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo cargar la información de cartera'
        });
    }
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
            setTimeout(() => {
                window.location = result.url;
            }, 1200);
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

            const response = await fetch(`/cartera/deletePago/${id}`, {
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
                    window.location = resData.url;
                }, 1200);
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

document.getElementById('formOtrosiRefe').addEventListener('submit', async function (e) {
    e.preventDefault();
    limpiarErrores();

    const formData = new FormData(this);

    try {
        Swal.fire({
            title: 'Guardando referencia...',
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
            setTimeout(() => {
                window.location = result.url;
            }, 1200);
        } else {
            mostrarErrores(result.errors ?? {});
            Swal.fire('Error', result.message, 'error');
        }

    } catch (error) {
        Swal.close();
        Swal.fire('Error', 'No se pudo guardar la referencia del pago.', 'error');
        console.error(error);
    }
});
