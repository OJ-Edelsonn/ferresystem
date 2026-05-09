<?php
// Configuración de conexión a MySQL usando PDO
// Este archivo es el único lugar donde van las credenciales de la BD

define('DB_HOST', 'localhost');
define('DB_NAME', 'ferresystem');
define('DB_USER', 'root');
define('DB_PASS', '');  // En XAMPP local la contraseña es vacía por defecto

function getConexion() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $opciones = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        return new PDO($dsn, DB_USER, DB_PASS, $opciones);
    } catch (PDOException $e) {
        die("Error de conexión: " . $e->getMessage());
    }
}
?>