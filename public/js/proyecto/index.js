const departamentos = window.departamentos;
document.getElementById('departamento').addEventListener('change', function() {
    let deptoId = this.value;
    let ciudadSelect = document.getElementById('ciudad');
    ciudadSelect.innerHTML = '<option value="">Seleccione una ciudad</option>';

    if (deptoId !== "") {
        let ciudades = departamentos.find(depto => depto.id == deptoId)?.ciudades || [];
        ciudades.forEach(ciudad => {
            let option = document.createElement('option');
            option.value = ciudad;
            option.textContent = ciudad;
            ciudadSelect.appendChild(option);
        });
    }
});

window.onload = function() {
    let selectedDepartamento = "{{ old('departamento', $usuario->departamento ?? '') }}";
    let selectedCiudad = "{{ old('ciudad', $usuario->ciudad ?? '') }}";

    if (selectedDepartamento) {
        document.getElementById('departamento').value = selectedDepartamento;
        document.getElementById('departamento').dispatchEvent(new Event('change'));

        setTimeout(() => {
            document.getElementById('ciudad').value = selectedCiudad;
        }, 200);
    }
};




async function listPrestamos(page = 1, id) {
    try {
        const response = await fetch(`listPrestamos/?page=${page}&id=${id}`, {
            method: "GET",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
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