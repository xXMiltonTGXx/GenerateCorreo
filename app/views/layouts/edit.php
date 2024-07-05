<?php

require_once __DIR__ . '/../inc/nav.php';
 

 

?> 

    <header class="bg-white shadow">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900 text-center uppercase">EDITAR</h1>
        </div>
    </header>



    <div class="max-w-lg mx-auto mt-10 px-4 py-8 bg-white shadow-lg rounded-lg">
    <h2 class="text-2xl font-semibold text-center text-gray-800">Editar Usuario </h2>
    <form action="/generatecorreo/edit-user" method="POST">
        <input type="hidden" name="idUsuario" value="<?= htmlspecialchars($user['idUsuario']) ?>">

        <div>
        <label for="identificacion" class="block text-sm font-medium text-gray-700">Cédula</label>
        <input id="identificacion" name="identificacion" type="text" value="<?= htmlspecialchars($user['identificacion']) ?>"  required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50">
        </div>




        <div>
            <label for="nombres" class="block text-sm font-medium text-gray-700">Nombre</label>
            <input id="nombres" name="nombres" type="text" value="<?= htmlspecialchars($user['nombres']) ?>" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50">
        </div>

        <div>
            <label for="apellidos" class="block text-sm font-medium text-gray-700">Apellidos</label>
            <input id="apellidos" name="apellidos" type="text" value="<?= htmlspecialchars($user['apellidos']) ?>" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50">
        </div>


        <div>
            <label for="correo_generado" class="block text-sm font-medium text-gray-700">Correo Generado</label>
            <input id="correo_generado" name="correo_generado" type="email" value="<?= htmlspecialchars($user['correo_generado']) ?>" required readonly class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50">
            <p class="mt-2 text-sm text-gray-500">El correo generado será: <span id="correo_generado_preview"></span></p>
        </div>

        <div>
             <label for="rol" class="block text-sm font-medium text-gray-700">Rol</label>
            <select id="rol" name="rol" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50">
                <option value="usuario" <?= ($user['rol'] == 'usuario') ? 'selected' : '' ?>>Usuario</option>
                <option value="administrador" <?= ($user['rol'] == 'administrador') ? 'selected' : '' ?>>Administrador</option>
                <option value="moderador" <?= ($user['rol'] == 'moderador') ? 'selected' : '' ?>>Moderador</option>
            </select>
        </div>

        <div class="mt-4">
            <button type="submit" class="flex justify-center w-full rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Guardar Cambios</button>
        </div>
    </form>
</div>
 


</div>


<script>
  // Script para validar y actualizar campos en el formulario de edición
  const nombresInput = document.getElementById('nombres');
  const apellidosInput = document.getElementById('apellidos');
  const correoGeneradoInput = document.getElementById('correo_generado');
  const correoGeneradoPreview = document.getElementById('correo_generado_preview');
  const identificacionInput = document.getElementById('identificacion');

  // Eventos para actualizar la vista previa del correo generado y validar campos
  nombresInput.addEventListener('input', updateCorreoGeneradoPreview);
  apellidosInput.addEventListener('input', updateCorreoGeneradoPreview);
  identificacionInput.addEventListener('input', validateIdentificacion);
  correoGeneradoInput.addEventListener('input', validateCorreoGenerado);

  function validateIdentificacion() {
    const identificacion = identificacionInput.value;
    const identificacionRegex = /^\d{10}$/;

    if (!identificacionRegex.test(identificacion) || /(.)\1{3}/.test(identificacion)) {
      identificacionInput.setCustomValidity('La identificación debe tener 10 dígitos, solo números y no tener seguido 4 veces un mismo número.');
    } else {
      identificacionInput.setCustomValidity('');
    }
  }

  function validateCorreoGenerado() {
    const correoGenerado = correoGeneradoInput.value;
    const correoRegex = /^(?=.*[A-Z])(?=.*\d)(?!.*[!@#$%^&*()_+}{":;'?/>.<,\s]).{8,20}$/;

    if (!correoRegex.test(correoGenerado)) {
      correoGeneradoInput.setCustomValidity('El nombre de usuario debe tener entre 8 y 20 caracteres, contener al menos una letra mayúscula, un número y no contener signos.');
    } else {
      correoGeneradoInput.setCustomValidity('');
    }
  }

  function updateCorreoGeneradoPreview() {
    const nombres = nombresInput.value.trim().split(' ');
    const apellidos = apellidosInput.value.trim().split(' ');

    if (nombres.length > 0 && apellidos.length > 1) {
      const primerNombreInicial = nombres[0][0];
      const primerApellido = apellidos[0];
      const segundoApellidoInicial = apellidos[1][0];
      
      const correoGenerado = `${primerNombreInicial.toLowerCase()}${primerApellido.toLowerCase()}${segundoApellidoInicial.toLowerCase()}@mail.com`;
      correoGeneradoInput.value = correoGenerado;
      correoGeneradoPreview.textContent = correoGenerado;
    } else if (nombres.length > 0 && apellidos.length === 1) {
      const primerNombreInicial = nombres[0][0];
      const primerApellido = apellidos[0];
      
      const correoGenerado = `${primerNombreInicial.toLowerCase()}${primerApellido.toLowerCase()}@mail.com`;
      correoGeneradoInput.value = correoGenerado;
      correoGeneradoPreview.textContent = correoGenerado;
    } else {
      correoGeneradoInput.value = '';
      correoGeneradoPreview.textContent = '';
    }
  }
</script>
