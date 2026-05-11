<?php
$titulo        = 'Catálogo';
$pagina_activa = 'catalogo';
require_once 'navbar.php';
require_once __DIR__ . '/../config/database.php';

$conn = getConexion();

// Categorías
$stmt = $conn->prepare("SELECT * FROM categorias WHERE activo = 1 ORDER BY nombre ASC");
$stmt->execute();
$categorias = $stmt->fetchAll();

// Productos con filtros
$categoria_id = $_GET['categoria'] ?? null;
$busqueda     = $_GET['buscar']    ?? '';

$sql    = "SELECT p.*, c.nombre AS categoria_nombre 
           FROM productos p JOIN categorias c ON p.categoria_id = c.id
           WHERE p.activo = 1";
$params = [];

if ($categoria_id) {
    $sql .= " AND p.categoria_id = ?";
    $params[] = $categoria_id;
}
if (!empty($busqueda)) {
    $sql .= " AND p.nombre LIKE ?";
    $params[] = "%" . $busqueda . "%";
}
$sql .= " ORDER BY p.nombre ASC";

$stmt2 = $conn->prepare($sql);
$stmt2->execute($params);
$productos = $stmt2->fetchAll();

// Iconos por categoría
$iconos = [
    'Herramientas' => '🔨', 'Construcción' => '🏗️',
    'Plomería'     => '🚿', 'Electricidad'  => '⚡',
    'Pintura'      => '🎨', 'Seguridad'     => '🔒',
    'Fijación'     => '🔩', 'Acabados'      => '🪣',
    'Gasfitería'   => '🚰', 'Otros'         => '📦',
];
?>

<section class="seccion seccion-gris" style="padding-top:2.5rem;padding-bottom:1.5rem;">
    <div class="container">
        <div class="seccion-titulo" style="margin-bottom:1.5rem;">
            <h2>Catálogo de productos</h2>
            <p>Selecciona una categoría o busca lo que necesitas</p>
            <div class="linea-roja"></div>
        </div>

        <!-- Buscador -->
        <form method="GET" style="display:flex;gap:0.5rem;max-width:460px;margin:0 auto 2rem;">
            <input type="hidden" name="categoria" value="<?= htmlspecialchars($categoria_id ?? '') ?>">
            <input type="text" name="buscar" value="<?= htmlspecialchars($busqueda) ?>"
                   placeholder="Buscar producto..." 
                   style="flex:1;padding:0.6rem 1rem;border:1.5px solid #ddd;border-radius:8px;font-size:0.95rem;outline:none;">
            <button type="submit" class="btn-rojo" style="padding:0.6rem 1.2rem;">Buscar</button>
            <?php if ($busqueda || $categoria_id): ?>
                <a href="catalogo.php" class="btn-outline" 
                   style="padding:0.6rem 1rem;border-color:#ccc;color:#555;">✕</a>
            <?php endif; ?>
        </form>

        <!-- Categorías -->
        <div class="grid-categorias" style="margin-bottom:2rem;">
            <a href="catalogo.php" 
               class="card-categoria <?= !$categoria_id ? 'activo' : '' ?>">
                <div class="icono">🏪</div>
                <p>Todos</p>
            </a>
            <?php foreach ($categorias as $cat): ?>
                <a href="?categoria=<?= $cat['id'] ?><?= $busqueda ? '&buscar='.urlencode($busqueda) : '' ?>"
                   class="card-categoria <?= $categoria_id == $cat['id'] ? 'activo' : '' ?>">
                    <div class="icono"><?= $iconos[$cat['nombre']] ?? '📦' ?></div>
                    <p><?= htmlspecialchars($cat['nombre']) ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="seccion" style="padding-top:2rem;">
    <div class="container">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:0.5rem;">
            <p style="color:var(--gris-medio);font-size:0.9rem;">
                <?= count($productos) ?> producto(s) encontrado(s)
                <?= $categoria_id ? '— ' . htmlspecialchars($categorias[array_search($categoria_id, array_column($categorias, 'id'))]['nombre'] ?? '') : '' ?>
            </p>
            <a href="cotizador.php" class="btn-rojo" style="font-size:0.88rem;padding:0.5rem 1.2rem;">
                🛒 Ir al cotizador
            </a>
        </div>

        <div class="grid-productos">
            <?php if (empty($productos)): ?>
                <div style="grid-column:1/-1;text-align:center;padding:3rem;color:var(--gris-medio);">
                    <div style="font-size:3rem;margin-bottom:1rem;">🔍</div>
                    <p>No encontramos productos con ese criterio.</p>
                    <a href="catalogo.php" style="color:var(--rojo);">Ver todos los productos</a>
                </div>
            <?php endif; ?>
            <?php foreach ($productos as $p): ?>
            <div class="card-producto">
                <div class="card-producto-img">
                    <?= $iconos[$p['categoria_nombre']] ?? '📦' ?>
                </div>
                <div class="card-producto-body">
                    <div class="card-producto-nombre"><?= htmlspecialchars($p['nombre']) ?></div>
                    <div class="card-producto-desc">
                        <?= htmlspecialchars($p['descripcion'] ?? $p['categoria_nombre']) ?>
                    </div>
                    <div class="card-producto-precio">S/. <?= number_format($p['precio_venta'], 2) ?></div>
                    <button class="btn-agregar"
                        onclick="agregarAlCotizador(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['nombre'])) ?>', <?= $p['precio_venta'] ?>)">
                        + Agregar a cotización
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<script>
function agregarAlCotizador(id, nombre, precio) {
    let carrito = JSON.parse(localStorage.getItem('js_cotizador') || '[]');
    const idx   = carrito.findIndex(i => i.id === id);
    if (idx >= 0) {
        carrito[idx].cantidad++;
    } else {
        carrito.push({ id, nombre, precio, cantidad: 1 });
    }
    localStorage.setItem('js_cotizador', JSON.stringify(carrito));

    // Feedback visual
    const btn = event.target;
    btn.textContent = '✓ Agregado';
    btn.style.background = '#27AE60';
    setTimeout(() => {
        btn.textContent = '+ Agregar a cotización';
        btn.style.background = '';
    }, 1500);
}
</script>

<?php require_once 'footer.php'; ?>