<?php
class ModeloVendedor {
    private $filePath = __DIR__ . '/../datos/vendedores.json';

    public function getVendedores() {
        return json_decode(file_get_contents($this->filePath), true) ?? [];
    }

    public function getVendedor($id) {
        $vendedores = $this->getVendedores();
        foreach ($vendedores as $vendedor) {
            if ($vendedor['id'] == $id) {
                return $vendedor;
            }
        }
        return null;
    }

    public function addVendedor($nombre, $email, $telefono) {
        $vendedores = $this->getVendedores();
        $nuevoVendedor = [
            "id" => count($vendedores) + 1,
            "nombre" => $nombre,
            "email" => $email,
            "telefono" => $telefono
        ];
        $vendedores[] = $nuevoVendedor;
        file_put_contents($this->filePath, json_encode($vendedores, JSON_PRETTY_PRINT));
        return $nuevoVendedor;
    }

    public function updateVendedor($id, $nombre = null, $email = null, $telefono = null) {
        $vendedores = $this->getVendedores();
        foreach ($vendedores as &$vendedor) {
            if ($vendedor['id'] == $id) {
                $vendedor['nombre'] = $nombre ?? $vendedor['nombre'];
                $vendedor['email'] = $email ?? $vendedor['email'];
                $vendedor['telefono'] = $telefono ?? $vendedor['telefono'];
                file_put_contents($this->filePath, json_encode($vendedores, JSON_PRETTY_PRINT));
                return $vendedor;
            }
        }
        return null;
    }

    public function deleteVendedor($id) {
        $vendedores = $this->getVendedores();
        foreach ($vendedores as $key => $vendedor) {
            if ($vendedor['id'] == $id) {
                unset($vendedores[$key]);
                file_put_contents($this->filePath, json_encode(array_values($vendedores), JSON_PRETTY_PRINT));
                return true;
            }
        }
        return false;
    }
}
?>
