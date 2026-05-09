<?php
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/VentaController.php';

$titulo_pagina = 'Ventas';
$pagina_activa = 'ventas';

$controller = new VentaController();
$mensaje = '';
$error   = '';

// ── Procesar acciones POST ──────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'registrar') {
        $productos_ids   = $_POST['producto_id']      ?? [];
        $cantidades      = $_POST['cantidad']          ?? [];
        $precios         = $_POST['precio_unitario']   ?? [];

        $detalle = [];
        foreach ($productos_ids as $i => $pid) {
            if (!empty($pid) && $cantidades[$i] > 0) {
                $cantidad  = (int)$cantidades[$i];
                $precio    = (float)$precios[$i];
                $detalle[] = [
                    'producto_id'     => $pid,
                    'cantidad'        => $cantidad,
                    'precio_unitario' => $precio,
                    'subtotal'        => $cantidad * $precio,
                ];
            }
        }

        $resultado = $controller->registrar(
            $_POST['fecha'] ?? date('Y-m-d'),
            $detalle,
            trim($_POST['observacion'] ?? '')
        );
        if ($resultado['ok']) {
            $mensaje = 'Venta registrada correctamente.';
        } else {
            $error = $resultado['error'];
        }
    }

    if ($accion === 'anular') {
        $resultado = $controller->anular($_POST['venta_id'], trim($_POST['motivo'] ?? ''));
        $mensaje   = $resultado['ok'] ? 'Venta anulada correctamente.' : $resultado['error'];
        if (!$resultado['ok']) $error = $mensaje;
    }
}

// ── Datos para mostrar ──────────────────────────────────────
$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
$fecha_fin    = $_GET['fecha_fin']    ?? date('Y-m-d');
$ventas       = $controller->listar($fecha_inicio, $fecha_fin);
$productos    = $controller->productos();
$total_hoy    = $controller->totalHoy();
$total_mes    = $controller->totalMes();

require_once 'layout.php';
?>

<!-- Alertas -->
<?php if ($mensaje && !$error): ?>
    <div class="alert alert-success py-2"><?= htmlspecialchars($mensaje) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<!-- KPIs rápidos -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div style="background:#fff;border-radius:10px;padding:1rem 1.4rem;border-left:4px solid #C0392B;">
            <div style="font-size:0.8rem;color:#888;">Ventas hoy</div>
            <div style="font-size:1.5rem;font-weight:700;color:#1B2A4A;">S/. <?= number_format($total_hoy, 2) ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div style="background:#fff;border-radius:10px;padding:1rem 1.4rem;border-left:4px solid #185FA5;">
            <div style="font-size:0.8rem;color:#888;">Ventas este mes</div>
            <div style="font-size:1.5rem;font-weight:700;color:#1B2A4A;">S/. <?= number_format($total_mes, 2) ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div style="background:#fff;border-radius:10px;padding:1rem 1.4rem;border-left:4px solid #27AE60;">
            <div style="font-size:0.8rem;color:#888;">Ventas en el período</div>
            <div style="font-size:1.5rem;font-weight:700;color:#1B2A4A;"><?= count($ventas) ?></div>
        </div>
    </div>
</div>

<!-- Barra superior -->
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <form method="GET" class="d-flex gap-2 flex-wrap align-items-center">
        <label class="form-label mb-0 fw-semibold">Desde:</label>
        <input type="date" name="fecha_inicio" value="<?= $fecha_inicio ?>" class="form-control form-control-sm" style="width:160px">
        <label class="form-label mb-0 fw-semibold">Hasta:</label>
        <input type="date" name="fecha_fin" value="<?= $fecha_fin ?>" class="form-control form-control-sm" style="width:160px">
        <button class="btn btn-sm btn-outline-secondary">Filtrar</button>
    </form>
    <button class="btn btn-sm text-white" style="background:#C0392B;"
            data-bs-toggle="modal" data-bs-target="#modalVenta">
        + Registrar venta
    </button>
</div>

<!-- Tabla de ventas -->
<div style="background:#fff;border-radius:10px;overflow:hidden;">
    <table class="table table-hover mb-0" style="font-size:0.9rem;">
        <thead style="background:#1B2A4A;color:#fff;">
            <tr>
                <th>#</th>
                <th>Fecha</th>
                <th>Total</th>
                <th>Observación</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($ventas)): ?>
            <tr><td colspan="6" class="text-center py-4 text-muted">No hay ventas en este período.</td></tr>
        <?php endif; ?>
        <?php foreach ($ventas as $v): ?>
            <tr class="<?= $v['anulada'] ? 'table-secondary text-muted' : '' ?>">
                <td><?= $v['id'] ?></td>
                <td><?= date('d/m/Y', strtotime($v['fecha'])) ?></td>
                <td><strong>S/. <?= number_format($v['total'], 2) ?></strong></td>
                <td><?= htmlspecialchars($v['observacion'] ?? '') ?></td>
                <td>
                    <?php if ($v['anulada']): ?>
                        <span class="badge bg-secondary">Anulada</span>
                    <?php else: ?>
                        <span class="badge bg-success">Válida</span>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="d-flex gap-1">
                        <button class="btn btn-sm btn-outline-primary"
                            onclick="verDetalle(<?= $v['id'] ?>)">Ver</button>
                        <?php if (!$v['anulada']): ?>
                            <button class="btn btn-sm btn-outline-danger"
                                onclick="abrirAnular(<?= $v['id'] ?>)">Anular</button>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- ══ MODAL: Registrar venta ══ -->
<div class="modal fade" id="modalVenta" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST" id="formVenta">
        <input type="hidden" name="accion" value="registrar">
        <div class="modal-header" style="background:#1B2A4A;">
            <h5 class="modal-title text-white">Registrar venta</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div class="row g-2 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Fecha *</label>
                    <input type="date" name="fecha" class="form-control"
                           value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Observación</label>
                    <input type="text" name="observacion" class="form-control"
                           placeholder="Ej: Cliente al contado">
                </div>
            </div>

            <!-- Líneas de productos -->
            <div id="lineasVenta"></div>

            <button type="button" class="btn btn-sm btn-outline-secondary mt-2"
                    onclick="agregarLinea()">+ Agregar producto</button>

            <hr>
            <div class="text-end">
                <strong>Total: S/. <span id="totalVenta">0.00</span></strong>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn text-white" style="background:#C0392B;">Guardar venta</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ══ MODAL: Anular venta ══ -->
<div class="modal fade" id="modalAnular" tabindex="-1">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <form method="POST">
        <input type="hidden" name="accion" value="anular">
        <input type="hidden" name="venta_id" id="anular_venta_id">
        <div class="modal-header" style="background:#C0392B;">
            <h5 class="modal-title text-white">Anular venta</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <p class="mb-2">Venta #<strong id="anular_venta_num"></strong></p>
            <label class="form-label fw-semibold">Motivo de anulación *</label>
            <input type="text" name="motivo" class="form-control" required
                   placeholder="Ej: Error en el precio">
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-danger">Anular</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ══ MODAL: Ver detalle ══ -->
<div class="modal fade" id="modalDetalle" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header" style="background:#1B2A4A;">
            <h5 class="modal-title text-white">Detalle de venta</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="contenidoDetalle">
            Cargando...
        </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const productos = <?= json_encode($productos) ?>;

function agregarLinea() {
    const contenedor = document.getElementById('lineasVenta');
    const idx = contenedor.children.length;
    const options = productos.map(p =>
        `<option value="${p.id}" data-precio="${p.precio_venta}">${p.nombre} (S/. ${parseFloat(p.precio_venta).toFixed(2)})</option>`
    ).join('');

    const div = document.createElement('div');
    div.className = 'row g-2 mb-2 linea-venta';
    div.innerHTML = `
        <div class="col-md-5">
            <select name="producto_id[]" class="form-select form-select-sm sel-producto" required onchange="actualizarPrecio(this, ${idx})">
                <option value="">Seleccionar producto...</option>
                ${options}
            </select>
        </div>
        <div class="col-md-2">
            <input type="number" name="cantidad[]" class="form-control form-control-sm inp-cantidad"
                   min="1" value="1" required onchange="calcularTotal()">
        </div>
        <div class="col-md-3">
            <input type="number" name="precio_unitario[]" class="form-control form-control-sm inp-precio"
                   step="0.01" min="0.01" value="0" required onchange="calcularTotal()">
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-sm btn-outline-danger w-100"
                    onclick="this.closest('.linea-venta').remove(); calcularTotal()">✕</button>
        </div>`;
    contenedor.appendChild(div);
}

function actualizarPrecio(sel, idx) {
    const precio = sel.options[sel.selectedIndex]?.dataset.precio || 0;
    const linea  = sel.closest('.linea-venta');
    linea.querySelector('.inp-precio').value = parseFloat(precio).toFixed(2);
    calcularTotal();
}

function calcularTotal() {
    let total = 0;
    document.querySelectorAll('.linea-venta').forEach(linea => {
        const cant  = parseFloat(linea.querySelector('.inp-cantidad').value) || 0;
        const precio = parseFloat(linea.querySelector('.inp-precio').value) || 0;
        total += cant * precio;
    });
    document.getElementById('totalVenta').textContent = total.toFixed(2);
}

function abrirAnular(id) {
    document.getElementById('anular_venta_id').value = id;
    document.getElementById('anular_venta_num').textContent = id;
    new bootstrap.Modal(document.getElementById('modalAnular')).show();
}

function verDetalle(venta_id) {
    document.getElementById('contenidoDetalle').innerHTML = 'Cargando...';
    new bootstrap.Modal(document.getElementById('modalDetalle')).show();
    fetch(`/ferresystem/admin/ajax/detalle_venta.php?id=${venta_id}`)
        .then(r => r.text())
        .then(html => document.getElementById('contenidoDetalle').innerHTML = html);
}

// Agregar primera línea al abrir el modal
document.getElementById('modalVenta').addEventListener('show.bs.modal', function() {
    document.getElementById('lineasVenta').innerHTML = '';
    agregarLinea();
    calcularTotal();
});
</script>

<?php require_once 'layout_footer.php'; ?>