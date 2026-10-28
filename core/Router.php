<?php

namespace core;

class Router {
    private $routes = [];

    public function get($route, $action, $middleware = []) {
        $this->addRoute('GET', $route, $action, $middleware);
    }

    public function post($route, $action, $middleware = []) {
        $this->addRoute('POST', $route, $action, $middleware);
    }

    private function addRoute($method, $route, $action, $middleware) {
        // Convert route variables like {id} to regex
        $routeRegex = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<\1>[a-zA-Z0-9_-]+)', $route);
        $routeRegex = '#^' . $routeRegex . '$#';

        $this->routes[] = [
            'method' => $method,
            'route' => $routeRegex,
            'action' => $action,
            'middleware' => $middleware
        ];
    }

    public function dispatch($url) {
        $method = $_SERVER['REQUEST_METHOD'];
        
        // Normalize URL
        $url = rtrim($url, '/');
        if (empty($url)) {
            $url = '/';
        } else {
            $url = '/' . ltrim($url, '/');
        }

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['route'], $url, $matches)) {
                
                // Run middleware first
                foreach ($route['middleware'] as $mw) {
                    $mwClass = "\\app\\Middleware\\" . $mw;
                    $middlewareInstance = new $mwClass();
                    if (!$middlewareInstance->handle()) {
                        // Middleware blocked the request
                        return;
                    }
                }

                // Extract params
                $params = [];
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $params[$key] = $match;
                    }
                }

                // Call Action
                list($controller, $method) = explode('@', $route['action']);
                $controllerClass = "\\app\\Controllers\\" . $controller;
                
                if (class_exists($controllerClass)) {
                    $controllerInstance = new $controllerClass();
                    if (method_exists($controllerInstance, $method)) {
                        // Pass params to method
                        call_user_func_array([$controllerInstance, $method], $params);
                        return;
                    }
                }
            }
        }

        // 404
        http_response_code(404);
        echo "404 Not Found";
    }
}
