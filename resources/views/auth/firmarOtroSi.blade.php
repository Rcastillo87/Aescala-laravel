<x-guest-layout>
    <div class="flex items-center justify-center min-h-screen w-full bg-gray-100 px-4">
        <div class="bg-white rounded-lg shadow-lg p-8 max-w-5xl w-full text-center relative">
            <!-- Logo -->
            <div class="flex justify-center mb-3">
                <img src="{{ asset('img/logo.png') }}" alt="logo" class="w-[200px] h-[100px] object-contain">
            </div>

            <!-- Título -->
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Otrosí N° {{ $otroSi->numero }} Del Proyecto: {{ $otroSi->proyecto->nombre_proyecto }}</h1>

            <!-- Subtítulo -->
            <p class="text-center text-gray-600 text-sm">
                Términos, Condiciones y Obligaciones del Otrosí
            </p>

            {{-- Contenido del contrato --}}
            <div class="contrato-container border border-gray-300 rounded-xl p-4 mt-6">
                @include('otrosi.pdfOtroSi', $data)
            </div>

            <input type="hidden" name="id_otro_si" id="id_otro_si" value="{{ old('img_firma', $otroSi->id) }}">

            {{-- Si no hay firma, se habilita el pad --}}
            @if ($otroSi->estado == 0)
                <div class="w-full justify-center mt-4">

                    <div class="justify-between space-x-4">
                        <x-primary-button class="my-2 py-1 px-1"
                            href="#"
                            data-tooltip-target="tooltip-hover-Entregable"
                            data-tooltip-trigger="hover"
                            x-data=""
                            x-on:click="$dispatch('open-modal', 'firma-modal')"
                            >
                            Firmar Otrosí
                        </x-primary-button>


                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-800 dark:bg-red-200 border-2 border-transparent rounded-md font-semibold text-xs
                            text-white dark:text-red-800 uppercase tracking-widest hover:text-red-800 border-red-800 hover:bg-white dark:hover:bg-white focus:bg-red-700 dark:focus:bg-white
                            dark:active:bg-red-300 focus:outline-none focus:ring-2 focus:ring-red-950 focus:ring-offset-2 dark:focus:ring-offset-red-800 focus:text-white
                            transition ease-in-out duration-150 my-2 p-1"
                                href="#"
                                data-tooltip-target="tooltip-hover-Entregable"
                                data-tooltip-trigger="hover"
                                x-data=""
                                x-on:click="$dispatch('open-modal', 'modalRechazoOtrosi-modal')">
                            Rechazo Otrosí
                        </button>
                    </div>
                </div>
            @elseif ($otroSi->estado == 1)
                <!-- Botón Descargar Contrato -->
                <div class="flex justify-center mt-6">
                    <div class="relative group">
                        <a href="{{ route('otroSiPdfPublic', $otroSi->id) }}"
                            target="_blank"
                            class="flex items-center justify-center w-12 h-12 text-white bg-blue-600
                                hover:bg-blue-700 border-2 border-blue-700
                                focus:ring-4 focus:outline-none focus:ring-blue-300
                                font-medium rounded-full text-sm transition">

                            <!-- Ícono de descarga -->
                            <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 16v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2M7 10l5 5m0 0l5-5m-5 5V4"/>
                            </svg>
                        </a>

                        <!-- Tooltip -->
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2
                                    w-max px-3 py-1 text-sm text-white bg-gray-900 rounded-lg opacity-0
                                    group-hover:opacity-100 transition pointer-events-none">
                            Descargar DPF Otrosí
                            <div class="absolute left-1/2 top-full -translate-x-1/2 w-2 h-2
                                        bg-gray-900 rotate-45"></div>
                        </div>
                    </div>
                </div>
            @else
                <div class="mt-6 p-5 bg-red-50 border-l-4 border-red-500 rounded-lg shadow-sm">
                    <div class="flex items-center mb-2">
                        <svg class="w-6 h-6 text-red-600 mr-2" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v2m0 4h.01M4.93 4.93a10 10 0 1114.14 14.14A10 10 0 014.93 4.93z" />
                        </svg>
                        <h3 class="text-lg font-semibold text-red-700">Términos rechazados</h3>
                    </div>

                    <p class="text-sm text-gray-800 leading-relaxed">
                        El usuario ha rechazado los términos del <span class="font-semibold text-gray-900">Otro Sí</span>.
                    </p>

                    <div class="mt-3 bg-white border border-red-200 rounded-md p-3">
                        <p class="text-sm text-gray-900 font-medium mb-1">Motivo del rechazo:</p>
                        <p class="text-gray-700 text-sm italic">{{ $otroSi->sugerencia_cliente }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-guest-layout>
@include('comercial.modalFirma')
@include('otrosi.modalRechazoOtrosi')

<script src="{{ asset('js/comercial/signature_pad.umd.min.js') }}"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const canvas = document.getElementById("signature-pad");
    const clearButton = document.getElementById("clear-signature");
    const saveButton = document.getElementById("save-signature");
    const saveRechazo = document.getElementById("rechazo-otro-si");
    const id_otro_si = document.getElementById("id_otro_si")?.value;
    const sugerenciaInput = document.getElementById("sugerencia_cliente");
    const hiddenInput = document.getElementById("img_firma");

    if (!canvas) {
        console.error("⚠️ No se encontró el canvas de firma.");
        return;
    }

    let signaturePad;

    /** 🖊️ Ajustar tamaño del canvas para buena calidad */
    function resizeCanvas() {
        const ratio = Math.min(window.devicePixelRatio || 1, 2);
        const container = document.getElementById("signature-pad-container");
        const rect = container?.getBoundingClientRect() || { width: 600, height: 200 };

        const width = rect.width || 600;
        const height = rect.height || 200;

        canvas.width = width * ratio;
        canvas.height = height * ratio;
        canvas.style.width = `${width}px`;
        canvas.style.height = `${height}px`;

        const ctx = canvas.getContext("2d");
        ctx.setTransform(ratio, 0, 0, ratio, 0, 0);
    }

    /** ✍️ Inicializar SignaturePad */
    function initSignaturePad() {
        resizeCanvas();
        signaturePad = new SignaturePad(canvas, {
            penColor: "#111",
            backgroundColor: "rgba(255,255,255,0)",
            minWidth: 0.7,
            maxWidth: 2.2,
            throttle: 16,
        });
    }

    initSignaturePad();

    // Ajustar firma si cambia el tamaño de pantalla
    window.addEventListener("resize", () => {
        const data = signaturePad.toData();
        resizeCanvas();
        signaturePad.clear();
        signaturePad.fromData(data);
    });

    /** 🧽 Limpiar firma */
    clearButton?.addEventListener("click", () => {
        signaturePad.clear();
        hiddenInput.value = "";
        Swal.fire({
            icon: "info",
            title: "Firma borrada",
            timer: 1200,
            showConfirmButton: false,
        });
    });

    /** ✅ Guardar aceptación del Otro Sí */
    saveButton?.addEventListener("click", async () => {
        if (signaturePad.isEmpty()) {
            return Swal.fire({
                icon: "warning",
                title: "Firma requerida",
                text: "Por favor firme antes de continuar.",
                confirmButtonColor: "#2563eb",
            });
        }

        const confirm = await Swal.fire({
            title: "¿Desea aceptar los términos del Otro Sí?",
            text: "Al aceptar, se enviará su firma como aceptación de los términos.",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Sí, aceptar y enviar",
            cancelButtonText: "Cancelar",
            confirmButtonColor: "#2563eb",
            cancelButtonColor: "#d33",
        });

        if (!confirm.isConfirmed) return;

        const firma_base64 = signaturePad.toDataURL("image/png");

        await sendForm({
            id_otro_si,
            firma_base64,
            estado: 1,
        });
    });

    /** ❌ Enviar rechazo con sugerencia */
    saveRechazo?.addEventListener("click", async () => {
        const sugerencia = sugerenciaInput?.value.trim();

        if (!sugerencia) {
            return Swal.fire({
                icon: "warning",
                title: "Sugerencia requerida",
                text: "Por favor ingrese la sugerencia antes de continuar.",
                confirmButtonColor: "#2563eb",
            });
        }

        const confirm = await Swal.fire({
            title: "¿Desea rechazar los términos del Otro Sí?",
            text: "Se enviará su comentario como motivo de rechazo.",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Sí, rechazar y enviar",
            cancelButtonText: "Cancelar",
            confirmButtonColor: "#2563eb",
            cancelButtonColor: "#d33",
        });

        if (!confirm.isConfirmed) return;

        await sendForm({
            id_otro_si,
            sugerencia_cliente: sugerencia,
            estado: 2,
        });
    });

    /** 🚀 Función para enviar datos al servidor */
    async function sendForm(data) {
        try {
            const resp = await fetch("{{ route('guardarFirmaOtroSi') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                },
                body: JSON.stringify(data),
            });

            if (resp.status === 422) {
                const { errors } = await resp.json();
                const messages = Object.values(errors)
                    .flat()
                    .map((m) => `• ${m}`)
                    .join("\n");

                return Swal.fire({
                    icon: "error",
                    title: "Error de validación",
                    text: messages || "Verifique los campos e intente nuevamente.",
                    confirmButtonColor: "#2563eb",
                });
            }

            const response = await resp.json();

            if (response.success) {
                Swal.fire({
                    icon: "success",
                    title: "¡Proceso exitoso!",
                    text: response.message || "Operación completada correctamente.",
                    confirmButtonColor: "#2563eb",
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Error al guardar",
                    text: response.message || "Ocurrió un error al guardar la información.",
                    confirmButtonColor: "#2563eb",
                });
            }
        } catch (error) {
            console.error("❌ Error al enviar:", error);
            Swal.fire({
                icon: "error",
                title: "Error de conexión",
                text: "No se pudo enviar la información. Intenta nuevamente.",
                confirmButtonColor: "#2563eb",
            });
        }
    }
});
</script>
