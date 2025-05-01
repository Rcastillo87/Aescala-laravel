document.addEventListener("DOMContentLoaded", function() {
    const departamentos = window.departamentos;

    document.getElementById('departamento').addEventListener('change', function() {
        let deptoId = this.value;
        let ciudadSelect = document.getElementById('ciudad');
        ciudadSelect.innerHTML = '<option value="">-- Seleccione --</option>';

        if (deptoId !== "") {
            let ciudades = departamentos.find(depto => depto.id == deptoId)?.ciudades || [];
            ciudades.forEach((ciudad, index) => {
                let option = document.createElement('option');
                option.value = index;
                option.textContent = ciudad;
                ciudadSelect.appendChild(option);
            });
        }
    });
});


document.addEventListener('DOMContentLoaded', function() {
    // Elementos del DOM
    const conFechaFin = document.getElementById('conFechaFin');
    const fechaFinContainer = document.getElementById('fechaFinContainer');
    const diasTrabajoContainer = document.getElementById('diasTrabajoContainer');
    const fechaFinInput = document.getElementById('fec_fin_estimado');
    const diasTrabajoInput = document.getElementById('dias_trabajo');

    // Función para alternar visibilidad
    function toggleFields() {
        if (conFechaFin.checked) {
            fechaFinContainer.classList.remove('hidden');
            diasTrabajoContainer.classList.add('hidden');
            fechaFinInput.required = true;
            diasTrabajoInput.required = false;
            if(diasTrabajoInput.value < '1'){
                diasTrabajoInput.value = '1';
            }
        } else {
            fechaFinContainer.classList.add('hidden');
            diasTrabajoContainer.classList.remove('hidden');
            fechaFinInput.required = false;
            diasTrabajoInput.required = true;
        }
    }

    // Event listener para el checkbox
    conFechaFin.addEventListener('change', toggleFields);

    // Inicializar el estado
    toggleFields();

    // Inicializar el datepicker de Flowbite
    if (typeof window.Datepicker !== 'undefined') {
        new Datepicker(fechaFinInput, {
            format: 'yyyy-mm-dd',
            autohide: true
        });
    }
});