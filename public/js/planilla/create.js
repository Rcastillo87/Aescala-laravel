let itemTemplate = null;

document.addEventListener("DOMContentLoaded", () => {

    const firstItem = document.querySelector(".item-group");

    if (firstItem) {
        itemTemplate = firstItem.cloneNode(true);
    }

    let entregables = [];
    let editIndex = null;

    const modal = "modalPlanillaEntregable-modal";
    const form = document.getElementById("formPlanillaEntregable");
    const areaDiv = document.getElementById("area-div");
    const selectArea = document.getElementById("id_tipo");
    const itemsContainer = document.getElementById("area-items");
    const addItemBtn = document.getElementById("addItem");
    
    // Referencias a la advertencia y totalizador
    const divAdvertencia = document.getElementById("divAdvertenciaPorcentaje");
    const sumaPorcentajeEl = document.getElementById("sumaPorcentajeTotal");

    // ==========================================
    // 🧮 Control de Visibilidad y Suma Porcentajes
    // ==========================================
    function calcularSumaPorcentajes() {
        let suma = 0;
        document.querySelectorAll("input[name='porcentage'], input[name='porcentage[]']").forEach(input => {
            const val = parseFloat(input.value);
            if (!isNaN(val)) {
                suma += val;
            }
        });

        if (sumaPorcentajeEl) {
            sumaPorcentajeEl.innerText = `${suma.toFixed(2).replace(/\.00$/, '')}%`;
            // Cambiar color a rojo si excede el 90%
            if (suma > 90) {
                sumaPorcentajeEl.classList.add("text-red-600");
                sumaPorcentajeEl.classList.remove("text-amber-900");
            } else {
                sumaPorcentajeEl.classList.remove("text-red-600");
                sumaPorcentajeEl.classList.add("text-amber-900");
            }
        }

        return suma;
    }

    function togglePorcentaje() {
        const esTipo3 = selectArea.value === "3" || selectArea.value === 3;
        const contenedoresPorcentaje = document.querySelectorAll("#divPorcentaje, .divPorcentaje");
        const inputsPorcentaje = document.querySelectorAll("input[name='porcentage'], input[name='porcentage[]']");

        if (esTipo3) {
            contenedoresPorcentaje.forEach(el => el.classList.remove("hidden"));
            if (divAdvertencia) divAdvertencia.classList.remove("hidden");
            calcularSumaPorcentajes();
        } else {
            contenedoresPorcentaje.forEach(el => el.classList.add("hidden"));
            if (divAdvertencia) divAdvertencia.classList.add("hidden");
            inputsPorcentaje.forEach(input => {
                input.value = 0;
            });
            if (sumaPorcentajeEl) sumaPorcentajeEl.innerText = "0%";
        }
    }

    // Escuchar el evento de cambio en el Select
    if (selectArea) {
        selectArea.addEventListener("change", togglePorcentaje);
        if (selectArea.tomselect) {
            selectArea.tomselect.on("change", togglePorcentaje);
        }
    }

    // Recalcular la suma total en tiempo real al escribir en los inputs de porcentaje
    itemsContainer.addEventListener("input", (e) => {
        if (e.target.matches("input[name='porcentage'], input[name='porcentage[]']")) {
            calcularSumaPorcentajes();
        }
    });

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
        togglePorcentaje(); // Re-evalúa visibilidad y resetea el marcador
    }

    // =========================
    // 🔢 Reindex items
    // =========================
    function reindexItems() {
        document.querySelectorAll(".item-group").forEach((el, i) => {
            el.dataset.itemNumber = i + 1;
            const h3 = el.querySelector("h3");
            if (h3) h3.innerText = `Item ${i + 1}`;
        });
    }

    // =========================
    // ➕ Agregar item
    // =========================
    addItemBtn.addEventListener("click", () => {
        const clone = itemTemplate.cloneNode(true);
        clone.querySelectorAll("input, textarea").forEach(e => {
            if (e.name === 'porcentage' || e.name === 'porcentage[]') {
                e.value = "0";
            } else {
                e.value = "";
            }
        });
        itemsContainer.appendChild(clone);
        reindexItems();
        togglePorcentaje();
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
                    calcularSumaPorcentajes(); // Actualizar suma tras borrar
                }
            });
        }
    });

    // =========================
    // 📦 Obtener data modal & Validación
    // =========================
    function getModalData() {
        const id_area = selectArea.value;
        const areaText = selectArea.options[selectArea.selectedIndex].text;
        const texto = areaText;
        const esTipo3 = id_area === "3" || id_area === 3;

        if (!id_area) {
            Swal.fire("Error", "Seleccione un tipo", "error");
            return null;
        }

        // 🛑 VALIDACIÓN DE PORCENTAJE MÁXIMO (90%)
        if (esTipo3) {
            const sumaTotal = calcularSumaPorcentajes();
            if (sumaTotal > 90) {
                Swal.fire("Suma excedida", `La suma de los porcentajes es ${sumaTotal}%. No debe superar el 90%.`, "error");
                return null;
            }
        }

        const items = [];
        let hasError = false;

        document.querySelectorAll(".item-group").forEach(group => {
            if (hasError) return;

            const cantidad = group.querySelector('[name="cantidad"]').value.trim();
            const valorRaw = group.querySelector('[name="valor_uni"]').value.trim();
            const material = group.querySelector("textarea").value.trim();
            const unidad = group.querySelector('[name="unidad"]').value.trim();
            
            // Capturar porcentaje si existe
            const inputPorcentaje = group.querySelector('[name="porcentage"], [name="porcentage[]"]');
            const porcentajeVal = inputPorcentaje ? Number(inputPorcentaje.value.trim() || 0) : 0;

            if (cantidad === "" || valorRaw === "" || material === "" || unidad === "") {
                Swal.fire("Error", "Todos los campos son obligatorios", "error");
                hasError = true;
                return;
            }

            const valor = Number(valorRaw) * (texto === 'Descuentos' ? -1 : 1);

            items.push({
                cantidad: Number(cantidad),
                valor_unitario: valor,
                material,
                unidad: Number(unidad),
                porcentage: porcentajeVal
            });
        });

        if (hasError) {
            return null;
        }

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
            Swal.fire("Error", "Este tipo ya fue agregada", "warning");
            return;
        }

        if (editIndex !== null) {
            entregables[editIndex] = data;
        } else {
            entregables.push(data);
        }

        renderTable();
        resetModal();

        const containerPadre = document.getElementById('entregables-container-padre');
        if (containerPadre) containerPadre.classList.remove('hidden');

        window.dispatchEvent(new CustomEvent('close-modal', { detail: modal }));
    });

    // =========================
    // 🧱 Render tabla
    // =========================
    function renderTable() {
        let html = "";
        let totalGeneral = 0;
        let globalIndex = 0;

        entregables.forEach((area, index) => {

            let subtotal = 0;
            let itemsHtml = "";

            area.items.forEach((item) => {

                let sub = item.cantidad * item.valor_unitario;
                subtotal += sub;

                itemsHtml += `
                    <div class="flex justify-between items-start gap-4 py-2 border-b">

                        <!-- DESCRIPCIÓN -->
                        <div class="text-gray-800 flex-1">
                            ${item.material}
                            ${item.porcentage ? `<span class="block text-xs text-blue-600 font-semibold">Porcentaje: ${item.porcentage}%</span>` : ''}
                        </div>

                        <!-- CANTIDAD + VALOR -->
                        <div class="text-right text-sm whitespace-nowrap">
                            <div>
                                <span class="text-gray-500">Unidad:</span>
                                <span class="font-semibold">${typeof unidades !== 'undefined' ? unidades[item.unidad] : item.unidad}</span>
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

                        <input type="hidden" name="entregables[${globalIndex}][id]" value="${item.id ?? ''}">
                        <input type="hidden" name="entregables[${globalIndex}][id_area]" value="${area.id_area}">
                        <input type="hidden" name="entregables[${globalIndex}][material]" value="${item.material}">
                        <input type="hidden" name="entregables[${globalIndex}][cantidad]" value="${item.cantidad}">
                        <input type="hidden" name="entregables[${globalIndex}][valor_unitario]" value="${item.valor_unitario}">
                        <input type="hidden" name="entregables[${globalIndex}][unidad]" value="${item.unidad}">
                        <input type="hidden" name="entregables[${globalIndex}][porcentage]" value="${item.porcentage ?? 0}">

                    </div>
                `;

                globalIndex++;
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
            <div class="col-span-1 md:col-span-4 bg-[#f7f9ff] border-2 border-[#242e68] rounded-xl p-4 text-right text-lg font-bold text-[#242e68]">
                Total General: ${money(totalGeneral)}
            </div>
        `;

        areaDiv.innerHTML = html;
    }

    // ==========================================
    // ✏️ Editar área (Carga de datos al modal)
    // ==========================================
    areaDiv.addEventListener("click", e => {

        const editBtn = e.target.closest(".edit-area");
        const deleteBtn = e.target.closest(".delete-area");

        // EDITAR
        if (editBtn) {

            const index = Number(editBtn.dataset.index);
            const data = entregables[index];

            editIndex = index;

            selectArea.value = data.id_area;
            if (selectArea.tomselect) {
                selectArea.tomselect.setValue(data.id_area);
            }

            itemsContainer.innerHTML = "";

            data.items.forEach(item => {
                const clone = itemTemplate.cloneNode(true);

                clone.querySelector('[name="cantidad"]').value = item.cantidad;
                clone.querySelector('[name="valor_uni"]').value = item.valor_unitario;
                clone.querySelector("textarea").value = item.material;
                clone.querySelector('[name="unidad"]').value = item.unidad;
                
                // Cargar el porcentaje almacenado
                const inputPorcentaje = clone.querySelector('[name="porcentage"], [name="porcentage[]"]');
                if (inputPorcentaje) {
                    inputPorcentaje.value = item.porcentage ?? 0;
                }

                itemsContainer.appendChild(clone);
            });

            reindexItems();
            togglePorcentaje(); // Mostrar/ocultar y calcular total

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
        editIndex = entregablesFromDB.length;
        renderTable();
    }

});

document.addEventListener("DOMContentLoaded", () => {
    FormManager.init("#savePlantilla", {
        confirmText: "Desea guardar los Entregables.",
        acepAlertText: "Sí, guardar",
        loadingText: "Guardando...",
        autoRedirect: true
    });
});

document.addEventListener("DOMContentLoaded", () => {

    const formAccept = document.getElementById("formAcceptConfig");
    const formDelete = document.getElementById("formDeleteConfig");

    // Textos descriptivos por tipo, para el Swal de confirmación
    const nombresTipo = {
        1: "Valor Área Proyecto",
        2: "Valor Área Enchape",
        3: "Porcentajes del Proyecto",
        4: "Obra Blanca",
        5: "Carpintería",
        6: "Excedente Enchape",
    };

    // =========================
    // ✅ Aceptar configuración
    // =========================
    FormManager.init(formAccept, {
        confirmText: (form) => form.dataset.confirmMsg ?? "Se guardará esta configuración.",
        acepAlertText: "Sí, aceptar",
        loadingText: "Guardando configuración...",
        autoRedirect: true, // recarga la página al terminar
    });

    document.querySelectorAll(".btn-accept-config").forEach(btn => {
        btn.addEventListener("click", () => {

            const block = btn.closest(".config-block");
            const tipo = block.dataset.tipo;
            const kind = block.dataset.kind;

            document.getElementById("accept-tipo").value = tipo;

            if (kind === "simple") {

                const input = block.querySelector(".suggested-value-input");
                const valor = input ? input.value.trim() : "";

                if (valor === "" || isNaN(Number(valor))) {
                    Swal.fire("Error", "Ingrese un valor numérico válido", "error");
                    return;
                }

                document.getElementById("accept-valor").value = valor;
                document.getElementById("accept-conceptos").value = "";

            } else {

                const conceptos = [];
                let hasError = false;

                block.querySelectorAll(".suggested-item").forEach(li => {
                    const concepto = li.dataset.concepto;
                    const input = li.querySelector(".suggested-porcentage-input");
                    const porcentage = input.value.trim();
                    const enPesosInput = li.querySelector(".suggested-en-pesos");

                    if (porcentage === "" || isNaN(Number(porcentage))) {
                        hasError = true;
                        return;
                    }

                    conceptos.push({ concepto, porcentage: Number(porcentage), en_pesos: Number(enPesosInput.value) });
                });

                if (hasError) {
                    Swal.fire("Error", "Todos los porcentajes deben ser válidos", "error");
                    return;
                }

                if (conceptos.length === 0) {
                    Swal.fire("Error", "Debe existir al menos un concepto", "warning");
                    return;
                }

                document.getElementById("accept-valor").value = "";
                document.getElementById("accept-conceptos").value = JSON.stringify(conceptos);
            }

            formAccept.dataset.confirmMsg = `Se guardará la configuración de "${nombresTipo[tipo]}".`;

            formAccept.requestSubmit();
        });
    });

    // =========================
    // 🔢 Total en vivo + ✕ eliminar item (tipo 3, antes de aceptar)
    // =========================
    function updateSuggestedTotal(list) {
        const totalEl = list.closest(".config-block").querySelector(".suggested-total");
        if (!totalEl) return;

        let total = 0;
        list.querySelectorAll(".suggested-porcentage-input").forEach(input => {
            const val = Number(input.value);
            if (!isNaN(val)) total += val;
        });

        totalEl.innerText = `${total}%`;
    }

    document.querySelectorAll('.config-block[data-kind="multi"] .suggested-list').forEach(list => {

        // Recalcular al editar un porcentaje
        list.addEventListener("input", e => {
            if (e.target.classList.contains("suggested-porcentage-input")) {
                updateSuggestedTotal(list);
            }
        });

        // Eliminar item y recalcular
        list.addEventListener("click", e => {
            const removeBtn = e.target.closest(".remove-suggested-item");
            if (!removeBtn) return;

            const items = list.querySelectorAll(".suggested-item");
            if (items.length === 1) {
                Swal.fire("Error", "Debe existir al menos un concepto", "warning");
                return;
            }

            removeBtn.closest(".suggested-item").remove();
            updateSuggestedTotal(list);
        });
    });

    // =========================
    // 🗑️ Eliminar configuración
    // =========================
    FormManager.init(formDelete, {
        confirmText: (form) => form.dataset.confirmMsg ?? "Se eliminará esta configuración.",
        acepAlertText: "Sí, eliminar",
        cancelColor: "#fa376c",
        loadingText: "Eliminando configuración...",
        autoRedirect: true,
    });

    document.querySelectorAll(".btn-delete-config").forEach(btn => {
        btn.addEventListener("click", () => {

            const block = btn.closest(".config-block");
            const tipo = block.dataset.tipo;

            formDelete.action = `${formDelete.dataset.baseUrl}/${tipo}`;
            formDelete.dataset.confirmMsg = `Se eliminará la configuración de "${nombresTipo[tipo]}" y volverá a mostrarse el valor sugerido.`;

            formDelete.requestSubmit();
        });
    });

});