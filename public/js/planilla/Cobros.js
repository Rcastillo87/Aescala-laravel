document.addEventListener("DOMContentLoaded", () => {

    const form = document.getElementById("formSaveCobros");
    if (!form) return;

    const fmt = (n) => "$ " + Number(n).toLocaleString("es-CO");

    // =========================
    // 🔴 Aviso en vivo si el valor supera el tope del ítem
    // =========================
    form.addEventListener("input", (e) => {
        const input = e.target.closest('input[type="number"][data-tope]');
        if (!input) return;

        const excede = Number(input.value) > Number(input.dataset.tope);

        input.classList.toggle("border-red-500", excede);
        input.classList.toggle("focus:ring-red-500", excede);

        const aviso = input.closest(".cobro-item")?.querySelector(".aviso-tope");
        if (aviso) aviso.classList.toggle("hidden", !excede);
    });

    // =========================
    // ✅ Validación antes de enviar
    // (fase de captura: corre antes que FormManager y, si algo falla, lo detiene)
    // =========================
    form.addEventListener("submit", (e) => {

        const detener = (titulo, mensaje, icono = "error") => {
            e.preventDefault();
            e.stopImmediatePropagation();
            Swal.fire(titulo, mensaje, icono);
        };

        // Solo se envían los campos habilitados para este rol y estado del ítem.
        const habilitados = form.querySelectorAll('[name^="cobros["]:not(:disabled)');
        if (habilitados.length === 0) {
            return detener("Sin cambios", "No hay campos habilitados para guardar.", "info");
        }

        const inputsValor = form.querySelectorAll('input[type="number"][data-tope]:not(:disabled)');
        for (const input of inputsValor) {
            const raw = input.value.trim();
            if (raw === "") continue;

            const valor = Number(raw);
            const tope = Number(input.dataset.tope);
            const concepto = input.dataset.concepto || "Ítem";

            if (isNaN(valor) || valor < 0) {
                return detener("Valor inválido", `"${concepto}": ingrese un valor numérico válido.`);
            }

            if (valor > tope) {
                return detener(
                    "Valor excedido",
                    `"${concepto}": el valor a cobrar no puede superar ${fmt(tope)}.`
                );
            }
        }
    }, true);

    // =========================
    // 💾 Envío (mismo manejo que el resto de la planilla) 
    // =========================
    FormManager.init(form, {
        confirmText: "Se guardarán los cobros del proyecto.",
        acepAlertText: "Sí, guardar",
        loadingText: "Guardando cobros...",
        autoRedirect: true, // recarga la página al terminar
    });

});