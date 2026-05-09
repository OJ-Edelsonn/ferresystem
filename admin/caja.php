<?php
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/CajaController.php';

$titulo_pagina = 'Caja';
$pagina_activa = 'caja';

$controller = new CajaController();
$mensaje = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'registrar') {
        $resultado = $controller->registrar(
            $_POST['fecha']        ?? date('Y-m-d'),
            $_POST['tipo']         ?? 'ingreso',
            trim($_POST['descripcion'] ?? ''),
            (float)($_POST['monto'] ?? 0)
        );
        $mensaje = $resultado['ok'] ? 'Movimiento registrado.' : '';
        $error   = $resultado['ok'] ? '' : $resultado['error'];
    }

    if ($accion === 'eliminar') {
        $controller->eliminar($_POST['movimiento_id']);
        $mensaje = 'Movimiento eliminado.';
    }
}

$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-d');
$fecha_fin    = $_GET['fecha_fin']    ?? date('Y-m-d');
$movimientos  = $controller->listar($fecha_inicio, $fecha_fin);
$saldo        = $controller->saldoDia(date('Y-m-d'));

require_once 'layout.php';
?>

<?php if ($mensaje && !$error): ?>
    <div class="alert alert-success py-2"><?= htmlspecialchars($mensaje) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<!-- Saldo del día -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div style="background:#fff;border-radius:10px;padding:1rem 1.4rem;border-left:4px solid #27AE60;">
            <div style="font-size:0.8rem;color:#888;">Ingresos hoy</div>
            <div style="font-size:1.5rem;font-weight:700;color:#1B2A4A;">S/. <?= number_format($saldo['ingresos'], 2) ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div style="background:#fff;border-radius:10px;padding:1rem 1.4rem;border-left:4px solid #C0392B;">
            <div style="font-size:0.8rem;color:#888;">Egresos hoy</div>
            <div style="font-size:1.5rem;font-weight:700;color:#1B2A4A;">S/. <?= number_format($saldo['egresos'], 2) ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div style="background:#fff;border-radius:10px;padding:1rem 1.4rem;border-left:4px solid #185FA5;">
            <div style="font-size:0.8rem;color:#888;">Saldo del día</div>
            <div style="font-size:1.5rem;font-weight:700;color:<?= $saldo['saldo'] >= 0 ? '#27AE60' : '#C0392B' ?>;">
                S/. <?= number_format($saldo['saldo'], 2) ?>
            </div>
        </div>
    </div>
</div>

<!-- Barra superior -->
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <form method="GET" class="d-flex gap-2 flex-wrap align-items-center">
        <label class="form-label mb-0 fw-semibold">Desde:</label>
        <input type="date" name="fecha_inicio" value="<?= $fecha_inicio ?>" class="form-control form-control-sm" style="width:155px">
        <label class="form-label mb-0 fw-semibold">Hasta:</label>
        <input type="date" name="fecha_fin" value="<?= $fecha_fin ?>" class="form-control form-control-sm" style="width:155px">
        <button class="btn btn-sm btn-outline-secondary">Filtrar</button>
    </form>
    <button class="btn btn-sm text-white" style="background:#C0392B;"
            data-bs-toggle="modal" data-bs-target="#modalMovimiento">
        + Registrar movimiento
    </button>
</div>

<!-- Tabla movimientos -->
<div style="background:#fff;border-radius:10px;overflow:hidden;">
    <table class="table table-hover mb-0" style="font-size:0.9rem;">
        <thead style="background:#1B2A4A;color:#fff;">
            <tr>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>Descripción</th>
                <th>Monto</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($movimientos)): ?>
            <tr><td colspan="5" class="text-center py-4 text-muted">No hay movimientos en este período.</td></tr>
        <?php endif; ?>
        <?php foreach ($movimientos as $m): ?>
            <tr>
                <td><?= date('d/m/Y', strtotime($m['fecha'])) ?></td>
                <td>
                    <?php if ($m['tipo'] === 'ingreso'): ?>
                        <span class="badge bg-success">Ingreso</span>
                    <?php else: ?>
                        <span class="badge bg-danger">Egreso</span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($m['descripcion']) ?></td>
                <td><strong>S/. <?= number_format($m['monto'], 2) ?></strong></td>
                <td>
                    <form method="POST" onsubmit="return confirm('¿Eliminar este movimiento?')">
                        <input type="hidden" name="accion" value="eliminar">
                        <input type="hidden" name="movimiento_id" value="<?= $m['id'] ?>">
                        <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- ══ MODAL: Registrar movimiento ══ -->
<div class="modal fade" id="modalMovimiento" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <input type="hidden" name="accion" value="registrar">
        <div class="modal-header" style="background:#1B2A4A;">
            <h5 class="modal-title text-white">Registrar movimiento de caja</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div class="row g-2 mb-2">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Fecha *</label>
                    <input type="date" name="fecha" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tipo *</label>
                    <select name="tipo" class="form-select" required>
                        <option value="ingreso">Ingreso</option>
                        <option value="egreso">Egreso</option>
                    </select>
                </div>
            </div>
            <div class="mb-2">
                <label class="form-label fw-semibold">Descripción *</label>
                <input type="text" name="descripcion" class="form-control" required
                       placeholder="Ej: Compra de mercadería a proveedor">
            </div>
            <div class="mb-2">
                <label class="form-label fw-semibold">Monto (S/.) *</label>
                <input type="number" name="monto" class="form-control" step="0.01" min="0.01" required>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn text-white" style="background:#C0392B;">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php require_once 'layout_footer.php'; ?>