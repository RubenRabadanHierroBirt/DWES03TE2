<?php
// Cargar los archivos necesarios
require_once __DIR__ . '/../router/Router.php';
require_once __DIR__ . '/../controlador/ControladorArticulo.php';
require_once __DIR__ . '/../controlador/ControladorCliente.php';
require_once __DIR__ . '/../controlador/ControladorPedido.php';
require_once __DIR__ . '/../controlador/ControladorVendedor.php';
require_once __DIR__ . '/../modelo/ModeloArticulo.php';
require_once __DIR__ . '/../modelo/ModeloCliente.php';
require_once __DIR__ . '/../modelo/ModeloPedido.php';
require_once __DIR__ . '/../modelo/ModeloVendedor.php';
require_once __DIR__ . '/../vista/VistaJSON.php';

// Obtener la URL relativa a partir de /public
$url = $_SERVER['REQUEST_URI'];
$url = parse_url($url, PHP_URL_PATH);
$basePath = '/rabadan_hierro_ruben_DWES03_TE2/public';
if (strpos($url, $basePath) === 0) {
    $relativeUrl = substr($url, strlen($basePath));
    $relativeUrl = (string) $relativeUrl;
} else {
    http_response_code(404);
    echo json_encode(["error" => "URL no válida. Debe comenzar con /public"]);
    exit;
}

$router = new Router();

$modeloArticulo = new ModeloArticulo();
$modeloCliente = new ModeloCliente();
$modeloPedido = new ModeloPedido();
$modeloVendedor = new ModeloVendedor();
$vistaJSON = new VistaJSON();
$controladorArticulo = new ControladorArticulo($modeloArticulo, $vistaJSON);
$controladorCliente = new ControladorCliente($modeloCliente, $vistaJSON);
$controladorPedido = new ControladorPedido($modeloPedido, $modeloCliente, $modeloVendedor, $modeloArticulo, $vistaJSON);
$controladorVendedor = new ControladorVendedor($modeloVendedor, $vistaJSON);

$router->add('/articulos', 'ControladorArticulo@index');
$router->add('/articulos/post', 'ControladorArticulo@create');
$router->add('/articulos/get/{id}', 'ControladorArticulo@show');
$router->add('/articulos/update', 'ControladorArticulo@update');

$router->add('/clientes', 'ControladorCliente@index');
$router->add('/clientes/get/{id}', 'ControladorCliente@show');
$router->add('/clientes/update', 'ControladorCliente@update');

$router->add('/pedidos', 'ControladorPedido@index');
$router->add('/pedidos/post', 'ControladorPedido@create');
$router->add('/pedidos/getCliente/{id}', 'ControladorPedido@pedidosPorCliente');
$router->add('/pedidos/getVendedor/{id}', 'ControladorPedido@pedidosPorVendedor');
$router->add('/pedidos/update', 'ControladorPedido@update');
$router->add('/pedidos/delete/{id}', 'ControladorPedido@delete');

$router->add('/vendedores', 'ControladorVendedor@index');
$router->add('/vendedores/post', 'ControladorVendedor@create');
$router->add('/vendedores/get/{id}', 'ControladorVendedor@show');
$router->add('/vendedores/update', 'ControladorVendedor@update');
$router->add('/vendedores/delete/{id}', 'ControladorVendedor@delete');


// Despachar la solicitud
$router->dispatch($relativeUrl);
