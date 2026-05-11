<?php
$titulo       = 'Inicio';
$pagina_activa = 'inicio';
require_once 'navbar.php';
?>

<!-- Hero -->
<section class="hero">
    <div class="container">
        <h1>Tu ferretería de confianza<br>en <span>Quiparacra</span></h1>
        <p>Materiales de construcción, herramientas y todo lo que necesitas para tu obra — directo en tu localidad.</p>
        <div class="hero-btns">
            <a href="catalogo.php" class="btn-rojo">Ver catálogo</a>
            <a href="cotizador.php" class="btn-outline">Hacer cotización</a>
        </div>
    </div>
</section>

<!-- Características -->
<section class="seccion">
    <div class="container">
        <div class="seccion-titulo">
            <h2>¿Por qué elegirnos?</h2>
            <div class="linea-roja"></div>
        </div>
        <div class="grid-features">
            <div class="feature-card">
                <div class="feature-icono">🏠</div>
                <h3>Local en Quiparacra</h3>
                <p>Estamos en tu localidad. Entrega directa sin esperas largas.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icono">🔨</div>
                <h3>Variedad de productos</h3>
                <p>Herramientas, materiales de obra, plomería, electricidad y más.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icono">👷</div>
                <h3>Servicio de obra</h3>
                <p>Maestro albañil con experiencia para construcción y remodelación.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icono">📱</div>
                <h3>Pedidos por WhatsApp</h3>
                <p>Cotiza desde tu celular y coordina la entrega fácilmente.</p>
            </div>
        </div>
    </div>
</section>

<!-- Vista previa del catálogo -->
<?php
require_once __DIR__ . '/../config/database.php';
$conn = getConexion();
$stmt = $conn->prepare("SELECT p.*, c.nombre AS categoria_nombre 
                        FROM productos p JOIN categorias c ON p.categoria_id = c.id
                        WHERE p.activo = 1 ORDER BY p.id DESC LIMIT 8");
$stmt->execute();
$productos_recientes = $stmt->fetchAll();
?>

<section class="seccion seccion-gris">
    <div class="container">
        <div class="seccion-titulo">
            <h2>Productos destacados</h2>
            <p>Una muestra de lo que tenemos disponible</p>
            <div class="linea-roja"></div>
        </div>
        <div class="grid-productos">
            <?php foreach ($productos_recientes as $p): ?>
                <div class="card-producto">
                    <div class="card-producto-img">🔧</div>
                    <div class="card-producto-body">
                        <div class="card-producto-nombre"><?= htmlspecialchars($p['nombre']) ?></div>
                        <div class="card-producto-desc"><?= htmlspecialchars($p['descripcion'] ?? $p['categoria_nombre']) ?></div>
                        <div class="card-producto-precio">S/. <?= number_format($p['precio_venta'], 2) ?></div>
                        <a href="/ferresystem/public/cotizador.php" class="btn-agregar">Agregar a cotización</a>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($productos_recientes)): ?>
                <p class="text-muted">Próximamente productos disponibles.</p>
            <?php endif; ?>
        </div>
        <div style="text-align:center;margin-top:2rem;">
            <a href="/ferresystem/public/catalogo.php" class="btn-rojo">Ver catálogo completo</a>
        </div>
    </div>
</section>

<!-- CTA WhatsApp -->
<section class="seccion" style="background:var(--navy);color:#fff;text-align:center;">
    <div class="container">
        <h2 style="color:#fff;font-size:1.6rem;margin-bottom:0.75rem;">
            ¿Tienes alguna consulta?
        </h2>
        <p style="color:rgba(255,255,255,0.75);margin-bottom:1.5rem;">
            Escríbenos por WhatsApp y te respondemos al instante.
        </p>
        <a href="https://wa.me/51900749742?text=Hola,%20quiero%20consultar%20sobre%20sus%20productos"
            class="btn-rojo" target="_blank">
            💬 Escribir por WhatsApp
        </a>
    </div>
</section>

<?php require_once 'footer.php'; ?>