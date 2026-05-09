<?php
require_once __DIR__ . '/../../controllers/AuthController.php';
require_once __DIR__ . '/../../controllers/VentaController.php';

AuthController::verificarSesion();

$venta_id  = (int)($_GET['id'] ?? 0);
$controller = new VentaController();
$detalle    = $controller->detalle($venta_id);

if (empty($detalle)) {
    echo '<p class="text-muted">Sin detalle disponible.</p>';
    exit;
}

$total = 0;
echo '<table class="table table-sm mb-0">';
echo '<thead><tr><th>Producto</th><th>Cant.</th><th>Precio</th><th>Subtotal</th></tr></thead>';
echo '<tbody>';
foreach ($detalle as $item) {
    $total += $item['subtotal'];
    echo '<tr>';
    echo '<td>' . htmlspecialchars($item['producto_nombre']) . '</td>';
    echo '<td>' . $item['cantidad'] . '</td>';
    echo '<td>S/. ' . number_format($item['precio_unitario'], 2) . '</td>';
    echo '<td>S/. ' . number_format($item['subtotal'], 2) . '</td>';
    echo '</tr>';
}
echo '</tbody>';
echo '<tfoot><tr><td colspan="3" class="text-end fw-bold">Total:</td>';
echo '<td><strong>S/. ' . number_format($total, 2) . '</strong></td></tr></tfoot>';
echo '</table>';
?>