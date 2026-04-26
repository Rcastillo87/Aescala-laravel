/**
 * ──────────────────────────────────────────────────────────────────────────────
 * FormManager — Gestor avanzado de formularios con Fetch + SweetAlert2
 * ──────────────────────────────────────────────────────────────────────────────
 *
 * 📌 Descripción
 * ------------------------------------------------------------------------------
 * Componente reutilizable para manejar envíos de formularios de forma estándar:
 *
 * ✔ Confirmación previa con SweetAlert2
 * ✔ Envío vía Fetch API (AJAX)
 * ✔ Manejo automático de errores Laravel (422)
 * ✔ Manejo de sesión expirada (401 / 419)
 * ✔ Prevención de envíos duplicados
 * ✔ Loader global configurable
 * ✔ Soporte para payload dinámico
 * ✔ Restauración automática del botón (incluyendo HTML como SVG)
 *
 * Compatible con:
 * - Laravel 10 / 11+
 * - SweetAlert2
 * - Tailwind / Flowbite
 * - WebView Android
 *
 * ──────────────────────────────────────────────────────────────────────────────
 * 🚀 Uso básico
 * ------------------------------------------------------------------------------
 *
 * FormManager.init('#miFormulario');
 *
 *
 * ──────────────────────────────────────────────────────────────────────────────
 * ⚙️ Opciones disponibles
 * ------------------------------------------------------------------------------
 *
 * confirmText: string | function(form)
 *  → Texto del modal de confirmación
 *
 * payload: object | function | async function
 *  → Datos adicionales enviados junto al formulario
 *
 * loadingText: string | function(form)
 *  → Texto temporal del botón mientras se envía
 *
 * acepAlertText: string | function(form)
 *  → Texto del botón de confirmación en SweetAlert
 *
 * submitSelector: string
 *  → Selector del botón submit dentro del formulario
 *
 * confirmColor / cancelColor:
 *  → Colores de botones en SweetAlert
 *
 * loginUrl:
 *  → URL de redirección si la sesión expira
 *
 * onSuccess(data, form)
 *  → Callback al enviar correctamente (ANTES del Swal success)
 *
 * onError(errors, form)
 *  → Callback cuando Laravel retorna errores 422
 *
 * onFinally(form)
 *  → Callback final siempre ejecutado
 *
 * autoRedirect: boolean
 *  → true → redirige automáticamente (data.url o reload)
 *  → false → tú controlas todo en onSuccess
 *
 *
 * ──────────────────────────────────────────────────────────────────────────────
 * 🧠 Comportamiento interno importante
 * ------------------------------------------------------------------------------
 *
 * ✔ El botón submit:
 *    - Se bloquea durante el envío
 *    - Cambia temporalmente su contenido (innerHTML)
 *    - Se restaura completamente (incluye SVG)
 *
 * ✔ Si cancelas en el Swal:
 *    - El botón vuelve a su estado original
 *
 * ✔ Si hay error (422):
 *    - Se muestran errores en inputs
 *    - NO se hace redirect
 *
 * ✔ Si sesión expira:
 *    - Se bloquea UI
 *    - Se redirige al login
 *
 * ✔ Prevención de doble envío:
 *    - Usa flag interno + AbortController
 *
 *
 * ──────────────────────────────────────────────────────────────────────────────
 * 📦 EJEMPLOS DE USO (AVANZADOS)
 * ------------------------------------------------------------------------------
 *
 * // 1. Uso básico
 * ---------------------------------------------------------------------------
 * FormManager.init('#formUser');
 *
 *
 * // 2. Confirmación personalizada
 * ---------------------------------------------------------------------------
 * FormManager.init('#formUser', {
 *   confirmText: '¿Deseas guardar los cambios del usuario?'
 * });
 *
 *
 * // 3. Payload dinámico (sincronico)
 * ---------------------------------------------------------------------------
 * FormManager.init('#formConsent', {
 *   payload: {
 *     signatures: JSON.stringify(firmas),
 *     origen: 'web'
 *   }
 * });
 *
 *
 * // 4. Payload dinámico (función)
 * ---------------------------------------------------------------------------
 * FormManager.init('#formConsent', {
 *   payload: () => ({
 *     signatures: JSON.stringify(recogerFirmas()),
 *     timestamp: Date.now()
 *   })
 * });
 *
 *
 * // 5. Payload async (ej: OCR, firma, API externa)
 * ---------------------------------------------------------------------------
 * FormManager.init('#formConsent', {
 *   payload: async () => {
 *     const firmas = await procesarFirmasAsync();
 *     return {
 *       signatures: JSON.stringify(firmas),
 *       tipo: 'biometrico'
 *     };
 *   }
 * });
 *
 *
 * // 6. Control manual del flujo (sin redirect automático)
 * ---------------------------------------------------------------------------
 * FormManager.init('#formUpdate', {
 *   autoRedirect: false,
 *   onSuccess: async (data, form) => {
 *     await Swal.fire({
 *       icon: 'success',
 *       title: 'Guardado',
 *       text: data.message
 *     });
 *
 *     // lógica personalizada
 *     actualizarTabla(data.registro);
 *   }
 * });
 *
 *
 * // 7. Flujo con recarga manual (tu caso típico)
 * ---------------------------------------------------------------------------
 * FormManager.init('#formPassword', {
 *   autoRedirect: false,
 *   confirmText: '¿Deseas cambiar tu contraseña?',
 *
 *   onSuccess: async (data) => {
 *     await Swal.fire({
 *       icon: 'success',
 *       title: 'Éxito',
 *       text: data.message || 'Contraseña actualizada'
 *     });
 *
 *     window.location.reload();
 *   }
 * });
 *
 *
 * // 8. Integración con modal (Flowbite)
 * ---------------------------------------------------------------------------
 * FormManager.init('#formModal', {
 *   autoRedirect: false,
 *   onSuccess: async (data) => {
 *     await Swal.fire({
 *       icon: 'success',
 *       title: 'Guardado'
 *     });
 *
 *     document.getElementById('btnCloseModal').click();
 *   }
 * });
 *
 *
 * // 9. Validación adicional en frontend antes de enviar
 * ---------------------------------------------------------------------------
 * FormManager.init('#formUser', {
 *   onSuccess: (data, form) => {
 *     if (!form.checkValidity()) {
 *       return false; // cancela flujo success
 *     }
 *   }
 * });
 *
 *
 * // 10. Múltiples formularios en la misma vista
 * ---------------------------------------------------------------------------
 * FormManager.init('#formA');
 * FormManager.init('#formB');
 * FormManager.init('#formC');
 *
 *
 * // 11. Uso en WebView Android (evitar reload automático)
 * ---------------------------------------------------------------------------
 * FormManager.init('#formMobile', {
 *   autoRedirect: false,
 *   onSuccess: async (data) => {
 *     await Swal.fire({
 *       icon: 'success',
 *       text: data.message
 *     });
 *
 *     // comunicar con Android
 *     window.Android?.onSuccess(JSON.stringify(data));
 *   }
 * });
 *
 *
 * // 12. Manejo de errores personalizado
 * ---------------------------------------------------------------------------
 * FormManager.init('#formUser', {
 *   onError: (errors) => {
 *     console.log('Errores Laravel:', errors);
 *   }
 * });
 *
 *
 * ──────────────────────────────────────────────────────────────────────────────
 * 🧩 Buenas prácticas
 * ------------------------------------------------------------------------------
 *
 * ✔ No mezclar submit normal + FormManager
 * ✔ Siempre usar @csrf en Laravel
 * ✔ Evitar múltiples inicializaciones del mismo form
 * ✔ Usar autoRedirect:false si manejas UI dinámica
 * ✔ Usar payload para datos no visibles en el form
 *
 * ──────────────────────────────────────────────────────────────────────────────
 */

const FormManager = (() => {
    const _state = new WeakMap();

    const DEFAULTS = {
        confirmText: null,
        payload: null,
        responseType: 'json',

        loadingText: "Enviando información...",
        acepAlertText: "Guardar",
        submitSelector: 'button[type="submit"]',

        confirmColor: "#242e68",
        cancelColor: "#fa376c",

        loginUrl: window.ROUTE??'' + "/login",

        onSuccess: null, // (data, form) => void
        onError: null, // (errors, form) => void
        onFinally: null, // (form) => void

        // Redirección automática tras éxito (a data.url o reload)
        autoRedirect: true,

        // Loader global
        showLoader: (msg) => typeof showLoader === "function" && showLoader(msg),
        hideLoader: () => typeof hideLoader === "function" && hideLoader(),

        // Validación Laravel
        clearErrors: (form) => typeof limpiarErroresValidacion === "function" && limpiarErroresValidacion(form),
        showErrors: (form, errors) => typeof mostrarErroresValidacion === "function" && mostrarErroresValidacion(form, errors),
    };

    function _lockButton(btn, text) {
        btn.dataset.locked = "true";
        btn.disabled = true;
        btn.innerHTML  = text;
    }

    function _unlockButton(btn) {
        btn.disabled = false;
        btn.dataset.locked = "";
        btn.innerHTML  = btn._originalText || "Guardar";
    }

    // ─── Manejo de sesión expirada ────────────────────────────────────────────────
    async function _handleSessionExpired(loginUrl, confirmColor) {
        // Ocultar loader y bloquear UI mientras se muestra el Swal
        typeof hideLoader === "function" && hideLoader();
        document.querySelectorAll('button[type="submit"]').forEach((btn) => {
            btn.disabled = true;
            btn.textContent = "Sesión expirada...";
        });

        await Swal.fire({
            icon: "warning",
            title: "Sesión expirada",
            text: "Tu sesión ha expirado. Por favor inicia sesión nuevamente.",
            confirmButtonText: "Ir al login",
            confirmButtonColor: confirmColor,
            allowOutsideClick: false,
            allowEscapeKey: false,
        });

        // replace() evita que quede el formulario en el historial
        window.location.replace(loginUrl);
    }

    // ─── Detecta si la respuesta es HTML (302 seguido al login) ──────────────────
    function _isHtmlResponse(response) {
        const ct = response.headers.get("Content-Type") ?? "";
        return ct.includes("text/html") || ct.includes("application/xhtml+xml");
    }

    // ─── init ────────────────────────────────────────────────────────────────────
    function init(formSelector, userOptions = {}) {
        const form =
            typeof formSelector === "string"
                ? document.querySelector(formSelector)
                : formSelector;

        if (!form) {
            console.warn(
                `[FormManager] Formulario no encontrado: ${formSelector}`,
            );
            return;
        }

        if (_state.has(form)) {
            console.warn("[FormManager] Este formulario ya fue inicializado.");
            return;
        }

        const opts = { ...DEFAULTS, ...userOptions };
        const formState = { requestInProgress: false, controller: null };
        _state.set(form, formState);

        form.addEventListener("submit", async (e) => {
            e.preventDefault();

            const submitBtn = form.querySelector(opts.submitSelector);
            if (!submitBtn || submitBtn.dataset.locked) return;

            // guardar texto original UNA sola vez
            if (!submitBtn._originalText) {
                submitBtn._originalText = submitBtn.innerHTML;
            }

            const loadingText =
                typeof opts.loadingText === "function"
                    ? opts.loadingText(form)
                    : opts.loadingText;

            const acepAlertText =
                typeof opts.acepAlertText === "function"
                    ? opts.acepAlertText(form)
                    : opts.acepAlertText;

            _lockButton(submitBtn, loadingText);

            // ── Confirmación Swal ─────────────────────────────────────────────────────
            //const swalText = opts.confirmText ?? 'Se procederá a realizar la operación.';
            const swalText =
                typeof opts.confirmText === "function"
                    ? opts.confirmText(form)
                    : (opts.confirmText ??
                      "Se procederá a realizar la operación.");

            const confirmed = await Swal.fire({
                title: "¿Está seguro?",
                text: swalText,
                icon: "warning",
                reverseButtons: true,
                showCancelButton: true,
                confirmButtonText: acepAlertText,
                confirmButtonColor: opts.confirmColor,
                cancelButtonColor: opts.cancelColor,
                cancelButtonText: "Cancelar",
            });

            if (!confirmed.isConfirmed) {
                _unlockButton(submitBtn);
                return;
            }

            // ── Prevención de duplicados ──────────────────────────────────────────────
            if (formState.requestInProgress) {
                console.log("[FormManager] Envío duplicado bloqueado.");
                return;
            }
            formState.requestInProgress = true;

            opts.showLoader(loadingText);
            opts.clearErrors(form);

            if (formState.controller) formState.controller.abort();
            formState.controller = new AbortController();

            // ── Armar FormData + payload extra ────────────────────────────────────────
            const formData = new FormData(form);

            if (opts.payload) {
                const extra =
                    typeof opts.payload === "function"
                        ? await opts.payload()
                        : opts.payload;

                Object.entries(extra).forEach(([key, value]) => {
                    formData.append(key, value);
                });
            }

            const csrfToken =
                form.querySelector('[name="_token"]')?.value ??
                document.querySelector('meta[name="csrf-token"]')?.content ??
                "";

            let redirected = false;

            try {
                const response = await fetch(form.action, {
                    method: form.method?.toUpperCase() || "POST",
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                        Accept: "application/json",
                    },
                    body: formData,
                    signal: formState.controller.signal,
                });

                let data;

                if (
                    response.status === 401 ||
                    response.status === 419 ||
                    _isHtmlResponse(response)
                ) {
                    redirected = true;
                    await _handleSessionExpired(
                        opts.loginUrl,
                        opts.confirmColor,
                    );
                    return;
                }

                if (opts.responseType === 'blob') {
                    data = await response.blob();
                } else {
                    try {
                        data = await response.json();
                    } catch {
                        throw new Error("Respuesta inválida del servidor");
                    }
                }

                if (response.status === 422) {
                    opts.clearErrors(form);
                    opts.showErrors(form, data.errors);
                    if (opts.onError) opts.onError(data.errors, form);
                    return;
                }

                if (!response.ok) {
                    throw new Error(data.message || `Error en el servidor`);
                }

                opts.hideLoader();
                _unlockButton(submitBtn);
                formState.requestInProgress = false;

                if (opts.onSuccess) {
                    const result = await opts.onSuccess(data, form);
                    if (result === false) return;
                }

                await Swal.fire({
                    icon: "success",
                    title: "¡Éxito!",
                    text: data.message ?? "Operación realizada correctamente",
                    confirmButtonColor: opts.confirmColor,
                });

                if (opts.autoRedirect) {
                    redirected = true;
                    data.url
                        ? window.location.assign(data.url)
                        : window.location.reload();
                }
            } catch (error) {
                if (error.name === 'AbortError' || error.message === '__handled__') {
                    return; // ← NUEVO: errores ya notificados al usuario
                }

                if (error.name === "AbortError") {
                    console.log("[FormManager] Petición cancelada.");
                    return;
                }
                console.error("[FormManager]", error);
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: error.message ?? "Ocurrió un error inesperado.",
                    confirmButtonColor: opts.confirmColor,
                });
            } finally {
                if (!redirected) {
                    opts.hideLoader();
                    _unlockButton(submitBtn);
                    formState.requestInProgress = false;
                }
                if (opts.onFinally) opts.onFinally(form);
            }
        });

        console.log(`[FormManager] Inicializado en: ${formSelector}`);
    }

    // ─── destroy ─────────────────────────────────────────────────────────────────
    function destroy(formSelector) {
        const form =
            typeof formSelector === "string"
                ? document.querySelector(formSelector)
                : formSelector;
        if (form && _state.has(form)) {
            _state.delete(form);
            console.log(`[FormManager] Destruido: ${formSelector}`);
        }
    }

    return { init, destroy };
})();

// ─── Validación de formularios ────────────────────────────────────────────────

function showError(input, message) {
    const errorDiv = input.parentElement.querySelector(".input-error-dynamic");
    if (errorDiv) errorDiv.remove();
    if (message) {
        const p = document.createElement("p");
        p.classList.add(
            "text-red-500",
            "text-sm",
            "mt-1",
            "input-error-dynamic",
        );
        p.innerText = message;
        input.classList.remove(
            "focus:border-indigo-500",
            "focus:ring-indigo-500",
        );
        input.classList.add(
            "border-red-500",
            "focus:border-red-500",
            "focus:ring-red-500",
        );
        input.parentElement.appendChild(p);
    } else {
        input.classList.add("focus:border-indigo-500", "focus:ring-indigo-500");
        input.classList.remove(
            "border-red-500",
            "focus:border-red-500",
            "focus:ring-red-500",
        );
    }
}

function limpiarErroresValidacion(form) {
    form.querySelectorAll(".input-error-dynamic").forEach((e) => e.remove());
    form.querySelectorAll("input, select, textarea").forEach((input) =>
        showError(input, null),
    );
}

function mostrarErroresValidacion(form, errores) {
    let primerInputConError = null;
    for (const campo in errores) {
        const input = form.querySelector(`[name="${campo}"]`);
        if (input) {
            showError(input, errores[campo][0]);
            if (!primerInputConError) primerInputConError = input;
        }
    }
    if (primerInputConError) primerInputConError.focus();
    Swal.fire({
        icon: "error",
        title: "Errores de validación",
        text: "Revisa los campos marcados en rojo.",
    });
}

