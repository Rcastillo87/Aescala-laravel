<x-modal name="tratamiento-datos-modal" maxWidth="4xl">
    <div class="p-6">

        <div class="space-y-4 text-sm text-gray-700 leading-relaxed">

            <h2 class="w-full text-center text-lg font-semibold text-gray-900">
                Autorización para el Tratamiento de Datos Personales
            </h2>

            <p>
                La presente autorización se expide en cumplimiento de lo dispuesto en la Ley Estatutaria 1581 de 2012,
                el Decreto 1377 de 2013 y demás normas concordantes sobre protección de datos personales.
            </p>

            <p>
                Toda persona natural que, en calidad de <strong>titular de datos personales</strong> (en adelante, el
                “TITULAR”), suministre información personal a
                <strong>{{ env('RAZON') }}</strong>, identificada con NIT
                <strong>{{ env('NIT') }}</strong> (en adelante, la “EMPRESA”),
                autoriza de manera libre, expresa, voluntaria e informada a la EMPRESA para que recolecte,
                almacene, use, procese y trate sus datos personales conforme a su Política de Tratamiento
                de Datos Personales y para las finalidades relacionadas con el desarrollo de su objeto social.
            </p>

            <p>
                La EMPRESA informa que el TITULAR no se encuentra obligado a otorgar la presente autorización
                y que podrá revocarla en cualquier momento, de conformidad con la normatividad vigente.
            </p>

            <p class="font-medium text-gray-900">
                La EMPRESA informa que:
            </p>

            <ul class="list-disc pl-5 space-y-2">
                <li>
                    Los datos personales recolectados no serán cedidos, vendidos ni compartidos con terceros.
                    Su tratamiento se realizará únicamente para las finalidades propias de la ejecución del
                    objeto social de la EMPRESA.
                </li>

                <li>
                    La EMPRESA se compromete a tratar los datos personales con estricta confidencialidad,
                    conforme a los principios y garantías establecidos en la Constitución Política,
                    la Ley 1581 de 2012 y demás normas aplicables.
                </li>

                <li>
                    La EMPRESA garantizará la seguridad y confidencialidad de los datos personales,
                    implementando medidas técnicas, humanas y administrativas necesarias para evitar su
                    adulteración, pérdida, consulta, uso o acceso no autorizado o fraudulento.
                </li>

                <li>
                    Para revocar la presente autorización, el TITULAR deberá enviar una solicitud al correo
                    electrónico <strong>{{ env('MAIL_FROM_ADDRESS') }}</strong>, indicando su nombre completo,
                    número de identificación y la manifestación expresa de su voluntad de revocar la
                    autorización para el tratamiento de sus datos personales.
                </li>
            </ul>

            <p class="font-medium text-gray-900">
                Derechos del TITULAR:
            </p>

            <ul class="list-disc pl-5 space-y-2">
                <li>Conocer, actualizar y rectificar sus datos personales.</li>
                <li>Solicitar prueba de la autorización otorgada para el tratamiento de sus datos.</li>
                <li>Ser informado, previa solicitud, sobre el uso que se ha dado a sus datos personales.</li>
                <li>Presentar quejas ante la Superintendencia de Industria y Comercio.</li>
                <li>
                    Revocar la autorización y/o solicitar la supresión de los datos cuando no se respeten
                    los principios constitucionales y legales.
                </li>
                <li>Acceder de forma gratuita a sus datos personales.</li>
            </ul>

            <p>
                Así mismo, se informa que en el establecimiento de comercio de
                <strong>{{ env('RAZON') }}</strong> existen cámaras de videovigilancia,
                utilizadas de manera exclusiva para la seguridad de la empresa, sus colaboradores,
                proveedores y clientes. En consecuencia, toda persona que ingrese a las instalaciones
                autoriza de manera expresa la captación y grabación de su imagen mientras permanezca en las mismas.
            </p>

            <p>
                La EMPRESA pone a disposición del TITULAR su Política de Tratamiento de Datos Personales,
                disponible en el establecimiento de comercio ubicado en
                <strong>{{ env('DIREC') }}</strong>, mediante la cual se garantiza el cumplimiento
                de las normas aplicables y se proporciona información sobre las finalidades del tratamiento,
                los derechos del titular y los datos del responsable.
            </p>

            <p class="font-medium">
                En virtud de lo anterior, se entiende otorgada la autorización para el tratamiento
                de los datos personales bajo los términos aquí expuestos.
            </p>

        </div>

        <div class="flex justify-end gap-3 mt-6">
            <button
                type="button"
                class="bg-gray-400 text-white px-4 py-2 rounded-lg hover:bg-gray-500"
                x-on:click="
                    $dispatch('revocar-tratamiento');
                    $dispatch('close-modal', 'tratamiento-datos-modal');
                "
            >
                Revocar
            </button>

            <button
                type="button"
                class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700"
                x-on:click="
                    $dispatch('aceptar-tratamiento');
                    $dispatch('close-modal', 'tratamiento-datos-modal');
                "
            >
                Aceptar
            </button>
        </div>
    </div>
</x-modal>
