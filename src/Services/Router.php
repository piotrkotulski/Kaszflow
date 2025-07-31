<?php

namespace Kaszflow\Services;

use Kaszflow\Core\Request;
use Kaszflow\Core\Response;

/**
 * Serwis routera do obsługi routingu
 */
class Router
{
    private $routes = [];
    private $middleware = [];
    
    /**
     * Dodanie trasy GET
     */
    public function get(string $path, $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }
    
    /**
     * Dodanie trasy POST
     */
    public function post(string $path, $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }
    
    /**
     * Dodanie trasy PUT
     */
    public function put(string $path, $handler): void
    {
        $this->addRoute('PUT', $path, $handler);
    }
    
    /**
     * Dodanie trasy DELETE
     */
    public function delete(string $path, $handler): void
    {
        $this->addRoute('DELETE', $path, $handler);
    }
    
    /**
     * Dodanie trasy
     */
    private function addRoute(string $method, string $path, $handler): void
    {
        $this->routes[$method][$path] = $handler;
    }
    
    /**
     * Dodanie middleware
     */
    public function middleware(string $name, callable $handler): void
    {
        $this->middleware[$name] = $handler;
    }
    
    /**
     * Dispatch żądania
     */
    public function dispatch(Request $request): Response
    {
        $method = $request->getMethod();
        $path = $request->getPath();
        
        // Sprawdzenie czy trasa istnieje (z obsługą parametrów)
        $handler = $this->findRoute($method, $path);
        
        if (!$handler) {
            return new Response('404 Not Found', 404);
        }
        
        // Wykonanie middleware
        foreach ($this->middleware as $middleware) {
            $middleware($request);
        }
        
        // Wykonanie handlera
        if (is_array($handler['handler'])) {
            // Handler to [Controller::class, 'method']
            $controllerClass = $handler['handler'][0];
            $method = $handler['handler'][1];
            
            $controller = new $controllerClass();
            
            // Dodanie parametrów do request
            if (isset($handler['params'])) {
                $request->setPathParams($handler['params']);
            }
            
            $result = $controller->$method($request);
        } else {
            // Handler to callable
            $result = $handler['handler']($request);
        }
        
        if ($result instanceof Response) {
            return $result;
        }
        
        return new Response($result);
    }
    
    /**
     * Znajdź trasę z obsługą parametrów
     */
    private function findRoute(string $method, string $path): ?array
    {
        if (!isset($this->routes[$method])) {
            return null;
        }
        
        // Najpierw sprawdź dokładne dopasowanie
        if (isset($this->routes[$method][$path])) {
            return ['handler' => $this->routes[$method][$path]];
        }
        
        // Sprawdź trasy z parametrami
        foreach ($this->routes[$method] as $routePath => $handler) {
            $params = $this->matchRoute($routePath, $path);
            if ($params !== false) {
                return [
                    'handler' => $handler,
                    'params' => $params
                ];
            }
        }
        
        return null;
    }
    
    /**
     * Sprawdź czy ścieżka pasuje do wzorca z parametrami
     */
    private function matchRoute(string $routePath, string $requestPath)
    {
        // Konwertuj {param} na regex
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';
        
        if (preg_match($pattern, $requestPath, $matches)) {
            // Wyciągnij nazwy parametrów
            preg_match_all('/\{([^}]+)\}/', $routePath, $paramNames);
            
            $params = [];
            for ($i = 1; $i < count($matches); $i++) {
                $params[$paramNames[1][$i - 1]] = $matches[$i];
            }
            
            return $params;
        }
        
        return false;
    }
} 