<?php
// Este archivo se incluye al inicio de cada página del panel.
// Recibe $titulo_pagina y $pagina_activa desde la página que lo incluye.
AuthController::verificarSesion();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FerreSystem — <?= htmlspecialchars($titulo_pagina ?? 'Panel') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #f4f4f4; font-family: 'Segoe UI', sans-serif; }

        /* ── Sidebar ── */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: 230px;
            height: 100vh;
            background: #1B2A4A;
            display: flex;
            flex-direction: column;
            z-index: 100;
        }
        .sidebar-logo {
            padding: 1.4rem 1.2rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .sidebar-logo h2 {
            color: #fff;
            font-size: 1.1rem;
            font-weight: 700;
            margin: 0;
        }
        .sidebar-logo p {
            color: rgba(255,255,255,0.45);
            font-size: 0.75rem;
            margin: 3px 0 0;
        }
        .sidebar-nav {
            flex: 1;
            padding: 1rem 0;
            overflow-y: auto;
        }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0.65rem 1.2rem;
            color: rgba(255,255,255,0.65);
            text-decoration: none;
            font-size: 0.9rem;
            transition: background 0.15s, color 0.15s;
            border-left: 3px solid transparent;
        }
        .sidebar-nav a:hover {
            background: rgba(255,255,255,0.06);
            color: #fff;
        }
        .sidebar-nav a.activo {
            background: rgba(192,57,43,0.18);
            color: #fff;
            border-left-color: #C0392B;
            font-weight: 600;
        }
        .sidebar-footer {
            padding: 1rem 1.2rem;
            border-top: 1px solid rgba(255,255,255,0.08);
        }
        .sidebar-footer a {
            color: rgba(255,255,255,0.5);
            font-size: 0.82rem;
            text-decoration: none;
        }
        .sidebar-footer a:hover { color: #fff; }

        /* ── Contenido principal ── */
        .main-content {
            margin-left: 230px;
            padding: 2rem;
            min-height: 100vh;
        }
        .page-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #1B2A4A;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-logo">
        <h2>J&S Ferretería</h2>
        <p>Panel de administración</p>
    </div>
    <nav class="sidebar-nav">
        <?php require_once __DIR__ . '/../config/app.php'; ?>
        <a href="<?= BASE_URL ?>/admin/dashboard.php"
           class="<?= ($pagina_activa ?? '') === 'dashboard'    ? 'activo' : '' ?>">
            📊 Dashboard
        </a>
        <a href="<?= BASE_URL ?>/admin/inventario.php"
           class="<?= ($pagina_activa ?? '') === 'inventario'   ? 'activo' : '' ?>">
            📦 Inventario
        </a>
        <a href="<?= BASE_URL ?>/admin/ventas.php"
           class="<?= ($pagina_activa ?? '') === 'ventas'       ? 'activo' : '' ?>">
            🛒 Ventas
        </a>
        <a href="<?= BASE_URL ?>/admin/caja.php"
           class="<?= ($pagina_activa ?? '') === 'caja'         ? 'activo' : '' ?>">
            💰 Caja
        </a>
        <a href="<?= BASE_URL ?>/admin/pedidos.php"
           class="<?= ($pagina_activa ?? '') === 'pedidos'      ? 'activo' : '' ?>">
            📋 Pedidos
        </a>
        <a href="<?= BASE_URL ?>/admin/cotizaciones.php"
           class="<?= ($pagina_activa ?? '') === 'cotizaciones' ? 'activo' : '' ?>">
            📝 Cotizaciones de obra
        </a>
    </nav>
    <div class="sidebar-footer">
        <a href="<?= BASE_URL ?>/admin/logout.php">⬅ Cerrar sesión</a>
    </div>
</div>

<div class="main-content">
    <div class="page-title"><?= htmlspecialchars($titulo_pagina ?? '') ?></div> 