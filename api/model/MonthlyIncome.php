<?php

require_once HOME . 'api/model/Model.php';
require_once HOME . 'api/interfaces/ModelInterface.php';

class MonthlyIncome extends Model implements ModelInterface
{
    public static string $name_table = 'monthly_incomes';
    public static array $fields_db   = [
        'monthly_income_id',
        'user_id',
        'amount', // no banco é salvo como decimal (float)
        'reference_month'
    ];

    protected string $monthly_income_id;
    protected string $user_id;
    protected int $amount;
    protected string $reference_month;

    public function __construct(string $monthly_income_id, string $user_id, int $amount, string $reference_month)
    {
        $this->monthly_income_id = $monthly_income_id;
        $this->user_id = $user_id;
        $this->amount = $amount;
        $this->reference_month = $reference_month;
    }

    public function validateData()
    {
        try {
            $data = $this->getData();

            $this->amount          = (ffilter($data, 'amount'))->required()->int();
            $this->reference_month = (ffilter($data, 'reference_month'))->required()->date();
        } catch (\Throwable $th) {
            Functions::isCustomThrow($th);
            throw new \Exception('Erro ao validar dados do usuário', 7400, $th);
        }
    }

    public function validateMonthlyIncomeId()
    {
        try {
            $data = $this->getData();

            $this->monthly_income_id = (ffilter($data, 'monthly_income_id'))->required()->string();
        } catch (\Throwable $th) {
            Functions::isCustomThrow($th);
            throw new \Exception('Erro ao validar ID do usuário', 7401, $th);
        }
    }
}
