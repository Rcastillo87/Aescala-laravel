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


 async function loadCartera(id) {
    try {

        // =========================
        // LOADING SWEETALERT
        // =========================
        Swal.fire({
            title: 'Cargando...',
            text: 'Obteniendo información de la cartera',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const res = await fetch(`pagosProyecto/${id}`);
        const data = await res.json();

        Swal.close(); // 🔥 cerrar loading cuando responde

        const div = document.getElementById('divCartera');
        div.innerHTML = '';

        // =========================
        // TABLA PAGOS
        // =========================
        let pagosHTML = `
        <div class="mb-6">
            <h2 class="text-lg font-semibold mb-2">Lista de Pagos realizados</h2>

            <div class="overflow-x-auto rounded-xl border shadow bg-white">
                <table class="w-full text-sm text-center">
                    <thead class="bg-green-700 text-white uppercase text-xs">
                        <tr>
                            <th class="p-2">Concepto</th>
                            <th class="p-2">Valor</th>
                            <th class="p-2">Factura</th>
                            <th class="p-2">Fecha</th>
                            <th class="p-2">Comentario</th>
                            <th class="p-2">Opciones</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        if (data.pagos.length) {
            data.pagos.forEach(p => {
                pagosHTML += `
                <tr class="border-t hover:bg-gray-50">
                    <td class="p-2">${p.concepto}</td>
                    <td class="p-2">$${formatMoney(p.valor_pago)}</td>
                    <td class="p-2">${p.factura}</td>
                    <td class="p-2">${p.fecha_pago}</td>
                    <td class="p-2">${p.comentarios ?? '--'}</td>
                    <td class="p-2">
                        <button onclick="deletePago(${p.id})"
                            class="bg-red-600 hover:bg-red-700 text-white rounded-full w-8 h-8">
                            ✕
                        </button>
                    </td>
                </tr>`;
            });

            pagosHTML += `
                <tr class="bg-gray-100 font-semibold">
                    <td colspan="6" class="p-3 text-right">
                        Total Pagado:
                        <span class="text-red-600">$${formatMoney(data.total_pagos)}</span>
                    </td>
                </tr>`;
        } else {
            pagosHTML += `
                <tr>
                    <td colspan="6" class="p-3">No hay registros</td>
                </tr>`;
        }

        pagosHTML += `</tbody></table></div></div>`;

        // =========================
        // RESUMEN
        // =========================
        let resumenHTML = `
        <div class="grid md:grid-cols-2 gap-4">

            <div class="rounded-xl border shadow bg-white p-4">
                <h2 class="font-semibold mb-3">Resumen Total</h2>
                <table class="w-full text-sm text-center">
                    <thead class="bg-green-700 text-white text-xs">
                        <tr>
                            <th class="p-2">Concepto</th>
                            <th class="p-2">Valor</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        data.resumen.items.forEach(r => {
            resumenHTML += `
                <tr class="border-t">
                    <td class="p-2">${r.concepto}</td>
                    <td class="p-2">$${formatMoney(r.valor_total)}</td>
                </tr>`;
        });

        resumenHTML += `
                <tr class="bg-gray-100 font-semibold">
                    <td>Total Proyecto</td>
                    <td>$${formatMoney(data.resumen.total_proyecto)}</td>
                </tr>
                <tr class="bg-gray-100 font-semibold">
                    <td>Total Pagado</td>
                    <td>$${formatMoney(data.resumen.total_pagado)}</td>
                </tr>
                <tr class="bg-gray-100 font-semibold text-red-600">
                    <td>Saldo</td>
                    <td>$${formatMoney(data.resumen.saldo)}</td>
                </tr>
            </tbody></table></div>
        `;

        // =========================
        // RELACIÓN DE PAGOS
        // =========================
        let relacionHTML = `
        <div class="rounded-xl border shadow bg-white p-4 overflow-x-auto">
            <h2 class="font-semibold mb-3">Relación de pagos</h2>
            <table class="w-full text-sm text-center">
                <thead class="bg-green-700 text-white text-xs">
                    <tr>
                        <th>Concepto</th>
        `;

        data.porcentajes.forEach(p => {
            relacionHTML += `<th>${p}%</th>`;
        });

        relacionHTML += `</tr></thead><tbody>`;

        relacionHTML += `<tr class="border-t">
            <td>Contrato</td>`;
        data.relacion_pagos.contrato.forEach(v => {
            relacionHTML += `<td>$${formatMoney(v)}</td>`;
        });
        relacionHTML += `</tr>`;

        relacionHTML += `<tr class="border-t font-semibold">
            <td>Pagado</td>`;
        data.relacion_pagos.totales.forEach(v => {
            relacionHTML += `<td>$${formatMoney(v)}</td>`;
        });
        relacionHTML += `</tr>`;

        relacionHTML += `</tbody></table></div></div>`;

        div.innerHTML = pagosHTML + resumenHTML + relacionHTML;

    } catch (e) {

        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo cargar la cartera'
        });

        console.error(e);
    }
}

// =========================
// FORMATO DINERO
// =========================
function formatMoney(value) {
    return new Intl.NumberFormat('es-CO').format(value);
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

