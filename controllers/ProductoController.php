<?php
require_once __DIR__ . '/../models/ProductoModel.php';

class ProductoController {

    private $model;

    public function __construct() {
        $this->model = new ProductoModel();
    }

    public function listar($categoria_id = null, $busqueda = '') {
        return $this->model->obtenerTodos($categoria_id, $busqueda);
    }

    public function obtener($id) {
        return $this->model->obtenerPorId($id);
    }

    public function crear($datos) {
        $errores = $this->validar($datos);
        if (!empty($errores)) return ['ok' => false, 'errores' => $errores];
        $this->model->crear($datos);
        return ['ok' => true];
    }

    public function actualizar($id, $datos) {
        $errores = $this->validar($datos);
        if (!empty($errores)) return ['ok' => false, 'errores' => $errores];
        $this->model->actualizar($id, $datos);
        return ['ok' => true];
    }

    public function eliminar($id) {
        $this->model->eliminar($id);
        return ['ok' => true];
    }

    public function stockCritico() {
        return $this->model->obtenerStockCritico();
    }

    public function registrarEntrada($producto_id, $cantidad, $observacion = '') {
        if ($cantidad <= 0) return ['ok' => false, 'errores' => ['Cantidad debe ser mayor a 0']];
        $this->model->registrarEntrada($producto_id, $cantidad, $observacion);
        return ['ok' => true];
    }

    public function categorias() {
        return $this->model->obtenerCategorias();
    }

    private function validar($datos) {
        $errores = [];
        if (empty($datos['nombre']))       $errores[] = "El nombre es obligatorio.";
        if (empty($datos['categoria_id'])) $errores[] = "Selecciona una categoría.";
        if (!is_numeric($datos['precio_venta']) || $datos['precio_venta'] <= 0)
            $errores[] = "El precio de venta debe ser mayor a 0.";
        if (!is_numeric($datos['stock_actual']) || $datos['stock_actual'] < 0)
            $errores[] = "El stock no puede ser negativo.";
        return $errores;
    }
}
?>