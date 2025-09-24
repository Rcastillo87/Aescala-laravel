function setModalData(element) {
    const link = element.getAttribute('data-link');
    const id = element.getAttribute('data-id');
    document.getElementById("sendLink").value =  link;
    document.getElementById("id_proyect_link").value =  id;

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
    const id = document.getElementById("id_proyect_link").value;

    if (!email) {
        Swal.fire({
            icon: 'warning',
            title: 'Atención',
            text: 'Debes ingresar un correo válido.'
        });
        return;
    }

    fetch("sendLinkByEmail", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            email: email,
            link: link,
            id: id
        })
    })
    .then(res => res.json())
    .then(data => {
        Swal.fire({
            icon: 'success',
            title: '¡Enviado!',
            text: 'El link fue enviado al correo.',
            confirmButtonColor: '#3085d6'
        }).then(() => {
            window.dispatchEvent(new CustomEvent('close-modal', { detail: 'sendLink-modal' }));
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
