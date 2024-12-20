<?php
require_once '../models/ResourceModel.php';

class ResourceController {
    private $model;

    public function __construct() {
        $this->model = new ResourceModel('resources.json');
    }

    public function index() {
        echo json_encode($this->model->getAll());
    }

    public function store() {
        $data = json_decode(file_get_contents('php://input'), true);
        $result = $this->model->create($data);
        if ($result) {
            http_response_code(201);
            echo json_encode(["message" => "Recurso creado con éxito"]);
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Error al crear el recurso"]);
        }
    }

    public function show() {
        $id = $_GET['id'] ?? null;
        $resource = $this->model->getById($id);
        if ($resource) {
            echo json_encode($resource);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Recurso no encontrado"]);
        }
    }

    public function update() {
        $id = $_GET['id'] ?? null;
        $data = json_decode(file_get_contents('php://input'), true);
        $result = $this->model->update($id, $data);
        if ($result) {
            echo json_encode(["message" => "Recurso actualizado con éxito"]);
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Error al actualizar el recurso"]);
        }
    }

    public function destroy() {
        $id = $_GET['id'] ?? null;
        $result = $this->model->delete($id);
        if ($result) {
            echo json_encode(["message" => "Recurso eliminado con éxito"]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Recurso no encontrado"]);
        }
    }
}
?>