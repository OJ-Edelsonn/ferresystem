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
    <link rel="stylesheet" href="css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar-js">
    <a href="index.php" class="navbar-logo-link">
        <div class="navbar-logo">J&S <span>Ferretería</span></div>
    </a>

    <button class="nav-toggle" id="navToggle" aria-label="Abrir menú">
        <span></span>
        <span></span>
        <span></span>
    </button>

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
(function() {
    var toggle = document.getElementById('navToggle');
    var menu   = document.getElementById('navMenu');

    toggle.addEventListener('click', function() {
        var abierto = menu.getAttribute('data-abierto') === 'true';
        if (abierto) {
            menu.setAttribute('data-abierto', 'false');
            menu.style.display = 'none';
            toggle.classList.remove('activo');
        } else {
            menu.setAttribute('data-abierto', 'true');
            menu.style.display = 'flex';
            toggle.classList.add('activo');
        }
    });

    // Cerrar menú al tocar un enlace en móvil
    var links = menu.querySelectorAll('a');
    links.forEach(function(link) {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                menu.setAttribute('data-abierto', 'false');
                menu.style.display = 'none';
                toggle.classList.remove('activo');
            }
        });
    });
})();
</script>