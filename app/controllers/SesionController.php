<?php 

namespace App\Controllers;

use App\Models\User;
use App\Models\Sesion;
use App\Config\Database;

class SesionController {
    private $userModel;
    private $sesionModel;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->userModel = new User($db);
        $this->sesionModel = new Sesion($db);
    }

    public function login() {
        session_start();
        unset($_SESSION['error_message']); // Clear any previous error message
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $emailOrUsername = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($emailOrUsername) || empty($password)) {
                $_SESSION['error_message'] = 'Faltan datos para iniciar sesión.';
                echo 'error';
                exit;
            }

            // Verifica si el usuario existe y la contraseña es correcta
            $user = $this->userModel->login($emailOrUsername, $password);

            if ($user) {
                // Verificar si el usuario está bloqueado
                if ($this->sesionModel->verificarBloqueo($user['idUsuario'])) {
                    $_SESSION['error_message'] = 'La cuenta está bloqueada debido a múltiples intentos fallidos de inicio de sesión.';
                    echo 'error';
                    exit;
                }

                // Verificar si ya existe una sesión activa para este usuario
                $sesion_activa = $this->sesionModel->obtenerSesionActiva($user['idUsuario']);
                if ($sesion_activa) {
                    $_SESSION['error_message'] = 'Ya tienes una sesión activa en otro dispositivo.';
                    http_response_code(409); // Conflict
                    echo 'active_session';
                    exit;
                }

                // Registrar inicio de sesión
                $this->sesionModel->registrarInicioSesion($user['idUsuario']);

                // Iniciar sesión
                $_SESSION['logged_in'] = true;
                $_SESSION['user_id'] = $user['idUsuario'];
                $_SESSION['user_name'] = $user['nombres'];
                $_SESSION['user_email'] = $user['correo_generado'];

                echo 'success';
                exit;
            } else {
                // Registrar intento fallido
                $user_id = $this->userModel->getUserIdByEmail($emailOrUsername);
                if ($user_id) {
                    $intentos_fallidos = $this->sesionModel->registrarIntentoFallido($user_id);
                    // Bloquear usuario después de 3 intentos fallidos
                    if ($intentos_fallidos >= 3) {
                        $this->sesionModel->bloquearUsuario($user_id);
                        $_SESSION['error_message'] = 'La cuenta ha sido bloqueada después de 3 intentos fallidos.';
                        echo 'blocked';
                        exit;
                    } else {
                        $_SESSION['error_message'] = "Intento $intentos_fallidos de 3 fallido.";
                        echo 'attempts:' . $intentos_fallidos;
                        exit;
                    }
                }

                $_SESSION['error_message'] = 'Usuario o contraseña incorrectos.';
                echo 'error';
                exit;
            }
        } else {
            require __DIR__ . '/../views/layouts/login.php'; // Cargar la vista de login si no es un POST
        }
    }

    public function logout() {
        session_start();
        $user_id = $_SESSION['user_id'];
        $this->sesionModel->registrarCierreSesion($user_id);

        session_unset();
        session_destroy();

        header("Location: /generatecorreo/login");
        exit;
    }

    public function obtenerUltimosAccesos() {
        session_start();
        $user_id = $_SESSION['user_id'];
        $accesos = $this->sesionModel->obtenerAccesosRecientes($user_id);
        echo json_encode($accesos);
        exit;
    }
    
    
}
?>
