<?php
require_once __DIR__ . '/../models/PedidoModel.php';

class PedidoController {

    private $model;

    public function __construct() {
        $this->model = new PedidoModel();
    }

    public function crear($cliente_nombre, $cliente_telefono, $cliente_direccion, $detalle) {
        if (empty($cliente_nombre))    return ['ok' => false, 'error' => 'El nombre es obligatorio.'];
        if (empty($cliente_telefono))  return ['ok' => false, 'error' => 'El teléfono es obligatorio.'];
        if (empty($cliente_direccion)) return ['ok' => false, 'error' => 'La dirección es obligatoria.'];
        if (empty($detalle))           return ['ok' => false, 'error' => 'Agrega al menos un producto.'];
        return $this->model->crear($cliente_nombre, $cliente_telefono, $cliente_direccion, $detalle);
    }

    public function listar($estado = null) {
        return $this->model->obtenerTodos($estado);
    }

    public function obtener($id) {
        return $this->model->obtenerPorId($id);
    }

    public function detalle($pedido_id) {
        return $this->model->obtenerDetalle($pedido_id);
    }

    public function cambiarEstado($pedido_id, $estado) {
        $estados_validos = ['pendiente', 'en_proceso', 'entregado'];
        if (!in_array($estado, $estados_validos)) {
            return ['ok' => false, 'error' => 'Estado no válido.'];
        }
        $this->model->cambiarEstado($pedido_id, $estado);
        return ['ok' => true];
    }

    public function totalPendientes() {
        return $this->model->totalPendientes();
    }
}
?>