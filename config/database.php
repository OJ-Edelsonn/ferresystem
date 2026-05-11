<?php
define('DB_HOST', 'sql300.infinityfree.com');
define('DB_NAME', 'if0_41886787_ferresystem');
define('DB_USER', 'if0_41886787');
define('DB_PASS', 'TU_CONTRASEÑA_AQUI');  // Pon tu contraseña real aquí

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