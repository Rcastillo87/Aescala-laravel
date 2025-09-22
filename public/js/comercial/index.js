function setModalData(element) {
    const link = element.getAttribute('data-link');
    document.getElementById("sendLink").value =  link;
}

// Copiar al portapapeles
function copyLink() {
    const link = document.getElementById("sendLink").value;

    navigator.clipboard.writeText(link).then(() => {
        Swal.fire({
            icon: 'success',
            title: '¡Copiado!',
            text: 'El link se copió al portapapeles.',
            confirmButtonColor: '#3085d6'
        });
    }).catch(err => {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'No se pudo copiar el link.'
        });
    });
}

// Enviar por correo
function sendLinkByEmail() {
    const link = document.getElementById("sendLink").value;
    const email = document.getElementById("emailDestino").value;

    if (!email) {
        Swal.fire({
            icon: 'warning',
            title: 'Atención',
            text: 'Debes ingresar un correo válido.'
        });
        return;
    }

    fetch("{{ route('proyecto.enviarLink') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({
            email: email,
            link: link
        })
    })
    .then(res => res.json())
    .then(data => {
        Swal.fire({
            icon: 'success',
            title: '¡Enviado!',
            text: 'El link fue enviado al correo.',
            confirmButtonColor: '#3085d6'
        });
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo enviar el correo.'
        });
    });
}
