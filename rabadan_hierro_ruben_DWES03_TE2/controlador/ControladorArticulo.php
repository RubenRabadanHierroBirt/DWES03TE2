<?php
class ControladorArticulo {
    private $modelo;
    private $vista;

    public function __construct($modelo, $vista) {
        $this->modelo = $modelo;
        $this->vista = $vista;
    }

    // Mostrar todos los artículos
    public function index() {
        try {
            $articulos = $this->modelo->getArticulos();
            $this->vista->render($articulos);
        } catch (Exception $e) {
            http_response_code(500);
            $this->vista->render(["error" => "Error al obtener los artículos: " . $e->getMessage()]);
        }
    }

    // Buscar un artículo por ID
    public function show($id) {
        try {
            if (!is_numeric($id) || $id <= 0) {
                http_response_code(400);
                $this->vista->render(["error" => "$id ID de artículo inválido"]);
                return;
            }

            $articulo = $this->modelo->getArticulo($id);
            if ($articulo) {
                $this->vista->render($articulo);
            } else {
                http_response_code(404);
                $this->vista->render(["error" => "Artículo con ID $id no encontrado."]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            $this->vista->render(["error" => "Error al buscar el artículo: " . $e->getMessage()]);
        }
    }

    // Crear un nuevo artículo
    public function create() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (isset($input['nombre'], $input['descripcion'], $input['precio'], $input['stock'])) {
                // Validaciones
                if (!is_string($input['nombre']) || strlen($input['nombre']) < 3) {
                    http_response_code(400);
                    $this->vista->render(["error" => "El nombre debe ser un texto de al menos 3 caracteres."]);
                    return;
                }
                if (!is_numeric($input['precio']) || $input['precio'] <= 0) {
                    http_response_code(400);
                    $this->vista->render(["error" => "El precio debe ser un número mayor a 0."]);
                    return;
                }
                if (!is_int($input['stock']) || $input['stock'] < 0) {
                    http_response_code(400);
                    $this->vista->render(["error" => "El stock debe ser un número entero no negativo."]);
                    return;
                }

                $nuevoArticulo = $this->modelo->addArticulo(
                    $input['nombre'],
                    $input['descripcion'],
                    $input['precio'],
                    $input['stock']
                );

                $this->vista->render([
                    "message" => "Artículo creado exitosamente",
                    "articulo" => $nuevoArticulo
                ]);
            } else {
                http_response_code(400);
                $this->vista->render(["error" => "Datos inválidos. Asegúrate de enviar 'nombre', 'descripcion', 'precio' y 'stock'."]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            $this->vista->render(["error" => "Error al crear el artículo: " . $e->getMessage()]);
        }
    }

    // Actualizar artículo
    public function update() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (isset($input['id'])) {
                $id = $input['id'];
                $nombre = $input['nombre'] ?? null;
                $descripcion = $input['descripcion'] ?? null;
                $precio = $input['precio'] ?? null;
                $stock = $input['stock'] ?? null;

                // Validaciones de datos opcionales
                if ($nombre && (!is_string($nombre) || strlen($nombre) < 3)) {
                    http_response_code(400);
                    $this->vista->render(["error" => "El nombre debe ser un texto de al menos 3 caracteres."]);
                    return;
                }
                if ($precio && (!is_numeric($precio) || $precio <= 0)) {
                    http_response_code(400);
                    $this->vista->render(["error" => "El precio debe ser un número mayor a 0."]);
                    return;
                }
                if ($stock && (!is_int($stock) || $stock < 0)) {
                    http_response_code(400);
                    $this->vista->render(["error" => "El stock debe ser un número entero no negativo."]);
                    return;
                }

                $articuloActualizado = $this->modelo->updateArticulo($id, $nombre, $descripcion, $precio, $stock);

                if ($articuloActualizado) {
                    $this->vista->render([
                        "message" => "Artículo actualizado exitosamente",
                        "articulo" => $articuloActualizado
                    ]);
                } else {
                    http_response_code(404);
                    $this->vista->render(["error" => "Artículo con ID $id no encontrado."]);
                }
            } else {
                http_response_code(400);
                $this->vista->render(["error" => "Falta el ID del artículo para actualizar."]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            $this->vista->render(["error" => "Error al actualizar el artículo: " . $e->getMessage()]);
        }
    }
}
?>
