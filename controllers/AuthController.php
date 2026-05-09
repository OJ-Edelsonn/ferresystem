<?php
require_once __DIR__ . '/../config/database.php';

class AuthController {

    // Inicia sesión verificando usuario y contraseña
    public function login($email, $password) {
        $conn = getConexion();

        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($password, $usuario['password'])) {
            session_start();
            $_SESSION['usuario_id']     = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            header("Location: /ferresystem/admin/dashboard.php");
            exit;
        } else {
            return "Correo o contraseña incorrectos.";
        }
    }

    // Cierra la sesión activa
    public function logout() {
        session_start();
        session_destroy();
        header("Location: /ferresystem/admin/login.php");
        exit;
    }

    // Verifica si hay sesión activa — usar al inicio de cada página del panel
    public static function verificarSesion() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: /ferresystem/admin/login.php");
            exit;
        }
    }
}
?>