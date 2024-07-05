<?php

require_once __DIR__ . '/../inc/nav.php';
 

 

?>



    <header class="bg-white shadow">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900 text-center">BUSCAR</h1>
        </div>
    </header>

    <div class="max-w-lg mx-auto mt-10 px-4 py-8 bg-white shadow-lg rounded-lg">
    <h2 class="text-2xl font-semibold text-center text-gray-800">Buscar Usuario</h2>
    <form class="mt-8 space-y-6" action="/generatecorreo/buscar-user" method="GET">
        <div>
            <label for="search" class="block text-sm font-medium text-gray-700">Ingrese la cedula del usuario a buscar:</label>
            <input type="text" id="search" name="search" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Escribe aquí la cedula">
        </div>
        <div>
            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Buscar
            </button>
        </div>
    </form>
</div>



 


</div>