<?php
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/CotizacionController.php';

$titulo_pagina = 'Cotizaciones de obra';
$pagina_activa = 'cotizaciones';

$controller = new CotizacionController();
$mensaje = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'crear') {
        $datos = [
            'cliente_nombre'   => trim($_POST['cliente_nombre'] ?? ''),
            'cliente_telefono' => trim($_POST['cliente_telefono'] ?? ''),
            'descripcion_obra' => trim($_POST['descripcion_obra'] ?? ''),
            'mano_obra'        => (float)($_POST['mano_obra'] ?? 0),
            'fecha'            => $_POST['fecha'] ?? date('Y-m-d'),
        ];

        $descripciones = $_POST['mat_descripcion'] ?? [];
        $cantidades    = $_POST['mat_cantidad']    ?? [];
        $precios       = $_POST['mat_precio']      ?? [];

        $materiales = [];
        foreach ($descripciones as $i => $desc) {
            if (!empty($desc) && $cantidades[$i] > 0) {
                $cant  = (float)$cantidades[$i];
                $precio = (float)$precios[$i];
                $materiales[] = [
                    'descripcion'    => $desc,
                    'cantidad'       => $cant,
                    'precio_unitario'=> $precio,
                    'subtotal'       => $cant * $precio,
                ];
            }
        }

        $resultado = $controller->crear($datos, $materiales);
        $mensaje   = $resultado['ok'] ? 'Cotización creada correctamente.' : '';
        $error     = $resultado['ok'] ? '' : $resultado['error'];
    }

    if ($accion === 'eliminar') {
        $controller->eliminar($_POST['cotizacion_id']);
        $mensaje = 'Cotización eliminada.';
    }
}

$busqueda    = $_GET['buscar'] ?? '';
$cotizaciones = $controller->listar($busqueda);

require_once 'layout.php';
?>

<?php if ($mensaje && !$error): ?>
    <div class="alert alert-success py-2"><?= htmlspecialchars($mensaje) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<!-- Barra superior -->
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <form method="GET" class="d-flex gap-2">
        <input type="text" name="buscar" value="<?= htmlspecialchars($busqueda) ?>"
               placeholder="Buscar por cliente..." class="form-control form-control-sm" style="width:220px">
        <button class="btn btn-sm btn-outline-secondary">Buscar</button>
        <a href="cotizaciones.php" class="btn btn-sm btn-outline-secondary">Limpiar</a>
    </form>
    <button class="btn btn-sm text-white" style="background:#C0392B;"
            data-bs-toggle="modal" data-bs-target="#modalCotizacion">
        + Nueva cotización
    </button>
</div>

<!-- Tabla -->
<div style="background:#fff;border-radius:10px;overflow:hidden;">
    <table class="table table-hover mb-0" style="font-size:0.9rem;">
        <thead style="background:#1B2A4A;color:#fff;">
            <tr>
                <th>#</th>
                <th>Cliente</th>
                <th>Teléfono</th>
                <th>Obra</th>
                <th>Materiales</th>
                <th>Mano de obra</th>
                <th>Total</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($cotizaciones)): ?>
            <tr><td colspan="9" class="text-center py-4 text-muted">No hay cotizaciones registradas.</td></tr>
        <?php endif; ?>
        <?php foreach ($cotizaciones as $c): ?>
            <tr>
                <td><?= $c['id'] ?></td>
                <td><strong><?= htmlspecialchars($c['cliente_nombre']) ?></strong></td>
                <td><?= htmlspecialchars($c['cliente_telefono']) ?></td>
                <td><?= htmlspecialchars(substr($c['descripcion_obra'], 0, 40)) ?>...</td>
                <td>S/. <?= number_format($c['total_materiales'], 2) ?></td>
                <td>S/. <?= number_format($c['mano_obra'], 2) ?></td>
                <td><strong>S/. <?= number_format($c['total_general'], 2) ?></strong></td>
                <td><?= date('d/m/Y', strtotime($c['fecha'])) ?></td>
                <td>
                    <div class="d-flex gap-1">
                        <button class="btn btn-sm btn-outline-primary"
                            onclick="verCotizacion(<?= $c['id'] ?>)">Ver</button>
                        <form method="POST" onsubmit="return confirm('¿Eliminar esta cotización?')">
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="cotizacion_id" value="<?= $c['id'] ?>">
                            <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                        </form>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- ══ MODAL: Nueva cotización ══ -->
<div class="modal fade" id="modalCotizacion" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST">
        <input type="hidden" name="accion" value="crear">
        <div class="modal-header" style="background:#1B2A4A;">
            <h5 class="modal-title text-white">Nueva cotización de obra</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div class="row g-2 mb-2">
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Cliente *</label>
                    <input type="text" name="cliente_nombre" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Teléfono</label>
                    <input type="text" name="cliente_telefono" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Fecha *</label>
                    <input type="date" name="fecha" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
            </div>
            <div class="mb-2">
                <label class="form-label fw-semibold">Descripción de la obra *</label>
                <textarea name="descripcion_obra" class="form-control" rows="2" required
                          placeholder="Ej: Construcción de cuarto de 4x5m en adobe"></textarea>
            </div>
            <hr>
            <div class="fw-semibold mb-2">Materiales</div>
            <div class="row g-1 mb-1" style="font-size:0.82rem;color:#888;">
                <div class="col-md-5">Descripción</div>
                <div class="col-md-2">Cantidad</div>
                <div class="col-md-3">Precio unit. (S/.)</div>
                <div class="col-md-2">Subtotal</div>
            </div>
            <div id="lineasMaterial"></div>
            <button type="button" class="btn btn-sm btn-outline-secondary mt-2"
                    onclick="agregarMaterial()">+ Agregar material</button>
            <hr>
            <div class="row g-2 align-items-center">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Mano de obra (S/.)</label>
                    <input type="number" name="mano_obra" id="mano_obra" class="form-control"
                           step="0.01" min="0" value="0" onchange="calcularTotalCot()">
                </div>
                <div class="col-md-6 text-end">
                    <div style="font-size:0.85rem;color:#888;">Total materiales: S/. <span id="totalMat">0.00</span></div>
                    <div style="font-size:1.1rem;font-weight:700;color:#1B2A4A;">
                        Total general: S/. <span id="totalGeneral">0.00</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn text-white" style="background:#C0392B;">Guardar cotización</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ══ MODAL: Ver cotización ══ -->
<div class="modal fade" id="modalVerCot" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header" style="background:#1B2A4A;">
            <h5 class="modal-title text-white">Detalle de cotización</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="contenidoVerCot">Cargando...</div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function agregarMaterial() {
    const contenedor = document.getElementById('lineasMaterial');
    const div = document.createElement('div');
    div.className = 'row g-1 mb-1 linea-material';
    div.innerHTML = `
        <div class="col-md-5">
            <input type="text" name="mat_descripcion[]" class="form-control form-control-sm"
                   placeholder="Ej: Bolsa de cemento" required>
        </div>
        <div class="col-md-2">
            <input type="number" name="mat_cantidad[]" class="form-control form-control-sm inp-cant"
                   min="0.01" step="0.01" value="1" required onchange="calcularTotalCot()">
        </div>
        <div class="col-md-3">
            <input type="number" name="mat_precio[]" class="form-control form-control-sm inp-precio"
                   min="0" step="0.01" value="0" required onchange="calcularTotalCot()">
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-sm btn-outline-danger w-100"
                    onclick="this.closest('.linea-material').remove(); calcularTotalCot()">✕</button>
        </div>`;
    contenedor.appendChild(div);
}

function calcularTotalCot() {
    let totalMat = 0;
    document.querySelectorAll('.linea-material').forEach(linea => {
        const cant  = parseFloat(linea.querySelector('.inp-cant').value)  || 0;
        const precio = parseFloat(linea.querySelector('.inp-precio').value) || 0;
        totalMat += cant * precio;
    });
    const manoObra = parseFloat(document.getElementById('mano_obra').value) || 0;
    document.getElementById('totalMat').textContent     = totalMat.toFixed(2);
    document.getElementById('totalGeneral').textContent = (totalMat + manoObra).toFixed(2);
}

function verCotizacion(id) {
    document.getElementById('contenidoVerCot').innerHTML = 'Cargando...';
    new bootstrap.Modal(document.getElementById('modalVerCot')).show();
    fetch(`/ferresystem/admin/ajax/detalle_cotizacion.php?id=${id}`)
        .then(r => r.text())
        .then(html => document.getElementById('contenidoVerCot').innerHTML = html);
}

document.getElementById('modalCotizacion').addEventListener('show.bs.modal', function() {
    document.getElementById('lineasMaterial').innerHTML = '';
    agregarMaterial();
    calcularTotalCot();
});
</script>

<?php require_once 'layout_footer.php'; ?>