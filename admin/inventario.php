<?php
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/ProductoController.php';

$titulo_pagina = 'Inventario';
$pagina_activa = 'inventario';

$controller = new ProductoController();

$mensaje = '';
$error   = '';

// ── Procesar acciones POST ──────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'crear' || $accion === 'editar') {
        $datos = [
            'nombre'        => trim($_POST['nombre'] ?? ''),
            'categoria_id'  => $_POST['categoria_id'] ?? '',
            'descripcion'   => trim($_POST['descripcion'] ?? ''),
            'precio_compra' => $_POST['precio_compra'] ?? 0,
            'precio_venta'  => $_POST['precio_venta'] ?? 0,
            'stock_actual'  => $_POST['stock_actual'] ?? 0,
            'stock_minimo'  => $_POST['stock_minimo'] ?? 5,
            'foto'          => $_POST['foto'] ?? null,
        ];
        if ($accion === 'crear') {
            $resultado = $controller->crear($datos);
        } else {
            $resultado = $controller->actualizar($_POST['producto_id'], $datos);
        }
        if ($resultado['ok']) {
            $mensaje = $accion === 'crear' ? 'Producto agregado correctamente.' : 'Producto actualizado.';
        } else {
            $error = implode('<br>', $resultado['errores']);
        }
    }

    if ($accion === 'eliminar') {
        $controller->eliminar($_POST['producto_id']);
        $mensaje = 'Producto eliminado.';
    }

    if ($accion === 'entrada_stock') {
        $resultado = $controller->registrarEntrada(
            $_POST['producto_id'],
            (int)$_POST['cantidad'],
            trim($_POST['observacion'] ?? '')
        );
        $mensaje = $resultado['ok'] ? 'Stock actualizado correctamente.' : implode('<br>', $resultado['errores']);
    }
}

// ── Obtener datos para mostrar ──────────────────────────────
$categoria_filtro = $_GET['categoria'] ?? null;
$busqueda         = $_GET['buscar'] ?? '';
$productos        = $controller->listar($categoria_filtro, $busqueda);
$categorias       = $controller->categorias();
$stock_critico    = $controller->stockCritico();

require_once 'layout.php';
?>

<!-- Alertas -->
<?php if ($mensaje): ?>
    <div class="alert alert-success py-2"><?= htmlspecialchars($mensaje) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger py-2"><?= $error ?></div>
<?php endif; ?>

<!-- Alerta stock crítico -->
<?php if (!empty($stock_critico)): ?>
    <div class="alert alert-warning py-2 mb-3">
        ⚠️ <strong><?= count($stock_critico) ?> producto(s) con stock crítico:</strong>
        <?= implode(', ', array_column($stock_critico, 'nombre')) ?>
    </div>
<?php endif; ?>

<!-- Barra superior -->
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <form method="GET" class="d-flex gap-2 flex-wrap">
        <input type="text" name="buscar" value="<?= htmlspecialchars($busqueda) ?>"
               placeholder="Buscar producto..." class="form-control form-control-sm" style="width:200px">
        <select name="categoria" class="form-select form-select-sm" style="width:180px">
            <option value="">Todas las categorías</option>
            <?php foreach ($categorias as $cat): ?>
                <option value="<?= $cat['id'] ?>"
                    <?= $categoria_filtro == $cat['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button class="btn btn-sm btn-outline-secondary">Filtrar</button>
        <a href="inventario.php" class="btn btn-sm btn-outline-secondary">Limpiar</a>
    </form>
    <button class="btn btn-sm text-white" style="background:#C0392B;"
            data-bs-toggle="modal" data-bs-target="#modalProducto">
        + Agregar producto
    </button>
</div>

<!-- Tabla de productos -->
<div style="background:#fff;border-radius:10px;overflow:hidden;">
    <table class="table table-hover mb-0" style="font-size:0.9rem;">
        <thead style="background:#1B2A4A;color:#fff;">
            <tr>
                <th>Foto</th>
                <th>Producto</th>
                <th>Categoría</th>
                <th>P. Compra</th>
                <th>P. Venta</th>
                <th>Stock</th>
                <th>Mín.</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($productos)): ?>
            <tr><td colspan="9" class="text-center py-4 text-muted">No hay productos registrados.</td></tr>
        <?php endif; ?>
        <?php foreach ($productos as $p): ?>
            <?php $critico = $p['stock_actual'] <= $p['stock_minimo']; ?>
            <tr class="<?= $critico ? 'table-danger' : '' ?>">
                <td>
                    <?php if (!empty($p['foto'])): ?>
                        <img src="/ferresystem/public/img/productos/<?= htmlspecialchars($p['foto']) ?>"
                             style="width:48px;height:48px;object-fit:cover;border-radius:6px;">
                    <?php else: ?>
                        <div style="width:48px;height:48px;background:#f0f0f0;border-radius:6px;
                                    display:flex;align-items:center;justify-content:center;font-size:1.4rem;">
                            📦
                        </div>
                    <?php endif; ?>
                </td>
                <td>
                    <strong><?= htmlspecialchars($p['nombre']) ?></strong>
                    <?php if ($p['descripcion']): ?>
                        <br><small class="text-muted"><?= htmlspecialchars($p['descripcion']) ?></small>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($p['categoria_nombre']) ?></td>
                <td>S/. <?= number_format($p['precio_compra'], 2) ?></td>
                <td>S/. <?= number_format($p['precio_venta'], 2) ?></td>
                <td><strong><?= $p['stock_actual'] ?></strong></td>
                <td><?= $p['stock_minimo'] ?></td>
                <td>
                    <?php if ($critico): ?>
                        <span class="badge bg-danger">Stock crítico</span>
                    <?php else: ?>
                        <span class="badge bg-success">OK</span>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="d-flex gap-1">
                        <button class="btn btn-sm btn-outline-primary"
                            onclick='abrirEditar(<?= json_encode($p) ?>)'>Editar</button>
                        <button class="btn btn-sm btn-outline-success"
                            onclick="abrirEntrada(<?= $p['id'] ?>, '<?= htmlspecialchars($p['nombre']) ?>')">+Stock</button>
                        <form method="POST" onsubmit="return confirm('¿Eliminar este producto?')">
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="producto_id" value="<?= $p['id'] ?>">
                            <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                        </form>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- ══ MODAL: Agregar / Editar producto ══ -->
<div class="modal fade" id="modalProducto" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <input type="hidden" name="accion" id="modal_accion" value="crear">
        <input type="hidden" name="producto_id" id="modal_producto_id">
        <div class="modal-header" style="background:#1B2A4A;">
            <h5 class="modal-title text-white" id="modal_titulo">Agregar producto</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div class="mb-2">
                <label class="form-label fw-semibold">Nombre *</label>
                <input type="text" name="nombre" id="modal_nombre" class="form-control" required>
            </div>
            <div class="mb-2">
                <label class="form-label fw-semibold">Categoría *</label>
                <select name="categoria_id" id="modal_categoria" class="form-select" required>
                    <option value="">Seleccionar...</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-2">
                <label class="form-label fw-semibold">Descripción</label>
                <textarea name="descripcion" id="modal_descripcion" class="form-control" rows="2"></textarea>
            </div>
            <div class="row g-2 mb-2">
                <div class="col">
                    <label class="form-label fw-semibold">Precio compra (S/.)</label>
                    <input type="number" name="precio_compra" id="modal_precio_compra"
                           class="form-control" step="0.01" min="0" value="0">
                </div>
                <div class="col">
                    <label class="form-label fw-semibold">Precio venta (S/.) *</label>
                    <input type="number" name="precio_venta" id="modal_precio_venta"
                           class="form-control" step="0.01" min="0.01" required>
                </div>
            </div>
            <div class="row g-2 mb-2">
                <div class="col">
                    <label class="form-label fw-semibold">Stock actual</label>
                    <input type="number" name="stock_actual" id="modal_stock_actual"
                           class="form-control" min="0" value="0">
                </div>
                <div class="col">
                    <label class="form-label fw-semibold">Stock mínimo</label>
                    <input type="number" name="stock_minimo" id="modal_stock_minimo"
                           class="form-control" min="0" value="5">
                </div>
            </div>
            <!-- Selector de imagen -->
            <div class="mb-2">
                <label class="form-label fw-semibold">Imagen del producto</label>
                <select name="foto" id="modal_foto" class="form-select">
                    <option value="">Sin imagen</option>
                    <?php
                    $imgs = glob(__DIR__ . '/../public/img/productos/*.webp');
                    if ($imgs): foreach ($imgs as $img):
                        $nombre_img = basename($img);
                    ?>
                    <option value="<?= $nombre_img ?>"><?= $nombre_img ?></option>
                    <?php endforeach; endif; ?>
                </select>
                <div id="preview_foto" style="margin-top:0.5rem;"></div>
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

<!-- ══ MODAL: Entrada de stock ══ -->
<div class="modal fade" id="modalEntrada" tabindex="-1">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <form method="POST">
        <input type="hidden" name="accion" value="entrada_stock">
        <input type="hidden" name="producto_id" id="entrada_producto_id">
        <div class="modal-header" style="background:#1B2A4A;">
            <h5 class="modal-title text-white">Entrada de stock</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <p class="mb-2">Producto: <strong id="entrada_producto_nombre"></strong></p>
            <div class="mb-2">
                <label class="form-label fw-semibold">Cantidad a agregar *</label>
                <input type="number" name="cantidad" class="form-control" min="1" required>
            </div>
            <div class="mb-2">
                <label class="form-label fw-semibold">Observación</label>
                <input type="text" name="observacion" class="form-control"
                       placeholder="Ej: Compra a proveedor Lima">
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-success">Agregar stock</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Preview de imagen al seleccionar en el selector
document.getElementById('modal_foto').addEventListener('change', function() {
    var preview = document.getElementById('preview_foto');
    if (this.value) {
        preview.innerHTML = '<img src="/ferresystem/public/img/productos/' + this.value + '" ' +
            'style="width:80px;height:80px;object-fit:cover;border-radius:8px;">';
    } else {
        preview.innerHTML = '';
    }
});

function abrirEditar(p) {
    document.getElementById('modal_accion').value        = 'editar';
    document.getElementById('modal_titulo').textContent  = 'Editar producto';
    document.getElementById('modal_producto_id').value   = p.id;
    document.getElementById('modal_nombre').value        = p.nombre;
    document.getElementById('modal_categoria').value     = p.categoria_id;
    document.getElementById('modal_descripcion').value   = p.descripcion || '';
    document.getElementById('modal_precio_compra').value = p.precio_compra;
    document.getElementById('modal_precio_venta').value  = p.precio_venta;
    document.getElementById('modal_stock_actual').value  = p.stock_actual;
    document.getElementById('modal_stock_minimo').value  = p.stock_minimo;
    document.getElementById('modal_foto').value          = p.foto || '';

    // Mostrar preview de imagen actual
    var preview = document.getElementById('preview_foto');
    if (p.foto) {
        preview.innerHTML = '<img src="/ferresystem/public/img/productos/' + p.foto + '" ' +
            'style="width:80px;height:80px;object-fit:cover;border-radius:8px;">';
    } else {
        preview.innerHTML = '';
    }

    new bootstrap.Modal(document.getElementById('modalProducto')).show();
}

function abrirEntrada(id, nombre) {
    document.getElementById('entrada_producto_id').value       = id;
    document.getElementById('entrada_producto_nombre').textContent = nombre;
    new bootstrap.Modal(document.getElementById('modalEntrada')).show();
}

// Resetear modal al cerrar
document.getElementById('modalProducto').addEventListener('hidden.bs.modal', function() {
    document.getElementById('modal_accion').value       = 'crear';
    document.getElementById('modal_titulo').textContent = 'Agregar producto';
    document.getElementById('modal_producto_id').value  = '';
    document.getElementById('preview_foto').innerHTML   = '';
    this.querySelector('form').reset();
});
</script>

<?php require_once 'layout_footer.php'; ?>