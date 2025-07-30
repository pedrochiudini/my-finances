<?php

require_once HOME . 'api/controller/NotFoundController.php';

class ControllerFactory
{
    public static function create(string $type): object
    {
        switch ($type) {
            case 'NotFoundController':
                return new NotFoundController();

            default:
                throw new \Exception("Controller desconhecido", 404);
        }
    }
}
