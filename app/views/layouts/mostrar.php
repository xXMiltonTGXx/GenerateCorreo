
<?php

require_once __DIR__ . '/../inc/nav.php'; 

 

?>
 
    <header class="bg-white shadow">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900 text-center uppercase">Mostrar Usuarios</h1>
        </div>
    </header>
    
    <div class="container mx-auto px-4">
    <h1 class="text-xl font-bold my-4">Lista de Usuarios</h1>
    <?php if (!empty($users)): ?>
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3">Cédula</th>
                    <th scope="col" class="px-6 py-3">Nombre</th>
                    <th scope="col" class="px-6 py-3">Apellidos</th>
                    <th scope="col" class="px-6 py-3">Correo</th>
                    <th scope="col" class="px-6 py-3">ROL</th>
                    <th scope="col" class="px-6 py-3">Editar</th>
                    <th scope="col" class="px-6 py-3">Eliminar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr class="bg-white border-b">
                        <td class="px-6 py-4"><?= htmlspecialchars($user['identificacion']) ?></td>
                        <td class="px-6 py-4"><?= htmlspecialchars($user['nombres']) ?></td>
                        <td class="px-6 py-4"><?= htmlspecialchars($user['apellidos']) ?></td>
                        <td class="px-6 py-4"><?= htmlspecialchars($user['correo_generado']) ?></td>
                        <td class="px-6 py-4"><?= htmlspecialchars($user['rol']) ?></td>

                        <td class="px-6 py-4"><a class="text-blue-600 hover:underline" href="edit-user?idUsuario=<?= $user['idUsuario'] ?>" >Editar</a></td> 

                        <td class="px-6 py-4">
                        <form action="/generatecorreo/delete-user" method="POST">  
                            <input type="hidden" name="idUsuario" value="<?= $user['idUsuario'] ?>">
                            <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                        </form> 
 
                        </td> 
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay usuarios disponibles.</p>
    <?php endif; ?>
</div>




 </div>


 