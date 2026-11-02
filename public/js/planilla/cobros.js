document.addEventListener("DOMContentLoaded", () => {

    const form = document.getElementById("formSaveCobros");
    if (!form) return;

    const fmt = (n) => "$ " + Number(n).toLocaleString("es-CO");

    // =========================================================
    // 🔄 Sincronización dinámica Valor <-> Aprobación
    // =========================================================
    const cobroItems = form.querySelectorAll('.cobro-item');

    cobroItems.forEach(item => {
        const tipo = item.dataset.tipo;
        const idx = item.dataset.idx;
        
        const selectValor = item.querySelector(`#cobro-valor-${tipo}-${idx}`);
        const labelValor = item.querySelector(`#cobro-valor-label-${tipo}-${idx}`);
        const hiddenValor = item.querySelector(`#cobro-valor-hidden-${tipo}-${idx}`);
        
        if (!selectValor) return;

        const tope = Number(selectValor.dataset.tope);
        const radioAprob = item.querySelectorAll(`input[name="cobros[${tipo}][${idx}][aprobacion]"]`);
        
        const radio50 = item.querySelector(`input[name="cobros[${tipo}][${idx}][aprobacion]"][value="50"]`);
        const radio50Label = radio50 ? radio50.closest('label') : null;
        const radio0 = item.querySelector(`input[name="cobros[${tipo}][${idx}][aprobacion]"][value="0"]`);

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

        function syncRadio50UI() {
            if (!radio50Label) return;
            
            const isMitad = Number(selectValor.value) === (tope / 2);
            
            if (isMitad) {
                radio50Label.classList.add('hidden');
                if (radio50.checked && radio0) {
                    radio0.checked = true;
                    syncValorUI(false);
                }
            } else {
                radio50Label.classList.remove('hidden');
            }
        }

        selectValor.addEventListener('change', () => {
            syncRadio50UI();
        });

        radioAprob.forEach(radio => {
            radio.addEventListener('change', () => {
                if (Number(radio.value) > 0) {
                    syncValorUI(true);
                } else {
                    syncValorUI(false);
                }
            });
        });

        const aprobChecked = item.querySelector(`input[name="cobros[${tipo}][${idx}][aprobacion]"]:checked`);
        if (aprobChecked && Number(aprobChecked.value) > 0) {
            syncValorUI(true);
        }
        syncRadio50UI();
    });

    // =========================
    // ✅ Validación antes de enviar
    // =========================
    form.addEventListener("submit", (e) => {

        const detener = (titulo, mensaje, icono = "error") => {
            e.preventDefault();
            e.stopImmediatePropagation();
            Swal.fire(titulo, mensaje, icono);
        };

        const habilitados = form.querySelectorAll('[name^="cobros["]:not(:disabled)');
        if (habilitados.length === 0) {
            return detener("Sin cambios", "No hay campos habilitados para guardar.", "info");
        }
    }, true);

    // =========================
    // 💾 Envío 
    // =========================
    FormManager.init(form, {
        confirmText: "Se guardarán los cobros del proyecto.",
        acepAlertText: "Sí, guardar",
        loadingText: "Guardando cobros...",
        autoRedirect: true,
    });

});