<?php
// Ruta base para los archivos JSON
const DATA_PATH = __DIR__ . '/../data/';

/**
 * Lee un archivo JSON y lo convierte a un array PHP.
 * 
 * @param string $filename Nombre del archivo JSON.
 * @return array Datos decodificados del archivo.
 */
function readJsonFile($filename) {
    $filePath = DATA_PATH . $filename;
    if (!file_exists($filePath)) {
        return [];
    }
    $content = file_get_contents($filePath);
    return json_decode($content, true) ?? [];
}

/**
 * Escribe un array PHP en un archivo JSON.
 * 
 * @param string $filename Nombre del archivo JSON.
 * @param array $data Datos a escribir.
 */
function writeJsonFile($filename, $data) {
    $filePath = DATA_PATH . $filename;
    file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));
}
