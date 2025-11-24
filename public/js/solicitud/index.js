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
                html += `
                    <tr>
                        <td class="border px-3 py-2">${item.nombre_material}</td>
                        <td class="border px-3 py-2">${item.cantidad_sol}</td>
                        <td class="border px-3 py-2">${item.span_estado}</td>
                        <td class="border px-3 py-2">${item.cantidad_des ?? ''}</td>
                        <td class="border px-3 py-2">${item.createdAt ?? ''}</td>
                        <td class="border px-3 py-2">${item.codigo ?? ''}</td>
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
