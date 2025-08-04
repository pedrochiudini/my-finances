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

    public function __construct(ExpenseRepository $expense_repository, MonthlyIncomeRepository $monthly_income_repository)
    {
        $this->expense_repository = $expense_repository;
        $this->monthly_income_repository = $monthly_income_repository;
    }

    public function dashboard()
    {
        try {
            $current_month = (new DateTime())->format('Y-m');

            $expenses        = $this->expense_repository->getAllFromCurrentMonth($current_month);
            $monthly_incomes = $this->monthly_income_repository->getAllFromCurrent($current_month);

            if (empty($expenses) && empty($monthly_incomes)) {
                throw new \Exception('Nenhum dado encontrado para o dashboard.', 7401);
            }

            $total_amount_expenses = getAmountInInteger(array_sum(array_column($expenses, 'amount')));
            $total_amount_incomes  = getAmountInInteger(array_sum(array_column($monthly_incomes, 'amount')));

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
                $category = $expense['category'];
                if (!isset($carry[$category])) {
                    $carry[$category] = 0;
                }

                $amount = getAmountInInteger($expense['amount']);

                if (!is_numeric($amount)) {
                    throw new \Exception("Valor inválido para a despesa.", 7402);
                }

                $carry[$category] += $amount;
                return $carry;
            }, []);

            $ideal_expenses       = array_map('getAmountInFloat', $ideal_expenses);
            $expenses_by_category = array_map('getAmountInFloat', $expenses_by_category);

            $expenses        = (int) array_sum(array_keys($expenses));
            $monthly_incomes = (int) array_sum(array_keys($monthly_incomes));

            return [
                'total_amount_expenses' => getAmountInFloat($total_amount_expenses),
                'total_amount_incomes'  => getAmountInFloat($total_amount_incomes),
                'balance'               => getAmountInFloat($balance),
                'expenses'              => $expenses,
                'monthly_incomes'       => $monthly_incomes,
                'ideal_expenses'        => $ideal_expenses,
                'expenses_by_category'  => $expenses_by_category,
            ];
        } catch (\Throwable $th) {
            Functions::isCustomThrow($th);
            throw new \Exception('Erro ao buscar dados do dashboard.', 7401, $th);
        }
    }
}