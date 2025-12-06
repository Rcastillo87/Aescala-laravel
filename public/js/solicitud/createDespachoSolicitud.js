document.addEventListener("DOMContentLoaded", function () {

    const btn = document.getElementById('btnEnviar');
    const form = document.getElementById('formSolicitud');

    btn.addEventListener('click', function (e) {
        e.preventDefault();

        let accion = btn.textContent.trim();

        let mensaje = accion === 'Despachar'
            ? '¿Deseas despachar los materiales solicitados?'
            : '¿Deseas aprobar los items solicitados?';

        Swal.fire({
            title: 'Confirmar acción',
            text: mensaje,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: accion,
            cancelButtonText: 'Cancelar',
        }).then(result => {

            if (result.isConfirmed) {

                Swal.fire({
                    title: accion + '...',
                    text: 'Por favor espera',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                // Aquí SI se envía el formulario
                form.submit();
            }

        });
    });

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

