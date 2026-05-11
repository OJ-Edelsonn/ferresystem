<?php
$titulo        = 'Servicios de obra';
$pagina_activa = 'servicios';
require_once 'navbar.php';

$tel_whatsapp = '51900749742';
$mensaje_wa   = urlencode('Hola, me interesa solicitar un presupuesto para una obra. ¿Me pueden ayudar?');
?>

<!-- Hero servicios -->
<section style="background:linear-gradient(135deg,var(--navy) 0%,#2C3E6A 100%);
                color:#fff;padding:4rem 1.5rem 3rem;text-align:center;">
    <div class="container">
        <div style="font-size:3.5rem;margin-bottom:1rem;">👷</div>
        <h1 style="font-size:clamp(1.8rem,4vw,2.6rem);font-weight:800;margin-bottom:0.75rem;">
            Servicio de maestro albañil
        </h1>
        <p style="color:rgba(255,255,255,0.8);max-width:540px;margin:0 auto 1.5rem;font-size:1rem;">
            Más de 15 años de experiencia en construcción y remodelación en la zona de Quiparacra y Huachón.
        </p>
        <a href="https://wa.me/<?= $tel_whatsapp ?>?text=<?= $mensaje_wa ?>"
           class="btn-rojo" target="_blank">
            💬 Solicitar presupuesto
        </a>
    </div>
</section>

<!-- Servicios -->
<section class="seccion">
    <div class="container">
        <div class="seccion-titulo">
            <h2>¿Qué hacemos?</h2>
            <div class="linea-roja"></div>
        </div>
        <div class="grid-features" style="gap:1.5rem;">
            <?php
            $servicios = [
                ['🏠', 'Construcción de cuartos', 'Levantamos cuartos desde cero con adobe, ladrillo o material noble según tu presupuesto.'],
                ['🧱', 'Remodelación', 'Ampliaciones, refacciones y mejoras en tu vivienda con acabado limpio y duradero.'],
                ['🪟', 'Instalación de puertas y ventanas', 'Colocación y nivelación de marcos, puertas y ventanas de madera o metal.'],
                ['🚿', 'Instalaciones sanitarias', 'Instalación de baños, tuberías, desagüe y todo el sistema de agua.'],
                ['⚡', 'Instalaciones eléctricas', 'Cableado, interruptores, tomacorrientes y tablero eléctrico.'],
                ['🎨', 'Pintura y acabados', 'Empaste, pintura interior y exterior con materiales de calidad.'],
            ];
            foreach ($servicios as [$icono, $titulo_s, $desc]):
            ?>
            <div class="feature-card" style="background:#f9f9f9;border-radius:12px;padding:1.8rem 1.2rem;">
                <div class="feature-icono"><?= $icono ?></div>
                <h3><?= $titulo_s ?></h3>
                <p><?= $desc ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Proceso -->
<section class="seccion seccion-gris">
    <div class="container">
        <div class="seccion-titulo">
            <h2>¿Cómo trabajamos?</h2>
            <div class="linea-roja"></div>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1.5rem;margin-top:1rem;">
            <?php
            $pasos = [
                ['1', 'Nos contactas', 'Escríbenos por WhatsApp o visítanos en la ferretería y cuéntanos qué necesitas.'],
                ['2', 'Visita y presupuesto', 'Visitamos la obra, evaluamos el trabajo y te damos un presupuesto claro sin sorpresas.'],
                ['3', 'Iniciamos la obra', 'Comenzamos el trabajo con nuestro personal en el plazo acordado.'],
                ['4', 'Entrega y garantía', 'Terminamos la obra y te la entregamos con garantía de trabajo bien hecho.'],
            ];
            foreach ($pasos as [$num, $titulo_p, $desc_p]):
            ?>
            <div style="text-align:center;padding:1.5rem 1rem;">
                <div style="width:48px;height:48px;background:var(--rojo);color:#fff;
                            border-radius:50%;display:flex;align-items:center;justify-content:center;
                            font-size:1.2rem;font-weight:800;margin:0 auto 0.75rem;">
                    <?= $num ?>
                </div>
                <h3 style="font-size:1rem;font-weight:700;color:var(--navy);margin-bottom:0.4rem;">
                    <?= $titulo_p ?>
                </h3>
                <p style="font-size:0.88rem;color:var(--gris-medio);"><?= $desc_p ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA final -->
<section class="seccion" style="background:var(--rojo);text-align:center;">
    <div class="container">
        <h2 style="color:#fff;margin-bottom:0.75rem;">¿Listo para empezar tu obra?</h2>
        <p style="color:rgba(255,255,255,0.85);margin-bottom:1.5rem;">
            Contáctanos hoy y te damos un presupuesto sin compromiso.
        </p>
        <a href="https://wa.me/<?= $tel_whatsapp ?>?text=<?= $mensaje_wa ?>"
           class="btn-outline" target="_blank" style="border-color:#fff;color:#fff;">
            💬 Solicitar presupuesto gratis
        </a>
    </div>
</section>

<?php require_once 'footer.php'; ?>