<?php
$titulo        = 'Cotizador';
$pagina_activa = 'cotizador';
require_once 'navbar.php';
require_once __DIR__ . '/../config/database.php';

$conn = getConexion();

// Obtener todos los productos activos con su categoría
$stmt = $conn->prepare("SELECT p.*, c.nombre AS categoria_nombre 
                        FROM productos p 
                        JOIN categorias c ON p.categoria_id = c.id
                        WHERE p.activo = 1 AND p.stock_actual > 0
                        ORDER BY c.nombre ASC, p.nombre ASC");
$stmt->execute();
$productos = $stmt->fetchAll();

// Agrupar por categoría para el selector
$por_categoria = [];
foreach ($productos as $p) {
    $por_categoria[$p['categoria_nombre']][] = $p;
}

// Procesar el pedido enviado
$pedido_enviado = false;
$pedido_error   = '';
$pedido_id      = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviar_pedido'])) {
    require_once __DIR__ . '/../controllers/PedidoController.php';

    $cliente_nombre    = trim($_POST['cliente_nombre']    ?? '');
    $cliente_telefono  = trim($_POST['cliente_telefono']  ?? '');
    $cliente_direccion = trim($_POST['cliente_direccion'] ?? '');

    $productos_ids = $_POST['producto_id']      ?? [];
    $cantidades    = $_POST['cantidad']          ?? [];
    $precios       = $_POST['precio_unitario']   ?? [];

    $detalle = [];
    foreach ($productos_ids as $i => $pid) {
        if (!empty($pid) && isset($cantidades[$i]) && $cantidades[$i] > 0) {
            $cant    = (int)$cantidades[$i];
            $precio  = (float)$precios[$i];
            $detalle[] = [
                'producto_id'     => (int)$pid,
                'cantidad'        => $cant,
                'precio_unitario' => $precio,
                'subtotal'        => $cant * $precio,
            ];
        }
    }

    $controller = new PedidoController();
    $resultado  = $controller->crear(
        $cliente_nombre,
        $cliente_telefono,
        $cliente_direccion,
        $detalle
    );

    if ($resultado['ok']) {
        $pedido_enviado = true;
        $pedido_id      = $resultado['pedido_id'];
    } else {
        $pedido_error = $resultado['error'];
    }
}
?>

<section class="seccion seccion-gris" style="padding-top:2.5rem;padding-bottom:1.5rem;">
    <div class="container">
        <div class="seccion-titulo" style="margin-bottom:1rem;">
            <h2>Cotizador de materiales</h2>
            <p>Selecciona los productos que necesitas y envía tu pedido</p>
            <div class="linea-roja"></div>
        </div>
    </div>
</section>

<?php if ($pedido_enviado): ?>
    <!-- ══ CONFIRMACIÓN DE PEDIDO ══ -->
    <section class="seccion">
        <div class="container" style="max-width:600px;text-align:center;">
            <div style="font-size:4rem;margin-bottom:1rem;">✅</div>
            <h2 style="color:var(--navy);margin-bottom:0.75rem;">¡Pedido recibido!</h2>
            <p style="color:var(--gris-medio);margin-bottom:1.5rem;">
                Tu pedido <strong>#<?= $pedido_id ?></strong> fue registrado correctamente.
                Nos comunicaremos contigo pronto para coordinar la entrega en Quiparacra.
            </p>
            <div style="background:#f9f9f9;border-radius:10px;padding:1.5rem;margin-bottom:1.5rem;text-align:left;">
                <p style="margin-bottom:0.5rem;">📞 También puedes escribirnos por WhatsApp para confirmar tu pedido:</p>
            </div>
            <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
                <a href="https://wa.me/51900749742?text=Hola,%20acabo%20de%20enviar%20mi%20pedido%20%23<?= $pedido_id ?>%20y%20quiero%20confirmarlo"
                    class="btn-rojo" target="_blank">💬 Confirmar por WhatsApp</a>
                <a href="cotizador.php" class="btn-outline" style="border-color:#ccc;color:#555;">
                    Hacer otra cotización
                </a>
            </div>
        </div>
    </section>

<?php else: ?>
    <!-- ══ COTIZADOR ══ -->
    <section class="seccion" style="padding-top:2rem;">
        <div class="container">

            <?php if ($pedido_error): ?>
                <div class="alert alert-danger mb-3"><?= htmlspecialchars($pedido_error) ?></div>
            <?php endif; ?>

            <div class="row g-4">

                <!-- Columna izquierda: selector de productos -->
                <div class="col-lg-7">
                    <div style="background:#fff;border-radius:12px;padding:1.5rem;box-shadow:0 2px 12px rgba(0,0,0,0.06);">
                        <h3 style="font-size:1.1rem;font-weight:700;color:var(--navy);margin-bottom:1.2rem;">
                            🛒 Selecciona tus productos
                        </h3>

                        <!-- Buscador de productos -->
                        <div style="margin-bottom:1rem;">
                            <input type="text" id="buscadorProducto"
                                placeholder="Buscar producto por nombre..."
                                oninput="filtrarProductos()"
                                style="width:100%;padding:0.6rem 1rem;border:1.5px solid #ddd;border-radius:8px;font-size:0.9rem;outline:none;">
                        </div>

                        <!-- Lista de productos por categoría -->
                        <?php foreach ($por_categoria as $cat => $prods): ?>
                            <div class="grupo-categoria" data-categoria="<?= htmlspecialchars($cat) ?>">
                                <div style="font-size:0.78rem;font-weight:700;color:var(--gris-medio);
                                    text-transform:uppercase;letter-spacing:0.05em;
                                    margin:1rem 0 0.5rem;border-bottom:1px solid #eee;padding-bottom:0.3rem;">
                                    <?= htmlspecialchars($cat) ?>
                                </div>
                                <?php foreach ($prods as $p): ?>
                                    <div class="item-producto"
                                        data-nombre="<?= strtolower(htmlspecialchars($p['nombre'])) ?>"
                                        style="display:flex;align-items:center;justify-content:space-between;
                                    padding:0.6rem 0.5rem;border-bottom:1px solid #f5f5f5;gap:0.5rem;">
                                        <div style="flex:1;min-width:0;">
                                            <div style="font-size:0.9rem;font-weight:600;color:var(--navy);
                                            white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                                <?= htmlspecialchars($p['nombre']) ?>
                                            </div>
                                            <div style="font-size:0.8rem;color:var(--gris-medio);">
                                                S/. <?= number_format($p['precio_venta'], 2) ?>
                                                · Stock: <?= $p['stock_actual'] ?>
                                            </div>
                                        </div>
                                        <button onclick="agregarProducto(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['nombre'])) ?>', <?= $p['precio_venta'] ?>, <?= $p['stock_actual'] ?>)"
                                            style="background:var(--navy);color:#fff;border:none;border-radius:6px;
                                           padding:0.35rem 0.8rem;font-size:0.82rem;cursor:pointer;
                                           white-space:nowrap;transition:background 0.2s;"
                                            onmouseover="this.style.background='var(--rojo)'"
                                            onmouseout="this.style.background='var(--navy)'">
                                            + Agregar
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>

                        <?php if (empty($productos)): ?>
                            <p style="color:var(--gris-medio);text-align:center;padding:2rem;">
                                No hay productos disponibles en este momento.
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Columna derecha: resumen + formulario -->
                <div class="col-lg-5">

                    <!-- Resumen -->
                    <div style="background:#fff;border-radius:12px;padding:1.5rem;
                            box-shadow:0 2px 12px rgba(0,0,0,0.06);margin-bottom:1.2rem;
                            position:sticky;top:80px;">
                        <h3 style="font-size:1.1rem;font-weight:700;color:var(--navy);margin-bottom:1rem;">
                            📋 Tu cotización
                        </h3>

                        <!-- Zona de items — siempre visible, el JS la llena -->
                        <div id="zonaItems"></div>

                        <!-- Total — oculto hasta que haya items -->
                        <div id="zonaTotal" style="display:none;border-top:2px solid #eee;
                                               padding-top:0.75rem;margin-top:0.75rem;">
                            <div style="display:flex;justify-content:space-between;
                                    font-size:1.1rem;font-weight:700;color:var(--navy);">
                                <span>Total estimado:</span>
                                <span>S/. <span id="spanTotal">0.00</span></span>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario de pedido — oculto hasta que haya items -->
                    <div id="zonaPedido" style="display:none;background:#fff;border-radius:12px;
                                            padding:1.5rem;box-shadow:0 2px 12px rgba(0,0,0,0.06);">
                        <h3 style="font-size:1.1rem;font-weight:700;color:var(--navy);margin-bottom:1rem;">
                            📦 Datos para la entrega
                        </h3>
                        <form method="POST" id="formPedido">
                            <input type="hidden" name="enviar_pedido" value="1">
                            <div id="inputsOcultos"></div>

                            <div style="margin-bottom:0.75rem;">
                                <label style="font-size:0.88rem;font-weight:600;color:var(--navy);
                                          display:block;margin-bottom:0.3rem;">Nombre completo *</label>
                                <input type="text" name="cliente_nombre" required
                                    placeholder="Ej: Juan Quispe Huanca"
                                    style="width:100%;padding:0.6rem;border:1.5px solid #ddd;
                                          border-radius:8px;font-size:0.9rem;outline:none;">
                            </div>
                            <div style="margin-bottom:0.75rem;">
                                <label style="font-size:0.88rem;font-weight:600;color:var(--navy);
                                          display:block;margin-bottom:0.3rem;">Número de celular *</label>
                                <input type="tel" name="cliente_telefono" required
                                    placeholder="Ej: 987654321"
                                    style="width:100%;padding:0.6rem;border:1.5px solid #ddd;
                                          border-radius:8px;font-size:0.9rem;outline:none;">
                            </div>
                            <div style="margin-bottom:1rem;">
                                <label style="font-size:0.88rem;font-weight:600;color:var(--navy);
                                          display:block;margin-bottom:0.3rem;">
                                    Dirección de entrega en Quiparacra *
                                </label>
                                <input type="text" name="cliente_direccion" required
                                    placeholder="Ej: Jr. Lima 123, Quiparacra"
                                    style="width:100%;padding:0.6rem;border:1.5px solid #ddd;
                                          border-radius:8px;font-size:0.9rem;outline:none;">
                            </div>

                            <button type="submit" class="btn-rojo" style="width:100%;font-size:1rem;">
                                📦 Enviar pedido
                            </button>
                            <p style="font-size:0.78rem;color:var(--gris-medio);
                                  text-align:center;margin-top:0.75rem;">
                                Te contactaremos para confirmar la entrega
                            </p>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <script>
        var carrito = [];

        function agregarProducto(id, nombre, precio, stock) {
            id = parseInt(id);
            precio = parseFloat(precio);
            stock = parseInt(stock);

            var encontrado = false;
            for (var i = 0; i < carrito.length; i++) {
                if (carrito[i].id === id) {
                    if (carrito[i].cantidad < carrito[i].stock) {
                        carrito[i].cantidad++;
                    } else {
                        alert('No hay más stock disponible.');
                    }
                    encontrado = true;
                    break;
                }
            }
            if (!encontrado) {
                carrito.push({
                    id: id,
                    nombre: nombre,
                    precio: precio,
                    cantidad: 1,
                    stock: stock
                });
            }
            renderizar();
        }

        function cambiarCantidad(id, delta) {
            for (var i = 0; i < carrito.length; i++) {
                if (carrito[i].id === id) {
                    carrito[i].cantidad += delta;
                    if (carrito[i].cantidad <= 0) {
                        carrito.splice(i, 1);
                    } else if (carrito[i].cantidad > carrito[i].stock) {
                        carrito[i].cantidad = carrito[i].stock;
                    }
                    break;
                }
            }
            renderizar();
        }

        function eliminarItem(id) {
            for (var i = 0; i < carrito.length; i++) {
                if (carrito[i].id === id) {
                    carrito.splice(i, 1);
                    break;
                }
            }
            renderizar();
        }

        function renderizar() {
            var zonaItems = document.getElementById('zonaItems');
            var zonaTotal = document.getElementById('zonaTotal');
            var zonaPedido = document.getElementById('zonaPedido');
            var spanTotal = document.getElementById('spanTotal');
            var inputs = document.getElementById('inputsOcultos');

            // Limpiar zonas
            zonaItems.innerHTML = '';
            inputs.innerHTML = '';

            if (carrito.length === 0) {
                zonaItems.innerHTML = '<p style="color:#888;font-size:0.9rem;text-align:center;padding:1rem 0;">Aún no agregaste productos</p>';
                zonaTotal.style.display = 'none';
                zonaPedido.style.display = 'none';
                spanTotal.textContent = '0.00';
                return;
            }

            var total = 0;

            for (var i = 0; i < carrito.length; i++) {
                var item = carrito[i];
                var sub = Math.round(item.precio * item.cantidad * 100) / 100;
                total += sub;

                // Crear fila con data-id para identificar el item
                var fila = document.createElement('div');
                fila.setAttribute('data-id', item.id);
                fila.style.cssText = 'display:flex;align-items:center;gap:0.5rem;padding:0.6rem 0;border-bottom:1px solid #f0f0f0;';

                // Texto del item
                var info = document.createElement('div');
                info.style.cssText = 'flex:1;min-width:0;';
                info.innerHTML =
                    '<div style="font-size:0.88rem;font-weight:600;color:#1B2A4A;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' +
                    item.nombre + '</div>' +
                    '<div style="font-size:0.8rem;color:#888;">S/. ' + item.precio.toFixed(2) +
                    ' × ' + item.cantidad + ' = <strong>S/. ' + sub.toFixed(2) + '</strong></div>';

                // Controles — usando data-id en los botones
                var ctrl = document.createElement('div');
                ctrl.style.cssText = 'display:flex;align-items:center;gap:0.3rem;flex-shrink:0;';
                ctrl.innerHTML =
                    '<button type="button" data-id="' + item.id + '" data-accion="menos" ' +
                    'style="width:28px;height:28px;border:1px solid #ddd;background:#f5f5f5;border-radius:5px;cursor:pointer;font-size:1.1rem;">−</button>' +
                    '<span style="min-width:22px;text-align:center;font-weight:700;">' + item.cantidad + '</span>' +
                    '<button type="button" data-id="' + item.id + '" data-accion="mas" ' +
                    'style="width:28px;height:28px;border:1px solid #ddd;background:#f5f5f5;border-radius:5px;cursor:pointer;font-size:1.1rem;">+</button>' +
                    '<button type="button" data-id="' + item.id + '" data-accion="del" ' +
                    'style="width:28px;height:28px;border:none;background:none;color:#C0392B;cursor:pointer;font-size:1rem;">✕</button>';

                fila.appendChild(info);
                fila.appendChild(ctrl);
                zonaItems.appendChild(fila);

                // Inputs ocultos para POST
                var a = document.createElement('input');
                a.type = 'hidden';
                a.name = 'producto_id[]';
                a.value = item.id;
                inputs.appendChild(a);

                var b = document.createElement('input');
                b.type = 'hidden';
                b.name = 'cantidad[]';
                b.value = item.cantidad;
                inputs.appendChild(b);

                var c = document.createElement('input');
                c.type = 'hidden';
                c.name = 'precio_unitario[]';
                c.value = item.precio;
                inputs.appendChild(c);
            }

            spanTotal.textContent = total.toFixed(2);
            zonaTotal.style.display = 'block';
            zonaPedido.style.display = 'block';
        }

        // UN SOLO listener en zonaItems usando delegación de eventos
        document.getElementById('zonaItems').addEventListener('click', function(e) {
            var btn = e.target;
            if (btn.tagName !== 'BUTTON') return;
            var id = parseInt(btn.getAttribute('data-id'));
            var accion = btn.getAttribute('data-accion');
            if (!id || !accion) return;

            if (accion === 'menos') cambiarCantidad(id, -1);
            if (accion === 'mas') cambiarCantidad(id, 1);
            if (accion === 'del') eliminarItem(id);
        });

        function filtrarProductos() {
            var q = document.getElementById('buscadorProducto').value.toLowerCase().trim();
            var items = document.querySelectorAll('.item-producto');
            for (var i = 0; i < items.length; i++) {
                items[i].style.display = items[i].dataset.nombre.indexOf(q) >= 0 ? 'flex' : 'none';
            }
            var grupos = document.querySelectorAll('.grupo-categoria');
            for (var j = 0; j < grupos.length; j++) {
                var hijos = grupos[j].querySelectorAll('.item-producto');
                var visibles = 0;
                for (var k = 0; k < hijos.length; k++) {
                    if (hijos[k].style.display !== 'none') visibles++;
                }
                grupos[j].style.display = visibles > 0 ? 'block' : 'none';
            }
        }
    </script>

    </div>
    </div>
    </section>

<?php endif; ?>

<?php require_once 'footer.php'; ?>