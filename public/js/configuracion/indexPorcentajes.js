document.getElementById('formValorArea').addEventListener('submit', function (e) {
    e.preventDefault();

    Swal.fire({
        title: '¿Deseas guardar los cambios?',
        text: 'Esta acción guardará la configuración del año seleccionado',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, guardar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            submitForm();
        }
    });
});

/* ==============================
   ENVÍO DEL FORMULARIO
================================*/
async function submitForm() {
    clearErrors();

    const form = document.getElementById('formValorArea');
    const formData = new FormData(form);

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
            title: 'Guardado',
            text: 'La configuración fue guardada correctamente',
        });

        location.reload();
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ocurrió un error al procesar la solicitud',
        });
    }
}

function showValidationErrors(errors) {
    let globalMessages = [];

    Object.keys(errors).forEach((key) => {
        const messages = errors[key];
        const field = getFieldByName(key);

        // Error global
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

    document.querySelectorAll(
        'input, select, textarea'
    ).forEach(el => {
        el.classList.remove('border-red-500');
    });
}

function getFieldByName(name) {
    // items.0.area_min → items[0][area_min]
    const formattedName = name.replace(/\.(\d+)\./g, '[$1][') + ']';

    return document.querySelector(
        `[name="${name}"], [name="${formattedName}"]`
    );
}

document.addEventListener('DOMContentLoaded', () => {
    const btnAdd = document.getElementById('btnAddItem');
    const container = document.getElementById('importarAño');
    const template = document.getElementById('item-template').innerHTML;

    btnAdd.addEventListener('click', () => {
        const deleteDiv = container.querySelector('.deleteDiv');
        if(btnAdd.dataset.empy == 1 && deleteDiv){
            container.innerHTML = '';
        };
        const index = container.querySelectorAll('[name^="items["]').length
            ? Math.max(...[...container.querySelectorAll('[name^="items["]')]
                .map(i => parseInt(i.name.match(/items\[(\d+)\]/)[1])))
                + 1
            : 0;
        const html = template.replaceAll('__INDEX__', index);
        container.insertAdjacentHTML('beforeend', html);
    });
    

    const selectImporte = document.getElementById('importe_año');
    if (!selectImporte) return;

    selectImporte.addEventListener('change', async () => {
        const year = selectImporte.value;
        const type = selectImporte.dataset.type;

        if (!year || !type) return;

        try {
            const response = await fetch(
                `/configuracion/listConfigYearModel/${type}/${year}`,
                { headers: { 'Accept': 'application/json' } }
            );

            const json = await response.json();

            if (!json.status) {
                throw new Error('No se pudo importar la configuración');
            }

            // Limpiar items actuales
            container.innerHTML = '';

            let index = 0;

            json.data.forEach(item => {
                let html = template.replaceAll('__INDEX__', index);

                const wrapper = document.createElement('div');
                wrapper.innerHTML = html;
                const block = wrapper.firstElementChild;

                // 🔥 Rellenar valores
                block.querySelector('[name$="[concepto]"]').value = item.concepto ?? '';
                block.querySelector('[name$="[porcentage]"]').value = item.area_max ?? '';
                block.querySelector('[name$="[descripccion]"]').value = item.descripccion ?? '';

                // 🔥 Forzar ID vacío (importado)
                block.querySelector('[name$="[id]"]').value = '';

                container.appendChild(block);
                index++;
            });

        } catch (error) {
            console.error(error);
            alert('Error al importar la configuración');
        }
    });
});

document.addEventListener('click', function (e) {
    const btn = e.target.closest('.remove-item');
    if (!btn) return;

    const block = btn.closest('.item-block');

    Swal.fire({
        title: '¿Eliminar intervalo?',
        text: 'Este cambio se perderá si no guardas',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Eliminar',
        cancelButtonText: 'Cancelar'
    }).then(r => {
        if (r.isConfirmed && block) {
            block.remove();
        }
    });
});
