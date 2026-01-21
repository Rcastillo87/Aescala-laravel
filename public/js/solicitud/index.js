function cargarItemsSolicitud(id) {
    Swal.fire({
        title: 'Cargando...',
        html: 'Obteniendo información',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });
    const tbody = document.getElementById("tbodyListaItems");
    tbody.innerHTML = `
        <tr>
            <td colspan="6" class="py-4 text-center text-gray-500">Cargando...</td>
        </tr>
    `;
    fetch(`listaSolicitud/${id}`)
        .then(resp => resp.json())
        .then(data => {
            Swal.close();
            if (!data.status || !data.data) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="py-4 text-center text-red-600 font-semibold">
                            No se pudo obtener la información
                        </td>
                    </tr>
                `;
                return;
            }
            const items = data.data;
            if (items.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="py-4 text-center text-gray-500">
                            No hay registros para esta solicitud
                        </td>
                    </tr>
                `;
                return;
            }
            let html = "";
            items.forEach(item => {

                let bnt = item.codigo ? 
                `<div class="flex text-center justify-center">
                    <a tabindex="0" href="pdfDespacho?codigo=${item.codigo ?? ''}&id=${item.id_proyecto ?? ''}"
                            class="tooltip flex items-center justify-center w-10 h-10 text-white bg-fuchsia-600 hover:bg-white hover:text-fuchsia-500 border-2 border-fuchsia-500 focus:ring-4 
                            focus:outline-none focus:ring-fuchsia-300 font-medium rounded-full text-sm dark:bg-fuchsia-400 dark:hover:bg-fuchsia-500 dark:focus:ring-fuchsia-500 cursor-pointer">
                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 15v2a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-2m-8 1V4m0 12-4-4m4 4 4-4"></path>
                        </svg>
                        <span class="tooltiptext">Descarga PDf del Despacho</span>
                    </a>
                </div>` : '';

                html += `
                    <tr>
                        <td class="border px-3 py-2">${item.nombre_material}</td>
                        <td class="border px-3 py-2">${item.cantidad_sol}</td>
                        <td class="border px-3 py-2">${item.span_estado}</td>
                        <td class="border px-3 py-2">${item.usuario_aprueba??''}</td>
                        <td class="border px-3 py-2">${item.fecha_aprobacion??''}</td>
                        <td class="border px-3 py-2">${item.cantidad_des ?? ''}</td>
                        <td class="border px-3 py-2">${item.createdAt ?? ''}</td>
                        <td class="border px-3 py-2">${item.isCobro ?? ''}</td>
                        <td class="border px-3 py-2">${item.codigo ?? ''}</td>
                        <td class="border px-3 py-2">
                            ${ bnt }
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        })
        .catch(err => {
            Swal.close();
            console.error(err);
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="py-4 text-center text-red-600 font-semibold">
                        No se pudo obtener la información
                    </td>
                </tr>
            `;
        });
}

function confirmDelete(el) {
    Swal.fire({
        title: '¿Eliminar Solicitud?',
        text: '¿Desea eliminar la Solicitud de Material?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            el.closest('form').submit();
        }
    });
}
