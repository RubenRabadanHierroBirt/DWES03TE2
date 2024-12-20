<?php
class VistaCliente {
    public function render($data) {
        header('Content-Type: application/json; charset=utf-8');

        if (isset($data['error'])) {
            echo json_encode(["status" => "error", "message" => $data['error']], JSON_PRETTY_PRINT);
        } elseif (isset($data['message'])) {
            echo json_encode(["status" => "success", "message" => $data['message'], "data" => $data['cliente'] ?? null], JSON_PRETTY_PRINT);
        } else {
            echo json_encode(["status" => "success", "data" => $data], JSON_PRETTY_PRINT);
        }
    }
}
?>
