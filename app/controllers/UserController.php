<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\Sesion; 
use App\Config\Database;


class UserController {
    private $userModel;
    private $sesionModel;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->userModel = new User($db);
        $this->sesionModel = new Sesion($db);
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cedula = $_POST['identificacion'];
            $nombre = $_POST['nombres'];
            $apellido = $_POST['apellidos']; 
            $correo_generado = $_POST['correo_generado']; 
            $password = $_POST['password']; 
            $rol = $_POST['rol']; 
  
         
            if ($this->userModel->register($cedula, $nombre, $apellido, $correo_generado, $password, $rol)) {
                // Iniciar sesión después de registrar
                session_start();
                $_SESSION['logged_in'] = true;
                $_SESSION['user_id'] = $this->userModel->getUserIdByEmail($correo_generado);
                $_SESSION['user_name'] = $nombre;
                $_SESSION['user_email'] = $correo_generado;
                // Registrar la sesión
                $this->sesionModel->registrarInicioSesion($_SESSION['user_id']);
    
                header("Location: /generatecorreo/dashboard");
                exit;
            } else {
                echo "<div class='alert alert-danger'>No se pudo registrar al usuario.</div>";
            }
        }
    }

    public function checkCedula() {
        header('Content-Type: application/json');
        if (isset($_GET['cedula'])) {
            $cedula = $_GET['cedula'];
            $exists = $this->userModel->checkCedulaExists($cedula);
            echo json_encode(['exists' => $exists]);
        } else {
            echo json_encode(['exists' => false]);
        }
        exit(); // Asegúrate de terminar el script para que no se envíe contenido adicional
    }
    

    
    public function checkEmail() {
        header('Content-Type: application/json');
        if (isset($_GET['email'])) {
            $email = $_GET['email'];
            $exists = $this->userModel->checkEmailExists($email);
            echo json_encode(['exists' => $exists]);
        } else {
            echo json_encode(['exists' => false]);
        }
        exit(); // Asegúrate de terminar el script para que no se envíe contenido adicional
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
    
        // Eliminar las sesiones del usuario
        if (!$this->sesionModel->eliminarSesiones($userId)) {
            die("No se pudieron eliminar las sesiones del usuario.");
        }

        // Eliminar el usuario
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