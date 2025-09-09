document.addEventListener("DOMContentLoaded", function () { 
    const canvas = document.getElementById("signature-pad");
    const clearButton = document.getElementById("clear-signature");
    const saveButton = document.getElementById("save-signature");
    const hiddenInput = document.getElementById("firma_base64");
    const preview = document.getElementById("firma-preview");
    let signaturePad;

    function resizeCanvas() {
        const ratio = window.devicePixelRatio || 1;
        const container = document.getElementById("signature-pad-container");
        const rect = container.getBoundingClientRect();

        // Ajuste al tamaño del contenedor
        canvas.width = rect.width * ratio;
        canvas.height = rect.height * ratio;

        const ctx = canvas.getContext("2d");
        ctx.setTransform(ratio, 0, 0, ratio, 0, 0);
    }

    function initSignaturePad() {
        resizeCanvas();
        signaturePad = new SignaturePad(canvas, {
            penColor: "#111",
            minWidth: 0.8,
            maxWidth: 2.5,
            throttle: 16, // suaviza el trazo
            velocityFilterWeight: 0.7,
        });
    }

    document.addEventListener("open-modal", function (event) {
        if (event.detail === "firma-modal") {
            setTimeout(() => {
                initSignaturePad();
                signaturePad.clear();
            }, 300);
        }
    });

    clearButton.addEventListener("click", function () {
        if (signaturePad) signaturePad.clear();
        hiddenInput.value = "";
        preview.innerHTML = '<span class="text-gray-400 text-sm">Sin firma</span>';
    });

    saveButton.addEventListener("click", function () {
        if (!signaturePad || signaturePad.isEmpty()) {
            Swal.fire({
                icon: "warning",
                title: "Firma requerida",
                text: "Por favor, dibuja tu firma antes de guardar.",
            });
            return;
        }

        const dataURL = signaturePad.toDataURL("image/png");
        hiddenInput.value = dataURL;
        preview.innerHTML = `<img src="${dataURL}" alt="Firma previa" class="w-full h-full object-contain">`;

        Swal.fire({
            icon: "success",
            title: "Firma guardada",
            timer: 1200,
            showConfirmButton: false,
        });
        window.dispatchEvent(new CustomEvent("close-modal", { detail: "firma-modal" }));
    });

    window.addEventListener("resize", () => {
        if (signaturePad) {
            resizeCanvas();
            signaturePad.clear();
        }
    });
});

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
                <div class="text-sm"><span class="font-semibold">Valor Unitario:</span>${formatCurrency(valor)}</div>
                <div class="text-sm"><span class="font-semibold">Total:</span>${formatCurrency(total)}</div>
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

    const inputs = [
        "termino_1_por",
        "termino_2_por",
        "termino_3_por",
        "termino_4_por",
        "termino_5_por",
        "termino_6_por"
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

    // 🔹 Función para actualizar los porcentajes y montos
    function actualizarResumen() {
        const total = calcularTotalEntregables();

        let acumuladoMonto = 0;
        let acumuladoPorcentaje = 0;

        inputs.forEach(id => {
            const input = document.getElementById(id);
            const porcentaje = parseFloat(input?.value || 0);
            const monto = (total * porcentaje / 100).toFixed(2);

            // actualizar porcentaje
            const p = document.getElementById(id.replace("_por", "_p"));
            if (p) {
                p.textContent = `${porcentaje}%`;
            }

            // actualizar monto
            const span = document.getElementById(id.replace("_por", "_spa"));
            if (span) {
                span.textContent = formatCurrency(monto);
            }

            acumuladoMonto += parseFloat(monto);
            acumuladoPorcentaje += porcentaje;
        });

        // actualizar total
        const totalP = document.getElementById("total_p");
        const totalSpa = document.getElementById("total_spa");

        if (totalP) {
            totalP.textContent = `(${acumuladoPorcentaje}%)`;
        }
        if (totalSpa) {
            totalSpa.textContent = formatCurrency(acumuladoMonto);
        }
    }

    // 🔹 Escuchar cambios en inputs
    inputs.forEach(id => {
        document.getElementById(id)?.addEventListener("input", actualizarResumen);
    });

    // 🔹 Escuchar cambios en entregables
    entregablesDiv.addEventListener("DOMSubtreeModified", actualizarResumen);

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

document.addEventListener("DOMContentLoaded", () => {
    const checkOpcion = document.getElementById("checkOpcion");
    const inputExtra = document.getElementById("inputExtra");
    const inputPorcentaje = document.getElementById("por_inicia");

    checkOpcion.addEventListener("change", () => {
        if (checkOpcion.checked) {
            inputExtra.classList.remove("hidden");
            inputPorcentaje.required = true;
            inputPorcentaje.value = 30;
        } else {
            inputExtra.classList.add("hidden");
            inputPorcentaje.required = false;
            inputPorcentaje.value = "";
        }
    });
});

document.addEventListener("DOMContentLoaded", () => {

    const form = document.getElementById("formComercial");

    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        document.querySelectorAll(".input-error").forEach(el => el.textContent = "");
        const formData = new FormData(form);

        const result = await Swal.fire({
            title: "¿Estás seguro?",
            text: "Se guardarán los cambios en el proyecto.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Sí, guardar",
            cancelButtonText: "Cancelar",
        });

        // Si confirma, se envía el formulario
        if (result.isConfirmed) {
        try {
            const response = await fetch(saveUrl, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
                    "Accept": "application/json"
                },
                body: formData
            });

            const data = await response.json();

            if (!response.ok) {
                if (data.errors) {
                    for (const [field, messages] of Object.entries(data.errors)) {
                        const input = document.querySelector(`[name="${field}"]`);

                        if (!input) continue;

                        // buscar x-input-error si existe
                        let errorContainer = input.parentNode.querySelector("x-input-error");

                        if (errorContainer) {
                            // si es componente Blade, colocamos mensaje dentro
                            errorContainer.innerHTML = messages.join(", ");
                        } else {
                            // si no existe, lo creamos dinámicamente
                            errorContainer = document.createElement("p");
                            errorContainer.classList.add("input-error", "text-red-600", "text-sm", "mt-2");
                            errorContainer.textContent = messages.join(", ");
                            input.insertAdjacentElement("afterend", errorContainer);
                        }
                    }
                } else if (data.error) {
                    Swal.fire("Error", data.error, "error");
                }
                return;
            }

            if (data.success) {
                Swal.fire({
                    icon: "success",
                    title: data.message,
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = data.redirect;
                });
            }
            } catch (error) {
                Swal.fire("Error", "Ocurrió un error inesperado", "error");
                console.error(error);
            }
        }
    });
});

