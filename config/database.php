<?php
// Forzar zona horaria de Perú en todo el sistema
date_default_timezone_set('America/Lima');

if ($_SERVER['HTTP_HOST'] === 'localhost') {
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'ferresystem');
    define('DB_USER', 'root');
    define('DB_PASS', '');
} else {
    define('DB_HOST', 'sql300.infinityfree.com');
    define('DB_NAME', 'if0_41886787_ferresystem');
    define('DB_USER', 'if0_41886787');
    define('DB_PASS', 'd6fHVqm8KERm');
}

function getConexion() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $opciones = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $conn = new PDO($dsn, DB_USER, DB_PASS, $opciones);

        // Forzar zona horaria en MySQL también
        $conn->exec("SET time_zone = '-05:00'");

        return $conn;
    } catch (PDOException $e) {
        die("Error de conexión: " . $e->getMessage());
    }
}
?>