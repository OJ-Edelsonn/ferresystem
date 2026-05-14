<?php
require_once __DIR__ . '/../models/VentaModel.php';
require_once __DIR__ . '/../models/ProductoModel.php';

class VentaController
{

    private $model;
    private $productoModel;

    public function __construct()
    {
        $this->model         = new VentaModel();
        $this->productoModel = new ProductoModel();
    }

    public function registrar($fecha, $detalle, $observacion = '')
    {
        if (empty($detalle)) {
            return ['ok' => false, 'error' => 'Agrega al menos un producto a la venta.'];
        }
        $resultado = $this->model->crear($fecha, $detalle, $observacion);

        // Registrar automáticamente en caja como ingreso
        if ($resultado['ok']) {
            require_once __DIR__ . '/../models/CajaModel.php';
            $cajaModel = new CajaModel();

            // Calcular total de la venta
            $total = 0;
            foreach ($detalle as $item) {
                $total += $item['subtotal'];
            }

            $descripcion = 'Venta #' . $resultado['venta_id'];
            if (!empty($observacion)) {
                $descripcion .= ' — ' . $observacion;
            }

            $cajaModel->registrar($fecha, 'ingreso', $descripcion, $total);
        }

        return $resultado;
    }

    public function listar($fecha_inicio = null, $fecha_fin = null)
    {
        return $this->model->obtenerTodas($fecha_inicio, $fecha_fin);
    }

    public function detalle($venta_id)
    {
        return $this->model->obtenerDetalle($venta_id);
    }

    public function anular($venta_id, $motivo)
    {
        if (empty($motivo)) {
            return ['ok' => false, 'error' => 'Indica el motivo de anulación.'];
        }
        $resultado = $this->model->anular($venta_id, $motivo);
        return $resultado ? ['ok' => true] : ['ok' => false, 'error' => 'Error al anular la venta.'];
    }

    public function totalHoy()
    {
        return $this->model->totalHoy();
    }

    public function totalMes()
    {
        return $this->model->totalMes();
    }

    public function productos()
    {
        return $this->productoModel->obtenerTodos();
    }
}
