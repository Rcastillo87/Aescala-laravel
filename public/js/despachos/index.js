document.addEventListener("DOMContentLoaded", function(event) {
    new TomSelect("#id_material",{
        create: true,
        dropdownParent: 'body',
        sortField: {
            field: "text",
            direction: "asc"
        },
        onInitialize: function() {
            this.wrapper.classList.add("tom-select-custom");
        },
        onChange: function(value) {
            if(value) {
                addSelectedMaterial(value);
                this.clear();
            }
        }
    });

    new TomSelect("#id_proyecto",{
        create: true,
        dropdownParent: 'body',
        sortField: {
            field: "text",
            direction: "asc"
        },
        onInitialize: function() {
            this.wrapper.classList.add("tom-select-custom");
        }
    });

    // Obtener datos de materiales
    const materialesData =  [];
    const addedMaterials = new Set(); // Para trackear materiales añadidos


    // Función para agregar material seleccionado
    function addSelectedMaterial(materialId) {

        // Validar si el material ya fue añadido
        if(addedMaterials.has(materialId)) {
            Swal.fire({
                icon: 'warning',
                title: 'Material duplicado',
                text: 'Este material ya ha sido seleccionado',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Entendido'
            });
            return;
        }

        const material = materialesData.find(m => m.id == materialId);
        if(!material) return;

        addedMaterials.add(materialId);

        const materialIndex = addedMaterials.size - 1;

        const materialDiv = document.createElement('div');
        materialDiv.className = 'bg-white px-2 py-1 border-2 m-1 space-y-1 border-blue-500 rounded-xl';
        materialDiv.innerHTML = `
            <div class="flex items-center">
                <p class="text-md font-bold text-gray-500">Material: <span class="ml-2 text-black">${material.nombre_material}</span></p>
            </div>
            <div class="flex items-center space-x-2">
                <button class="border-2 border-red-500 rounded-md" type="button" onclick="removeMaterial(this, ${material.id})">
                    <svg class="w-7 h-7 text-red-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z"/>
                    </svg>
                </button>
                <div class="flex items-center justify-center text-[12px] text-center font-bold rounded h-8 w-[120px] text-white bg-gradient-to-tr from-red-600 to-red-400">
                    ${material.unidades}: ${material.cantidad}
                </div>
                <input type="number" 
                       value="1"
                       max="${material.cantidad}"
                       min="1"
                       name="materiales[${materialIndex}][cantidad]" 
                       placeholder="Cantidad"
                       class="text-sm py-1 px-4 border outline-none border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 
                       focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full"
                       onchange="calculateTotal(this, ${material.cantidad})">
            </div>
            <div class="flex justify-between">
                <p class="text-md font-bold text-left  text-gray-500">Observación: <small class="ml-2 text-black">${material.descripccion}</small></p>
                
                <div class="flex items-center">
                    <input type="hidden" name="materiales[${materialIndex}][cobro]" value="0">
                    <input
                        id="materiales[${materialIndex}][cobro]"
                        type="checkbox"
                        name="materiales[${materialIndex}][cobro]"
                        value="1"
                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm 
                            focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 
                            focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                        checked
                    >
                    <label for="materiales[${materialIndex}][cobro]" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                        Se cobra
                    </label>
                </div>

            </div>
            <div class="flex bg-gradient-to-r text-left justify-between from-slate-200 to-slate-100 rounded p-1 w-full">
                <p class="font-medium">Costo: ${formatCurrency(material.valor_unidad)} * 
                <span class="quantity">1</span> = <b class="text-red-500 total">${formatCurrency(material.valor_unidad)}</b></p>
                <p>Tipo: ${material.spanTipo}</p>
            </div>
            <input type="hidden" name="materiales[${materialIndex}][id_material]" value="${material.id}">
            <input type="hidden" name="materiales[${materialIndex}][valor_unidad]" value="${material.valor_unidad}">
            <input type="hidden" name="materiales[${materialIndex}][valor_inventario]" value="${material.valor_inventario}">
        `;



        document.getElementById('selectMateriales').appendChild(materialDiv);
    }

    // Función para calcular total
    window.calculateTotal = function(inputCantidad, maxCantidad) {
        const container = inputCantidad.closest('div'); // contenedor del input cantidad

        // Asegura que el valor esté dentro del rango
        if (inputCantidad.value > maxCantidad) {
            inputCantidad.value = maxCantidad;
        }
        if (inputCantidad.value < 1) {
            inputCantidad.value = 1;
        }

        const cantidad = inputCantidad.value;

        // Buscar el input hidden con el valor unitario
        const inputValor = container.parentElement.querySelector('input[name^="materiales"][name$="[valor_unidad]"]');
        const valor = parseFloat(inputValor.value) || 0;

        // Calcular total
        const total = cantidad * valor;

        // Actualizar en el DOM
        container.parentElement.querySelector('.quantity').textContent = cantidad;
        container.parentElement.querySelector('.total').textContent = formatCurrency(total);
    };

    // Función para remover material
    window.removeMaterial = function(button, materialId) {
        const container = button.closest('div.bg-white');
        container.remove();

        // Remover de la lista de materiales añadidos
        addedMaterials.delete(String(materialId));
    };

    const proyectoSelect = document.getElementById('id_proyecto');
    const userSelect = document.getElementById('id_user');
    const tipoSelect = document.getElementById('tipo');
    const materialSelect = document.getElementById('id_material').tomselect;

    proyectoSelect.addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const dataAttr = selectedOption.getAttribute('data-datax');
        if (!dataAttr) return;
        try {
            const data = JSON.parse(dataAttr);
            const idUser = data.id_user;
            if (!idUser) return;
            userSelect.value = idUser;
        } catch (e) {
            console.error("Error al parsear data-datax:", e);
        }
    });


    proyectoSelect?.addEventListener('change', ejecutarServicioSiCompleto);
    tipoSelect?.addEventListener('change', ejecutarServicioSiCompleto);
    function ejecutarServicioSiCompleto() {
        const tipo = tipoSelect?.value;
        const idProyecto = proyectoSelect?.value;

        if (tipo && idProyecto) {
            userSelect.disabled = false;
            userSelect.classList.remove("bg-gray-100", "cursor-not-allowed");
            materialSelect.enable();
            ejecutarServicio(tipo, idProyecto);
        } else {
            userSelect.disabled = true;
            userSelect.classList.add("bg-gray-100", "cursor-not-allowed");
            materialSelect.disable();
        }
    }

    function ejecutarServicio(tipo, idProyecto) {
        const params = new URLSearchParams({
            tipo: tipo,
            id_proyecto: idProyecto
        });

        fetch(`selectMaterales?${params.toString()}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Respuesta del servicio:', data);

            materialSelect.enable();
            materialSelect.clear();
            materialSelect.clearOptions();
            materialesData.length = 0; // 👈 LIMPIAR ARRAY
            addedMaterials.clear();
            document.getElementById('selectMateriales').innerHTML = '';

            data.results.forEach(item => {
                const material = item.material ?? item;
                materialSelect.addOption({
                    value: material.id,
                    text: material.nombre_material
                });
                materialesData.push(material);
            });

            materialSelect.refreshOptions(false);
            materialSelect.open();
        })

        .catch(error => {
            console.error('Error al ejecutar el servicio:', error);
        });
    }
});