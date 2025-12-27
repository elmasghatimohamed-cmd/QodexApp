<?php
namespace App\Core;

use Closure;

class Router
{
    private array $routes = [];

    public function get(string $path, Closure $handler): void
    {
        $this->map('GET', $path, $handler);
    }

    public function post(string $path, Closure $handler): void
    {
        $this->map('POST', $path, $handler);
    }

    private function map(string $method, string $path, Closure $handler): void
    {
        $this->routes[$method][$path] = $handler;
    }

    public function dispatch(string $method, string $uri): mixed
    {
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';

        if (!isset($this->routes[$method])) {
            http_response_code(404);
            echo "Not Found - Method not allowed";
            return null;
        }

        // 1. D'abord, chercher une correspondance exacte
        if (isset($this->routes[$method][$path])) {
            return $this->routes[$method][$path]();
        }

        // 2. Ensuite, chercher avec des paramètres dynamiques dans l'URL
        foreach ($this->routes[$method] as $route => $handler) {
            // Convertir ":param" en regex avec capture
            $pattern = preg_replace('#:([a-zA-Z0-9_]+)#', '([^/]+)', $route);
            $pattern = "#^" . $pattern . "$#";

            if (preg_match($pattern, $path, $matches)) {
                // Enlever le premier élément (le match complet)
                array_shift($matches);

                // Convertir les paramètres en entiers si ce sont des nombres
                $params = array_map(function ($param) {
                    return is_numeric($param) ? (int) $param : $param;
                }, $matches);

                // Appeler le handler avec les paramètres
                return $handler(...$params);
            }
        }

        // 3. NOUVEAU : Chercher avec query string pour compatibilité
        // Si la route attend un :id mais reçoit ?id=X ou ?quiz_id=X
        foreach ($this->routes[$method] as $route => $handler) {
            // Extraire le pattern de base sans le paramètre
            $baseRoute = preg_replace('#/:([a-zA-Z0-9_]+)$#', '', $route);

            if ($path === $baseRoute && strpos($route, ':') !== false) {
                // La route correspond à la base, chercher l'ID dans query string
                $paramName = null;
                if (preg_match('#:([a-zA-Z0-9_]+)$#', $route, $paramMatch)) {
                    $paramName = $paramMatch[1];
                }

                // Chercher l'ID dans plusieurs formats possibles
                $id = $_GET[$paramName] ?? $_GET['id'] ?? $_GET['quiz_id'] ?? null;

                if ($id !== null) {
                    return $handler((int) $id);
                }
            }
        }

        // 4. Aucune route trouvée
        http_response_code(404);
        echo "Not Found - No route matches for: " . $path;
        return null;
    }
}