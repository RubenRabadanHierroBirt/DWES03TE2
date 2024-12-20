<?php

class ModeloArticulo {
    private $archivo = '../datos/articulos.json';

    public function getArticulos() {
        if (!file_exists($this->archivo)) {
            throw new Exception("El archivo de artículos no existe.");
        }

        $json = file_get_contents($this->archivo);
        return json_decode($json, true);
    }

    public function getArticulo($id) {
        $articulos = $this->getArticulos();
        foreach ($articulos as $articulo) {
            if ($articulo['id'] == $id) {
                return $articulo;
            }
        }
        return null;
    }

    public function addArticulo($nombre, $descripcion, $precio, $stock) {
        $articulos = $this->getArticulos();
        $nuevoArticulo = [
            'id' => count($articulo) + 1, 
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'precio' => $precio,
            'stock' => $stock
        ];

        $articulos[] = $nuevoArticulo;

        file_put_contents($this->archivo, json_encode($articulos, JSON_PRETTY_PRINT));

        return $nuevoArticulo;
    }

    public function updateArticulo($id, $nombre = null, $descripcion = null, $precio = null, $stock = null) {
        $articulos = $this->getArticulos();
        foreach ($articulos as &$articulo) {
            if ($articulo['id'] == $id) {
                if ($nombre) {
                    $articulo['nombre'] = $nombre;
                }
                if ($descripcion) {
                    $articulo['descripcion'] = $descripcion;
                }
                if ($precio) {
                    $articulo['precio'] = $precio;
                }
                if ($stock) {
                    $articulo['stock'] = $stock;
                }

                file_put_contents($this->archivo, json_encode($articulos, JSON_PRETTY_PRINT));
                return $articulo;
            }
        }
        return null;
    }
    public function updateStock($id, $cantidad) {
        $articulos = $this->getArticulos();
        foreach ($articulos as &$articulo) {
            if ($articulo['id'] == $id) {
                // Verificar si hay suficiente stock
                if ($articulo['stock'] >= $cantidad) {
                    // Restar la cantidad del stock
                    $articulo['stock'] -= $cantidad;
    
                    file_put_contents($this->archivo, json_encode($articulos, JSON_PRETTY_PRINT));
                    return $articulo; 
                } else {
                    // Si no hay suficiente stock
                    throw new Exception("No hay suficiente stock para el artículo con ID {$id}. Stock disponible: {$articulo['stock']}");
                }
            }
        }
    
        // Si no se encuentra el artículo
        throw new Exception("Artículo con ID {$id} no encontrado.");
    }
}

?>
