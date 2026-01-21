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

    new TomSelect("#id_proyecto",{
        create: true,
        sortField: {
            field: "text",
            direction: "asc"
        },
        onInitialize: function() {
            this.wrapper.classList.add("tom-select-custom");
        }
    });

    // Obtener datos de materiales
    const materialesData = JSON.parse(document.getElementById('arrayMateriales').value);
    const addedMaterials = new Set(); // Para trackear materiales añadidos


    // Función para agregar material seleccionadoSS
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
                <input type="number" 
                       value="1"
                       min="1"
                       name="materiales[${materialIndex}][cantidad]" 
                       placeholder="Cantidad"
                       class="text-sm py-1 px-4 border outline-none border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 
                       focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full"
                       onchange="calculateTotal(this)">
            </div>
            <div class="flex justify-start">
                <p class="text-md font-bold text-left  text-gray-500">Observación: <small class="ml-2 text-black">${material.descripccion??''}</small></p>
            </div>
            <div class="flex bg-gradient-to-r text-left justify-between from-slate-200 to-slate-100 rounded p-1 w-full">
                <p class="font-medium">Costo: ${formatCurrency(material.valor_unidad)} * 
                <span class="quantity">1</span> = <b class="text-red-500 total">${formatCurrency(material.valor_unidad)}</b></p>
                <p>Tipo: ${material.spanTipo}</p>
            </div>
            <input type="hidden" name="materiales[${materialIndex}][id_material]" value="${material.id}">
            <input type="hidden" name="materiales[${materialIndex}][valor_unidad]" value="${material.valor_unidad}">
        `;
        document.getElementById('selectMateriales').appendChild(materialDiv);
    }

    // Función para calcular total
    window.calculateTotal = function(inputCantidad) {
        const container = inputCantidad.closest('div'); // contenedor del input cantidad

        // Asegura que el valor esté dentro del rango
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


    document.getElementById("id_fases").addEventListener("change", function() {
         const option = this.options[this.selectedIndex];
        const datax = JSON.parse(option.dataset.datax);

        // Limpiar materiales seleccionados
        document.getElementById('selectMateriales').innerHTML = '';
        addedMaterials.clear();

        // Agregar materiales de las fases seleccionadas
        datax.materiales.forEach(id_material => {
            addSelectedMaterial(id_material);
        });
    });

    document.getElementById("btnLimpiar").addEventListener("click", function(e) {
        e.preventDefault();
        // Limpiar todos los campos del formulario
        const form = document.getElementById("formSolicitud");
        form.reset();

        document.getElementById("id_proyecto").tomselect.clear();
        document.getElementById("id_material").tomselect.clear();
    
        // Limpiar el contenedor de materiales seleccionados
        document.getElementById('selectMateriales').innerHTML = '';

        // Limpiar mensajes de error si existen
        document.querySelectorAll(".error-msg").forEach(e => e.remove());
        addedMaterials.clear();
    });
});

document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("formSolicitud");

    form.addEventListener("submit", async function (e) {
        e.preventDefault();

        if (!document.querySelector('[name^="materiales["]')) {
            Swal.fire({
                icon: 'warning',
                title: 'Sin materiales',
                text: 'Debe agregar al menos un material, en "Seleccione Material".'
            });
            return;
        }

        Swal.fire({
            title: 'Procesando...',
            html: `
                <div class="flex items-center justify-center">
                    <div class="w-10 h-10 border-4 border-gray-300 border-t-blue-600 rounded-full animate-spin"></div>
                </div>
            `,
            allowOutsideClick: false,
            showConfirmButton: false
        });

        const formData = new FormData(form);

        try {
            const resp = await fetch(form.action, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": formData.get("_token"),
                    "Accept": "application/json"
                },
                body: formData
            });

            // Éxito ✔
            if (resp.ok) {
                Swal.fire({
                    icon: "success",
                    title: "Solicitud guardada",
                    text: "La solicitud se registró correctamente.",
                    confirmButtonText: "Ir al listado",
                    confirmButtonColor: "#2563eb"
                }).then(() => {
                    window.location.href = "index";
                });
                return;
            }

            // Errores de validación 422 ✔
            if (resp.status === 422) {
                const data = await resp.json();
                mostrarErrores(data.errors);

                Swal.fire({
                    icon: "error",
                    title: "Formulario incompleto",
                    text: "Corrige los campos marcados."
                });
                return;
            }

            // Error general
            Swal.fire({
                icon: "error",
                title: "Error inesperado",
                text: "Ocurrió un problema. Intenta nuevamente."
            });

        } catch (error) {
            console.error("Error:", error);
            Swal.fire({
                icon: "error",
                title: "Error inesperado",
                text: "No se pudo enviar la solicitud. Intenta nuevamente."
            });
        }
    });

    function mostrarErrores(errors) {
        document.querySelectorAll(".error-msg").forEach(e => e.remove());

        Object.keys(errors).forEach(campo => {
            const campoForm = campo.replace(/\./g, "][");
            const input = document.querySelector(`[name="${campoForm}"]`)
                || document.querySelector(`[name="${campoForm}]"]`);

            if (!input) return;
            const div = document.createElement("div");
            div.className = "error-msg text-red-600 mt-1 text-sm";
            div.innerText = errors[campo][0];
            input.insertAdjacentElement("afterend", div);
        });
    }

});
