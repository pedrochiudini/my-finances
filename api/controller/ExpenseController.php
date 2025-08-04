<?php

require_once HOME . 'api/model/Expense.php';
require_once HOME . 'api/dto/ExpenseResponseDTO.php';
require_once HOME . 'api/dto/ExpenseRequestDTO.php';
require_once HOME . 'api/http/Route.php';
require_once HOME . 'api/helper/Helpers.php';

class ExpenseController
{
    public static function getRoutes()
    {
        $path = self::class;

        Route::post("/api/expenses/create", "$path::createExpense");
        Route::get("/api/expenses/all/{id}", "$path::getAllExpenses");
        Route::get("/api/expenses/{id}", "$path::getExpenseById");
    }

    private RepositoryInterface $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getExpenseById(Request $request, $expense_id)
    {
        try {
            $dto     = ExpenseRequestDTO::fromArray(["expense_id" => $expense_id]);
            $expense = $dto->transformToObject();

            $expense->validateExpenseId();

            $expense = $this->repository->findById($expense->getId());

            if (!$expense) {
                throw new \Exception('Erro ao buscar despesa.', 7400);
            }

            return ExpenseResponseDTO::transformToDTO($expense);
        } catch (\Throwable $th) {
            Functions::isCustomThrow($th);
            throw new \Exception('Erro ao buscar despesa.', 7400, $th);
        }
    }

    public function getAllExpenses(Request $request, $user_id)
    {
        try {
            $dto     = ExpenseRequestDTO::fromArray(["user_id" => $user_id]);
            $expense = $dto->transformToObject();

            $expenses = $this->repository->findAll($expense->getUserId());

            if (empty($expenses)) {
                throw new \Exception('Nenhuma despesa encontrada.', 7401);
            }

            $func_map_expenses = (function ($expense) {
                return ExpenseResponseDTO::transformToDTO($expense);
            });

            return array_map($func_map_expenses, $expenses);
        } catch (\Throwable $th) {
            Functions::isCustomThrow($th);
            throw new \Exception('Erro ao buscar despesas.' . $th, 7401, $th);
        }
    }

    public function createExpense(Request $request)
    {
        try {
            $dto     = $request::body(ExpenseRequestDTO::class);
            $expense = $dto->transformToObject();

            $expense->validateData();

            if (!$this->repository->save($expense)) {
                throw new \Exception('Erro ao criar despesa.', 7402);
            }

            return [
                'success' => true,
                'message' => 'Saída registrada com sucesso.',
            ];
        } catch (\Throwable $th) {
            Functions::isCustomThrow($th);
            throw new \Exception('Erro ao criar despesa.', 7402, $th);
        }
    }
}
