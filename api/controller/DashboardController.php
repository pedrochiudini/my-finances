<?php

require_once HOME . 'api/http/Route.php';
require_once HOME . 'api/helper/Helpers.php';

class DashboardController
{
    public static function getRoutes()
    {
        $path = self::class;

        Route::get("/api/dashboard", "$path::dashboard");
    }

    private ExpenseRepository $expense_repository;
    private MonthlyIncomeRepository $monthly_income_repository;
    private UserRepository $user_repository;

    public function __construct(ExpenseRepository $expense_repository, MonthlyIncomeRepository $monthly_income_repository, UserRepository $user_repository)
    {
        $this->expense_repository = $expense_repository;
        $this->monthly_income_repository = $monthly_income_repository;
        $this->user_repository = $user_repository;
    }

    public function dashboard(Request $request)
    {
        $result = null;
        protectedRoute(function ($payload) use (&$result) {
            $user_id = $payload['user_id'] ?? null;

            if (!$user_id) {
                throw new \Exception('Usuário não autenticado.', 7401);
            }

            try {
                $current_month = (new DateTime())->format('Y-m');

                if (!$this->user_repository->findById($user_id)) {
                    throw new \Exception('Usuário não encontrado.', 7401);
                }

                $expenses        = $this->expense_repository->getAllFromCurrentMonth($current_month, $user_id);
                $monthly_incomes = $this->monthly_income_repository->getAllFromCurrentMonth($current_month, $user_id);

                if (empty($expenses) && empty($monthly_incomes)) {
                    throw new \Exception('Nenhum dado encontrado para o dashboard.', 7401);
                }

                $func_amount_expenses = (function ($expense) {
                    return getAmountInInteger($expense->getAmount());
                });

                $func_amount_monthly_incomes = (function ($monthly_income) {
                    return getAmountInInteger($monthly_income->getAmount());
                });

                $total_amount_expenses = array_sum(array_map($func_amount_expenses, $expenses));
                $total_amount_incomes  = array_sum(array_map($func_amount_monthly_incomes, $monthly_incomes));

                $balance = $total_amount_incomes - $total_amount_expenses;

                if ($balance < 0) {
                    $balance = 0; // Ensure balance is not negative
                }

                $ideal_expenses = [
                    'FI' => 0.5 * $total_amount_incomes,
                    'DE' => 0.3 * $total_amount_incomes,
                    'PO' => 0.2 * $total_amount_incomes,
                ];

                $expenses_by_category = array_reduce($expenses, function ($carry, $expense) {
                    $category = $expense->getCategory();

                    if (!isset($carry[$category])) {
                        $carry[$category] = 0;
                    }

                    $amount = getAmountInInteger($expense->getAmount());

                    if (!is_numeric($amount)) {
                        throw new \Exception("Valor inválido para a despesa.", 7402);
                    }

                    $carry[$category] += $amount;
                    return $carry;
                }, []);

                $ideal_expenses       = array_map('getAmountInFloat', $ideal_expenses);
                $expenses_by_category = array_map('getAmountInFloat', $expenses_by_category);

                $expenses        = count($expenses);
                $monthly_incomes = count($monthly_incomes);

                $result = [
                    'total_amount_expenses' => getAmountInFloat($total_amount_expenses),
                    'total_amount_incomes'  => getAmountInFloat($total_amount_incomes),
                    'balance'               => getAmountInFloat($balance),
                    'monthly_expenses'      => $expenses,
                    'monthly_incomes'       => $monthly_incomes,
                    'ideal_expenses'        => $ideal_expenses,
                    'expenses_by_category'  => $expenses_by_category,
                ];
            } catch (\Throwable $th) {
                Functions::isCustomThrow($th);
                throw new \Exception('Erro ao buscar dados do dashboard.' . $th, 7401, $th);
            }
        });

        return $result;
    }
}