<?php
require_once __DIR__ . '/../models/CajaModel.php';

class CajaController {

    private $model;

    public function __construct() {
        $this->model = new CajaModel();
    }

    public function registrar($fecha, $tipo, $descripcion, $monto) {
        if (empty($descripcion)) return ['ok' => false, 'error' => 'La descripción es obligatoria.'];
        if ($monto <= 0)         return ['ok' => false, 'error' => 'El monto debe ser mayor a 0.'];
        $this->model->registrar($fecha, $tipo, $descripcion, $monto);
        return ['ok' => true];
    }

    public function listar($fecha_inicio = null, $fecha_fin = null) {
        return $this->model->obtenerMovimientos($fecha_inicio, $fecha_fin);
    }

    public function saldoDia($fecha = null) {
        return $this->model->saldoDia($fecha);
    }

    public function eliminar($id) {
        $this->model->eliminar($id);
        return ['ok' => true];
    }
}
?>