const addedMaterials = new Set();      // manuales
const historialMaterials = new Set();  // SOLO para remover grupos
let materialCounter = 0;

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
    const materialesData = JSON.parse(document.getElementById('arrayMateriales').value);

    // Función para agregar material seleccionado
    function addSelectedMaterial(materialId) {

        materialId = Number(materialId);

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
                <input step="any"
                       onkeydown="if(['e','E','+','-'].includes(event.key)) event.preventDefault();"
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
                            ${material.fase == 3 ? '' : 'checked'}
                    >
                    <label for="materiales[${materialIndex}][cobro]" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
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
    tituloMateriales = document.getElementById('tituloMateriales');

    function ejecutarServicioSiCompleto() {
        const tipo = tipoSelect?.value;
        const idProyecto = proyectoSelect?.value;

        document.getElementById('selectMateriales').innerHTML = '';
        addedMaterials.clear();
        historialMaterials.clear();
        materialCounter = 0;   // 👈 resetear

        if (tipo == 2 && idProyecto) {
            tituloMateriales.textContent = 'Materiales a Devolver';
            materialSelect.disable();
            ejecutarServicio(idProyecto);
        } else {
            tituloMateriales.textContent = 'Materiales a Despachar';
            materialSelect.enable();
        }
    }

    function ejecutarServicio(idProyecto) {
        const params = new URLSearchParams({
            id_proyecto: idProyecto
        });

        fetch(`historialMateriales?${params.toString()}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(async data => {
            await addMaterialDevolucion(data.results);
        })
        .catch(error => {
            console.error('Error al ejecutar el servicio:', error);
        });
    }
});

// Función para agregar material seleccionado
async function addMaterialDevolucion(data) {

    if (!Array.isArray(data)) return;

    const container = document.getElementById('selectMateriales');
    container.innerHTML = '';
    addedMaterials.clear();

    data.forEach(group => {

        // ===== CONTENEDOR DEL GRUPO =====
        const groupDiv = document.createElement('div');
        groupDiv.className = 'border-2 border-gray-400 rounded-xl m-1 mb-2 bg-gray-50';
        groupDiv.setAttribute('data-codigo', group.codigo);

        groupDiv.innerHTML = `
            <div class="flex justify-between items-center text-center border-b pb-2 mb-1">
                <div class="flex-row text-start mx-2 my-1">
                    <div class="font-bold text-blue-700 text-md">
                        Código: ${group.codigo}
                    </div>
                    <div class="text-sm text-gray-500">
                        Fecha: ${group.createdAt}
                    </div>
                </div>
                <button type="button"
                    class="bg-red-600 text-white font-bold text-sm hover:bg-red-800 px-2 py-1 rounded mr-2"
                    onclick="removeGroup(this)"> ✕
                </button>
            </div>
        `;

        // ===== RECORRER MATERIALES =====
        group.items.forEach(item => {

            const materialIndex = materialCounter++;

            historialMaterials.add(item.id_material);

            const materialDiv = document.createElement('div');
            materialDiv.className = 'bg-white px-3 py-2 border m-2 rounded-lg';
            materialDiv.setAttribute('data-material-id', item.id_material);

            materialDiv.innerHTML = `
            <div class="flex items-center">
                <p class="text-md font-bold text-gray-500">Material: <span class="ml-2 text-black">${item.material_nombre}</span></p>
            </div>
            <div class="flex items-center space-x-2 mb-1">
                <button class="border-2 border-red-500 rounded-md" type="button" onclick="removeMaterial(this, ${item.id_material})">
                    <svg class="w-7 h-7 text-red-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z"/>
                    </svg>
                </button>
                <div class="flex items-center justify-center text-[12px] text-center font-bold rounded h-8 w-[120px] text-white bg-gradient-to-tr from-red-600 to-red-400">
                    ${item.unidades}: ${item.cantidad}
                </div>
                <input step="any"
                       onkeydown="if(['e','E','+','-'].includes(event.key)) event.preventDefault();"
                       value="1"
                       max="${item.cantidad}"
                       min="1"
                       name="materiales[${materialIndex}][cantidad]" 
                       placeholder="Cantidad"
                       class="text-sm py-1 px-4 border outline-none border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 
                       focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full"
                       onchange="calculateTotal(this, ${item.cantidad})">
            </div>
            <input type="hidden" name="materiales[${materialIndex}][cobro]" value="1">
            <div class="flex bg-gradient-to-r text-left justify-between from-slate-200 to-slate-100 rounded p-1 w-full">
                <p class="font-medium">Costo: ${formatCurrency(item.valor_unidad)} * 
                <span class="quantity">1</span> = <b class="text-red-500 total">${formatCurrency(item.valor_unidad)}</b></p>
                <p>Tipo: ${item.spanTipo}</p>
            </div>
            <input type="hidden" name="materiales[${materialIndex}][id_material]" value="${item.id_material}">
            <input type="hidden" name="materiales[${materialIndex}][valor_unidad]" value="${item.valor_unidad}">
            <input type="hidden" name="materiales[${materialIndex}][valor_inventario]" value="${item.valor_inventario}">
            <input type="hidden" name="materiales[${materialIndex}][id_ref_devolucion]" value="${item.id}">
        `;

            groupDiv.appendChild(materialDiv);
        });

        container.appendChild(groupDiv);
    });
}

window.removeGroup = function(button) {
    const groupDiv = button.closest('[data-codigo]');
    if (!groupDiv) return;
    // eliminar materiales del Set
    groupDiv.querySelectorAll('[data-material-id]').forEach(el => {
        const id = el.getAttribute('data-material-id');
        addedMaterials.delete(Number(id));
    });
    groupDiv.remove();
};


document.getElementById('formDespachos').addEventListener('submit', function (e) {
    e.preventDefault();

    Swal.fire({
        title: '¿Deseas realizar el Despacho o Devolución?',
        text: 'Esta acción guardará los ítems seleccionados.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            submitForm();
        }
    });
});

/* ==============================
   ENVÍO DEL FORMULARIO (API)
================================*/
async function submitForm() {
    clearErrors();

    const form = document.getElementById('formDespachos');
    const formData = new FormData(form);

    // 🔄 Spinner de carga
    Swal.fire({
        title: 'Procesando...',
        text: 'Por favor espera',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        });

        if (response.status === 422) {
            Swal.close();
            const data = await response.json();
            showValidationErrors(data.errors);
            return;
        }

        if (!response.ok) {
            throw new Error('Error inesperado');
        }

        const data = await response.json();

        Swal.fire({
            icon: 'success',
            title: 'Proceso exitoso',
            text: data.message,
            confirmButtonText: 'Aceptar'
        }).then(() => {
            // 👉 si quieres abrir el PDF
            if (data.data?.pdf_url) {
                window.open(data.data.pdf_url, '_blank');
            }
            location.reload();
        });

    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ocurrió un error al procesar la solicitud'
        });
    }
}

/* ==============================
   MOSTRAR ERRORES
================================*/
function showValidationErrors(errors) {
    let globalMessages = [];

    Object.keys(errors).forEach((key) => {
        const messages = errors[key];
        const field = getFieldByName(key);

        if (!field) {
            globalMessages.push(messages[0]);
            return;
        }

        field.classList.add('border-red-500');

        const error = document.createElement('p');
        error.className = 'mt-1 text-sm text-red-600 error-message';
        error.innerText = messages[0];

        field.parentNode.appendChild(error);
    });

    if (globalMessages.length) {
        Swal.fire({
            icon: 'error',
            title: 'Errores de validación',
            html: globalMessages.join('<br>')
        });
    }
}

/* ==============================
   LIMPIAR ERRORES
================================*/
function clearErrors() {
    document.querySelectorAll('.error-message').forEach(el => el.remove());

    document.querySelectorAll('input, select, textarea')
        .forEach(el => el.classList.remove('border-red-500'));
}

function getFieldByName(name) {
    const formattedName = name.replace(/\.(\d+)\./g, '[$1][') + ']';
    return document.querySelector(
        `[name="${name}"], [name="${formattedName}"]`
    );
}