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
