<?php
require_once __DIR__ . '/../../controllers/AuthController.php';
require_once __DIR__ . '/../../controllers/PedidoController.php';

AuthController::verificarSesion();

$pedido_id  = (int)($_GET['id'] ?? 0);
$controller = new PedidoController();
$pedido     = $controller->obtener($pedido_id);
$detalle    = $controller->detalle($pedido_id);

if (!$pedido) {
    echo '<p class="text-muted">Pedido no encontrado.</p>';
    exit;
}
?>
<p><strong>Cliente:</strong> <?= htmlspecialchars($pedido['cliente_nombre']) ?></p>
<p><strong>Teléfono:</strong> <?= htmlspecialchars($pedido['cliente_telefono']) ?></p>
<p><strong>Dirección:</strong> <?= htmlspecialchars($pedido['cliente_direccion']) ?></p>
<hr>
<table class="table table-sm mb-0">
    <thead>
        <tr><th>Producto</th><th>Cant.</th><th>Precio</th><th>Subtotal</th></tr>
    </thead>
    <tbody>
    <?php $total = 0; foreach ($detalle as $item): $total += $item['subtotal']; ?>
        <tr>
            <td><?= htmlspecialchars($item['producto_nombre']) ?></td>
            <td><?= $item['cantidad'] ?></td>
            <td>S/. <?= number_format($item['precio_unitario'], 2) ?></td>
            <td>S/. <?= number_format($item['subtotal'], 2) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3" class="text-end fw-bold">Total:</td>
            <td><strong>S/. <?= number_format($total, 2) ?></strong></td>
        </tr>
    </tfoot>
</table>