let itemTemplate = null;

document.addEventListener("DOMContentLoaded", () => {

    const firstItem = document.querySelector(".item-group");

    if (firstItem) {
        itemTemplate = firstItem.cloneNode(true);
    }

    let entregables = [];
    let editIndex = null;

    const modal = "modalItemsArea-modal";
    const form = document.getElementById("formItemsArea");
    const areaDiv = document.getElementById("area-div");
    const selectArea = document.getElementById("id_area");
    const itemsContainer = document.getElementById("area-items");
    const addItemBtn = document.getElementById("addItem");

    // =========================
    // 💰 Formato dinero
    // =========================
    const money = (n) => {
        return "$ " + Number(n).toLocaleString("es-CO");
    };

    // =========================
    // 🔄 Reset modal
    // =========================
    function resetModal() {
        form.reset();
        editIndex = null;

        itemsContainer.innerHTML = "";
        itemsContainer.appendChild(itemTemplate.cloneNode(true));

        reindexItems();
    }

    // =========================
    // 🔢 Reindex items
    // =========================
    function reindexItems() {
        document.querySelectorAll(".item-group").forEach((el, i) => {
            el.dataset.itemNumber = i + 1;
            el.querySelector("h3").innerText = `Item ${i + 1}`;
        });
    }

    // =========================
    // ➕ Agregar item
    // =========================
    addItemBtn.addEventListener("click", () => {
        const clone = document.querySelector(".item-group").cloneNode(true);
        clone.querySelectorAll("input, textarea").forEach(e => e.value = "");
        itemsContainer.appendChild(clone);
        reindexItems();
    });

    // =========================
    // ❌ Eliminar item
    // =========================
    itemsContainer.addEventListener("click", e => {
        if (e.target.closest(".remove-item")) {

            const items = document.querySelectorAll(".item-group");
            if (items.length === 1) {
                Swal.fire("Error", "Debe existir al menos un item", "warning");
                return;
            }

            Swal.fire({
                title: "¿Eliminar item?",
                icon: "warning",
                showCancelButton: true
            }).then(res => {
                if (res.isConfirmed) {
                    e.target.closest(".item-group").remove();
                    reindexItems();
                }
            });
        }
    });

    // =========================
    // 📦 Obtener data modal
    // =========================
    function getModalData() {

        const id_area = selectArea.value;
        const areaText = selectArea.options[selectArea.selectedIndex].text;

        if (!id_area) {
            Swal.fire("Error", "Seleccione un área", "error");
            return null;
        }

        const items = [];

        document.querySelectorAll(".item-group").forEach(group => {

            const cantidad = group.querySelector('[name="cantidad"]').value;
            const valor = group.querySelector('[name="valor"]').value;
            const material = group.querySelector("textarea").value;
            const unidad = group.querySelector('[name="unidad"]').value;

            if (!cantidad || !valor || !material || !unidad) {
                Swal.fire("Error", "Todos los campos son obligatorios", "error");
                return null;
            }

            items.push({
                cantidad: Number(cantidad),
                valor_unitario: Number(valor),
                material,
                unidad: Number(unidad)
            });
        });

        return { id_area, areaText, items };
    }

    // =========================
    // 🚫 Área duplicada
    // =========================
    function existsArea(id_area) {
        return entregables.some((e, i) => {
            return String(e.id_area) === String(id_area) && i !== editIndex;
        });
    }

    // =========================
    // 💾 Guardar área
    // =========================
    form.addEventListener("submit", (e) => {
        e.preventDefault();

        const data = getModalData();
        if (!data) return;

        if (existsArea(data.id_area)) {
            Swal.fire("Error", "Esta área ya fue agregada", "warning");
            return;
        }

        if (editIndex !== null) {
            entregables[editIndex] = data;
        } else {
            entregables.push(data);
        }

        renderTable();
        resetModal();

        window.dispatchEvent(new CustomEvent('close-modal', { detail: modal }));
    });

    // =========================
    // 🧱 Render tabla
    // =========================
    function renderTable() {

        let html = "";
        let totalGeneral = 0;

        entregables.forEach((area, index) => {

            let subtotal = 0;
            let itemsHtml = "";

            area.items.forEach(item => {

                let sub = item.cantidad * item.valor_unitario;
                subtotal += sub;

                itemsHtml += `
                    <div class="flex justify-between items-start gap-4 py-2 border-b">

                        <!-- DESCRIPCIÓN -->
                        <div class="text-gray-800 flex-1">
                            ${item.material}
                        </div>

                        <!-- CANTIDAD + VALOR -->
                        <div class="text-right text-sm whitespace-nowrap">
                            <div>
                                <span class="text-gray-500">Unidad:</span>
                                <span class="font-semibold">${unidades[item.unidad]}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Cant:</span>
                                <span class="font-semibold">${item.cantidad}</span>
                            </div>

                            <div>
                                <span class="text-gray-500">Valor:</span>
                                <span class="font-semibold">${money(item.valor_unitario)}</span>
                            </div>

                        </div>

                    </div>
                `;
            });

            totalGeneral += subtotal;

            html += `
            <div class="bg-white border rounded-2xl shadow-md p-5 space-y-3">

                <!-- HEADER -->
                <div class="flex justify-between items-center">

                    <h2 class="text-lg font-bold text-[#242e68]">
                        ${area.areaText}
                    </h2>

                    <div class="flex gap-2">

                        <button type="button"
                            class="edit-area group"
                            data-index="${index}">

                            <div class="p-2 rounded-lg bg-blue-100 hover:bg-blue-200">
                                ✏️
                            </div>

                            <span class="absolute hidden group-hover:block text-xs bg-black text-white px-2 py-1 rounded">
                                Editar
                            </span>
                        </button>

                        <button type="button"
                            class="delete-area group"
                            data-index="${index}">

                            <div class="p-2 rounded-lg bg-red-100 hover:bg-red-200">
                                🗑️
                            </div>

                            <span class="absolute hidden group-hover:block text-xs bg-black text-white px-2 py-1 rounded">
                                Eliminar
                            </span>
                        </button>

                    </div>
                </div>

                <!-- ITEMS -->
                <div class="space-y-1">
                    ${itemsHtml}
                </div>

                <!-- SUBTOTAL -->
                <div class="text-right font-semibold text-[#242e68] pt-2">
                    Subtotal: ${money(subtotal)}
                </div>

            </div>
            `;
        });

        html += `
            <div class="bg-[#f7f9ff] border-2 border-[#242e68] rounded-xl p-4 text-right text-lg font-bold text-[#242e68]">
                Total General: ${money(totalGeneral)}
            </div>
        `;

        areaDiv.innerHTML = html;

        generateHiddenInputs();
    }

    // =========================
    // 🧾 Inputs hidden
    // =========================
    function generateHiddenInputs() {

        let container = document.getElementById("hidden-entregables");

        if (!container) {
            container = document.createElement("div");
            container.id = "hidden-entregables";
            container.style.display = "none";
            document.getElementById("formOtroSi").appendChild(container);
        }

        let html = "";

        entregables.forEach((area, i) => {

            html += `<input type="hidden" name="entregables[${i}][id_area]" value="${area.id_area}">`;

            area.items.forEach((item, j) => {
                html += `
                <input type="hidden" name="entregables[${i}][items][${j}][material]" value="${item.material}">
                <input type="hidden" name="entregables[${i}][items][${j}][cantidad]" value="${item.cantidad}">
                <input type="hidden" name="entregables[${i}][items][${j}][valor_unitario]" value="${item.valor_unitario}">
                <input type="hidden" name="entregables[${i}][items][${j}][unidad]" value="${item.unidad}">
                `;
            });
        });

        container.innerHTML = html;
    }

    // =========================
    // ✏️ Editar / eliminar
    // =========================
    areaDiv.addEventListener("click", e => {

        const editBtn = e.target.closest(".edit-area");
        const deleteBtn = e.target.closest(".delete-area");

        // EDITAR
        if (editBtn) {

            const index = Number(editBtn.dataset.index);
            const data = entregables[index];

            editIndex = index;

            selectArea.value = data.id_area;
            itemsContainer.innerHTML = "";

            data.items.forEach(item => {
                const clone = itemTemplate.cloneNode(true);

                clone.querySelector('[name="cantidad"]').value = item.cantidad;
                clone.querySelector('[name="valor"]').value = item.valor_unitario;
                clone.querySelector("textarea").value = item.material;
                clone.querySelector('[name="unidad"]').value =  item.unidad;

                itemsContainer.appendChild(clone);
            });

            reindexItems();

            window.dispatchEvent(new CustomEvent('open-modal', { detail: modal }));
        }

        // ELIMINAR
        if (deleteBtn) {
            const index = deleteBtn.dataset.index;

            Swal.fire({
                title: "¿Eliminar área?",
                icon: "warning",
                showCancelButton: true
            }).then(res => {
                if (res.isConfirmed) {
                    entregables.splice(index, 1);
                    renderTable();
                }
            });
        }
    });

    document.getElementById("btn-open-modal").addEventListener("click", () => {
        resetModal();
    });

    if (typeof entregablesFromDB !== "undefined" && entregablesFromDB.length > 0) {
        entregables = entregablesFromDB;
        renderTable();
        generateHiddenInputs();
    }

});
