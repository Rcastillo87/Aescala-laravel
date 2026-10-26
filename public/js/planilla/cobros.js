document.addEventListener("DOMContentLoaded", () => {

    const form = document.getElementById("formSaveCobros");
    if (!form) return;

    const fmt = (n) => "$ " + Number(n).toLocaleString("es-CO");

    // =========================================================
    // 🔄 Sincronización dinámica Valor <-> Aprobación
    // =========================================================
    const cobroItems = form.querySelectorAll('.cobro-item');

    cobroItems.forEach(item => {
        const idx = item.dataset.idx;
        const selectValor = item.querySelector(`#cobro-valor-${idx}`);
        const labelValor = item.querySelector(`#cobro-valor-label-${idx}`);
        const hiddenValor = item.querySelector(`#cobro-valor-hidden-${idx}`);
        
        if (!selectValor) return;

        const tope = Number(selectValor.dataset.tope);
        const radioAprob = item.querySelectorAll(`input[name="cobros[${idx}][aprobacion]"]`);
        
        // Buscar el label del radio 50%
        const radio50 = item.querySelector(`input[name="cobros[${idx}][aprobacion]"][value="50"]`);
        const radio50Label = radio50 ? radio50.closest('label') : null;
        const radio0 = item.querySelector(`input[name="cobros[${idx}][aprobacion]"][value="0"]`);

        // Sincronizar el estado visual del select (bloqueado o no)
        function syncValorUI(isLocked) {
            if (isLocked) {
                selectValor.classList.add('hidden');
                selectValor.disabled = true;
                
                hiddenValor.value = selectValor.value;
                hiddenValor.disabled = false;

                labelValor.innerText = selectValor.value == 0 ? '$ 0' : fmt(selectValor.value);
                labelValor.classList.remove('hidden');
            } else {
                selectValor.classList.remove('hidden');
                selectValor.disabled = false;
                
                hiddenValor.disabled = true;
                
                labelValor.classList.add('hidden');
            }
        }

        // Manejar la visibilidad del radio 50%
        function syncRadio50UI() {
            if (!radio50Label) return;
            
            const isMitad = Number(selectValor.value) === (tope / 2);
            
            if (isMitad) {
                radio50Label.classList.add('hidden');
                if (radio50.checked && radio0) {
                    radio0.checked = true; // Forzar a Sin Aprobar
                    syncValorUI(false);    // Desbloquear el valor
                }
            } else {
                radio50Label.classList.remove('hidden');
            }
        }

        // 1. Evento cuando cambia el valor a cobrar
        selectValor.addEventListener('change', () => {
            syncRadio50UI();
        });

        // 2. Evento cuando cambia la aprobación
        radioAprob.forEach(radio => {
            radio.addEventListener('change', () => {
                if (Number(radio.value) > 0) {
                    syncValorUI(true);
                } else {
                    syncValorUI(false);
                }
            });
        });

        // 3. Inicialización (por si viene bloqueado de la BD o es la mitad)
        const aprobChecked = item.querySelector(`input[name="cobros[${idx}][aprobacion]"]:checked`);
        if (aprobChecked && Number(aprobChecked.value) > 0) {
            syncValorUI(true);
        }
        syncRadio50UI();
    });

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