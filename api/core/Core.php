<?php

require_once __DIR__ . '/../http/Route.php';
require_once __DIR__ . '/../http/Response.php';
require_once __DIR__ . '/../http/Request.php';
require_once __DIR__ . '/../factory/ControllerFactory.php';

class Core
{
    public static function dispatch(array $routes): void
    {
        $url = self::getUrlFromRequest();

        foreach ($routes as $route) {
            if (self::matchRoute($route['path'], $url, $matches)) {
                self::handleRoute($route, $matches);

                return;
            }
        }

        self::handleNotFound();
    }

    private static function getUrlFromRequest(): string
    {
        $url = $_SERVER['REQUEST_URI'];

        return rtrim($url, '/');
    }

    private static function matchRoute(string $route_path, string $url, &$matches): bool
    {
        $pattern = self::getRoutePattern($route_path);

        return preg_match($pattern, $url, $matches);
    }

    private static function getRoutePattern(string $route_path): string
    {
        return '#^' . preg_replace('/{id}/', '([\w-]+)', $route_path) . '$#';
    }

    private static function handleRoute(array $route, array $matches): void
    {
        try {
            if ($route['method'] !== Request::getMethod()) {
                Response::json([
                    'success' => false,
                    'message' => 'Método não permitido.'
                ], 405);
                return;
            }

            [$controller, $action] = explode('::', $route['action']);

            $controller_instance = ControllerFactory::create($controller);

            $controller_instance->$action(new Request, new Response, $matches);
        } catch (Exception $e) {
            Response::json([
                'success' => false,
                'message' => 'Falha ao encontrar rota.',
            ], 400);
        }
    }

    private static function handleNotFound(): void
    {
        $controller = NOT_FOUND_CONTROLLER;

        $controller_instance = ControllerFactory::create($controller);

        $controller_instance->index(new Response);
    }
}
