<?php
class ModeloPedido {
    private $filePath = __DIR__ . '/../datos/pedidos.json';

    public function getPedidos() {
        return json_decode(file_get_contents($this->filePath), true) ?? [];
    }

    public function getPedido($id) {
        $pedidos = $this->getPedidos();
        foreach ($pedidos as $pedido) {
            if ($pedido['id'] == $id) {
                return $pedido;
            }
        }
        return null;
    }

    public function getPedidosCliente($cliente_id) {
        $pedidos = $this->getPedidos();
        $pedidosCliente = [];
    
        foreach ($pedidos as $pedido) {
            if ($pedido['cliente_id'] == $cliente_id) {
                $pedidosCliente[] = $pedido;
            }
        }
    
        return empty($pedidosCliente) ? null : $pedidosCliente;
    }

    public function getPedidosVendedor($vendedor_id) {
        $pedidos = $this->getPedidos();
        $pedidosVendedor = [];
    
        foreach ($pedidos as $pedido) {
            if ($pedido['vendedor_id'] == $vendedor_id) {
                $pedidosVendedor[] = $pedido;
            }
        }
    
        return empty($pedidosVendedor) ? null : $pedidosVendedor;
    }
    
    

    public function addPedido($cliente_id, $articulos, $total, $fecha, $estado) {
        $pedidos = $this->getPedidos();
        $nuevoPedido = [
            "id" => count($pedidos) + 1,
            "cliente_id" => $cliente_id,
            "articulos" => $articulos,
            "total" => $total,
            "fecha" => $fecha,
            "estado" => $estado
        ];
        $pedidos[] = $nuevoPedido;
        file_put_contents($this->filePath, json_encode($pedidos, JSON_PRETTY_PRINT));
        return $nuevoPedido;
    }

    public function updatePedido($id, $cliente_id = null, $articulos = null, $total = null, $fecha = null, $estado = null) {
        $pedidos = $this->getPedidos();
        foreach ($pedidos as &$pedido) {
            if ($pedido['id'] == $id) {
                $pedido['cliente_id'] = $cliente_id ?? $pedido['cliente_id'];
                $pedido['articulos'] = $articulos ?? $pedido['articulos'];
                $pedido['total'] = $total ?? $pedido['total'];
                $pedido['fecha'] = $fecha ?? $pedido['fecha'];
                $pedido['estado'] = $estado ?? $pedido['estado'];
                file_put_contents($this->filePath, json_encode($pedidos, JSON_PRETTY_PRINT));
                return $pedido;
            }
        }
        return null;
    }

    public function deletePedido($id) {
        $pedidos = $this->getPedidos();
        foreach ($pedidos as $key => $pedido) {
            if ($pedido['id'] == $id) {
                unset($pedidos[$key]);
                file_put_contents($this->filePath, json_encode(array_values($pedidos), JSON_PRETTY_PRINT));
                return true;
            }
        }
        return false;
    }
}
?>
