<?php
namespace App\Models;

use PDO;

class User {
    private $conn;
    private $table_name = "usuarios";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function checkCedulaExists($cedula) {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE identificacion = :cedula";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cedula', $cedula, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function register($identificacion, $nombres, $apellidos, $correo_generado, $password, $rol) {
        $query = "INSERT INTO usuarios (identificacion, nombres, apellidos, correo_generado, password, rol) VALUES (:identificacion, :nombres, :apellidos, :correo_generado, :password, :rol)";
        $stmt = $this->conn->prepare($query);
    
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
        $stmt->bindParam(':identificacion', $identificacion);
        $stmt->bindParam(':nombres', $nombres);
        $stmt->bindParam(':apellidos', $apellidos);
        $stmt->bindParam(':correo_generado', $correo_generado);
        $stmt->bindParam(':password', $password_hash);
        $stmt->bindParam(':rol', $rol);
    
        return $stmt->execute();
    }

    
    public function login($email, $password) {
        $query = "SELECT idUsuario, nombres, correo_generado, password FROM " . $this->table_name . " WHERE correo_generado = :correo_generado LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':correo_generado', $email);
        $stmt->execute();
    
        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $row['password'])) {
                return $row; // Retorna todos los detalles del usuario
            }
        }
        return false;
    }
    

    public function getAllUsers() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    



    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE idUsuario = :idUsuario";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idUsuario', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
     
    
    public function getUserById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE idUsuario = :idUsuario");
        $stmt->bindParam(':idUsuario', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserIdByEmail($email) {
        $query = "SELECT idUsuario FROM " . $this->table_name . " WHERE correo_generado = :correo_generado LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':correo_generado', $email);
        $stmt->execute();
    
        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['idUsuario'];
        }
        return false;
    }
    

    public function update($idUsuario, $identificacion, $nombres, $apellidos, $correo_generado, $rol) {
        $stmt = $this->conn->prepare("UPDATE usuarios SET identificacion = :identificacion, nombres = :nombres, apellidos = :apellidos, correo_generado = :correo_generado, rol = :rol WHERE idUsuario = :idUsuario");
        $stmt->bindParam(':idUsuario', $idUsuario);
        $stmt->bindParam(':identificacion', $identificacion);
        $stmt->bindParam(':nombres', $nombres); 
        $stmt->bindParam(':correo_generado', $correo_generado);
        $stmt->bindParam(':apellidos', $apellidos);
        $stmt->bindParam(':rol', $rol); 
        return $stmt->execute();
    }
    
    public function searchUsersByID($identificacion) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE identificacion LIKE :identificacion";
        $stmt = $this->conn->prepare($query);
        $identificacion = "%" . $identificacion . "%"; // Preparar la cédula para una búsqueda parcial
        $stmt->bindParam(':identificacion', $identificacion, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}
?>
