<?php

require_once HOME . 'api/database/Database.php';
require_once HOME . 'api/helper/Functions.php';

class ControllerFactory
{
    public static function create(string $type): object
    {
        try {
            switch ($type) {
                case 'UserController':
                    require_once HOME . 'api/controller/UserController.php';
                    require_once HOME . 'api/repository/UserRepository.php';
                    return new UserController(new UserRepository(Database::getConnection()));

                case 'MonthlyIncomeController':
                    require_once HOME . 'api/controller/MonthlyIncomeController.php';
                    require_once HOME . 'api/repository/MonthlyIncomeRepository.php';
                    return new MonthlyIncomeController(new MonthlyIncomeRepository(Database::getConnection()));

                case 'NotFoundController':
                    require_once HOME . 'api/controller/NotFoundController.php';
                    return new NotFoundController();

                default:
                    throw new \Exception("Controller desconhecido.", 404);
            }
        } catch (\Throwable $th) {
            Functions::isCustomThrow($th);
            throw new \Exception("Erro ao criar o controller.", $th->getCode(), $th);
        }
    }
}
