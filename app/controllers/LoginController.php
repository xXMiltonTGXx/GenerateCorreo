<?php
namespace App\Controllers;

use App\Models\User;
use App\Config\Database;

class LoginController {
    private $userModel;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->userModel = new User($db);
    }

    public function login() {
        session_start();
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verifica si ya existe la variable de sesión para intentos fallidos
            if (!isset($_SESSION['login_attempts'])) {
                $_SESSION['login_attempts'] = 0;
            }

            // Configuración de límite de intentos y tiempo de bloqueo
            $max_login_attempts = 3; // Número máximo de intentos permitidos
            $lockout_time = 60; // Tiempo de bloqueo en segundos después de superar los intentos

            // Verifica si el usuario está bloqueado
            if (isset($_SESSION['lockout_time']) && $_SESSION['lockout_time'] > time()) {
                $remaining_time = $_SESSION['lockout_time'] - time();
                echo "<div class='alert alert-danger'>Has excedido el número máximo de intentos. Por favor, inténtalo de nuevo después de $remaining_time segundos.</div>";
                require __DIR__ . '/../views/layouts/login.php';
                exit;
            }

            $email = $_POST['email'];
            $password = $_POST['password'];
    
            if ($user = $this->userModel->login($email, $password)) {
                // Restablece el contador de intentos al iniciar sesión correctamente
                $_SESSION['login_attempts'] = 0;
                $_SESSION['logged_in'] = true;  // Establece una variable de sesión que indica que el usuario está logueado.
                $_SESSION['user_email'] = $user['correo_generado']; // Almacena el correo generado en la sesión
                $_SESSION['user_name'] = $user['nombres']; // Almacena el nombre del usuario en la sesión
                
                header("Location: /generatecorreo/dashboard");
                exit;
            } else {
                // Incrementa el contador de intentos fallidos
                $_SESSION['login_attempts']++;

                // Verifica si se ha alcanzado el límite de intentos
                if ($_SESSION['login_attempts'] >= $max_login_attempts) {
                    $_SESSION['lockout_time'] = time() + $lockout_time; // Establece el tiempo de bloqueo
                    echo "<div class='alert alert-danger'>Has excedido el número máximo de intentos. Por favor, inténtalo de nuevo después de $lockout_time segundos.</div>";
                    require __DIR__ . '/../views/layouts/login.php'; 
                    exit;
                } else {
                    echo "<div class='alert alert-danger'>Email o contraseña incorrectos. Intento " . $_SESSION['login_attempts'] . " de $max_login_attempts.</div>";
                    require __DIR__ . '/../views/layouts/login.php';
                }
            }
        }
    }
}
