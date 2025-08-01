<?php

require_once HOME . 'api/model/MonthlyIncome.php';
require_once HOME . 'api/dto/MonthlyIncomeResponseDTO.php';
require_once HOME . 'api/dto/MonthlyIncomeRequestDTO.php';
require_once HOME . 'api/http/Route.php';
require_once HOME . 'api/helper/Helpers.php';

class MonthlyIncomeController
{
    public static function getRoutes()
    {
        $path = self::class;

        Route::post("/api/monthly-incomes/create", "$path::createMonthlyIncome");
        Route::get("/api/monthly-incomes", "$path::getAllMonthlyIncomes");
        Route::get("/api/monthly-incomes/{id}", "$path::getMonthlyIncomeById");
    }

    private RepositoryInterface $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getMonthlyIncomeById(Request $request, $monthly_income_id) {}

    public function getAllMonthlyIncomes() {}

    public function createMonthlyIncome(Request $request)
    {
        try {
            $dto  = $request::body(MonthlyIncomeRequestDTO::class);
            $monthly_income = $dto->transformToObject();

            $monthly_income->validateData();

            if (!$this->repository->save($monthly_income)) {
                throw new \Exception('Erro ao criar rendimento mensal.', 7402);
            }

            return [
                'success' => true,
                'message' => 'Rendimento mensal criado com sucesso.',
            ];
        } catch (\Throwable $th) {
            Functions::isCustomThrow($th);
            throw new \Exception('Erro ao criar rendimento mensal.', 7402, $th);
        }
    }
}
