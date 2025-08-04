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
        Route::get("/api/monthly-incomes/all/{id}", "$path::getAllMonthlyIncomes");
        Route::get("/api/monthly-incomes/{id}", "$path::getMonthlyIncomeById");
    }

    private RepositoryInterface $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getMonthlyIncomeById(Request $request, $monthly_income_id)
    {
        try {
            $dto            = MonthlyIncomeRequestDTO::fromArray(["monthly_income_id" => $monthly_income_id]);
            $monthly_income = $dto->transformToObject();

            $monthly_income->validateMonthlyIncomeId();

            $monthly_income = $this->repository->findById($monthly_income->getId());

            if (!$monthly_income) {
                throw new \Exception('Erro ao buscar rendimento mensal.', 7400);
            }

            return MonthlyIncomeResponseDTO::transformToDTO($monthly_income);
        } catch (\Throwable $th) {
            Functions::isCustomThrow($th);
            throw new \Exception('Erro ao buscar rendimento mensal.', 7400, $th);
        }
    }

    public function getAllMonthlyIncomes(Request $request, $user_id)
    {
        try {
            $dto            = MonthlyIncomeRequestDTO::fromArray(["user_id" => $user_id]);
            $monthly_income = $dto->transformToObject();

            $monthly_income->validateData();

            MonthlyIncomeRepository::setUserId($monthly_income->getUserId());

            $monthly_incomes = $this->repository->findAll();

            if (empty($monthly_incomes)) {
                throw new \Exception('Nenhum rendimento mensal encontrado.', 7401);
            }

            $func_map_monthly_incomes = (function ($monthly_income) {
                return MonthlyIncomeResponseDTO::transformToDTO($monthly_income);
            });

            return array_map($func_map_monthly_incomes, $monthly_incomes);
        } catch (\Throwable $th) {
            Functions::isCustomThrow($th);
            throw new \Exception('Erro ao buscar rendimentos mensais.', 7401, $th);
        }
    }

    public function createMonthlyIncome(Request $request)
    {
        try {
            $dto            = $request::body(MonthlyIncomeRequestDTO::class);
            $monthly_income = $dto->transformToObject();

            $monthly_income->validateData();

            if (!$this->repository->save($monthly_income)) {
                throw new \Exception('Erro ao criar rendimento mensal.', 7402);
            }

            return [
                'success' => true,
                'message' => 'Entrada registrada com sucesso.',
            ];
        } catch (\Throwable $th) {
            Functions::isCustomThrow($th);
            throw new \Exception('Erro ao criar rendimento mensal.', 7402, $th);
        }
    }
}
