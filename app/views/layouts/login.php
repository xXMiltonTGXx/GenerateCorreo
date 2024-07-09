<div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
  <div class="sm:mx-auto sm:w-full sm:max-w-sm">
    <img class="mx-auto h-10 w-auto" src="https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=600" alt="Your Company">
    <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">Ingresa con tu Cuenta</h2>
  </div>

  <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
    <form id="FormLogin" class="space-y-6" novalidate>
      <div>
        <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Usuario o Correo Electrónico</label>
        <div class="mt-2">
          <input id="email" name="email" type="text" autocomplete="email" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
        </div>
      </div>

      <div>
        <div class="flex items-center justify-between">
          <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label>
          <div class="text-sm">
            <a href="#" class="font-semibold text-indigo-600 hover:text-indigo-500">¿Ha olvidado su contraseña?</a>
          </div>
        </div>
        <div class="mt-2">
          <input id="password" name="password" type="password" autocomplete="current-password" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
        </div>
      </div>

      <div>
        <button type="submit" class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Iniciar Sesión</button>
      </div>
    </form>

    <p class="mt-10 text-center text-sm text-gray-500">
      ¿No tienes cuenta?
      <a href="/generatecorreo/register" class="font-semibold leading-6 text-indigo-600 hover:text-indigo-500">Regístrate aquí</a>
    </p>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/toastr/build/toastr.min.js"></script>
<script>
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

  document.getElementById('FormLogin').addEventListener('submit', async function (event) {
      event.preventDefault();

      let email = document.getElementById('email').value.trim();
      let password = document.getElementById('password').value.trim();

      if (!email || !password) {
          toastr.error('Usuario o contraseña no pueden estar vacíos.', 'Error');
          return;
      }

      if (password.length < 8) {
          toastr.error('La contraseña debe tener al menos 8 caracteres.', 'Error');
          return;
      }

      try {
          const response = await fetch('/generatecorreo/iniciar-sesion', {
              method: 'POST',
              headers: {
                  'Content-Type': 'application/x-www-form-urlencoded'
              },
              body: new URLSearchParams({
                  email: email,
                  password: password
              })
          });

          const result = await response.text();

          if (result === 'success') {
              Swal.fire({
                  title: 'Inicio de sesión exitoso',
                  text: 'Redirigiendo al dashboard...',
                  icon: 'success',
                  timer: 2000,
                  showConfirmButton: false
              }).then(() => {
                  window.location.href = '/generatecorreo/dashboard';
              });
          } else if (result === 'active_session') {
              toastr.error('Ya tienes una sesión activa en otro dispositivo.', 'Error');
          } else if (result === 'blocked') {
              toastr.error('La cuenta ha sido bloqueada después de 3 intentos fallidos.', 'Error');
          } else if (result.startsWith('attempts:')) {
              const attempts = result.split(':')[1];
              toastr.warning(`Intento ${attempts} de 3 fallido.`, 'Advertencia');
          } else {
              toastr.error('Usuario o contraseña incorrectos.', 'Error');
          }
      } catch (error) {
          console.error('Error al iniciar sesión:', error);
          toastr.error('Error al intentar iniciar sesión. Por favor, inténtelo de nuevo.', 'Error');
      }
  });
</script>
