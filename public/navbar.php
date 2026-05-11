<?php
require_once __DIR__ . '/../config/app.php';
$tel_whatsapp = '51900749742';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link rel="shortcut icon" type="image/png" href="img/favicon.png">
    <title><?= htmlspecialchars($titulo ?? 'J&S Ferretería') ?> — Quiparacra, Pasco</title>
    <link rel="stylesheet" href="/public/css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar-js">
    <!-- Logo clickeable que lleva al inicio -->
    <a href="index.php" style="text-decoration:none;">
        <div class="navbar-logo">J&S <span>Ferretería</span></div>
    </a>

    <!-- Botón hamburguesa solo visible en móvil -->
    <button class="nav-toggle" id="navToggle" aria-label="Menú">
        <span></span><span></span><span></span>
    </button>

    <!-- Menú de navegación -->
    <ul class="navbar-nav" id="navMenu">
        <li><a href="index.php"
               class="<?= ($pagina_activa ?? '') === 'inicio'    ? 'activo' : '' ?>">Inicio</a></li>
        <li><a href="catalogo.php"
               class="<?= ($pagina_activa ?? '') === 'catalogo'  ? 'activo' : '' ?>">Catálogo</a></li>
        <li><a href="cotizador.php"
               class="<?= ($pagina_activa ?? '') === 'cotizador' ? 'activo' : '' ?>">Cotizador</a></li>
        <li><a href="servicios.php"
               class="<?= ($pagina_activa ?? '') === 'servicios' ? 'activo' : '' ?>">Servicios de obra</a></li>
        <li><a href="contacto.php"
               class="<?= ($pagina_activa ?? '') === 'contacto'  ? 'activo' : '' ?>">Contacto</a></li>
    </ul>
</nav>

<script>
// Toggle del menú hamburguesa
document.getElementById('navToggle').addEventListener('click', function() {
    var menu = document.getElementById('navMenu');
    menu.classList.toggle('abierto');

    // Animar las tres líneas del botón
    this.classList.toggle('activo');
});

// Cerrar menú al hacer clic en un enlace (en móvil)
document.querySelectorAll('#navMenu a').forEach(function(link) {
    link.addEventListener('click', function() {
        document.getElementById('navMenu').classList.remove('abierto');
        document.getElementById('navToggle').classList.remove('activo');
    });
});
</script>