async function mostrarPagos(id, nombreProyecto) {
    try {
        Swal.fire({
            title: 'Cargando pagos...',
            text: 'Por favor espera',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        const response = await fetch(`pagos/${id}`);
        const result = await response.json();
        Swal.close();

        if (!result.status) {
            Swal.fire('Error', result.message, 'error');
            return;
        }

        const data = result.data;
        document.getElementById('txTitulo').textContent = 'Pagos del Proyecto: ' + nombreProyecto;

        const tbody = document.getElementById('tablaPagos');
        tbody.innerHTML = '';

        // Inicializamos acumuladores
        let totalApagar = 0;
        let totalPagado = 0;

        if (!data || data.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="py-5 text-gray-500 text-center italic">
                        No hay pagos registrados.
                    </td>
                </tr>`;
        } else {
            data.forEach(pago => {
                const pagoRealizado = pago.pago === true;

                totalApagar += pago.valor_apagar ?? 0;
                totalPagado += pago.valor_pagado ?? 0;

                const clickHandler = pagoRealizado
                    ? ''
                    : `onclick="abrirModalPago('${pago.id_proyecto}', '${pago.termino}', '${pago.msg}', ${pago.valor_apagar}, '${nombreProyecto}')"`;


                const icono = pagoRealizado
                    ? `<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                       </svg>`
                    : `<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                       </svg>`;

                const colorBtn = pagoRealizado
                    ? 'bg-green-600 hover:bg-green-700 border-green-700 focus:ring-green-300 cursor-not-allowed'
                    : 'bg-red-600 hover:bg-red-700 border-red-700 focus:ring-red-300 ';

                const textoBtn = pagoRealizado ? 'Pagado' : 'Pendiente';

                const fila = `
                    <tr class="hover:bg-gray-50 transition-all">
                        <td class="px-4 py-3 text-left">${pago.msg}</td>
                        <td class="px-4 py-3 text-center">${pago.porcentaje}%</td>
                        <td class="px-4 py-3 text-right">$${pago.valor_apagar.toLocaleString()}</td>
                        <td class="px-4 py-3 text-right">$${pago.valor_pagado.toLocaleString()}</td>
                        <td class="px-4 py-3 text-center">${pago.fecha_pago || '-'}</td>
                        <td class="px-4 py-3 text-center">
                            <a ${clickHandler} 
                               class="inline-flex items-center justify-center w-9 h-9 rounded-full border-2 ${colorBtn} text-white transition-all focus:ring-2 focus:ring-offset-1 ">
                                ${icono}
                            </a>
                            <div class="text-xs text-gray-600 mt-1">${textoBtn}</div>
                        </td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', fila);
            });

            // Calcular saldo pendiente
            const saldoPendiente = totalApagar - totalPagado;

            // Fila de totales
            const filaTotales = `
                <tr class="bg-gray-100 font-semibold border-t">
                    <td colspan="2" class="py-3 px-4 text-right text-gray-700">Totales:</td>
                    <td class="py-3 px-4 text-right text-gray-700">$${totalApagar.toLocaleString()}</td>
                    <td class="py-3 px-4 text-right text-gray-700">$${totalPagado.toLocaleString()}</td>
                    <td colspan="2" class="py-3 px-4 text-right">
                        <span class="text-sm text-gray-600">Saldo pendiente:</span>
                        <span class="font-bold text-red-600 ml-2">$${saldoPendiente.toLocaleString()}</span>
                    </td>
                </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', filaTotales);
        }

        // Mostrar el modal
        window.dispatchEvent(new CustomEvent('open-modal', { detail: 'modalPagos-modal' }));

    } catch (error) {
        Swal.close();
        Swal.fire('Error', 'No se pudo obtener la información de los pagos.', 'error');
        console.error(error);
    }
}

function abrirModalPago(idProyecto, campoDesc, mensaje, valorApagar, nombreProyecto) {
    // Cierra el modal principal
    window.dispatchEvent(new CustomEvent('close-modal', { detail: 'modalPagos-modal' }));

    // Llena los datos del formulario
    document.getElementById('proyecto_id').value = idProyecto;
    document.getElementById('campo_desc').value = campoDesc;
    document.getElementById('tipo').value = 1; // tipo de pago

    document.getElementById('tituloFormPago').textContent = `Registrar Pago del Proyecto ${nombreProyecto}`;
    document.getElementById('tituloDescripccion').textContent = `Concepto del pago - ${mensaje}`;
    document.getElementById('valorApagarLabel').textContent = `$${valorApagar.toLocaleString()}`;

    // Abre el modal de formulario
    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'modalFormPago-modal' }));
}

document.getElementById('formPago').addEventListener('submit', async function (e) {
    e.preventDefault();

    const formData = new FormData(this);

    try {
        Swal.fire({
            title: 'Guardando pago...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        const response = await fetch(this.action, {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        Swal.close();

        if (result.status) {
            Swal.fire('Éxito', result.message, 'success');
            window.location.reload();
            // Cierra el modal
            //window.dispatchEvent(new CustomEvent('close-modal', { detail: 'modalFormPago-modal' }));
            // Opcional: recarga lista de pagos
            //mostrarPagos(formData.get('proyecto_id'), ''); 
        } else {
            Swal.fire('Error', result.message, 'error');
        }
    } catch (error) {
        Swal.close();
        Swal.fire('Error', 'No se pudo guardar el pago.', 'error');
        console.error(error);
    }
});

