<?php
class ControladorPedido {
    
    private $modeloPedido;
    private $modeloCliente; // Agregar modelo de cliente
    private $modeloVendedor; // Agregar modelo de vendedor
    private $modeloArticulo; // Agregar modelo de artículos
    private $vista;

    public function __construct($modeloPedido, $modeloCliente, $modeloVendedor, $modeloArticulo, $vista) {
        $this->modeloPedido = $modeloPedido;
        $this->modeloCliente = $modeloCliente;
        $this->modeloVendedor = $modeloVendedor;
        $this->modeloArticulo = $modeloArticulo;
        $this->vista = $vista;
    }

    // Mostrar todos los pedidos
    public function index() {
        try {
            $pedidos = $this->modeloPedido->getPedidos();
            $this->vista->render($pedidos);
        } catch (Exception $e) {
            http_response_code(500);
            $this->vista->render(["error" => "Error al obtener los pedidos: " . $e->getMessage()]);
        }
    }

    // Mostrar pedidos de un cliente
    public function pedidosPorCliente($id) {
        try {
            if (!is_numeric($id) || $id <= 0) {
                http_response_code(400);
                $this->vista->render(["error" => "ID de cliente inválido."]);
                return;
            }

            $pedidos = $this->modeloPedido->getPedidosCliente($id);
            if ($pedidos) {
                $this->vista->render($pedidos);
            } else {
                http_response_code(404);
                $this->vista->render(["error" => "No se encontraron pedidos para el cliente con ID $id."]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            $this->vista->render(["error" => "Error al obtener los pedidos: " . $e->getMessage()]);
        }
    }

    // Mostrar pedidos de un vendedor
    public function pedidosPorVendedor($id) {
        try {
            if (!is_numeric($id) || $id <= 0) {
                http_response_code(400);
                $this->vista->render(["error" => "ID de vendedor inválido."]);
                return;
            }

            $pedidos = $this->modeloPedido->getPedidosVendedor($id);
            if ($pedidos) {
                $this->vista->render($pedidos);
            } else {
                http_response_code(404);
                $this->vista->render(["error" => "No se encontraron pedidos para el vendedor con ID $id."]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            $this->vista->render(["error" => "Error al obtener los pedidos: " . $e->getMessage()]);
        }
    }

    // Crear un nuevo pedido
    public function create() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (!isset($input['cliente_id'], $input['vendedor_id'], $input['articulos'])) {
                http_response_code(400);
                $this->vista->render(["error" => "Datos inválidos. Asegúrate de enviar 'cliente_id', 'vendedor_id' y 'articulos'."]);
                return;
            }

            // Validar cliente
            $cliente = $this->modeloCliente->getCliente($input['cliente_id']);
            if (!$cliente) {
                http_response_code(404);
                $this->vista->render(["error" => "Cliente con ID {$input['cliente_id']} no encontrado."]);
                return;
            }

            // Validar vendedor
            $vendedor = $this->modeloVendedor->getVendedor($input['vendedor_id']);
            if (!$vendedor) {
                http_response_code(404);
                $this->vista->render(["error" => "Vendedor con ID {$input['vendedor_id']} no encontrado."]);
                return;
            }

            // Validar artículos
            $articulos = $input['articulos'];
            $total = 0;

            foreach ($articulos as $item) {
                if (!isset($item['articulo_id'], $item['cantidad']) || $item['cantidad'] <= 0) {
                    http_response_code(400);
                    $this->vista->render(["error" => "Datos de artículo inválidos."]);
                    return;
                }

                $articulo = $this->modeloArticulo->getArticulo($item['articulo_id']);
                if (!$articulo) {
                    http_response_code(404);
                    $this->vista->render(["error" => "Artículo con ID {$item['articulo_id']} no encontrado."]);
                    return;
                }

                if ($articulo['stock'] < $item['cantidad']) {
                    http_response_code(400);
                    $this->vista->render(["error" => "Stock insuficiente para el artículo con ID {$item['articulo_id']}."]);
                    return;
                }

                $total += $articulo['precio'] * $item['cantidad'];
            }

            // Reducir stock de artículos
            foreach ($articulos as $item) {
                $this->modeloArticulo->updateStock($item['articulo_id'], -$item['cantidad']);
            }

            // Crear pedido
            $nuevoPedido = $this->modeloPedido->addPedido(
                $input['cliente_id'],
                $input['vendedor_id'],
                $articulos,
                $total,
                date('Y-m-d'),
                "pendiente"
            );

            $this->vista->render([
                "message" => "Pedido creado exitosamente.",
                "pedido" => $nuevoPedido
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            $this->vista->render(["error" => "Error al crear el pedido: " . $e->getMessage()]);
        }
    }

    // Eliminar un pedido
    public function delete($id) {
        try {
            if (!is_numeric($id) || $id <= 0) {
                http_response_code(400);
                $this->vista->render(["error" => "ID de pedido inválido."]);
                return;
            }

            $pedido = $this->modeloPedido->getPedido($id);

            if (!$pedido) {
                http_response_code(404);
                $this->vista->render(["error" => "Pedido con ID $id no encontrado."]);
                return;
            }

            $fechaPedido = strtotime($pedido['fecha']);
            $fechaHoy = strtotime(date('Y-m-d'));

            if ($fechaPedido > $fechaHoy) {
                $this->modeloPedido->deletePedido($id);
                $this->vista->render(["message" => "Pedido con ID $id eliminado exitosamente."]);
            } else {
                http_response_code(400);
                $this->vista->render(["error" => "No se puede eliminar el pedido con ID $id porque la fecha no es menor a hoy."]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            $this->vista->render(["error" => "Error al eliminar el pedido: " . $e->getMessage()]);
        }
    }
}
?>
