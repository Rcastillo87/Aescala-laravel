async function mostrarPagos(id, nombreProyecto, pazSalvo) {
    try {
        Swal.fire({
            title: 'Cargando pagos...',
            text: 'Por favor espera',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        document.getElementById('tipo').value = 1;
        document.getElementById('proyecto_id').value = id;
        const response = await fetch(`pagos/${id}`);
        const result = await response.json();
        Swal.close();

        if (!result.status) {
            Swal.fire('Error', result.message, 'error');
            return;
        }

        const data = result.data.pagado;
        const conceptos = result.data.conceptos_pago;
        const totalApagar = result.data.total_apagar;

        if(pazSalvo == 1){
            document.getElementById('formPago').classList.add('hidden');
        } else {
            document.getElementById('formPago').classList.remove('hidden');
        }

        document.getElementById('txTitulo').textContent = 'Pagos del Proyecto: ' + nombreProyecto;
        const select = document.getElementById('concepto');
        select.innerHTML = '<option value="">-- Seleccione --</option>';
        conceptos.forEach(item => {
            const option = document.createElement('option');
            option.value = item.termino; // valor interno
            option.textContent = item.msg;
            //option.dataset.valor = item.valor_apagar; // dato extra opcional
            select.appendChild(option);
        });

        const contenedor = document.getElementById('tablaPagos');
        const tablaHTML = `
        <table class="w-full text-sm text-gray-700">
            <thead class="bg-green-700 text-white text-center uppercase">
                <tr>
                    <th class="px-2 py-2">Recibo De Caja</th>
                    <th class="px-2 py-2">Factura de Venta</th>
                    <th class="px-2 py-2">Concepto</th>
                    <th class="px-2 py-2">Valor Pagado</th>
                    <th class="px-2 py-2">Fecha de Pago</th>
                    <th class="px-2 py-2">Comentario</th>
                    <th class="px-2 py-2">Opciones</th>
                </tr>
            </thead>
            <tbody id="bodyTablaPagos" class="divide-y divide-gray-100 text-center"></tbody>
        </table>
        `;
        contenedor.innerHTML = tablaHTML;

        const tbody = document.getElementById('bodyTablaPagos');
        tbody.innerHTML = '';
        let totalPagado = 0;

        if (!data || data.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="py-4 text-gray-500 text-center italic">
                        No hay pagos registrados.
                    </td>
                </tr>`;
        } else {
            data.forEach(pago => {
                totalPagado += Number(pago.valor_pagado);
                const getConcepto = conceptos.find(c => c.termino === Number(pago.concepto));
                const fila = `
                    <tr class="hover:bg-gray-50 transition-all">
                        <th class="px-2 py-1 text-center">${pago.rc}</th>
                        <th class="px-2 py-1 text-center">${pago.fv??''}</th>
                        <th class="px-2 py-1 text-center">${getConcepto.msg}</th>
                        <td class="px-2 py-1 text-center">$${pago.valor_pagado.toLocaleString()}</td>
                        <td class="px-2 py-1 text-center">$${pago.fecha_pago}</td>
                        <td class="px-2 py-1 text-center w-50">${pago.comentario || '-'}</td>
                        <td class="px-2 py-1 text-center">
                            <a onclick="deletePago(${pago.id})"
                                class="inline-flex items-center justify-center w-9 h-9 rounded-full border-2 bg-red-600 hover:bg-red-700 border-red-700 focus:ring-red-300 text-white transition-all focus:ring-2 focus:ring-offset-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </a>
                        </td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', fila);
            });
        }

        // Calcular saldo pendiente
        const saldoPendiente = totalApagar - totalPagado;

        // Fila de totales
        const filaTotales = `
            <tr class="bg-gray-100 font-semibold border-t">
                <td colspan="2" class="py-3 px-4 text-right text-gray-700">Total A Pagar: $${totalApagar.toLocaleString()}</td>
                <td colspan="2" class="py-3 px-4 text-right text-gray-700">Total Pagado: $${totalPagado.toLocaleString()}</td>
                <td colspan="3" class="py-3 px-4 text-right">
                    <span class="text-gray-600">Saldo pendiente:</span>
                    <span class="font-bold text-red-600 ml-2">$${saldoPendiente.toLocaleString()}</span>
                </td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', filaTotales);

        // Mostrar el modal
        window.dispatchEvent(new CustomEvent('open-modal', { detail: 'modalPagos-modal' }));

    } catch (error) {
        Swal.close();
        Swal.fire('Error', 'No se pudo obtener la información de los pagos.', 'error');
        console.error(error);
    }
}

async function mostrarPagosOtroSi(id, nombreOtroSi, pazSalvo) {
    try {
        Swal.fire({
            title: 'Cargando pagos...',
            text: 'Por favor espera',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        document.getElementById('tipo').value = 2;
        document.getElementById('proyecto_id').value = id;
        const response = await fetch(`pagosOtroSi/${id}`);
        const result = await response.json();
        Swal.close();

        if (!result.status) {
            Swal.fire('Error', result.message, 'error');
            return;
        }

        const data = result.data.pagado;
        const totalDeve = Number(result.data.totalDeve);
        const totalPago = result.data.totalPago;

        if(pazSalvo == 1){
            document.getElementById('formPago').classList.add('hidden');
        } else {
            document.getElementById('formPago').classList.remove('hidden');
        }

        document.getElementById('txTitulo').textContent = `Pagos de ${nombreOtroSi}`;
        const divselect = document.getElementById('id_concepto');
        divselect.classList.add('hidden');
        const select = document.getElementById('concepto');
        select.disabled = true;

        const contenedor = document.getElementById('tablaPagos');
        const tablaHTML = `
        <table class="w-full text-sm text-gray-700">
            <thead class="bg-green-700 text-white text-center uppercase">
                <tr>
                    <th class="px-2 py-2">Recibo De Caja</th>
                    <th class="px-2 py-2">Factura de Venta</th>
                    <th class="px-2 py-2">Valor Pagado</th>
                    <th class="px-2 py-2">Fecha de Pago</th>
                    <th class="px-2 py-2">Comentario</th>
                    <th class="px-2 py-2">Opciones</th>
                </tr>
            </thead>
            <tbody id="bodyTablaPagos" class="divide-y divide-gray-100 text-center"></tbody>
        </table>
        `;
        contenedor.innerHTML = tablaHTML;

        const tbody = document.getElementById('bodyTablaPagos');
        tbody.innerHTML = '';

        if (!data || data.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="py-4 text-gray-500 text-center italic">
                        No hay pagos registrados.
                    </td>
                </tr>`;
        } else {
            data.forEach(pago => {
                const fila = `
                    <tr class="hover:bg-gray-50 transition-all">
                        <th class="px-2 py-1 text-center">${pago.rc}</th>
                        <th class="px-2 py-1 text-center">${pago.fv??''}</th>
                        <td class="px-2 py-1 text-center">$${pago.valor_pagado.toLocaleString()}</td>
                        <td class="px-2 py-1 text-center">$${pago.fecha_pago}</td>
                        <td class="px-2 py-1 text-center w-50">${pago.comentario || '-'}</td>
                        <td class="px-2 py-1 text-center">
                            <a onclick="deletePago(${pago.id})"
                                class="inline-flex items-center justify-center w-9 h-9 rounded-full border-2 bg-red-600 hover:bg-red-700 border-red-700 focus:ring-red-300 text-white transition-all focus:ring-2 focus:ring-offset-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </a>
                        </td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', fila);
            });
        }

        // Calcular saldo pendiente
        const saldoPendiente =  totalDeve - totalPago;

        // Fila de totales
        const filaTotales = `
            <tr class="bg-gray-100 font-semibold border-t">
                <td colspan="2" class="py-3 px-4 text-right text-gray-700">Total A Pagar: $${totalDeve.toLocaleString()}</td>
                <td colspan="2" class="py-3 px-4 text-right text-gray-700">Total Pagado: $${totalPago.toLocaleString()}</td>
                <td colspan="2" class="py-3 px-4 text-right">
                    <span class="text-gray-600">Saldo pendiente:</span>
                    <span class="font-bold text-red-600 ml-2">$${saldoPendiente.toLocaleString()}</span>
                </td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', filaTotales);

        // Mostrar el modal
        window.dispatchEvent(new CustomEvent('open-modal', { detail: 'modalPagos-modal' }));

    } catch (error) {
        Swal.close();
        Swal.fire('Error', 'No se pudo obtener la información de los pagos.', 'error');
        console.error(error);
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

