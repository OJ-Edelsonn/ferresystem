<?php
$titulo        = 'Contacto';
$pagina_activa = 'contacto';
require_once 'navbar.php';

$tel_whatsapp = '51900749742';
$enviado      = false;
$error_form   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre  = trim($_POST['nombre']  ?? '');
    $celular = trim($_POST['celular'] ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');

    if (empty($nombre) || empty($celular) || empty($mensaje)) {
        $error_form = 'Por favor completa todos los campos.';
    } else {
        $texto = urlencode("Hola, soy {$nombre} ({$celular}). {$mensaje}");
        header("Location: https://wa.me/{$tel_whatsapp}?text={$texto}");
        exit;
    }
}
?>

<section class="seccion seccion-gris" style="padding-top:2.5rem;padding-bottom:1.5rem;">
    <div class="container">
        <div class="seccion-titulo" style="margin-bottom:0.5rem;">
            <h2>Contáctanos</h2>
            <p>Estamos en Quiparacra, Huachón, Pasco — cerca de ti</p>
            <div class="linea-roja"></div>
        </div>
    </div>
</section>

<section class="seccion" style="padding-top:2rem;">
    <div class="container">

        <!-- Mapa completo arriba -->
        <div style="border-radius:12px;overflow:hidden;margin-bottom:2rem;
                    box-shadow:0 2px 12px rgba(0,0,0,0.08);">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3901.234!2d-75.866949!3d-10.642924!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zMTDCsDM4JzM0LjUiUyA3NcKwNTInMDEuMCJX!5e0!3m2!1ses!2spe!4v1234567890!5m2!1ses!2spe"
                width="100%"
                height="300"
                style="border:0;display:block;"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>

        <!-- Info + Formulario en dos columnas centradas -->
        <div style="display:flex;flex-wrap:wrap;gap:2rem;justify-content:center;align-items:flex-start;">

            <!-- Info de contacto -->
            <div style="flex:1;min-width:260px;max-width:380px;display:flex;
                        flex-direction:column;justify-content:space-between;">
                <h3 style="font-size:1.1rem;font-weight:700;color:var(--navy);
                           margin-bottom:1.5rem;text-align:center;">
                    Información de contacto
                </h3>
                <?php
                $infos = [
                    ['📍', 'Dirección',  'Calle San Cristóbal S/N — Quiparacra — Huachón — Pasco — Perú'],
                    ['📞', 'Celular',    '900 749 742'],
                    ['🕐', 'Horario',   'Lunes a sábado: 7:00am – 7:00pm'],
                    ['📦', 'Entregas',  'Solo en Quiparacra y alrededores'],
                ];
                foreach ($infos as [$icono, $label, $valor]):
                ?>
                    <div style="text-align:center;margin-bottom:1.2rem;">
                        <div style="font-size:1.8rem;margin-bottom:0.3rem;"><?= $icono ?></div>
                        <div style="font-weight:700;color:var(--navy);font-size:0.9rem;"><?= $label ?></div>
                        <div style="color:var(--gris-medio);font-size:0.9rem;"><?= $valor ?></div>
                    </div>
                <?php endforeach; ?>

                <div style="text-align:center;margin-top:auto;padding-top:1.5rem;">
                    <a href="https://wa.me/<?= $tel_whatsapp ?>?text=Hola,%20quiero%20consultar"
                        target="_blank" class="btn-rojo"
                        style="display:inline-flex;align-items:center;gap:0.5rem;
                               width:100%;justify-content:center;padding:0.75rem;">
                        💬 Escribir por WhatsApp
                    </a>
                </div>
            </div>

            <!-- Formulario -->
            <div style="flex:1;min-width:260px;max-width:420px;">
                <div style="background:#f9f9f9;border-radius:12px;padding:1.5rem;">
                    <h3 style="font-size:1rem;font-weight:700;color:var(--navy);
                               margin-bottom:1rem;text-align:center;">
                        Envíanos un mensaje
                    </h3>

                    <?php if ($error_form): ?>
                        <div class="alert alert-danger py-2 mb-3">
                            <?= htmlspecialchars($error_form) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div style="margin-bottom:0.75rem;">
                            <label style="font-size:0.88rem;font-weight:600;color:var(--navy);
                                          display:block;margin-bottom:0.3rem;">Nombre *</label>
                            <input type="text" name="nombre" required
                                placeholder="Tu nombre completo"
                                value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>"
                                style="width:100%;padding:0.6rem;border:1.5px solid #ddd;
                                          border-radius:8px;font-size:0.9rem;outline:none;
                                          background:#fff;">
                        </div>
                        <div style="margin-bottom:0.75rem;">
                            <label style="font-size:0.88rem;font-weight:600;color:var(--navy);
                                          display:block;margin-bottom:0.3rem;">Celular *</label>
                            <input type="tel" name="celular" required
                                placeholder="Ej: 987654321"
                                value="<?= htmlspecialchars($_POST['celular'] ?? '') ?>"
                                style="width:100%;padding:0.6rem;border:1.5px solid #ddd;
                                          border-radius:8px;font-size:0.9rem;outline:none;
                                          background:#fff;">
                        </div>
                        <div style="margin-bottom:1rem;">
                            <label style="font-size:0.88rem;font-weight:600;color:var(--navy);
                                          display:block;margin-bottom:0.3rem;">Mensaje *</label>
                            <textarea name="mensaje" required rows="3"
                                placeholder="¿En qué te podemos ayudar?"
                                style="width:100%;padding:0.6rem;border:1.5px solid #ddd;
                                             border-radius:8px;font-size:0.9rem;outline:none;
                                             background:#fff;resize:vertical;
                                             max-height:120px;"><?= htmlspecialchars($_POST['mensaje'] ?? '') ?></textarea>
                        </div>
                        <button type="submit" class="btn-rojo" style="width:100%;">
                            💬 Enviar mensaje por WhatsApp
                        </button>
                        <p style="font-size:0.78rem;color:var(--gris-medio);
                                  text-align:center;margin-top:0.5rem;">
                            Al enviar serás redirigido a WhatsApp con tu mensaje listo
                        </p>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>

<?php require_once 'footer.php'; ?>