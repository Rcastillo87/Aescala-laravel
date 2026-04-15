document.addEventListener("DOMContentLoaded", function () {

    // ── Referencias DOM ──────────────────────────────────────────────────────
    const tipoSelect     = document.querySelector('[name="tipo"]');
    const proveedorSelect = document.getElementById('id_proveedor');
    const materialSelect  = document.getElementById('id_material');
    const divProyecto    = document.getElementById('divProyecto');
    const proyectoSelect = document.getElementById('id_proyecto');
    const selectMateriales = document.getElementById('selectMateriales');

    // ── Datos desde el DOM ───────────────────────────────────────────────────
    const arrayMateriales  = JSON.parse(document.getElementById('arrayMateriales').value  || '[]');
    const arrayInsumos     = JSON.parse(document.getElementById('arrayInsumos').value     || '[]');
    const arrayProveedores = JSON.parse(document.getElementById('arrayProveedores').value || '[]');

    // ── Estado ───────────────────────────────────────────────────────────────
    const addedMaterials = new Set();

    // ── Instancia TomSelect ──────────────────────────────────────────────────
    const tomSelectInstance = new TomSelect('#id_material', {
        create: false,
        sortField: { field: 'text', direction: 'asc' },
        placeholder: 'Busqueda..',
        onInitialize: function () {
            this.wrapper.classList.add('tom-select-custom');
        },
        onChange: function (value) {
            if (value) {
                addSelectedMaterial(value);
                this.clear();
            }
        }
    });

    // Empieza deshabilitado
    tomSelectInstance.disable();

    // ── Helpers select nativo (proveedor) ────────────────────────────────────
    const disableSelect = (el) => {
        el.setAttribute('disabled', true);
        el.classList.add('bg-gray-100', 'cursor-not-allowed');
    };

    const enableSelect = (el) => {
        el.removeAttribute('disabled');
        el.classList.remove('bg-gray-100', 'cursor-not-allowed');
    };

    const clearSelect = (el) => {
        el.innerHTML = '<option value="">-- Seleccione --</option>';
    };

    const fillSelect = (el, data, valueKey, textKey) => {
        clearSelect(el);
        data.forEach(item => {
            const option = document.createElement('option');
            option.value = item[valueKey];
            option.textContent = item[textKey].toLowerCase();
            el.appendChild(option);
        });
    };

    // ── Helpers TomSelect (material) ─────────────────────────────────────────
    const fillTomSelect = (data, valueKey, textKey) => {
        tomSelectInstance.clearOptions();
        const options = data.map(item => ({
            value: String(item[valueKey]),
            text: item[textKey]
        }));
        tomSelectInstance.addOptions(options);
        tomSelectInstance.refreshOptions(false);
    };

    // ── Devuelve el array activo según tipo ──────────────────────────────────
    const getActiveData = () => {
        const tipo = tipoSelect ? tipoSelect.value : '';
        if (tipo === '1') return arrayMateriales;
        if (tipo === '2') return arrayInsumos;
        return [];
    };

    const getActiveKey = () => {
        const tipo = tipoSelect ? tipoSelect.value : '';
        return tipo === '2' ? 'nombre_insumo' : 'nombre_material';
    };

    // ── Limpiar materiales seleccionados ─────────────────────────────────────
    const clearSelectedMaterials = () => {
        addedMaterials.clear();
        selectMateriales.innerHTML = '';
    };

    // ── Filtrar proveedores por tipo ─────────────────────────────────────────
    const filterProveedores = (tipo) => {
        const filtrados = arrayProveedores.filter(p => p.tipo == tipo);
        fillSelect(proveedorSelect, filtrados, 'id', 'razon_social');
    };

    // ── Lógica cambio de tipo ────────────────────────────────────────────────
    const manejarCambioTipo = () => {
        const tipo = tipoSelect ? tipoSelect.value : '';
        clearSelectedMaterials();

        if (!tipo) {
            divProyecto.classList.add('hidden');
            proyectoSelect.value = '';
            clearSelect(proveedorSelect);
            disableSelect(proveedorSelect);
            tomSelectInstance.clearOptions();
            tomSelectInstance.disable();
            return;
        }

        enableSelect(proveedorSelect);

        if (tipo === '1') {
            divProyecto.classList.remove('hidden');
            filterProveedores(1);
            fillTomSelect(arrayMateriales, 'id', 'nombre_material');
        }

        if (tipo === '2') {
            divProyecto.classList.add('hidden');
            proyectoSelect.value = '';
            filterProveedores(2);
            fillTomSelect(arrayInsumos, 'id', 'nombre_insumo');
        }

        tomSelectInstance.enable();
    };

    if (tipoSelect) {
        tipoSelect.addEventListener('change', manejarCambioTipo);
        manejarCambioTipo(); // ejecutar al cargar por si hay old()
    }

    // ── Cambio de proveedor → historial de la API ────────────────────────────
    if (proveedorSelect) {
        proveedorSelect.addEventListener('change', function () {
            const proveedorId = this.value;
            clearSelectedMaterials();

            if (!proveedorId) return;

            fetch(`hPedidoproveedor/${proveedorId}`)
                .then(response => response.json())
                .then(data => {
                    if (!data.status || !data.data.length) return;

                    const activeData = getActiveData();
                    const nameKey   = getActiveKey();

                    data.data.forEach(id => {
                        // Verificar que el ID existe en el array activo
                        const existe = activeData.find(m => m.id == id);
                        if (existe) {
                            addSelectedMaterial(String(id));
                        }
                    });
                })
                .catch(error => {
                    console.error('Error fetching provider info:', error);
                });
        });
    }

    // ── Agregar material/insumo a la lista ───────────────────────────────────
    function addSelectedMaterial(materialId) {
        if (addedMaterials.has(materialId)) {
            Swal.fire({
                icon: 'warning',
                title: 'Material duplicado',
                text: 'Este material ya ha sido seleccionado',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Entendido'
            });
            return;
        }

        const activeData = getActiveData();
        const nameKey    = getActiveKey();
        const material   = activeData.find(m => m.id == materialId);
        const tipo       = tipoSelect.value;

        console.log(tipo);

        if (!material) return;

        addedMaterials.add(materialId);
        const materialIndex = addedMaterials.size - 1;

        const nombre = material[nameKey] || '';

        const materialDiv = document.createElement('div');
        materialDiv.className = 'bg-white dark:bg-gray-800 px-2 py-1 border-2 m-1 space-y-1 border-blue-500 rounded-xl';
        materialDiv.innerHTML = `
            <div class="flex items-center justify-between">
                <p class="text-md font-bold text-gray-500 dark:text-gray-300">
                    Material: <span class="ml-2 text-black dark:text-white">${nombre}</span>
                </p>
                <button class="border-2 border-red-500 rounded-md p-0.5" type="button" onclick="removeMaterial(this, '${materialId}')">
                    <svg class="w-5 h-5 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z"/>
                    </svg>
                </button>
            </div>
            <div class="flex flex-col gap-1">
                <div class="flex items-center gap-2">
                    <div class="flex items-center justify-center text-[11px] mt-6 font-bold rounded h-7 px-2 text-white bg-gradient-to-tr from-red-600 to-red-400 whitespace-nowrap">
                        ${material.unidades??'Unid'}: ${material.cantidad ?? 0}
                    </div>
                    <div class="flex-2 max-w-[200px]">
                        <label class="text-xs text-gray-500 dark:text-gray-400">Cantidad</label>
                        <input type="number"
                            value="1" min="1"
                            name="materiales[${materialIndex}][cantidad]"
                            placeholder="Cantidad"
                            class="text-sm py-1 px-2 border outline-none border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300
                                focus:border-indigo-500 dark:focus:border-indigo-600 rounded-md shadow-sm block w-full"
                            onchange="calculateTotal(this, null, ${materialIndex})">
                    </div>
                    <div class="flex-1 ${(tipo == 1)? '' : 'hidden' }">
                        <label class="text-xs text-gray-500 dark:text-gray-400">Valor venta</label>
                        <input type="number"
                            value="${material.valor_unidad ?? 0}"
                            name="materiales[${materialIndex}][valor_unidad]"
                            placeholder="Valor venta"
                            class="text-sm py-1 px-2 border outline-none border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300
                                focus:border-indigo-500 dark:focus:border-indigo-600 rounded-md shadow-sm block w-full"
                            onchange="calculateTotal(null, this, ${materialIndex})">
                    </div>
                    <div class="flex-1 ${(tipo == 1)? '' : 'hidden' }">
                        <label class="text-xs text-gray-500 dark:text-gray-400">Valor compra</label>
                        <input type="number"
                            value="${material.valor_inventario ?? 0}"
                            name="materiales[${materialIndex}][valor_compra]"
                            placeholder="Valor compra"
                            class="text-sm py-1 px-2 border outline-none border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300
                                focus:border-indigo-500 dark:focus:border-indigo-600 rounded-md shadow-sm block w-full">
                    </div>
                </div>
                ${material.descripccion ? `
                <p class="text-xs text-gray-500 dark:text-gray-400 text-start">
                    Obs: <span class="text-black dark:text-white">${material.descripccion}</span>
                </p>` : ''}
                <div class="flex justify-between items-center bg-gradient-to-r from-slate-200 to-slate-100 dark:from-slate-700 dark:to-slate-600 rounded p-1 ${(tipo == 1)? '' : 'hidden' }">
                    <p class="text-sm font-medium">
                        <span class="valor">${formatCurrency(material.valor_unidad ?? 0)}</span>
                        × <span class="quantity">1</span>
                        = <b class="text-red-500 total">${formatCurrency(material.valor_unidad ?? 0)}</b>
                    </p>
                    ${material.spanTipo ? `<span class="text-xs">${material.spanTipo}</span>` : ''}
                </div>
            </div>
            <input type="hidden" name="materiales[${materialIndex}][id_material]" value="${material.id}">
        `;

        selectMateriales.appendChild(materialDiv);
    }

    // ── Calcular total ───────────────────────────────────────────────────────
    window.calculateTotal = function (inputCant, inputValor, index) {
        const cantInput   = inputCant  || document.querySelector(`[name="materiales[${index}][cantidad]"]`);
        const valorInput  = inputValor || document.querySelector(`[name="materiales[${index}][valor_unidad]"]`);
        const container   = (inputCant || inputValor).closest('div.bg-white, div.dark\\:bg-gray-800');

        if (!cantInput || !valorInput || !container) return;

        let cantidad = parseInt(cantInput.value) || 1;
        let valor    = parseFloat(valorInput.value) || 0;

        if (cantidad < 1) { cantInput.value = 1; cantidad = 1; }
        if (valor < 0)    { valorInput.value = 0; valor = 0; }

        const total = cantidad * valor;

        const elCantidad = container.querySelector('.quantity');
        const elValor    = container.querySelector('.valor');
        const elTotal    = container.querySelector('.total');

        if (elCantidad) elCantidad.textContent = cantidad;
        if (elValor)    elValor.textContent    = formatCurrency(valor);
        if (elTotal)    elTotal.textContent    = formatCurrency(total);
    };

    // ── Remover material ─────────────────────────────────────────────────────
    window.removeMaterial = function (button, materialId) {
        const container = button.closest('div.bg-white, div.dark\\:bg-gray-800');
        if (container) container.remove();
        addedMaterials.delete(String(materialId));
    };

    // ── Formato moneda ───────────────────────────────────────────────────────
    // (asumiendo que formatCurrency ya está definido globalmente en otro archivo)
    // Si no, descomenta esto:
    // window.formatCurrency = (value) =>
    //     new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 }).format(value);
});
