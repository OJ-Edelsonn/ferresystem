<?php
require_once __DIR__ . '/../config/database.php';

class VentaModel {

    // Registrar una venta completa con sus productos
    public function crear($fecha, $detalle, $observacion = '') {
        $conn = getConexion();

        try {
            $conn->beginTransaction();

            // Calcular total
            $total = 0;
            foreach ($detalle as $item) {
                $total += $item['subtotal'];
            }

            // Insertar cabecera de venta
            $stmt = $conn->prepare("INSERT INTO ventas (fecha, total, observacion) VALUES (?, ?, ?)");
            $stmt->execute([$fecha, $total, $observacion]);
            $venta_id = $conn->lastInsertId();

            // Insertar detalle y descontar stock
            foreach ($detalle as $item) {
                $stmt2 = $conn->prepare("INSERT INTO venta_detalle 
                    (venta_id, producto_id, cantidad, precio_unitario, subtotal) 
                    VALUES (?, ?, ?, ?, ?)");
                $stmt2->execute([
                    $venta_id,
                    $item['producto_id'],
                    $item['cantidad'],
                    $item['precio_unitario'],
                    $item['subtotal'],
                ]);

                // Descontar stock
                $stmt3 = $conn->prepare("UPDATE productos 
                    SET stock_actual = stock_actual - ? WHERE id = ?");
                $stmt3->execute([$item['cantidad'], $item['producto_id']]);

                // Registrar movimiento de salida
                $stmt4 = $conn->prepare("INSERT INTO stock_movimientos 
                    (producto_id, cantidad, tipo, observacion) VALUES (?, ?, 'salida', ?)");
                $stmt4->execute([$item['producto_id'], $item['cantidad'], 'Venta #' . $venta_id]);
            }

            $conn->commit();
            return ['ok' => true, 'venta_id' => $venta_id];

        } catch (Exception $e) {
            $conn->rollBack();
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    // Obtener ventas con filtro de fecha
    public function obtenerTodas($fecha_inicio = null, $fecha_fin = null) {
        $conn = getConexion();
        $sql = "SELECT * FROM ventas WHERE 1=1";
        $params = [];

        if ($fecha_inicio) {
            $sql .= " AND fecha >= ?";
            $params[] = $fecha_inicio;
        }
        if ($fecha_fin) {
            $sql .= " AND fecha <= ?";
            $params[] = $fecha_fin;
        }

        $sql .= " ORDER BY created_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Obtener detalle de una venta
    public function obtenerDetalle($venta_id) {
        $conn = getConexion();
        $stmt = $conn->prepare("SELECT vd.*, p.nombre AS producto_nombre 
                                FROM venta_detalle vd
                                JOIN productos p ON vd.producto_id = p.id
                                WHERE vd.venta_id = ?");
        $stmt->execute([$venta_id]);
        return $stmt->fetchAll();
    }

    // Anular una venta y restaurar stock
    public function anular($venta_id, $motivo) {
        $conn = getConexion();

        try {
            $conn->beginTransaction();

            // Marcar como anulada
            $stmt = $conn->prepare("UPDATE ventas SET anulada = 1, 
                observacion = CONCAT(COALESCE(observacion,''), ' | ANULADA: ', ?) 
                WHERE id = ?");
            $stmt->execute([$motivo, $venta_id]);

            // Restaurar stock
            $detalle = $this->obtenerDetalle($venta_id);
            foreach ($detalle as $item) {
                $stmt2 = $conn->prepare("UPDATE productos 
                    SET stock_actual = stock_actual + ? WHERE id = ?");
                $stmt2->execute([$item['cantidad'], $item['producto_id']]);

                $stmt3 = $conn->prepare("INSERT INTO stock_movimientos 
                    (producto_id, cantidad, tipo, observacion) VALUES (?, ?, 'entrada', ?)");
                $stmt3->execute([$item['producto_id'], $item['cantidad'], 'Anulación venta #' . $venta_id]);
            }

            $conn->commit();
            return true;

        } catch (Exception $e) {
            $conn->rollBack();
            return false;
        }
    }

    // Total de ventas del día
    public function totalHoy() {
        $conn = getConexion();
        $stmt = $conn->prepare("SELECT COALESCE(SUM(total), 0) AS total 
                                FROM ventas 
                                WHERE fecha = CURDATE() AND anulada = 0");
        $stmt->execute();
        return $stmt->fetch()['total'];
    }

    // Total de ventas del mes
    public function totalMes() {
        $conn = getConexion();
        $stmt = $conn->prepare("SELECT COALESCE(SUM(total), 0) AS total 
                                FROM ventas 
                                WHERE MONTH(fecha) = MONTH(CURDATE()) 
                                AND YEAR(fecha) = YEAR(CURDATE()) 
                                AND anulada = 0");
        $stmt->execute();
        return $stmt->fetch()['total'];
    }
}
?>