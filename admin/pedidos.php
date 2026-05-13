<?php
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/PedidoController.php';
require_once __DIR__ . '/../controllers/VentaController.php';

$titulo_pagina = 'Pedidos';
$pagina_activa = 'pedidos';

$pedidoCtrl = new PedidoController();
$ventaCtrl  = new VentaController();
$mensaje = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'cambiar_estado') {
        $resultado = $pedidoCtrl->cambiarEstado($_POST['pedido_id'], $_POST['estado']);
        $mensaje   = $resultado['ok'] ? 'Estado actualizado.' : $resultado['error'];
        if (!$resultado['ok']) $error = $mensaje;
    }

    // Registrar venta desde pedido entregado
    if ($accion === 'registrar_venta_desde_pedido') {
        $pedido_id = $_POST['pedido_id'];
        $detalle_pedido = $pedidoCtrl->detalle($pedido_id);
        $detalle_venta  = [];
        foreach ($detalle_pedido as $item) {
            $detalle_venta[] = [
                'producto_id'     => $item['producto_id'],
                'cantidad'        => $item['cantidad'],
                'precio_unitario' => $item['precio_unitario'],
                'subtotal'        => $item['subtotal'],
            ];
        }
        $resultado = $ventaCtrl->registrar(date('Y-m-d'), $detalle_venta, 'Pedido #' . $pedido_id);
        $mensaje   = $resultado['ok'] ? 'Venta registrada desde pedido #' . $pedido_id . '.' : $resultado['error'];
        if (!$resultado['ok']) $error = $mensaje;
    }
}

$estado_filtro = $_GET['estado'] ?? '';
$pedidos       = $pedidoCtrl->listar($estado_filtro ?: null);

require_once 'layout.php';
?>

<?php if ($mensaje && !$error): ?>
    <div class="alert alert-success py-2"><?= htmlspecialchars($mensaje) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<!-- Filtro por estado -->
<div class="d-flex gap-2 mb-3 flex-wrap">
    <?php
    $estados = ['' => 'Todos', 'pendiente' => 'Pendientes', 'en_proceso' => 'En proceso', 'entregado' => 'Entregados'];
    foreach ($estados as $val => $label):
    ?>
        <a href="?estado=<?= $val ?>"
            class="btn btn-sm <?= $estado_filtro === $val ? 'text-white' : 'btn-outline-secondary' ?>"
            style="<?= $estado_filtro === $val ? 'background:#1B2A4A;' : '' ?>">
            <?= $label ?>
        </a>
    <?php endforeach; ?>
</div>

<!-- Tabla de pedidos -->
<div style="background:#fff;border-radius:10px;overflow:hidden;">
    <table class="table table-hover mb-0" style="font-size:0.9rem;">
        <thead style="background:#1B2A4A;color:#fff;">
            <tr>
                <th>#</th>
                <th>Cliente</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($pedidos)): ?>
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">No hay pedidos.</td>
                </tr>
            <?php endif; ?>
            <?php foreach ($pedidos as $p): ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td><strong><?= htmlspecialchars($p['cliente_nombre']) ?></strong></td>
                    <td><?= htmlspecialchars($p['cliente_telefono']) ?></td>
                    <td><?= htmlspecialchars($p['cliente_direccion']) ?></td>
                    <td>S/. <?= number_format($p['total'], 2) ?></td>
                    <td>
                        <?php
                        $badges = [
                            'pendiente'  => 'bg-warning text-dark',
                            'en_proceso' => 'bg-primary',
                            'entregado'  => 'bg-success',
                        ];
                        $labels = [
                            'pendiente'  => 'Pendiente',
                            'en_proceso' => 'En proceso',
                            'entregado'  => 'Entregado',
                        ];
                        ?>
                        <span class="badge <?= $badges[$p['estado']] ?>">
                            <?= $labels[$p['estado']] ?>
                        </span>
                    </td>
                    <td><?= date('d/m/Y H:i', strtotime($p['fecha'])) ?></td>
                    <td>
                        <div class="d-flex gap-1 flex-wrap">
                            <button class="btn btn-sm btn-outline-primary"
                                onclick="verDetallePedido(<?= $p['id'] ?>)">Ver</button>
                            <?php if ($p['estado'] === 'pendiente'): ?>
                                <form method="POST">
                                    <input type="hidden" name="accion" value="cambiar_estado">
                                    <input type="hidden" name="pedido_id" value="<?= $p['id'] ?>">
                                    <input type="hidden" name="estado" value="en_proceso">
                                    <button class="btn btn-sm btn-outline-primary">En proceso</button>
                                </form>
                            <?php endif; ?>
                            <?php if ($p['estado'] === 'en_proceso'): ?>
                                <form method="POST">
                                    <input type="hidden" name="accion" value="cambiar_estado">
                                    <input type="hidden" name="pedido_id" value="<?= $p['id'] ?>">
                                    <input type="hidden" name="estado" value="entregado">
                                    <button class="btn btn-sm btn-outline-success">Entregado</button>
                                </form>
                                <form method="POST" onsubmit="return confirm('¿Registrar venta desde este pedido?')">
                                    <input type="hidden" name="accion" value="registrar_venta_desde_pedido">
                                    <input type="hidden" name="pedido_id" value="<?= $p['id'] ?>">
                                    <button class="btn btn-sm btn-outline-secondary">+ Venta</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal detalle pedido -->
<div class="modal fade" id="modalDetallePedido" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:#1B2A4A;">
                <h5 class="modal-title text-white">Detalle del pedido</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="contenidoDetallePedido">Cargando...</div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function verDetallePedido(id) {
        document.getElementById('contenidoDetallePedido').innerHTML = 'Cargando...';
        new bootstrap.Modal(document.getElementById('modalDetallePedido')).show();
        fetch(`/admin/ajax/detalle_pedido.php?id=${id}`)
            .then(r => r.text())
            .then(html => document.getElementById('contenidoDetallePedido').innerHTML = html);
    }
</script>

<?php require_once 'layout_footer.php'; ?>