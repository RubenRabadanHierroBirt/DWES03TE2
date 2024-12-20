<?php
class Router {
    private $routes = [];

    // Método para añadir rutas
    public function add($route, $action) {
        // Guardar la ruta y la acción
        $this->routes[$route] = $action;
    }

    // Método para despachar la solicitud
    public function dispatch($uri) {
        $uri = rtrim($uri, '/');

        // Buscar la ruta que coincida con la URI
        foreach ($this->routes as $route => $action) {
            // Comprobar si la ruta tiene parámetros dinámicos
            $routePattern = preg_replace('/\{(\w+)\}/', '(?P<\1>\w+)', $route); // Convierte {id} a (?P<id>\w+)
            if (preg_match("#^$routePattern$#", $uri, $matches)) {
                // Si hay coincidencia, obtener la acción (Controlador@metodo)
                list($controller, $method) = explode('@', $action);

                // Verificar si el controlador y el método existen
                if (class_exists($controller) && method_exists($controller, $method)) {
                    // Crear una instancia del controlador y pasarle las dependencias
                    $instance = $this->createControllerInstance($controller);

                    // Extraer solo los parámetros dinámicos con nombre
                    $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                    // Llamar al método con los parámetros
                    call_user_func_array([$instance, $method], $params);
                } else {
                    http_response_code(404);
                    echo json_encode(["error" => "Controlador o método no encontrado"]);
                }
                return;
            }
        }

        // Si no se encuentra la ruta
        http_response_code(404);
        echo json_encode(["error" => "Ruta no encontrada"]);
    }

    // Método para crear la instancia del controlador
    private function createControllerInstance($controller) {
        switch ($controller) {
            case 'ControladorArticulo':
                return new $controller(new ModeloArticulo(), new VistaJSON());
            case 'ControladorCliente':
                return new $controller(new ModeloCliente(), new VistaJSON());
            case 'ControladorPedido':
                return new $controller(new ModeloPedido(), new ModeloCliente(), new ModeloVendedor(), new ModeloArticulo(), new VistaJSON());
            case 'ControladorVendedor':
                return new $controller(new ModeloVendedor(), new VistaJSON());
            default:
                return new $controller();
        }
    }
}
