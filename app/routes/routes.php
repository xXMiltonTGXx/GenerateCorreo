<?php
use App\Controllers\UserController;
use App\Controllers\LoginController;
use App\Controllers\LogoutController;

function getView() {
    $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    $requestUri = trim($requestUri, '/');
    $baseDir = 'generatecorreo';

    if (strpos($requestUri, $baseDir) === 0) {
        $requestUri = substr($requestUri, strlen($baseDir) + 1);
    }
    $requestUri = trim($requestUri, '/');

    $getRoutes = [
        '' => function() { require __DIR__ . '/../views/layouts/login.php'; },
        'login' => function() { require __DIR__ . '/../views/layouts/login.php'; },  
        'dashboard' => function() { require __DIR__ . '/../views/layouts/dashboard.php'; },
        'register' => function() { require __DIR__ . '/../views/layouts/register.php'; },
        'mostrarP' => function() {
            $controller = new UserController();
            $controller->showUsers();
        },
        'buscarP' => function() { require __DIR__ . '/../views/layouts/buscar.php'; },
        'edit-user' => function() {
            $controller = new UserController();
            $controller->editView($_GET['idUsuario']);
        },
        'buscar-user' => function() {
            $controller = new UserController();
            $controller->search();
       },
        
        'logout' => function() {
            $controller = new LogoutController();
            $controller->logout();
        }, 
    ];

    $postRoutes = [
        'registrar' => function() {
            $controller = new UserController();
            $controller->register();
        },
        'iniciar-sesion'=>function(){
            $controller = new LoginController();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->login();
            } else {
                require __DIR__ . '/../views/layouts/login.php';
            }
            
        },
        'delete-user' => function() {
        $controller = new UserController();
        $controller->delete();
        },

        'edit-user' => function() {
            $controller = new UserController();
            $controller->editUser();
        },
    ];

    if ($requestMethod == 'GET' && isset($getRoutes[$requestUri])) {
        $getRoutes[$requestUri]();
    } elseif ($requestMethod == 'POST' && isset($postRoutes[$requestUri])) {
        $postRoutes[$requestUri]();
    } else {
        require __DIR__ . '/../views/layouts/404.php';
    }
}

getView();
