document.addEventListener("DOMContentLoaded", function(event) {
    new TomSelect("#id_material",{
        create: true,
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

    // Obtener datos de materiales
    const materialesData = JSON.parse(document.getElementById('arrayMateriales').value);
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
            <div class="flex flex-col-2 text-center justify-center space-x-2">
                <div class="flex w-full space-x-2">
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
                        min="1"
                        name="materiales[${materialIndex}][cantidad]" 
                        placeholder="Cantidad"
                        class="text-sm py-1 px-4 border outline-none border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 
                        focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block w-full"
                        onchange="calculateTotal(this, null, ${materialIndex}, ${material.cantidad})">
                </div>
                <div class="w-full">
                    <input type="number"
                        value="${material.valor_unidad}"
                        name="materiales[${materialIndex}][valor_unidad]" 
                        placeholder="Valor venta"
                        class="text-sm py-1 px-4 border outline-none border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 
                        focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block w-full moneda-cop"
                        onchange="calculateTotal(null, this, ${materialIndex}, ${material.cantidad})">
                </div>
                <div class="w-full">
                    <input type="number"
                        value="${material.valor_inventario}"
                        name="materiales[${materialIndex}][valor_compra]" 
                        placeholder="Valor compra"
                        class="text-sm py-1 px-4 border outline-none border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 
                        focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block w-full moneda-cop">
                </div>
            </div>
            <p class="text-md font-bold text-left  text-gray-500">Observación: <small class="ml-2 text-black">${material.descripccion??''}</small></p>
            <div class="md:flex bg-gradient-to-r text-left md:justify-between from-slate-200 to-slate-100 rounded p-1 w-full">
                <p class="font-medium"><span class="valor">${formatCurrency(material.valor_unidad)}</span> * 
                <span class="quantity">1</span> = <b class="text-red-500 total">${formatCurrency(material.valor_unidad)}</b></p>
                <p>Tipo: ${material.spanTipo}</p>
            </div>
            <input type="hidden" name="materiales[${materialIndex}][id_material]" value="${material.id}">
        `;

        document.getElementById('selectMateriales').appendChild(materialDiv);
    }

    // Función para calcular total
    window.calculateTotal = function(input1, input2, index, maxCant) {

        var container = null
        var quantity = null
        var total = null

        if(input1){
            const cantIngresada = input1.value;
            if(cantIngresada < 1){
                input1.value = 1
            }
            container = input1.closest('div.bg-white');
            const inputValor = document.querySelector(`[name="materiales[${index}][valor_unidad]"]`);
            const price = parseFloat(inputValor.value);
            quantity = parseInt(input1.value) || 0;
            total = price * quantity;
            container.querySelector('.quantity').textContent = quantity;
        }

        if(input2){
            container = input2.closest('div.bg-white');
            const inputValor = document.querySelector(`[name="materiales[${index}][cantidad]"]`);
            const count = parseFloat(inputValor.value);
            quantity = parseInt(input2.value) || 0;
            if(quantity<0){
                input2.value = 0;
                quantity = 0;
            }
            total = count * quantity;
    
            container.querySelector('.valor').textContent = formatCurrency(quantity);
            container.querySelector('.total').textContent = `${formatCurrency(total)}`;

        }
        container.querySelector('.total').textContent = `${formatCurrency(total)}`;

    };

    // Función para remover material
    window.removeMaterial = function(button, materialId) {
        const container = button.closest('div.bg-white');
        container.remove();
        // Remover de la lista de materiales añadidos
        addedMaterials.delete(String(materialId));

    };

    document.getElementById('id_proveedor').addEventListener('change', function() {
        const proveedorId = this.value;
        addedMaterials.clear();
        document.getElementById('selectMateriales').innerHTML = '';

        fetch(`hPedidoproveedor/${proveedorId}`)
            .then(response => response.json())
            .then(data => {
                data.data.forEach(item => {
                    addSelectedMaterial( item.toString() );
                });
            })
            .catch(error => {
                console.error('Error fetching provider info:', error);
            });
    });

});