<?php
require_once __DIR__ . '/../models/CotizacionModel.php';

class CotizacionController {

    private $model;

    public function __construct() {
        $this->model = new CotizacionModel();
    }

    public function crear($datos, $materiales) {
        if (empty($datos['cliente_nombre'])) return ['ok' => false, 'error' => 'El nombre del cliente es obligatorio.'];
        if (empty($materiales))              return ['ok' => false, 'error' => 'Agrega al menos un material.'];
        return $this->model->crear($datos, $materiales);
    }

    public function listar($busqueda = '') {
        return $this->model->obtenerTodas($busqueda);
    }

    public function obtener($id) {
        return $this->model->obtenerPorId($id);
    }

    public function materiales($cotizacion_id) {
        return $this->model->obtenerMateriales($cotizacion_id);
    }

    public function eliminar($id) {
        $this->model->eliminar($id);
        return ['ok' => true];
    }
}
?>