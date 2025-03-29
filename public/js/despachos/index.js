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

    // Función para agregar material seleccionado
    function addSelectedMaterial(materialId) {
        const material = materialesData.find(m => m.id == materialId);
        if(!material) return;

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
                <div class="flex items-center justify-center text-[12px] text-center font-bold rounded h-7 w-[80px] text-white bg-gradient-to-tr from-red-600 to-red-400">
                    ${material.id_unidad}: ${material.cantidad}
                </div>
                <input type="number" 
                       name="materiales[${material.id}][cantidad]" 
                       placeholder="Cantidad" 
                       min="1" 
                       max="${material.cantidad}"
                       class="w-full text-sm py-1 px-4 rounded-lg border outline-none"
                       onchange="calculateTotal(this)">
            </div>
            <p class="text-md font-bold text-gray-500">Observación: <small class="ml-2 text-black">${material.tipo || 'Ninguna'}</small></p>
            <div class="bg-gradient-to-r from-slate-200 to-slate-100 rounded p-1 w-full">
                <p class="font-medium">$${formatCurrency(material.valor_unidad)} * <span class="quantity">0</span> = <b class="text-red-500 total">$0</b></p>
            </div>
            <input type="hidden" name="materiales[${material.id}][id]" value="${material.id}">
        `;

        document.getElementById('selectMateriales').appendChild(materialDiv);
    }

    // Función para formatear moneda
    function formatCurrency(value) {
        return parseFloat(value).toLocaleString('es-ES');
    }

    // Función para calcular total
    window.calculateTotal = function(input) {
        const container = input.closest('div.bg-white');
        const priceText = container.querySelector('.font-medium').textContent.match(/\$([\d.,]+)/)[1].replace(/\./g, '').replace(',', '.');
        const price = parseFloat(priceText);
        const quantity = parseInt(input.value) || 0;
        const total = price * quantity;
        
        container.querySelector('.quantity').textContent = quantity;
        container.querySelector('.total').textContent = `$${formatCurrency(total)}`;
    };

    // Función para remover material
    window.removeMaterial = function(button, materialId) {
        const container = button.closest('div.bg-white');
        container.remove();
        
        // Volver a agregar la opción al select
        const material = materialesData.find(m => m.id == materialId);
        if(material) {
            tomSelect.addOption({
                value: material.id,
                text: material.nombre_material
            });
        }
    };

    
});