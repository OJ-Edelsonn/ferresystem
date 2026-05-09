<?php
require_once __DIR__ . '/../config/database.php';

class CajaModel {

    public function registrar($fecha, $tipo, $descripcion, $monto) {
        $conn = getConexion();
        $stmt = $conn->prepare("INSERT INTO caja (fecha, tipo, descripcion, monto) 
                                VALUES (?, ?, ?, ?)");
        return $stmt->execute([$fecha, $tipo, $descripcion, $monto]);
    }

    public function obtenerMovimientos($fecha_inicio = null, $fecha_fin = null) {
        $conn = getConexion();
        $sql  = "SELECT * FROM caja WHERE 1=1";
        $params = [];

        if ($fecha_inicio) { $sql .= " AND fecha >= ?"; $params[] = $fecha_inicio; }
        if ($fecha_fin)    { $sql .= " AND fecha <= ?"; $params[] = $fecha_fin; }

        $sql .= " ORDER BY created_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function saldoDia($fecha = null) {
        $conn  = getConexion();
        $fecha = $fecha ?? date('Y-m-d');
        $stmt  = $conn->prepare("SELECT 
            COALESCE(SUM(CASE WHEN tipo='ingreso' THEN monto ELSE 0 END), 0) AS ingresos,
            COALESCE(SUM(CASE WHEN tipo='egreso'  THEN monto ELSE 0 END), 0) AS egresos
            FROM caja WHERE fecha = ?");
        $stmt->execute([$fecha]);
        $row = $stmt->fetch();
        $row['saldo'] = $row['ingresos'] - $row['egresos'];
        return $row;
    }

    public function eliminar($id) {
        $conn = getConexion();
        $stmt = $conn->prepare("DELETE FROM caja WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>