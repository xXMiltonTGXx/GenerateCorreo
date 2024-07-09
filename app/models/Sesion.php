<?php

namespace App\Models;

use PDO;

class Sesion {
    private $conn;
    private $table_name = "sesion";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function registrarInicioSesion($user_id) {
        // Verificar si ya existe una sesión activa
        $sesion_activa = $this->obtenerSesionActiva($user_id);
        
        if ($sesion_activa) {
            // Cerrar la sesión activa existente
            $query = "UPDATE " . $this->table_name . " SET cierre_sesion = NOW() WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $sesion_activa['id'], PDO::PARAM_INT);
            $stmt->execute();
        }
    
        // Crear una nueva sesión
        $query = "INSERT INTO " . $this->table_name . " (idUsuario, inicio_sesion, intentos_fallidos, bloqueado) VALUES (:idUsuario, NOW(), 0, 0)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idUsuario', $user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function registrarCierreSesion($user_id) {
        $query = "UPDATE " . $this->table_name . " SET cierre_sesion = NOW() WHERE idUsuario = :idUsuario AND cierre_sesion IS NULL";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idUsuario', $user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function obtenerSesionActiva($user_id) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE idUsuario = :idUsuario AND cierre_sesion IS NULL";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idUsuario', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function registrarIntentoFallido($user_id) {
        $query = "UPDATE " . $this->table_name . " SET intentos_fallidos = intentos_fallidos + 1 WHERE idUsuario = :idUsuario ORDER BY inicio_sesion DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idUsuario', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        $query = "SELECT intentos_fallidos FROM " . $this->table_name . " WHERE idUsuario = :idUsuario ORDER BY inicio_sesion DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idUsuario', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['intentos_fallidos'] : 0;
    }
    
    public function bloquearUsuario($user_id) {
        $query = "UPDATE " . $this->table_name . " SET bloqueado = 1 WHERE idUsuario = :idUsuario ORDER BY inicio_sesion DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idUsuario', $user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function verificarBloqueo($user_id) {
        $query = "SELECT bloqueado FROM " . $this->table_name . " WHERE idUsuario = :idUsuario ORDER BY inicio_sesion DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idUsuario', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['bloqueado'] == 1 : false;
    }

    public function eliminarSesiones($user_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE idUsuario = :idUsuario";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idUsuario', $user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function obtenerAccesosRecientes($user_id, $limite = 10) {
        $query = "SELECT inicio_sesion, cierre_sesion FROM " . $this->table_name . " WHERE idUsuario = :idUsuario ORDER BY inicio_sesion DESC LIMIT :limite";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idUsuario', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
}
?>
