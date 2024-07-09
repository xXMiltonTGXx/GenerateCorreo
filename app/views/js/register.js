toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": false,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "preventDuplicates": true,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};

let shownMessages = new Set();

function showError(message) {
    if (!shownMessages.has(message)) {
        toastr.error(message, 'Error de validación');
        shownMessages.add(message);
        
        setTimeout(() => {
            shownMessages.delete(message);
        }, 6000);  
    }
}

const nombresInput = document.getElementById('nombres');
const apellidosInput = document.getElementById('apellidos');
const correoGeneradoInput = document.getElementById('correo_generado');
const correoGeneradoPreview = document.getElementById('correo_generado_preview');



async function checkCedulaExists(cedula) {
    try {
        const response = await fetch(`/generatecorreo/check-cedula?cedula=${cedula}`);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const data = await response.json();
        return data.exists;
    } catch (error) {
        console.error('Error fetching cedula check:', error);
        return false;
    }
}


async function checkEmailExists(email) {
    try {
        const response = await fetch(`/generatecorreo/check-email?email=${email}`);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const data = await response.json();
        return data.exists;
    } catch (error) {
        console.error('Error fetching email check:', error);
        return false;
    }
}

async function updateCorreoGeneradoPreview() {
    const nombres = nombresInput.value.trim().split(' ');
    const apellidos = apellidosInput.value.trim().split(' ');

    let correoGenerado = '';
    if (nombres.length > 0 && apellidos.length > 1) {
        const primerNombreInicial = nombres[0][0];
        const primerApellido = apellidos[0];
        const segundoApellidoInicial = apellidos[1][0];
        correoGenerado = `${primerNombreInicial.toLowerCase()}${primerApellido.toLowerCase()}${segundoApellidoInicial.toLowerCase()}@mail.com`;
    } else if (nombres.length > 0 && apellidos.length === 1) {
        const primerNombreInicial = nombres[0][0];
        const primerApellido = apellidos[0];
        correoGenerado = `${primerNombreInicial.toLowerCase()}${primerApellido.toLowerCase()}@mail.com`;
    } else {
        correoGeneradoInput.value = '';
        correoGeneradoPreview.textContent = '';
        return;
    }

    let uniqueEmail = correoGenerado;
    let counter = 1;

    while (await checkEmailExists(uniqueEmail) && counter < 10) {
        uniqueEmail = correoGenerado.replace('@mail.com', `${counter}@mail.com`);
        counter++;
    }

    if (counter === 10) {
        // Manejar el caso donde todos los correos hasta 9 están ocupados
        showError('No se pudo generar un correo único. Intenta con otros nombres o apellidos.');
        uniqueEmail = '';
    }

    correoGeneradoInput.value = uniqueEmail;
    correoGeneradoPreview.textContent = uniqueEmail; 
}


nombresInput.addEventListener('input', updateCorreoGeneradoPreview);
apellidosInput.addEventListener('input', updateCorreoGeneradoPreview);

document.getElementById('FormRegistro').addEventListener('submit', async function(event) {
    event.preventDefault(); 

    let isValid = true;

    // Obtener los valores de los campos
    let identificacion = document.getElementById('identificacion').value.trim();
    let nombres = document.getElementById('nombres').value.trim();
    let apellidos = document.getElementById('apellidos').value.trim();
    let correo_generado = document.getElementById('correo_generado').value.trim();
    let password = document.getElementById('password').value.trim();

    // Validar campo de identificación
    if (!identificacion) {
        showError('La cédula es obligatoria.');
        isValid = false;
    } else if (!/^\d+$/.test(identificacion) || identificacion.length < 10) {
        showError('La cédula solo debe contener números y debe tener al menos 10 dígitos.');
        isValid = false;
    } else if (/(\d)\1{3}/.test(identificacion)) {
        showError('La cédula no puede tener 4 números seguidos iguales.');
        isValid = false;
    } else {
        try {
            if (await checkCedulaExists(identificacion)) {
                showError('La cédula ya está registrada.');
                isValid = false;
            }
        } catch (error) {
            console.error('Error checking cedula:', error);
            showError('Error verificando la cédula.');
            isValid = false;
        }
    }

    // Validar campo de nombres
       if (!nombres) {
        showError('El campo de nombre es obligatorio.');
        isValid = false;
    } else if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(nombres)) {
        showError('El nombre solo debe contener letras y espacios.');
        isValid = false;
    }

    // Validar campo de apellidos
    if (!apellidos) {
        showError('El campo de apellido es obligatorio.');
        isValid = false;
    } else if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(apellidos)) {
        showError('El apellido solo debe contener letras y espacios.');
        isValid = false;
    }

    // Validar campo de correo generado
    if (!correo_generado) {
        showError('El campo de correo generado es obligatorio.');
        isValid = false;
    } else {
        // Verificar si el correo generado ya existe
        try {
            if (await checkEmailExists(correo_generado)) {
                showError('El correo generado ya existe.');
                isValid = false;
            }
        } catch (error) {
            console.error('Error checking email:', error);
            showError('Error verificando el correo generado.');
            isValid = false;
        }
    }

    // Validar campo de contraseña
    if (!password) {
        showError('El campo de contraseña es obligatorio.');
        isValid = false;
    } else if (password.length < 8) {
        showError('La contraseña debe tener al menos 8 dígitos, una letra mayúscula y un signo.');
        isValid = false;
    } else if (/\s/.test(password)) {
        showError('La contraseña no debe tener espacios.');
        isValid = false;
    } else if (!/[A-Z]/.test(password) || !/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
        showError('La contraseña debe incluir al menos una letra mayúscula y un signo.');
        isValid = false;
    }

    // Si todos los campos son válidos, mostrar SweetAlert2 y enviar el formulario
    if (isValid) {
        Swal.fire({
            title: 'Cuenta creada exitosamente',
            html: `Su correo generado es: <b>${correo_generado}</b><br>Su contraseña es: <b>${password}</b>`,
            icon: 'success',
            confirmButtonText: 'Aceptar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Crear un formulario temporal para enviar los datos al servidor
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/generatecorreo/registrar';

                const fields = [
                    { name: 'identificacion', value: identificacion },
                    { name: 'nombres', value: nombres },
                    { name: 'apellidos', value: apellidos },
                    { name: 'correo_generado', value: correo_generado },
                    { name: 'password', value: password },
                    { name: 'rol', value: 'usuario' }
                ];

                fields.forEach(field => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = field.name;
                    input.value = field.value;
                    form.appendChild(input);
                });

                document.body.appendChild(form);
                form.submit();
            }
        });
    }
});
