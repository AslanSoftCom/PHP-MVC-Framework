<?php

class Router {
    
    /**
     * Tüm tanımlanan rotalar burada saklanır.
     * 
     * @var array
     */
    private static array $routes = [];

    /**
     * 404 hata şablonunun yolu.
     * 
     * @var string
     */
    private static string $errorTemplate = __DIR__ . '/../core/errors/404.php';

    /**
     * Yeni bir rota ekler.
     *
     * @param string $module Modül adı (örneğin: "user", "admin").
     * @param string|array $methods HTTP yöntemleri (GET, POST vb.). Tek bir yöntem veya bir dizi olabilir.
     * @param string $path URL yolu (örneğin: "/users/{id}").
     * @param string $action Yönlendirilecek kontrolcü ve metot (örneğin: "UserController::show").
     * @return void
     */
    public static function add(string $module, $methods, string $path, string $action): void {
        $path = '/' . trim($path, '/');
        // URL parametrelerini yakala (alfanumerik, tire ve alt çizgi)
        $path = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([a-zA-Z0-9_\-]+)', $path);
        $methods = is_array($methods) ? $methods : explode('|', strtoupper($methods));
        
        self::$routes[] = [
            'module' => $module,
            'methods' => $methods,
            'path' => '#^' . $path . '$#u',
            'action' => $action,
        ];
    }

    /**
     * Gelen HTTP isteğini işler ve uygun rotayı bulur.
     * Eğer bir eşleşme bulunamazsa 404 hata sayfasını döner.
     *
     * @return void
     */
    public static function dispatch(): void {
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        foreach (self::$routes as $route) {
            if (
                in_array($requestMethod, $route['methods']) &&
                preg_match($route['path'], $requestUri, $matches)
            ) {
                array_shift($matches);

                self::callAction($route['module'], $route['action'], $matches);
                return;
            }
        }

        http_response_code(404);
        include self::$errorTemplate;
    }

    /**
     * Belirtilen kontrolcü ve metodu çağırır.
     *
     * @param string $module Modül adı (örneğin: "user", "admin").
     * @param string $action Kontrolcü ve metot (örneğin: "UserController::show").
     * @param array $params URL'den alınan parametreler.
     * @return void
     */
    private static function callAction(string $module, string $action, array $params): void {
        list($controllerPath, $method) = explode('::', $action);
        $controllerFile = "app/" . $module . "/controllers/" . $controllerPath . ".php";
        if (file_exists($controllerFile)) {
            require_once $controllerFile;

            if (class_exists($controllerPath)) {
                $controllerInstance = new $controllerPath();

                if (method_exists($controllerInstance, $method)) {
                    call_user_func_array([$controllerInstance, $method], $params);
                    return;
                }
            }
        }

        http_response_code(404);
        include self::$errorTemplate;
    }
}