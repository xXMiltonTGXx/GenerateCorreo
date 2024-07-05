<?php
namespace App\Controllers;

use App\Models\User;
use App\Config\Database;


class UserController {
    private $userModel;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->userModel = new User($db);
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cedula = $_POST['identificacion'];
            $nombre = $_POST['nombres'];
            $apellido = $_POST['apellidos']; 
            $password = $_POST['password']; 
            $rol = $_POST['rol']; 

             // Verificar si la cédula ya existe
             if ($this->userModel->checkCedulaExists($cedula)) {
                echo "<div class='alert alert-danger'>La cédula ya está registrada. Por favor, usa una cédula diferente.</div>";
                require __DIR__ . '/../views/layouts/register.php';
                exit;
            }
            
            // Generar correo
            $nombres = explode(' ', $nombre);
            $apellidos = explode(' ', $apellido);
            
            $primer_nombre = strtolower(substr($nombres[0], 0, 1)); // Primer letra del primer nombre en minúscula
            $primer_apellido = strtolower($apellidos[0]); // Primer apellido en minúscula
            $segundo_apellido_inicial = (count($apellidos) > 1) ? strtolower(substr($apellidos[1], 0, 1)) : ''; // Primer caracter del segundo apellido en minúscula si existe
    
            $correo_generado = $primer_nombre . $primer_apellido . $segundo_apellido_inicial . '@mail.com';
    
            if ($this->userModel->register($cedula, $nombre, $apellido, $correo_generado, $password, $rol)) {
                // Iniciar sesión después de registrar
                session_start();
                $_SESSION['user_id'] = $this->userModel->getUserIdByEmail($correo_generado);
                $_SESSION['user_email'] = $correo_generado;
    
                header("Location: /generatecorreo/dashboard");
                exit;
            } else {
                echo "<div class='alert alert-danger'>No se pudo registrar al usuario.</div>";
            }
        }
    }
 

    public function showUsers() {
        $users = $this->userModel->getAllUsers();
        require __DIR__ . '/../views/layouts/mostrar.php';
    }



    public function delete() {
        $userId = $_POST['idUsuario'] ?? null;
        if ($userId === null) {
            die('ID del usuario es requerido.');
        }
    
        $userId = filter_var($userId, FILTER_VALIDATE_INT);
        if (false === $userId) {
            die('ID inválido.');
        }
    
        // Debugging: print the ID
        echo "ID recibido: " . htmlspecialchars($userId);
    
        if ($this->userModel->delete($userId)) {
            header("Location: /generatecorreo/mostrarP");
            exit;
        } else {
            die("No se pudo eliminar el usuario.");
        }
    }

    public function editView($UsuarioId) {
        $user = $this->userModel->getUserById($UsuarioId);
        require __DIR__ . '/../views/layouts/edit.php';
    }
    
    public function editUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idUsuario = $_POST['idUsuario'];
            $identificacion = $_POST['identificacion'];
            $nombres = $_POST['nombres'];
            $apellidos = $_POST['apellidos'];
            $correo_generado = $_POST['correo_generado'];
            $rol = $_POST['rol'];
    
            if ($this->userModel->update($idUsuario, $identificacion, $nombres, $apellidos, $correo_generado, $rol)) {
                header("Location: /generatecorreo/mostrarP");
                exit;
            } else {
                echo "<div class='alert alert-danger'>No se pudo actualizar el usuario.</div>";
            }
        }
    }

    public function search() {
        if (!empty($_GET['search'])) {
            $search_term = $_GET['search'];
            $users = $this->userModel->searchUsersByID($search_term);
            require __DIR__ . '/../views/layouts/mostrar.php'; // Asegúrate de tener una vista para mostrar los resultados
        } else {
            // Llamar a una vista diferente o manejar la ausencia de término de búsqueda
            $users = [];
            require __DIR__ . '/../views/layouts/buscar.php';
        }
    }


    
    
}