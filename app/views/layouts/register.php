 

<div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
  <div class="sm:mx-auto sm:w-full sm:max-w-md">
    <img class="mx-auto h-10 w-auto" src="https://tailwindui.com/img/logos/workflow-mark-indigo-600.svg" alt="Your Company">
    <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Registro</h2>
  </div>

  <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
    <form id="FormRegistro" class="space-y-6" action="/generatecorreo/registrar" method="POST" novalidate>
        <div>
          <label for="identificacion" class="block text-sm font-medium text-gray-700">Cédula</label>
          <input id="identificacion" name="identificacion" type="int" autocomplete="cedula" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50">
        </div>

        <div>
          <label for="nombres" class="block text-sm font-medium text-gray-700">Nombre</label>
          <input id="nombres" name="nombres" type="text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50">
        </div>

        <div>
          <label for="apellidos" class="block text-sm font-medium text-gray-700">Apellido</label>
          <input id="apellidos" name="apellidos" type="text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50">
        </div>

        <div>
          <label for="correo_generado" class="block text-sm font-medium text-gray-700">Correo Generado</label>
          <input id="correo_generado" name="correo_generado" type="email" autocomplete="email" required readonly class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50">
          <p class="mt-2 text-sm text-gray-500">El correo generado será: <span id="correo_generado_preview"></span></p>
        </div>

        <div>
          <label for="password" class="block text-sm font-medium text-gray-700">Password</label>   
          <input id="password" name="password" type="password" autocomplete="password" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50">
        </div>

        <div>
          <input id="rol" name="rol" type="hidden" value="usuario"> <!-- Campo oculto para el rol -->
        </div>

        <div>
          <button type="submit" class="flex justify-center w-full rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Crear Cuenta</button>
        </div>


        <p class="mt-10 text-center text-sm text-gray-500">
        ¿Ya tienes cuenta?
        <a href="/generatecorreo/login" class="font-semibold leading-6 text-indigo-600 hover:text-indigo-500">Inicia sesión aquí

        
      </form>    
  </div> 
 
  


 </div>
