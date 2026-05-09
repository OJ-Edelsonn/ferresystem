<?php
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/VentaController.php';
require_once __DIR__ . '/../controllers/PedidoController.php';
require_once __DIR__ . '/../controllers/ProductoController.php';
require_once __DIR__ . '/../config/database.php';

$titulo_pagina = 'Dashboard';
$pagina_activa = 'dashboard';

$ventaCtrl   = new VentaController();
$pedidoCtrl  = new PedidoController();
$productoCtrl = new ProductoController();

$total_hoy       = $ventaCtrl->totalHoy();
$total_mes       = $ventaCtrl->totalMes();
$pedidos_pend    = $pedidoCtrl->totalPendientes();
$stock_critico   = $productoCtrl->stockCritico();

// Ingresos últimos 30 días
$conn = getConexion();
$stmt = $conn->prepare("SELECT fecha, SUM(total) AS total 
                        FROM ventas 
                        WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND anulada = 0 
                        GROUP BY fecha ORDER BY fecha ASC");
$stmt->execute();
$ingresos30 = $stmt->fetchAll();

$labels_ingresos = array_map(fn($r) => date('d/m', strtotime($r['fecha'])), $ingresos30);
$data_ingresos   = array_map(fn($r) => (float)$r['total'], $ingresos30);

// Top 8 productos más vendidos
$stmt2 = $conn->prepare("SELECT p.nombre, SUM(vd.cantidad) AS total_vendido
                         FROM venta_detalle vd
                         JOIN productos p ON vd.producto_id = p.id
                         JOIN ventas v ON vd.venta_id = v.id
                         WHERE v.anulada = 0
                         GROUP BY p.id ORDER BY total_vendido DESC LIMIT 8");
$stmt2->execute();
$top_productos = $stmt2->fetchAll();

$labels_top  = array_map(fn($r) => $r['nombre'], $top_productos);
$data_top    = array_map(fn($r) => (int)$r['total_vendido'], $top_productos);

// Ventas por categoría
$stmt3 = $conn->prepare("SELECT c.nombre, SUM(vd.subtotal) AS total
                         FROM venta_detalle vd
                         JOIN productos p ON vd.producto_id = p.id
                         JOIN categorias c ON p.categoria_id = c.id
                         JOIN ventas v ON vd.venta_id = v.id
                         WHERE v.anulada = 0
                         GROUP BY c.id ORDER BY total DESC");
$stmt3->execute();
$ventas_cat = $stmt3->fetchAll();

$labels_cat = array_map(fn($r) => $r['nombre'], $ventas_cat);
$data_cat   = array_map(fn($r) => (float)$r['total'], $ventas_cat);

require_once 'layout.php';
?>

<!-- KPIs -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div style="background:#fff;border-radius:10px;padding:1.2rem 1.4rem;border-left:4px solid #C0392B;">
            <div style="font-size:0.8rem;color:#888;margin-bottom:4px;">Ventas hoy</div>
            <div style="font-size:1.6rem;font-weight:700;color:#1B2A4A;">S/. <?= number_format($total_hoy, 2) ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div style="background:#fff;border-radius:10px;padding:1.2rem 1.4rem;border-left:4px solid #185FA5;">
            <div style="font-size:0.8rem;color:#888;margin-bottom:4px;">Ventas este mes</div>
            <div style="font-size:1.6rem;font-weight:700;color:#1B2A4A;">S/. <?= number_format($total_mes, 2) ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div style="background:#fff;border-radius:10px;padding:1.2rem 1.4rem;border-left:4px solid #E67E22;">
            <div style="font-size:0.8rem;color:#888;margin-bottom:4px;">Pedidos pendientes</div>
            <div style="font-size:1.6rem;font-weight:700;color:#1B2A4A;"><?= $pedidos_pend ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div style="background:#fff;border-radius:10px;padding:1.2rem 1.4rem;border-left:4px solid #E74C3C;">
            <div style="font-size:0.8rem;color:#888;margin-bottom:4px;">Stock crítico</div>
            <div style="font-size:1.6rem;font-weight:700;color:#C0392B;"><?= count($stock_critico) ?> productos</div>
        </div>
    </div>
</div>

<!-- Gráficos fila 1 -->
<div class="row g-3 mb-3">
    <div class="col-md-8">
        <div style="background:#fff;border-radius:10px;padding:1.4rem;">
            <div style="font-weight:600;color:#1B2A4A;margin-bottom:1rem;">Ingresos últimos 30 días</div>
            <canvas id="graficoIngresos" height="110"></canvas>
        </div>
    </div>
    <div class="col-md-4">
        <div style="background:#fff;border-radius:10px;padding:1.4rem;">
            <div style="font-weight:600;color:#1B2A4A;margin-bottom:1rem;">Ventas por categoría</div>
            <canvas id="graficoCategorias" height="200"></canvas>
        </div>
    </div>
</div>

<!-- Gráfico fila 2 + stock crítico -->
<div class="row g-3">
    <div class="col-md-7">
        <div style="background:#fff;border-radius:10px;padding:1.4rem;">
            <div style="font-weight:600;color:#1B2A4A;margin-bottom:1rem;">Top productos más vendidos</div>
            <canvas id="graficoTop" height="160"></canvas>
        </div>
    </div>
    <div class="col-md-5">
        <div style="background:#fff;border-radius:10px;padding:1.4rem;">
            <div style="font-weight:600;color:#C0392B;margin-bottom:1rem;">⚠ Productos con stock crítico</div>
            <?php if (empty($stock_critico)): ?>
                <p class="text-muted" style="font-size:0.9rem;">Sin productos en stock crítico.</p>
            <?php else: ?>
                <table class="table table-sm mb-0" style="font-size:0.85rem;">
                    <thead><tr><th>Producto</th><th>Stock</th><th>Mín.</th></tr></thead>
                    <tbody>
                    <?php foreach ($stock_critico as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['nombre']) ?></td>
                            <td><span class="badge bg-danger"><?= $p['stock_actual'] ?></span></td>
                            <td><?= $p['stock_minimo'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const labelsIngresos = <?= json_encode($labels_ingresos) ?>;
const dataIngresos   = <?= json_encode($data_ingresos) ?>;
const labelsTop      = <?= json_encode($labels_top) ?>;
const dataTop        = <?= json_encode($data_top) ?>;
const labelsCat      = <?= json_encode($labels_cat) ?>;
const dataCat        = <?= json_encode($data_cat) ?>;

new Chart(document.getElementById('graficoIngresos'), {
    type: 'line',
    data: {
        labels: labelsIngresos,
        datasets: [{
            label: 'Ingresos (S/.)',
            data: dataIngresos,
            borderColor: '#C0392B',
            backgroundColor: 'rgba(192,57,43,0.08)',
            tension: 0.4, fill: true, pointRadius: 4,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#f0f0f0' } },
            x: { grid: { display: false } }
        }
    }
});

new Chart(document.getElementById('graficoCategorias'), {
    type: 'doughnut',
    data: {
        labels: labelsCat,
        datasets: [{
            data: dataCat,
            backgroundColor: ['#C0392B','#1B2A4A','#185FA5','#E67E22','#27AE60','#8E44AD','#16A085','#F39C12'],
            borderWidth: 0,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } }
    }
});

new Chart(document.getElementById('graficoTop'), {
    type: 'bar',
    data: {
        labels: labelsTop,
        datasets: [{
            label: 'Unidades vendidas',
            data: dataTop,
            backgroundColor: '#1B2A4A',
            borderRadius: 4,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#f0f0f0' } },
            x: { grid: { display: false } }
        }
    }
});
</script>

<?php require_once 'layout_footer.php'; ?>