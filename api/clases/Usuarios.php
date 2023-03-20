<?php
require_once 'Conexion.class.php';

class User extends Conexion {

    public function login($username, $password) {
        $sql = "SELECT * FROM users WHERE username=:username";
        $stmt = $this->conectar()->prepare($sql);
        $stmt->bindValue(':username', $username);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            http_response_code(404);
            echo json_encode(array("success" => false, "message" => "Nombre de usuario no encontrado"));
            return;
        }

        if (!password_verify($password, $row['password'])) {
            http_response_code(401);
            echo json_encode(array("success" => false, "message" => "Contraseña incorrecta"));
            return;
        }

        session_start();
        $_SESSION['id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        http_response_code(200);
        echo json_encode(array("success" => true, "message" => "Inicio de sesión exitoso"));
        exit();
    }
}
