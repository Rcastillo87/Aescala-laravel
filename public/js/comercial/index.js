function setModalData(element) {
    document.getElementById("sendLink").value = element.dataset.link;
    document.getElementById("id_proyect_link").value = element.dataset.id;
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

    Swal.fire({
        title: 'Enviando...',
        text: 'Por favor espera',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch("sendLinkByEmail", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute('content')
        },
        body: JSON.stringify({ email, link, id })
    })
    .then(res => {
        if (!res.ok) throw new Error('Error en servidor');
        return res.json();
    })
    .then(() => {
        Swal.fire({
            icon: 'success',
            title: '¡Enviado!',
            text: 'El link fue enviado al correo.',
            confirmButtonColor: '#3085d6'
        }).then(() => {
            window.dispatchEvent(
                new CustomEvent('close-modal', { detail: 'sendLink-modal' })
            );
        });
    })
    .catch(() => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo enviar el correo.'
        });
    });
}