<?php
namespace App\Controllers;

class LogoutController {
    public function logout() {
        session_start();
        $_SESSION = [];
        session_destroy();
        header("Location: /generatecorreo/login");
        exit;
    }
}
