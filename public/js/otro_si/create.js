document.getElementById('plantilla_otro_si').addEventListener('change', async function (e) {
    const file = e.target.files[0];
    const input = e.target;
    if (!file) return;

    const formData = new FormData();
    formData.append('file', file);

    Swal.fire({
        title: 'Validando plantilla...',
        text: 'Por favor espera mientras se valida el archivo.',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    try {
        const response = await fetch( valUrl , {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        });

        const result = await response.json();
        Swal.close();

        const entregablesDiv = document.getElementById('entregables-div');
        entregablesDiv.innerHTML = '';

        // ❌ Validaciones
        if (response.status === 422 && result.errores && result.errores.length > 0) {
            const msg = result.errores.map(err => `• ${err}`).join('<br>');
            Swal.fire({
                icon: 'error',
                title: 'Errores en la plantilla',
                html: `<div class="text-left text-sm">${msg}</div>`,
                confirmButtonText: 'Entendido',
            });
            input.value = '';
            return;
        }

        if (!response.ok || result.error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: result.error || 'No se pudo procesar el archivo.'
            });
            input.value = '';
            return;
        }

        Swal.fire({
            icon: 'success',
            title: 'Archivo válido',
            text: `Proyecto: ${result.nombre_proyecto}`,
            timer: 1500,
            showConfirmButton: false
        });

        // ✅ Construcción del encabezado del proyecto
        let header = `
            <div class="bg-[#242e68] text-white p-4 mt-6">
                <h2 class="text-xl text-[#242e68] font-bold">📁 Proyecto: ${result.nombre_proyecto}</h2>
            </div>
        `;

        // ✅ Construcción de la tabla
        let html = `
            <div class="overflow-x-auto w-full border border-gray-200 rounded-b-lg shadow-md">
                <table class="min-w-full w-full text-sm text-left text-gray-700 border-collapse table-auto">
                    <colgroup>
                        <col style="width: 20%;">
                        <col style="width: 50%;">
                        <col style="width: 10%;">
                        <col style="width: 10%;">
                        <col style="width: 10%;">
                    </colgroup>
                    <thead class="bg-gray-100 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-2 font-semibold text-[#242e68]">Área</th>
                            <th class="px-4 py-2 font-semibold text-[#242e68]">Material / Actividad</th>
                            <th class="px-4 py-2 font-semibold text-right text-[#242e68]">Cantidad</th>
                            <th class="px-4 py-2 font-semibold text-right text-[#242e68]">Valor Unitario</th>
                            <th class="px-4 py-2 font-semibold text-right text-[#242e68]">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
        `;

        let totalGeneral = 0;

        console.log(result);

        result.espacios.forEach(espacio => {
            const subtotalEspacio = espacio.items.reduce((sum, item) => sum + (item.cantidad * item.valor_unitario), 0);
            totalGeneral += subtotalEspacio;

            // filas de cada item
            espacio.items.forEach((item, index) => {
                html += `
                    <tr>
                        ${index === 0
                            ? `<td class="px-4 py-2 font-semibold align-top" rowspan="${espacio.items.length}">
                                   ${espacio.area_nombre}
                               </td>`
                            : ''
                        }
                        <td class="px-4 py-2">${item.material}</td>
                        <td class="px-4 py-2 text-right">${item.cantidad}</td>
                        <td class="px-4 py-2 text-right">$ ${item.valor_unitario.toLocaleString()}</td>
                        <td class="px-4 py-2 text-right">$ ${(item.cantidad * item.valor_unitario).toLocaleString()}</td>
                    </tr>
                `;
            });

            // subtotal por área
            html += `
                <tr class="bg-gray-50 font-semibold">
                    <td colspan="4" class="px-4 py-2 text-right text-[#242e68]">Subtotal ${espacio.area_nombre}</td>
                    <td class="px-4 py-2 text-right text-[#242e68]">$ ${subtotalEspacio.toLocaleString()}</td>
                </tr>
            `;
        });

        // total general
        html += `
                    </tbody>
                    <tfoot class="bg-[#f7f9ff] font-bold text-[#242e68] border-t-2 border-[#242e68]">
                        <tr>
                            <td colspan="4" class="px-4 py-3 text-right text-lg">Total General</td>
                            <td class="px-4 py-3 text-right text-lg">$ ${totalGeneral.toLocaleString()}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        `;

        // ✅ Unir encabezado y tabla
        entregablesDiv.innerHTML = `
            <div class="w-full max-w-6xl mx-auto">
                ${header}
                ${html}
            </div>
        `;

        // Campos ocultos para el backend
        let hiddenInputs = `<input type="hidden" name="id_proyecto_excel" value="${result.id_proyecto}">;`
    result.espacios.forEach(espacio => {
        espacio.items.forEach((item, index) => {
            hiddenInputs += `
                <input type="hidden" name="entregables[${espacio.id_area}][id_area]" value="${espacio.id_area}">
                <input type="hidden" name="entregables[${espacio.id_area}][area_nombre]" value="${espacio.area_nombre}">
                <input type="hidden" name="entregables[${espacio.id_area}][items][${index}][material]" value="${item.material}">
                <input type="hidden" name="entregables[${espacio.id_area}][items][${index}][cantidad]" value="${item.cantidad}">
                <input type="hidden" name="entregables[${espacio.id_area}][items][${index}][valor_unitario]" value="${item.valor_unitario}">
            `;
        });
    });
        entregablesDiv.insertAdjacentHTML('beforeend', hiddenInputs);

    } catch (error) {
        Swal.close();
        Swal.fire({
            icon: 'error',
            title: 'Error del servidor',
            text: 'No se pudo conectar al servidor o el archivo es inválido.'
        });
        console.error('Error:', error);
        input.value = '';
    }
});

document.getElementById("btnDescargar").addEventListener("click", function(e) {
    e.preventDefault();

    let base64Data = this.getAttribute("data-excel");
    const filename = this.getAttribute("data-filename") || "archivo.xlsx";

    // ✅ Quitar prefijo si existe
    if (base64Data.includes(",")) {
        base64Data = base64Data.split(",")[1];
    }

    try {
        const byteCharacters = atob(base64Data);
        const byteNumbers = new Array(byteCharacters.length);
        for (let i = 0; i < byteCharacters.length; i++) {
            byteNumbers[i] = byteCharacters.charCodeAt(i);
        }
        const byteArray = new Uint8Array(byteNumbers);

        const blob = new Blob([byteArray], {
            type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
        });
        const url = URL.createObjectURL(blob);

        const a = document.createElement("a");
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);

        URL.revokeObjectURL(url);
    } catch (error) {
        console.error("❌ Error al decodificar el archivo:", error);
        alert("El archivo no se pudo descargar correctamente. Verifique que sea un Excel válido.");
    }
});