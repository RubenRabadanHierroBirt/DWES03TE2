<?php
class ControladorVendedor {
    private $modelo;
    private $vista;

    public function __construct($modelo, $vista) {
        $this->modelo = $modelo;
        $this->vista = $vista;
    }

    // Mostrar todos los vendedores
    public function index() {
        try {
            $vendedores = $this->modelo->getVendedores();
            $this->vista->render($vendedores);
        } catch (Exception $e) {
            http_response_code(500);
            $this->vista->render(["error" => "Error al obtener los vendedores: " . $e->getMessage()]);
        }
    }

    // Mostrar un vendedor por ID
    public function show($id) {
        try {
            if (!is_numeric($id) || $id <= 0) {
                http_response_code(400);
                $this->vista->render(["error" => "ID de vendedor inválido."]);
                return;
            }

            $vendedor = $this->modelo->getVendedor($id);
            if ($vendedor) {
                $this->vista->render($vendedor);
            } else {
                http_response_code(404);
                $this->vista->render(["error" => "Vendedor no encontrado."]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            $this->vista->render(["error" => "Error al buscar el vendedor: " . $e->getMessage()]);
        }
    }

    // Crear un nuevo vendedor
    public function create() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (isset($input['nombre'], $input['email'], $input['telefono'])) {
                $nuevoVendedor = $this->modelo->addVendedor($input['nombre'], $input['email'], $input['telefono']);
                $this->vista->render([
                    "message" => "Vendedor creado exitosamente",
                    "vendedor" => $nuevoVendedor
                ]);
            } else {
                http_response_code(400);
                $this->vista->render(["error" => "Faltan datos obligatorios: 'nombre', 'email' y 'telefono'."]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            $this->vista->render(["error" => "Error al crear el vendedor: " . $e->getMessage()]);
        }
    }

    // Actualizar un vendedor
    public function update() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (isset($input['id']) && is_numeric($input['id']) && $input['id'] > 0) {
                $id = $input['id'];
                $nombre = $input['nombre'] ?? null;
                $email = $input['email'] ?? null;
                $telefono = $input['telefono'] ?? null;

                $vendedorActualizado = $this->modelo->updateVendedor($id, $nombre, $email, $telefono);

                if ($vendedorActualizado) {
                    $this->vista->render([
                        "message" => "Vendedor actualizado exitosamente",
                        "vendedor" => $vendedorActualizado
                    ]);
                } else {
                    http_response_code(404);
                    $this->vista->render(["error" => "Vendedor no encontrado para actualizar."]);
                }
            } else {
                http_response_code(400);
                $this->vista->render(["error" => "ID inválido o faltante."]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            $this->vista->render(["error" => "Error al actualizar el vendedor: " . $e->getMessage()]);
        }
    }

    // Eliminar un vendedor
    public function delete($id) {
        try {
            if (!is_numeric($id) || $id <= 0) {
                http_response_code(400);
                $this->vista->render(["error" => "ID de vendedor inválido."]);
                return;
            }

            $resultado = $this->modelo->deleteVendedor($id);
            if ($resultado) {
                $this->vista->render(["message" => "Vendedor eliminado exitosamente."]);
            } else {
                http_response_code(404);
                $this->vista->render(["error" => "Vendedor no encontrado para eliminar."]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            $this->vista->render(["error" => "Error al eliminar el vendedor: " . $e->getMessage()]);
        }
    }
}
?>
