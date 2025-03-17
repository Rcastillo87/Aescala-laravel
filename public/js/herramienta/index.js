async function listPrestamos(page = 1, id) {
    let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    try {
        const response = await fetch(`listPrestamos/?page=${page}&id=${id}`, {
            method: "GET",
            headers: {
                "X-CSRF-TOKEN": csrfToken,
                "Content-Type": "application/json"
            }
        });
        const data = await response.json();
        tablePrestamos(data.data, id);
        document.getElementById('id_herramienta').value = id;
        const info = data.last??null;
        if (info) {
            window.lastPrestamo = info;
        } else {
            window.lastPrestamo = null;
        }
    } catch (error) {
        Swal.fire("Error", "No se pudo consultar la data.", "error");
    }
}

function tablePrestamos(data, id) {
    const serviceList = document.getElementById('serviceList');
    const pagination = document.getElementById('pagination');
    const noDataMessage = document.getElementById('noDataMessage');

    serviceList.innerHTML = '';
    pagination.innerHTML = '';

    if (data.data && data.data.length > 0) {
        data.data.forEach(service => {
            const fila = document.createElement("tr");
            fila.innerHTML = `
                <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">${service.spanPrestamo}</td>
                <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">${service.user?.nombre_completo.toLowerCase() || 'N/A'}</td>
                <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">${service.observacion}</td>
                <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">${formatFecha(service.createdAt)}</td>
            `;
            serviceList.appendChild(fila);
        });

        const paginationLinks = data.links.map(link => {
            if (link.url) {
                const page = new URL(link.url).searchParams.get('page') || 1;
                return `<a href="#" onclick="listPrestamos(${page}, ${id})" class="px-4 py-2 mx-1 text-blue-500 rounded-lg">${link.label}</a>`;
            }
            return `<span class="px-4 py-2 mx-1 text-blue-500 rounded-lg">${link.label}</span>`;
        }).join('');
        pagination.innerHTML = paginationLinks;

        noDataMessage.classList.add('hidden');
    } else {
        noDataMessage.classList.remove('hidden');
    }
}

document.getElementById("contacts-styled-tab").addEventListener("click", function () {
    setTimeout(() => {
        const contactsTab = document.getElementById("styled-contacts");

        if (!contactsTab || contactsTab.classList.contains("hidden")) {
            console.warn("El formulario aún no está visible.");
            return;
        }
        const idUserSelect = document.querySelector('[name="id_user"]');
        const tipoPrestamoSelect = document.querySelector('[name="tipo_prestamo"]');
        const info = window.lastPrestamo;

        if (info) {
            const idUserOptionExists = [...idUserSelect.options].some(opt => opt.value == info.id_user);
            const tipoPrestamoOptionExists = [...tipoPrestamoSelect.options].some(opt => opt.value == (info.tipo_prestamo == 1 ? 2 : 1));
            if (idUserOptionExists) {
                idUserSelect.value = info.id_user;
                idUserSelect.dispatchEvent(new Event('change'));
            }
            if (tipoPrestamoOptionExists) {
                tipoPrestamoSelect.value = (info.tipo_prestamo == 1) ? 2 : (info.tipo_prestamo == 2 ? 1 : '');
                tipoPrestamoSelect.dispatchEvent(new Event('change'));
            }
        } else {
            console.warn("No se encontró la información o los selects no están en el DOM.");
        }
    }, 500);
});