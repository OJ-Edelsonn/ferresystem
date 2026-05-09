<?php
require_once __DIR__ . '/../config/database.php';

class CotizacionModel {

    public function crear($datos, $materiales) {
        $conn = getConexion();
        try {
            $conn->beginTransaction();

            $total_materiales = 0;
            foreach ($materiales as $m) {
                $total_materiales += $m['subtotal'];
            }
            $total_general = $total_materiales + $datos['mano_obra'];

            $stmt = $conn->prepare("INSERT INTO cotizaciones 
                (cliente_nombre, cliente_telefono, descripcion_obra, mano_obra, 
                 total_materiales, total_general, fecha)
                VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $datos['cliente_nombre'],
                $datos['cliente_telefono'],
                $datos['descripcion_obra'],
                $datos['mano_obra'],
                $total_materiales,
                $total_general,
                $datos['fecha'],
            ]);
            $cot_id = $conn->lastInsertId();

            foreach ($materiales as $m) {
                $stmt2 = $conn->prepare("INSERT INTO cotizacion_materiales 
                    (cotizacion_id, descripcion, cantidad, precio_unitario, subtotal)
                    VALUES (?, ?, ?, ?, ?)");
                $stmt2->execute([
                    $cot_id,
                    $m['descripcion'],
                    $m['cantidad'],
                    $m['precio_unitario'],
                    $m['subtotal'],
                ]);
            }

            $conn->commit();
            return ['ok' => true, 'id' => $cot_id];

        } catch (Exception $e) {
            $conn->rollBack();
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    public function obtenerTodas($busqueda = '') {
        $conn = getConexion();
        $sql  = "SELECT * FROM cotizaciones WHERE 1=1";
        $params = [];
        if (!empty($busqueda)) {
            $sql .= " AND cliente_nombre LIKE ?";
            $params[] = "%" . $busqueda . "%";
        }
        $sql .= " ORDER BY created_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function obtenerPorId($id) {
        $conn = getConexion();
        $stmt = $conn->prepare("SELECT * FROM cotizaciones WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function obtenerMateriales($cotizacion_id) {
        $conn = getConexion();
        $stmt = $conn->prepare("SELECT * FROM cotizacion_materiales WHERE cotizacion_id = ?");
        $stmt->execute([$cotizacion_id]);
        return $stmt->fetchAll();
    }

    public function eliminar($id) {
        $conn = getConexion();
        $conn->prepare("DELETE FROM cotizacion_materiales WHERE cotizacion_id = ?")->execute([$id]);
        $conn->prepare("DELETE FROM cotizaciones WHERE id = ?")->execute([$id]);
        return true;
    }
}
?>