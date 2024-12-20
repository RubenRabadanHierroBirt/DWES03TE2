<?php

class ModeloCliente {
    private $archivo = '../datos/clientes.json';

    public function getClientes() {
        if (!file_exists($this->archivo)) {
            throw new Exception("El archivo de clientes no existe.");
        }

        $json = file_get_contents($this->archivo);
        return json_decode($json, true);
    }

    public function getCliente($id) {
        $clientes = $this->getClientes();
        foreach ($clientes as $cliente) {
            if ($cliente['id'] == $id) {
                return $cliente;
            }
        }
        return null;
    }

    public function updateCliente($id, $nombre = null, $email = null, $telefono = null, $direccion = null) {
        $clientes = $this->getClientes();
        foreach ($clientes as &$cliente) {
            if ($cliente['id'] == $id) {
                if ($nombre) {
                    $cliente['nombre'] = $nombre;
                }
                if ($email) {
                    $cliente['email'] = $email;
                }
                if ($telefono) {
                    $cliente['telefono'] = $telefono;
                }
                if ($direccion) {
                    $cliente['direccion'] = $direccion;
                }

                file_put_contents($this->archivo, json_encode($clientes, JSON_PRETTY_PRINT));
                return $cliente;
            }
        }
        return null;
    }
}

?>
