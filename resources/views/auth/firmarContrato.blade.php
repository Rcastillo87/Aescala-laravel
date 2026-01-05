<x-guest-layout>
    <div class="flex items-center justify-center min-h-screen w-full bg-gray-100 px-4">
        <div class="bg-white rounded-lg shadow-lg p-8 max-w-5xl w-full text-center relative">
            <!-- Logo -->
            <div class="flex justify-center mb-3">
                <img src="{{ asset('img/logo.png') }}" alt="logo" class="w-[200px] h-[100px] object-contain">
            </div>

            <!-- Título -->
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Contrato Aescala</h1>

            <!-- Subtítulo -->
            <p class="text-center text-gray-600 text-sm">
                Términos, Condiciones y Obligaciones del Contrato
            </p>

            {{-- Contenido del contrato --}}
            <div class="contrato-container border border-gray-300 rounded-xl p-4 mt-6">
                @include('proyecto.pdfContrato', $data)
            </div>

            {{-- Si no hay firma, se habilita el pad --}}
            @if (!$data['img_firma'])
                <div class="w-full flex justify-center mt-6">
                    <div class="w-full max-w-2xl">
                        <!-- Contenedor del canvas -->
                        <div id="signature-pad-container"
                            class="border border-gray-300 rounded-lg bg-gray-50 shadow-inner relative h-40 w-full max-w-2xl mx-auto overflow-hidden">
                            <canvas id="signature-pad" class="absolute top-0 left-0 w-full h-full z-10"></canvas>
                        </div>

                        <!-- Campo oculto donde se guarda la firma en Base64 -->
                        <form id="firmaForm" action="{{ route('guardarFirma') }}" method="POST">
                            @csrf

                            <!-- Opciones -->
                            <div class="flex justify-between mt-4">

                                <div 
                                    x-data="{ acepta: {{ old('acepta_trata_datos', $proyecto?->acepta_trata_datos ?? 0) ? 'true' : 'false' }} }"
                                    x-on:aceptar-tratamiento.window="acepta = true"
                                    x-on:revocar-tratamiento.window="acepta = false"
                                    class="w-full max-w-full px-3 pt-6 shrink-0 lg:w-6/12 2xl:w-4/12"
                                >
                                    <!-- Valor real -->
                                    <input type="hidden" name="acepta_trata_datos" :value="acepta ? 1 : 0">
                                    <label class="inline-flex items-center space-x-2">
                                        <input 
                                            type="checkbox"
                                            x-model="acepta"
                                            class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 
                                            focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 
                                            dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        >
                                        <button 
                                            type="button"
                                            class="text-blue-600 underline hover:text-blue-800"
                                            x-on:click="$dispatch('open-modal', 'tratamiento-datos-modal')"
                                        >
                                            Acepto el tratamiento de datos personales
                                        </button>
                                    </label>
                                </div>
                                <div class="space-x-3 flex">
                                    <!-- Borrar -->
                                    <button type="button" id="clear-signature"
                                        class="flex items-center bg-gray-500 text-white px-4 py-2 rounded-lg shadow hover:bg-gray-600 transition">
                                        Borrar Firma
                                    </button>

                                    <!-- Guardar -->
                                    <button type="button" id="save-signature"
                                        class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition">
                                        Guardar Firma
                                    </button>
                                </div>
                                
                            </div>
                            
                            <input type="hidden" name="id" id="id" value="{{ $data['id_proyecto'] }}">
                            <input type="hidden" name="img_firma" id="img_firma">
                        </form>
                    </div>
                </div>
                @else
                    <!-- Botón Descargar Contrato -->
                    <div class="flex justify-center mt-6">
                        <div class="relative group">
                            <a href="{{ route('contratoPdf', $data['id_proyecto']) }}" 
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
                                Descargar contrato
                                <div class="absolute left-1/2 top-full -translate-x-1/2 w-2 h-2 
                                            bg-gray-900 rotate-45"></div>
                            </div>
                        </div>
                    </div>
                @endif

        </div>
    </div>
    @include('comercial.modalTrataDatos')
</x-guest-layout>

<script src="{{ asset('js/comercial/signature_pad.umd.min.js') }}"></script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const canvas = document.getElementById("signature-pad");
        const clearButton = document.getElementById("clear-signature");
        const saveButton = document.getElementById("save-signature");
        const hiddenInput = document.getElementById("img_firma");
        const form = document.getElementById("firmaForm");
        let signaturePad;

        if (!canvas) {
            console.error("⚠️ No se encontró el canvas de firma.");
            return;
        }

        // Ajustar tamaño del canvas para buena calidad (retina)
        function resizeCanvas() {
            const ratio = Math.min(window.devicePixelRatio || 1, 2); //const ratio = Math.max(window.devicePixelRatio || 1, 1);
            const container = document.getElementById("signature-pad-container");
            const rect = container.getBoundingClientRect();

            const width = rect.width > 0 ? rect.width : 600;
            const height = rect.height > 0 ? rect.height : 200;

            canvas.width = width * ratio;
            canvas.height = height * ratio;
            canvas.style.width = width + "px";
            canvas.style.height = height + "px";

            const ctx = canvas.getContext("2d");
            ctx.setTransform(ratio, 0, 0, ratio, 0, 0);
        }

        // Inicializar SignaturePad
        function initSignaturePad() {
            resizeCanvas();
            signaturePad = new SignaturePad(canvas, {
                penColor: "#111",     // color de la firma
                backgroundColor: "rgba(255,255,255,0)", // fondo transparente
                //minWidth: 0.8,        // grosor mínimo del trazo
                //maxWidth: 2.5,        // grosor máximo del trazo
                minWidth: 0.7,   // 🔹 más liviano
                maxWidth: 2.2,   // 🔹 balance entre visibilidad y peso
                throttle: 16,         // suaviza la escritura
            });
        }

        initSignaturePad();

        // Ajustar si cambia el tamaño de pantalla
        window.addEventListener("resize", () => {
            const data = signaturePad.toData(); // guardar firma temporalmente
            resizeCanvas();
            signaturePad.clear();
            signaturePad.fromData(data); // restaurar firma
        });

        // Botón limpiar
        if (clearButton) {
            clearButton.addEventListener("click", () => {
                signaturePad.clear();
                hiddenInput.value = "";
                Swal.fire({
                    icon: "info",
                    title: "Firma borrada",
                    timer: 1200,
                    showConfirmButton: false,
                });
            });
        }

        // Botón guardar
        if (saveButton) {
            saveButton.addEventListener("click", () => {
                if (signaturePad.isEmpty()) {
                    Swal.fire({
                        icon: "warning",
                        title: "Firma requerida",
                        text: "Por favor, dibuja tu firma antes de guardar.",
                    });
                    return;
                }
                const acepta = document.querySelector('input[name="acepta_trata_datos"]').value;
                if (acepta !== '1') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Autorización requerida',
                        text: 'Debe aceptar el tratamiento de datos personales para continuar.',
                    });
                    return;
                }

                Swal.fire({
                    title: "¿Deseas enviar la firma?",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonText: "Sí, enviar",
                    cancelButtonText: "Cancelar",
                    confirmButtonColor: "#2563eb",
                    cancelButtonColor: "#ef4444",
                }).then((result) => {
                    if (result.isConfirmed) {
                        let formData = new FormData(form);
                        formData.set("img_firma", signaturePad.toDataURL("image/png"));

                        fetch(form.action, {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": document.querySelector(
                                    'meta[name="csrf-token"]'
                                ).content,
                            },
                            body: formData,
                        })
                            .then((res) => res.json())
                            .then((data) => {
                                if (data.status === "success") {
                                    Swal.fire({
                                        icon: "success",
                                        title: "¡Éxito!",
                                        text: data.message,
                                    }).then(() => {
                                        location.reload(); // recargar vista
                                    });
                                } else {
                                    Swal.fire({
                                        icon: "error",
                                        title: "Error",
                                        text: data.message,
                                    });
                                }
                            })
                            .catch((err) => {
                                Swal.fire({
                                    icon: "error",
                                    title: "Error",
                                    text: "No se pudo conectar con el servidor",
                                });
                            });
                    }
                });
            });
        }

    });
</script>
