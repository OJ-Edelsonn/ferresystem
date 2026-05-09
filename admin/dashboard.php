<?php
require_once __DIR__ . '/../controllers/AuthController.php';

$titulo_pagina = 'Dashboard';
$pagina_activa = 'dashboard';

require_once 'layout.php';
?>

<!-- KPIs principales -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div style="background:#fff;border-radius:10px;padding:1.2rem 1.4rem;border-left:4px solid #C0392B;">
            <div style="font-size:0.8rem;color:#888;margin-bottom:4px;">Ventas hoy</div>
            <div style="font-size:1.6rem;font-weight:700;color:#1B2A4A;">S/. 0.00</div>
        </div>
    </div>
    <div class="col-md-3">
        <div style="background:#fff;border-radius:10px;padding:1.2rem 1.4rem;border-left:4px solid #185FA5;">
            <div style="font-size:0.8rem;color:#888;margin-bottom:4px;">Ventas este mes</div>
            <div style="font-size:1.6rem;font-weight:700;color:#1B2A4A;">S/. 0.00</div>
        </div>
    </div>
    <div class="col-md-3">
        <div style="background:#fff;border-radius:10px;padding:1.2rem 1.4rem;border-left:4px solid #E67E22;">
            <div style="font-size:0.8rem;color:#888;margin-bottom:4px;">Pedidos pendientes</div>
            <div style="font-size:1.6rem;font-weight:700;color:#1B2A4A;">0</div>
        </div>
    </div>
    <div class="col-md-3">
        <div style="background:#fff;border-radius:10px;padding:1.2rem 1.4rem;border-left:4px solid #E74C3C;">
            <div style="font-size:0.8rem;color:#888;margin-bottom:4px;">Stock crítico</div>
            <div style="font-size:1.6rem;font-weight:700;color:#C0392B;">0 productos</div>
        </div>
    </div>
</div>

<!-- Gráficos placeholder -->
<div class="row g-3">
    <div class="col-md-8">
        <div style="background:#fff;border-radius:10px;padding:1.4rem;">
            <div style="font-weight:600;color:#1B2A4A;margin-bottom:1rem;">
                Ingresos últimos 30 días
            </div>
            <canvas id="graficoIngresos" height="120"></canvas>
        </div>
    </div>
    <div class="col-md-4">
        <div style="background:#fff;border-radius:10px;padding:1.4rem;">
            <div style="font-weight:600;color:#1B2A4A;margin-bottom:1rem;">
                Ventas por categoría
            </div>
            <canvas id="graficoCategorias" height="200"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Gráfico de línea — ingresos (datos de ejemplo por ahora)
new Chart(document.getElementById('graficoIngresos'), {
    type: 'line',
    data: {
        labels: ['Día 1','Día 5','Día 10','Día 15','Día 20','Día 25','Día 30'],
        datasets: [{
            label: 'Ingresos (S/.)',
            data: [0, 0, 0, 0, 0, 0, 0],
            borderColor: '#C0392B',
            backgroundColor: 'rgba(192,57,43,0.08)',
            tension: 0.4,
            fill: true,
            pointRadius: 4,
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

// Gráfico de dona — categorías (datos de ejemplo)
new Chart(document.getElementById('graficoCategorias'), {
    type: 'doughnut',
    data: {
        labels: ['Herramientas','Construcción','Plomería','Electricidad','Otros'],
        datasets: [{
            data: [0, 0, 0, 0, 0],
            backgroundColor: [
                '#C0392B','#1B2A4A','#185FA5','#E67E22','#95A5A6'
            ],
            borderWidth: 0,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom', labels: { font: { size: 11 } } }
        }
    }
});
</script>

<?php require_once 'layout_footer.php'; ?>