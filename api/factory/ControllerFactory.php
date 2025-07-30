<?php

require_once __DIR__ . '/../controller/NotFoundController.php';

class ControllerFactory
{
    public static function create(string $type): mixed
    {
        return match ($type) {
            NotFoundController::class => new NotFoundController(),
            default                   => throw new \Exception("Controller desconhecida.")
        };
    }
}