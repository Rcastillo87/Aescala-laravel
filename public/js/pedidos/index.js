let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

async function openPedidos(page = 1, id) {
    try {
        const response = await fetch(`listPedido?page=${page}&id=${id}`, {
            method: "GET",
            headers: {
                "X-CSRF-TOKEN": csrfToken,
                "Content-Type": "application/json"
            }
        });
        const data = await response.json();
        await tableFactura(data.data);
    } catch (error) {
        Swal.fire("Error", "No se pudo consultar la data.", "error");
    }
}

async function tableFactura(data) {
    const serviceList = document.getElementById('facturaList');
    const pagination = document.getElementById('paginationFactura');
    const noDataMessage = document.getElementById('noDataMessageFactura');

    serviceList.innerHTML = '';
    pagination.innerHTML = '';

    if (data && data.data && data.data.length > 0) {
        data.data.forEach(service => {
            const fila = document.createElement("tr");
            fila.innerHTML = `
                <td class="py-2 text-center">${service.id}</td>
                <td class="py-2 text-center">${service.nombre_material}</td>
                <td class="py-2 text-center">${service.cantidad}</td>
                <td class="py-2 text-center">${formatCurrency(service.valor_unidad)}</td>
                <td class="py-2 text-center">${service.fecha}</td>
            `;
            serviceList.appendChild(fila);
        });

        const paginationLinks = data.links.map(link => {
            if (link.url) {
                const page = new URL(link.url).searchParams.get('page') || 1;
                return `<a href="#" onclick="openPedidos(${page}, ${data.data[0].id})" class="px-4 py-2 mx-1 text-blue-500 rounded-lg">${link.label}</a>`;
            }
            return `<span class="px-4 py-2 mx-1 text-blue-500 rounded-lg">${link.label}</span>`;
        }).join('');
        pagination.innerHTML = paginationLinks;

        noDataMessage.classList.add('hidden');
    } else {
        noDataMessage.classList.remove('hidden');
    }
}
