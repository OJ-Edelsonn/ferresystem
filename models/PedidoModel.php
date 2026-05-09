<?php
require_once __DIR__ . '/../config/database.php';

class PedidoModel {

    public function crear($cliente_nombre, $cliente_telefono, $cliente_direccion, $detalle) {
        $conn = getConexion();
        try {
            $conn->beginTransaction();

            $total = 0;
            foreach ($detalle as $item) {
                $total += $item['subtotal'];
            }

            $stmt = $conn->prepare("INSERT INTO pedidos 
                (cliente_nombre, cliente_telefono, cliente_direccion, total) 
                VALUES (?, ?, ?, ?)");
            $stmt->execute([$cliente_nombre, $cliente_telefono, $cliente_direccion, $total]);
            $pedido_id = $conn->lastInsertId();

            foreach ($detalle as $item) {
                $stmt2 = $conn->prepare("INSERT INTO pedido_detalle 
                    (pedido_id, producto_id, cantidad, precio_unitario, subtotal) 
                    VALUES (?, ?, ?, ?, ?)");
                $stmt2->execute([
                    $pedido_id,
                    $item['producto_id'],
                    $item['cantidad'],
                    $item['precio_unitario'],
                    $item['subtotal'],
                ]);
            }

            $conn->commit();
            return ['ok' => true, 'pedido_id' => $pedido_id];

        } catch (Exception $e) {
            $conn->rollBack();
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    public function obtenerTodos($estado = null) {
        $conn = getConexion();
        $sql  = "SELECT * FROM pedidos WHERE 1=1";
        $params = [];
        if ($estado) {
            $sql .= " AND estado = ?";
            $params[] = $estado;
        }
        $sql .= " ORDER BY fecha DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function obtenerPorId($id) {
        $conn = getConexion();
        $stmt = $conn->prepare("SELECT * FROM pedidos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function obtenerDetalle($pedido_id) {
        $conn = getConexion();
        $stmt = $conn->prepare("SELECT pd.*, p.nombre AS producto_nombre 
                                FROM pedido_detalle pd
                                JOIN productos p ON pd.producto_id = p.id
                                WHERE pd.pedido_id = ?");
        $stmt->execute([$pedido_id]);
        return $stmt->fetchAll();
    }

    public function cambiarEstado($pedido_id, $estado) {
        $conn = getConexion();
        $stmt = $conn->prepare("UPDATE pedidos SET estado = ? WHERE id = ?");
        return $stmt->execute([$estado, $pedido_id]);
    }

    public function totalPendientes() {
        $conn = getConexion();
        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM pedidos WHERE estado = 'pendiente'");
        $stmt->execute();
        return $stmt->fetch()['total'];
    }
}
?>