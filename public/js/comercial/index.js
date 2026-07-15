function setModalData(element) {
    document.getElementById("sendLink").value = element.dataset.link;
    document.getElementById("id_proyect_link").value = element.dataset.id;
}

// Copiar al portapapeles
function copyLink() {
    const link = document.getElementById("sendLink").value;

    navigator.clipboard.writeText(link).then(() => {
        Swal.fire({
            icon: 'success',
            title: '¡Copiado!',
            text: 'El link se copió al portapapeles.',
            confirmButtonColor: '#3085d6'
        });
    }).catch(err => {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'No se pudo copiar el link.'
        });
    });
}

// Enviar por correo
function sendLinkByEmail() {
    const link = document.getElementById("sendLink").value;
    const email = document.getElementById("emailDestino").value;
    const id = document.getElementById("id_proyect_link").value;

    if (!email) {
        Swal.fire({
            icon: 'warning',
            title: 'Atención',
            text: 'Debes ingresar un correo válido.'
        });
        return;
    }

    Swal.fire({
        title: 'Enviando...',
        text: 'Por favor espera',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch("sendLinkByEmail", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute('content')
        },
        body: JSON.stringify({ email, link, id })
    })
    .then(res => {
        if (!res.ok) throw new Error('Error en servidor');
        return res.json();
    })
    .then(() => {
        Swal.fire({
            icon: 'success',
            title: '¡Enviado!',
            text: 'El link fue enviado al correo.',
            confirmButtonColor: '#3085d6'
        }).then(() => {
            window.dispatchEvent(
                new CustomEvent('close-modal', { detail: 'sendLink-modal' })
            );
        });
    })
    .catch(() => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo enviar el correo.'
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const formEntregable = document.getElementById('formConfigEntre');
    const addItemBtn = document.getElementById('addItem');
    const itemsContainer = document.getElementById('entregable-items');
    const closeBtn = formEntregable.querySelector('[x-on\\:click]'); // botón cerrar modal

    // 🔹 Función para crear un item (con estilos iguales al primero)
    function createItem(number, id = '', descripcion = '') {
        const newItem = document.createElement('div');
        newItem.classList.add('item-group', 'flex', 'items-center', 'gap-4', 'w-full', 'px-3', 'mb-2');
        newItem.dataset.itemNumber = number;

        newItem.innerHTML = `
            <label class="block font-medium text-sm text-gray-700 dark:text-gray-300 item-label whitespace-nowrap">
                Item ${number} *
            </label>

            <input type="hidden" name="item_ids[]" class="item-id" value="${id}">

            <textarea name="items[]" required
                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300
                    focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500
                    dark:focus:ring-indigo-600 rounded-md shadow-sm item-input block w-full">${descripcion}</textarea>

            <a href="#" class="remove-item bg-red-500 text-white px-4 py-2 rounded">
                <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/>
                </svg>
            </a>`;

        return newItem;
    }

    // 🔹 Resetear el modal (form + items)
    function resetModal() {
        formEntregable.reset();
        itemsContainer.innerHTML = "";
        itemsContainer.appendChild(createItem(1));
    }

    // 🔹 Añadir item
    addItemBtn.addEventListener('click', (e) => {
        e.preventDefault();
        const itemCount = itemsContainer.querySelectorAll('.item-group').length;
        itemsContainer.appendChild(createItem(itemCount + 1));
    });

    // 🔹 Borrar item (mínimo 1)
    itemsContainer.addEventListener('click', async (e) => {
        if (e.target.closest('.remove-item')) {
            e.preventDefault();
            const itemGroup = e.target.closest('.item-group');
            const textarea = itemGroup.querySelector('textarea');
            const itemId = itemGroup.querySelector('.item-id').value;
            const itemGroups = itemsContainer.querySelectorAll('.item-group');

            // 1. Validar mínimo 1 ítem
            if (itemGroups.length <= 1) {
                Swal.fire({
                    icon: "warning",
                    title: "Atención",
                    text: "Debe haber al menos 1 item.",
                    confirmButtonColor: "#d33",
                });
                return;
            }

            // 2. Verificar si tiene datos (si tiene ID o si el textarea tiene texto)
            const tieneDatos = itemId !== "" || textarea.value.trim() !== "";

            if (tieneDatos) {
                const confirmacion = await Swal.fire({
                    icon: "question",
                    title: "¿Eliminar ítem?",
                    text: "Este ítem contiene información. ¿Está seguro de eliminarlo?",
                    showCancelButton: true,
                    confirmButtonText: "Sí, eliminar",
                    cancelButtonText: "Cancelar",
                    confirmButtonColor: "#d33"
                });

                if (confirmacion.isConfirmed) {
                    itemGroup.remove();
                }
            } else {
                // Si está vacío, borrar directamente sin preguntar
                itemGroup.remove();
            }
        }
    });

    // 🔹 Resetear modal al cerrar con el botón "Cerrar"
    closeBtn.addEventListener('click', () => {
        resetModal();
    });

    document.getElementById('id_entregable').addEventListener('change', function () {
        const option = this.options[this.selectedIndex];
        const data = option.getAttribute('data-datax');
        if (!data) return;
        try {
            const parsed = JSON.parse(data);
            if (parsed.defaults && parsed.defaults.length > 0) {
                itemsContainer.innerHTML = "";
                parsed.defaults.forEach((def, index) => {
                    itemsContainer.appendChild(
                        createItem(
                            index + 1,
                            def.id,               // id del item
                            def.descripccion      // descripción
                        )
                    );
                });
            }
        } catch (e) {
            console.error("Error al parsear data-datax:", e);
        }
    });

    FormManager.init('#formConfigEntre', {
        autoRedirect: true,
        confirmText: '¿Desea guardar la configuración del entregable?'
    });
});