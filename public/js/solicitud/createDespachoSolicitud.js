document.getElementById('btnEnviar').addEventListener('click', function () {
    // Mostrar SweetAlert cargando
    Swal.fire({
        title: 'Enviando...',
        text: 'Por favor espera',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    // Enviar formulario manualmente
    document.getElementById('miFormulario').submit();
});

document.addEventListener("DOMContentLoaded", () => {

    // Cancelar despacho
    document.querySelectorAll("input[type=checkbox][name*='cancelo']").forEach(chk => {
        chk.addEventListener("change", function () {

            const card = this.closest(".item-card");
            const inputCantidad = card.querySelector(".input-cantidad");

            if (this.checked) {
                inputCantidad.disabled = true;
                inputCantidad.classList.add("bg-gray-200", "cursor-not-allowed");
                card.classList.add("opacity-60");
            } else {
                inputCantidad.disabled = false;
                inputCantidad.classList.remove("bg-gray-200", "cursor-not-allowed");
                card.classList.remove("opacity-60");
            }

        });
    });

    // Actualizar pendiente
    document.querySelectorAll(".input-cantidad").forEach(input => {
        input.addEventListener("input", function () {

            const card = this.closest(".item-card");
            const cantidadOriginal = parseInt(this.dataset.inv);
            const cantidadActual = parseInt(this.value) || 0;
            let pendiente = cantidadOriginal - cantidadActual;
            if (pendiente < 0) pendiente = 0;
            card.querySelector(".pendiente-value").textContent = pendiente;
        });
    });

});

