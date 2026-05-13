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
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link rel="shortcut icon" type="image/png" href="img/favicon.png">
    <link rel="stylesheet" href="css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Forzar menú oculto en móvil ANTES de que cualquier JS o Bootstrap cargue */
        @media (max-width: 768px) {
            #navMenu {
                display: none !important;
            }
            #navMenu.abierto {
                display: flex !important;
            }
        }
    </style>
</head>
<body>

<nav class="navbar-js">
    <a href="index.php" style="text-decoration:none;">
        <div class="navbar-logo">J&S <span>Ferretería</span></div>
    </a>

    <button class="nav-toggle" id="navToggle" aria-label="Abrir menú">
        <span></span>
        <span></span>
        <span></span>
    </button>

    <!-- El menú NO tiene clase 'abierto' por defecto — empieza oculto -->
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
    var abierto = false;

    toggle.addEventListener('click', function() {
        abierto = !abierto;
        if (abierto) {
            menu.classList.add('abierto');
            toggle.classList.add('activo');
        } else {
            menu.classList.remove('abierto');
            toggle.classList.remove('activo');
        }
    });

    // Cerrar al tocar un enlace en móvil
    var links = menu.querySelectorAll('a');
    for (var i = 0; i < links.length; i++) {
        links[i].addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                abierto = false;
                menu.classList.remove('abierto');
                toggle.classList.remove('activo');
            }
        });
    }

    // Cerrar si se redimensiona a desktop
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            menu.classList.remove('abierto');
            toggle.classList.remove('activo');
            abierto = false;
        }
    });
})();
</script>