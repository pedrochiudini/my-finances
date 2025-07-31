<?php

class ControllerFactory
{
    public static function create(string $type): object
    {
        switch ($type) {
            case 'UserController':
                require_once HOME . 'api/controller/UserController.php';
                return new UserController();

            case 'NotFoundController':
                require_once HOME . 'api/controller/NotFoundController.php';
                return new NotFoundController();

            default:
                throw new \Exception("Controller desconhecido", 404);
        }
    }
}
