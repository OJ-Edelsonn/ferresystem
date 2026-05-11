<?php
require_once __DIR__ . '/../config/app.php';
$tel_whatsapp = '51900749742';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo ?? 'J&S Ferretería') ?> — Quiparacra, Pasco</title>
    <link rel="stylesheet" href="/public/css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar-js">
    <div class="navbar-logo">J&S <span>Ferretería</span></div>
    <button class="nav-toggle" onclick="toggleMenu()" aria-label="Menú">
        <span></span><span></span><span></span>
    </button>
    <ul class="navbar-nav" id="navMenu">
        <li><a href="<?= BASE_URL ?>/public/index.php"
               class="<?= ($pagina_activa ?? '') === 'inicio'    ? 'activo' : '' ?>">Inicio</a></li>
        <li><a href="<?= BASE_URL ?>/public/catalogo.php"
               class="<?= ($pagina_activa ?? '') === 'catalogo'  ? 'activo' : '' ?>">Catálogo</a></li>
        <li><a href="<?= BASE_URL ?>/public/cotizador.php"
               class="<?= ($pagina_activa ?? '') === 'cotizador' ? 'activo' : '' ?>">Cotizador</a></li>
        <li><a href="<?= BASE_URL ?>/public/servicios.php"
               class="<?= ($pagina_activa ?? '') === 'servicios' ? 'activo' : '' ?>">Servicios de obra</a></li>
        <li><a href="<?= BASE_URL ?>/public/contacto.php"
               class="<?= ($pagina_activa ?? '') === 'contacto'  ? 'activo' : '' ?>">Contacto</a></li>
    </ul>
</nav>

<script>
function toggleMenu() {
    document.getElementById('navMenu').classList.toggle('abierto');
}
</script>