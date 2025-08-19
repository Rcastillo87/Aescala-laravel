document.addEventListener('DOMContentLoaded', () => {
    const formEntregable = document.getElementById('formEntregable');
    const entregablesDiv = document.getElementById('entregables-div');
    const addItemBtn = document.getElementById('addItem');
    const itemsContainer = document.getElementById('entregable-items');
    const closeBtn = formEntregable.querySelector('[x-on\\:click]'); // botón cerrar modal

    // 🔹 Función para crear un item (con estilos iguales al primero)
    function createItem(number) {
        const newItem = document.createElement('div');
        newItem.classList.add('item-group', 'flex', 'items-center', 'gap-4', 'w-full', 'px-3', 'mb-2');
        newItem.dataset.itemNumber = number;

        newItem.innerHTML = `
            <label class="block font-medium text-sm text-gray-700 dark:text-gray-300 item-label whitespace-nowrap">Item ${number} *</label>
            <input type="text" name="items[]" required class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 
            dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm item-input block w-full"/>
            <a href="#" class="remove-item bg-red-500 text-white px-4 py-2 rounded">
                <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/>
                </svg>
            </a>
        `;
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
    itemsContainer.addEventListener('click', (e) => {
        if (e.target.closest('.remove-item')) {
            e.preventDefault();
            const itemGroups = itemsContainer.querySelectorAll('.item-group');
            if (itemGroups.length > 1) {
                e.target.closest('.item-group').remove();
            } else {
                Swal.fire({
                    icon: "warning",
                    title: "Atención",
                    text: "Debe haber al menos 1 item.",
                    confirmButtonColor: "#d33",
                });
            }
        }
    });

    // 🔹 Validar cantidad (solo enteros positivos)
    document.getElementById('cantidad').addEventListener('input', function () {
        this.value = this.value.replace(/[^0-9]/g, ''); // solo números
        if (this.value !== "" && parseInt(this.value) < 1) this.value = 1;
    });

    // 🔹 Validar valor (solo decimales positivos)
    document.getElementById('valor_total').addEventListener('input', function () {
        this.value = this.value.replace(/[^0-9.]/g, ''); // solo números y punto
        if (this.value !== "" && parseFloat(this.value) < 0) this.value = 0;
    });

    // 🔹 Enviar formulario modal
    formEntregable.addEventListener('submit', (e) => {
        e.preventDefault();

        const idEntregable = document.getElementById('id_entregable').value;
        const nombreEntregable = document.querySelector(`#id_entregable option:checked`)?.textContent.trim();
        const cantidad = document.getElementById('cantidad').value;
        const valor = document.getElementById('valor_total').value;
        const items = Array.from(itemsContainer.querySelectorAll('.item-input')).map(i => i.value);

        // Validar que no esté repetido
        if (entregablesDiv.querySelector(`[data-entregable-id="${idEntregable}"]`)) {
            Swal.fire({
                icon: "error",
                title: "Duplicado",
                text: "Este entregable ya fue agregado.",
                confirmButtonColor: "#d33",
            });
            return;
        }

        const total = (parseInt(cantidad) * parseFloat(valor)).toFixed(2);

        // Crear card
        const entregableCard = document.createElement('div');
        entregableCard.classList.add("bg-white", "border", "border-gray-200", "rounded-lg", "p-4", "mb-3", "shadow-sm");
        entregableCard.dataset.entregableId = idEntregable;

        entregableCard.innerHTML = `
            <div class="flex justify-between items-start mb-2">
                <h3 class="font-bold text-lg text-[#242e68]">${nombreEntregable}</h3>
                <button class="remove-entregable text-red-500 hover:text-red-700">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/>
                    </svg>
                </button>
            </div>
            <div class="grid grid-cols-3 gap-2 mb-2">
                <div class="text-sm"><span class="font-semibold">Cantidad:</span> ${cantidad}</div>
                <div class="text-sm"><span class="font-semibold">Valor Unitario:</span> $${valor}</div>
                <div class="text-sm"><span class="font-semibold">Total:</span> $${total}</div>
            </div>
            <div class="bg-gray-50 p-2 rounded">
                <h4 class="font-medium text-sm mb-1">Items:</h4>
                <ul class="list-disc pl-5 text-sm space-y-1">
                    ${items.map(i => `<li>${i}</li>`).join('')}
                </ul>
            </div>
        `;

        // Agregar inputs hidden
        items.forEach((item) => {
            entregableCard.innerHTML += `<input type="hidden" name="entregables[${idEntregable}][items][]" value="${item}">`;
        });
        entregableCard.innerHTML += `
            <input type="hidden" name="entregables[${idEntregable}][id]" value="${idEntregable}">
            <input type="hidden" name="entregables[${idEntregable}][cantidad]" value="${cantidad}">
            <input type="hidden" name="entregables[${idEntregable}][valor]" value="${valor}">
        `;

        entregablesDiv.appendChild(entregableCard);

        // Confirmación
        Swal.fire({
            icon: "success",
            title: "Agregado",
            text: "El entregable fue añadido correctamente.",
            confirmButtonColor: "#3085d6",
        });

        // Cerrar modal y resetear
        window.dispatchEvent(new CustomEvent('close-modal', { detail: 'entregable-modal' }));
        resetModal();
        actualizarResumen();
    });

    // 🔹 Eliminar entregable desde el form padre
    entregablesDiv.addEventListener('click', (e) => {
        if (e.target.closest('.remove-entregable')) {
            e.preventDefault();
            e.target.closest('[data-entregable-id]').remove();
            Swal.fire({
                icon: "info",
                title: "Eliminado",
                text: "El entregable fue eliminado.",
                confirmButtonColor: "#3085d6",
            });
            actualizarResumen();
        }
    });

    // 🔹 Resetear modal al cerrar con el botón "Cerrar"
    closeBtn.addEventListener('click', () => {
        resetModal();
    });

    //const entregablesDiv = document.getElementById("entregables-div");

    const inputs = [
        "aprov_diseno_por",
        "ini_carpinteria_por",
        "ini_enchape_por",
        "ini_griferia_por",
        "entrega_obra_por",
    ];

    // 🔹 Función para obtener el total de entregables
    function calcularTotalEntregables() {
        let total = 0;
        entregablesDiv.querySelectorAll("[data-entregable-id]").forEach(card => {
            const cantidad = parseInt(card.querySelector(`input[name*='[cantidad]']`)?.value || 0);
            const valor = parseFloat(card.querySelector(`input[name*='[valor]']`)?.value || 0);
            total += cantidad * valor;
        });
        return total;
    }

    // 🔹 Función para actualizar los <p> y <span>
    function actualizarResumen() {
        const total = calcularTotalEntregables();

        let acumulado = 0;
        inputs.forEach(id => {
            const input = document.getElementById(id);
            const porcentaje = parseFloat(input.value || 0);
            const monto = (total * porcentaje / 100).toFixed(2);

            // Actualizar el <p> (texto del porcentaje)
            const p = document.getElementById(id.replace("_por", "_p"));
            if (p) {
                p.innerHTML = `(${porcentaje}%) -> <span id="${id.replace("_por", "_spa")}">$ ${monto}</span>`;
            }
            acumulado += parseFloat(monto);
        });

        // Actualizar total
        const totalSpa = document.getElementById("total_spa");
        if (totalSpa) {
            totalSpa.textContent = `$ ${acumulado.toFixed(2)}`;
        }
    }

    // 🔹 Escuchar cambios en inputs
    inputs.forEach(id => {
        document.getElementById(id)?.addEventListener("input", actualizarResumen);
    });

    // 🔹 Escuchar cambios en entregables (cuando se agregan o eliminan)
    entregablesDiv.addEventListener("DOMSubtreeModified", () => {
        actualizarResumen();
    });

    // 🔹 Inicializar al cargar
    actualizarResumen();
});


document.addEventListener("DOMContentLoaded", function() {
    const departamentos = window.departamentos;

    document.getElementById('departamento').addEventListener('change', function() {
        let deptoId = this.value;
        let ciudadSelect = document.getElementById('ciudad');
        ciudadSelect.innerHTML = '<option value="">-- Seleccione --</option>';

        if (deptoId !== "") {
            let ciudades = departamentos.find(depto => depto.id == deptoId)?.ciudades || [];
            ciudades.forEach((ciudad, index) => {
                let option = document.createElement('option');
                option.value = index;
                option.textContent = ciudad;
                ciudadSelect.appendChild(option);
            });
        }
    });
});

async function values(){
    const aprovDisenoInput = document.getElementById('aprov_diseno_por');
    const iniCarpinteriaInput = document.getElementById('ini_carpinteria_por');
    const iniEnchapeInput = document.getElementById('ini_enchape_por');
    const iniGriferiaInput = document.getElementById('ini_griferia_por');
    const entregaObraInput = document.getElementById('entrega_obra_por');

}