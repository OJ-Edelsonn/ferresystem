<?php
require_once __DIR__ . '/../../controllers/AuthController.php';
require_once __DIR__ . '/../../controllers/CotizacionController.php';

AuthController::verificarSesion();

$id          = (int)($_GET['id'] ?? 0);
$controller  = new CotizacionController();
$cotizacion  = $controller->obtener($id);
$materiales  = $controller->materiales($id);

if (!$cotizacion) { echo '<p>No encontrada.</p>'; exit; }
?>
<p><strong>Cliente:</strong> <?= htmlspecialchars($cotizacion['cliente_nombre']) ?></p>
<p><strong>Teléfono:</strong> <?= htmlspecialchars($cotizacion['cliente_telefono']) ?></p>
<p><strong>Obra:</strong> <?= htmlspecialchars($cotizacion['descripcion_obra']) ?></p>
<hr>
<table class="table table-sm mb-2">
    <thead><tr><th>Material</th><th>Cant.</th><th>Precio</th><th>Subtotal</th></tr></thead>
    <tbody>
    <?php foreach ($materiales as $m): ?>
        <tr>
            <td><?= htmlspecialchars($m['descripcion']) ?></td>
            <td><?= $m['cantidad'] ?></td>
            <td>S/. <?= number_format($m['precio_unitario'], 2) ?></td>
            <td>S/. <?= number_format($m['subtotal'], 2) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<div class="text-end">
    <div>Materiales: <strong>S/. <?= number_format($cotizacion['total_materiales'], 2) ?></strong></div>
    <div>Mano de obra: <strong>S/. <?= number_format($cotizacion['mano_obra'], 2) ?></strong></div>
    <div style="font-size:1.1rem;color:#1B2A4A;">
        Total general: <strong>S/. <?= number_format($cotizacion['total_general'], 2) ?></strong>
    </div>
</div>