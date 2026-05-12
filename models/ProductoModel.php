<?php
require_once __DIR__ . '/../config/database.php';

class ProductoModel
{

    // Obtener todos los productos con su categoría
    public function obtenerTodos($categoria_id = null, $busqueda = '')
    {
        $conn = getConexion();
        $sql = "SELECT p.*, c.nombre AS categoria_nombre 
                FROM productos p 
                JOIN categorias c ON p.categoria_id = c.id
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
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Obtener un producto por ID
    public function obtenerPorId($id)
    {
        $conn = getConexion();
        $stmt = $conn->prepare("SELECT * FROM productos WHERE id = ? AND activo = 1");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Crear un producto nuevo
    public function crear($datos)
    {
        $conn = getConexion();
        $stmt = $conn->prepare("INSERT INTO productos 
        (nombre, categoria_id, descripcion, precio_compra, precio_venta, stock_actual, stock_minimo, foto)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $datos['nombre'],
            $datos['categoria_id'],
            $datos['descripcion'],
            $datos['precio_compra'],
            $datos['precio_venta'],
            $datos['stock_actual'],
            $datos['stock_minimo'],
            $datos['foto'] ?? null,
        ]);
    }

    // Actualizar un producto existente
    public function actualizar($id, $datos)
    {
        $conn = getConexion();
        $stmt = $conn->prepare("UPDATE productos SET
        nombre        = ?,
        categoria_id  = ?,
        descripcion   = ?,
        precio_compra = ?,
        precio_venta  = ?,
        stock_actual  = ?,
        stock_minimo  = ?,
        foto          = ?
        WHERE id = ?");
        return $stmt->execute([
            $datos['nombre'],
            $datos['categoria_id'],
            $datos['descripcion'],
            $datos['precio_compra'],
            $datos['precio_venta'],
            $datos['stock_actual'],
            $datos['stock_minimo'],
            $datos['foto'] ?? null,
            $id,
        ]);
    }
    // Eliminar producto (baja lógica — no se borra de la BD)
    public function eliminar($id)
    {
        $conn = getConexion();
        $stmt = $conn->prepare("UPDATE productos SET activo = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Obtener productos con stock crítico
    public function obtenerStockCritico()
    {
        $conn = getConexion();
        $stmt = $conn->prepare("SELECT p.*, c.nombre AS categoria_nombre 
                                FROM productos p
                                JOIN categorias c ON p.categoria_id = c.id
                                WHERE p.activo = 1 AND p.stock_actual <= p.stock_minimo
                                ORDER BY p.stock_actual ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Registrar entrada de stock
    public function registrarEntrada($producto_id, $cantidad, $observacion = '')
    {
        $conn = getConexion();

        // Actualizar stock
        $stmt = $conn->prepare("UPDATE productos 
                                SET stock_actual = stock_actual + ? 
                                WHERE id = ?");
        $stmt->execute([$cantidad, $producto_id]);

        // Registrar movimiento
        $stmt2 = $conn->prepare("INSERT INTO stock_movimientos 
                                 (producto_id, cantidad, tipo, observacion) 
                                 VALUES (?, ?, 'entrada', ?)");
        return $stmt2->execute([$producto_id, $cantidad, $observacion]);
    }

    // Obtener todas las categorías activas
    public function obtenerCategorias()
    {
        $conn = getConexion();
        $stmt = $conn->prepare("SELECT * FROM categorias WHERE activo = 1 ORDER BY nombre ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
