<?php
class ControladorCliente {
    private $modelo;
    private $vista;

    public function __construct($modelo, $vista) {
        $this->modelo = $modelo;
        $this->vista = $vista;
    }

    // Mostrar todos los clientes
    public function index() {
        try {
            $clientes = $this->modelo->getClientes();
            $this->vista->render($clientes);
        } catch (Exception $e) {
            http_response_code(500);
            $this->vista->render(["error" => "Error al obtener los clientes: " . $e->getMessage()]);
        }
    }

    // Buscar un cliente por ID
    public function show($id) {
        try {
            if (!is_numeric($id) || $id <= 0) {
                http_response_code(400);
                $this->vista->render(["error" => "ID de cliente inválido"]);
                return;
            }

            $cliente = $this->modelo->getCliente($id);
            if ($cliente) {
                $this->vista->render($cliente);
            } else {
                http_response_code(404);
                $this->vista->render(["error" => "Cliente no encontrado"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            $this->vista->render(["error" => "Error al buscar el cliente: " . $e->getMessage()]);
        }
    }

    // Modificar cliente
    public function update() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (!isset($input['id']) || !is_numeric($input['id']) || $input['id'] <= 0) {
                http_response_code(400);
                $this->vista->render(["error" => "ID de cliente inválido o no proporcionado"]);
                return;
            }

            $id = $input['id'];
            $nombre = $input['nombre'] ?? null;
            $email = $input['email'] ?? null;
            $telefono = $input['telefono'] ?? null;
            $direccion = $input['direccion'] ?? null;

            // Validaciones adicionales de los datos
            if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                http_response_code(400);
                $this->vista->render(["error" => "Formato de email inválido"]);
                return;
            }

            if ($telefono && !preg_match('/^\+?[0-9]{7,15}$/', $telefono)) {
                http_response_code(400);
                $this->vista->render(["error" => "Formato de teléfono inválido"]);
                return;
            }

            $clienteActualizado = $this->modelo->updateCliente($id, $nombre, $email, $telefono, $direccion);

            if ($clienteActualizado) {
                $this->vista->render([
                    "message" => "Cliente actualizado exitosamente",
                    "cliente" => $clienteActualizado
                ]);
            } else {
                http_response_code(404);
                $this->vista->render(["error" => "Cliente no encontrado"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            $this->vista->render(["error" => "Error al actualizar el cliente: " . $e->getMessage()]);
        }
    }
}
?>
