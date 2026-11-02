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
    
    const divAdvertencia = document.getElementById("divAdvertenciaPorcentaje");
    const sumaPorcentajeEl = document.getElementById("sumaPorcentajeTotal");

    function calcularSumaPorcentajes() {
        let suma = 0;
        document.querySelectorAll("input[name='porcentage'], input[name='porcentage[]']").forEach(input => {
            const val = parseFloat(input.value);
            if (!isNaN(val)) suma += val;
        });

        if (sumaPorcentajeEl) {
            sumaPorcentajeEl.innerText = `${suma.toFixed(2).replace(/\.00$/, '')}%`;
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
            inputsPorcentaje.forEach(input => input.value = 0);
            if (sumaPorcentajeEl) sumaPorcentajeEl.innerText = "0%";
        }
    }

    if (selectArea) {
        selectArea.addEventListener("change", togglePorcentaje);
        if (selectArea.tomselect) selectArea.tomselect.on("change", togglePorcentaje);
    }

    itemsContainer.addEventListener("input", (e) => {
        if (e.target.matches("input[name='porcentage'], input[name='porcentage[]']")) {
            calcularSumaPorcentajes();
        }
    });

    function resetModal() {
        form.reset();
        editIndex = null;
        itemsContainer.innerHTML = "";
        itemsContainer.appendChild(itemTemplate.cloneNode(true));
        reindexItems();
        togglePorcentaje();
    }

    function reindexItems() {
        document.querySelectorAll(".item-group").forEach((el, i) => {
            el.dataset.itemNumber = i + 1;
            const h3 = el.querySelector("h3");
            if (h3) h3.innerText = `Item ${i + 1}`;
        });
    }

    addItemBtn.addEventListener("click", () => {
        const clone = itemTemplate.cloneNode(true);
        clone.querySelectorAll("input, textarea").forEach(e => {
            if (e.name === 'porcentage' || e.name === 'porcentage[]') e.value = "0";
            else e.value = "";
        });
        itemsContainer.appendChild(clone);
        reindexItems();
        togglePorcentaje();
    });

    itemsContainer.addEventListener("click", e => {
        if (e.target.closest(".remove-item")) {
            const items = document.querySelectorAll(".item-group");
            if (items.length === 1) {
                Swal.fire("Error", "Debe existir al menos un item", "warning");
                return;
            }
            Swal.fire({ title: "¿Eliminar item?", icon: "warning", showCancelButton: true }).then(res => {
                if (res.isConfirmed) {
                    e.target.closest(".item-group").remove();
                    reindexItems();
                    calcularSumaPorcentajes();
                }
            });
        }
    });

    function getModalData() {
        const id_area = selectArea.value;
        const areaText = selectArea.options[selectArea.selectedIndex].text;
        const esTipo3 = id_area === "3" || id_area === 3;

        if (!id_area) {
            Swal.fire("Error", "Seleccione un tipo", "error");
            return null;
        }

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
            
            const inputPorcentaje = group.querySelector('[name="porcentage"], [name="porcentage[]"]');
            const porcentajeVal = inputPorcentaje ? Number(inputPorcentaje.value.trim() || 0) : 0;

            if (cantidad === "" || valorRaw === "" || material === "" || unidad === "") {
                Swal.fire("Error", "Todos los campos son obligatorios", "error");
                hasError = true;
                return;
            }

            items.push({
                cantidad: Number(cantidad),
                valor_unitario: Number(valorRaw),
                material,
                unidad: Number(unidad),
                porcentage: porcentajeVal
            });
        });

        if (hasError) return null;
        return { id_area, areaText, items };
    }

    function existsArea(id_area) {
        return entregables.some((e, i) => String(e.id_area) === String(id_area) && i !== editIndex);
    }

    // --- LÓGICA DE GUARDADO AUTOMÁTICO ---
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
        window.dispatchEvent(new CustomEvent('close-modal', { detail: modal }));

        const formSave = document.getElementById('savePlantilla');
        const formData = new FormData(formSave);

        Swal.fire({
            title: 'Guardando entregable...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        fetch(formSave.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(res => {
            if (res.success) {
                window.location.reload(); 
            } else {
                Swal.fire('Error', res.error || 'Error al guardar', 'error');
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire('Error', 'Error de conexión con el servidor', 'error');
        });
    });

    function renderTable() {
        let html = "";
        let globalIndex = 0;

        entregables.forEach((area) => {
            area.items.forEach((item) => {
                html += `
                    <input type="hidden" name="entregables[${globalIndex}][id]" value="${item.id ?? ''}">
                    <input type="hidden" name="entregables[${globalIndex}][id_area]" value="${area.id_area}">
                    <input type="hidden" name="entregables[${globalIndex}][material]" value="${item.material}">
                    <input type="hidden" name="entregables[${globalIndex}][cantidad]" value="${item.cantidad}">
                    <input type="hidden" name="entregables[${globalIndex}][valor_unitario]" value="${item.valor_unitario}">
                    <input type="hidden" name="entregables[${globalIndex}][unidad]" value="${item.unidad}">
                    <input type="hidden" name="entregables[${globalIndex}][porcentage]" value="${item.porcentage ?? 0}">
                `;
                globalIndex++;
            });
        });

        areaDiv.innerHTML = html;
    }

    document.getElementById("btn-open-modal")?.addEventListener("click", () => {
        resetModal();
    });

    if (typeof entregablesFromDB !== "undefined" && entregablesFromDB.length > 0) {
        entregables = entregablesFromDB;
        renderTable();
    }

    // ==========================================
    // ✏️ Editar y 🗑️ Eliminar desde la Tabla de Cobros
    // ==========================================
    document.addEventListener('click', (e) => {
        // EDITAR
        const editBtn = e.target.closest('.edit-entregable');
        if (editBtn) {
            const tipo = Number(editBtn.dataset.tipo);
            const areaIndex = entregables.findIndex(a => a.id_area == tipo);
            
            if (areaIndex !== -1) {
                const data = entregables[areaIndex];
                editIndex = areaIndex;
                selectArea.value = data.id_area;
                if (selectArea.tomselect) selectArea.tomselect.setValue(data.id_area);

                itemsContainer.innerHTML = "";
                data.items.forEach(item => {
                    const clone = itemTemplate.cloneNode(true);
                    clone.querySelector('[name="cantidad"]').value = item.cantidad;
                    clone.querySelector('[name="valor_uni"]').value = item.valor_unitario;
                    clone.querySelector("textarea").value = item.material;
                    clone.querySelector('[name="unidad"]').value = item.unidad;
                    const inputPorcentaje = clone.querySelector('[name="porcentage"], [name="porcentage[]"]');
                    if (inputPorcentaje) inputPorcentaje.value = item.porcentage ?? 0;
                    itemsContainer.appendChild(clone);
                });

                reindexItems();
                togglePorcentaje();
                window.dispatchEvent(new CustomEvent('open-modal', { detail: modal }));
            }
        }

        // ELIMINAR
        const deleteBtn = e.target.closest('.delete-entregable');
        if (deleteBtn) {
            const tipo = Number(deleteBtn.dataset.tipo);
            Swal.fire({
                title: "¿Eliminar entregable?",
                text: "Se borrarán todos los ítems de este tipo.",
                icon: "warning",
                showCancelButton: true
            }).then(res => {
                if (res.isConfirmed) {
                    entregables = entregables.filter(a => a.id_area != tipo);
                    renderTable();
                    
                    const formSave = document.getElementById('savePlantilla');
                    const formData = new FormData(formSave);
                    
                    Swal.fire({ title: 'Eliminando...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                    
                    fetch(formSave.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(res => {
                        if (res.success) {
                            window.location.reload();
                        } else {
                            Swal.fire('Error', res.error || 'Error al eliminar', 'error');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire('Error', 'Error de conexión', 'error');
                    });
                }
            });
        }
    });
});