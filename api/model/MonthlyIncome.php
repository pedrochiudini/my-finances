<?php

require_once HOME . 'api/model/Model.php';
require_once HOME . 'api/interfaces/ModelInterface.php';

class MonthlyIncome extends Model implements ModelInterface
{
    public static string $name_table = 'monthly_incomes';
    public static array $fields_db   = [
        'monthly_income_id',
        'user_id', // foreign key
        'amount', // no banco é salvo como decimal (float)
        'reference_month' // no banco é salvo como date ('Y-m-d')
    ];

    protected string $monthly_income_id;
    protected string $user_id;
    protected float $amount;
    protected string $reference_month;

    public function __construct(string $monthly_income_id, string $user_id, float $amount, string $reference_month)
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

            $this->user_id         = (ffilter($data, 'user_id'))->required()->string();
            $this->amount          = (ffilter($data, 'amount'))->required()->float();
            $this->reference_month = (ffilter($data, 'reference_month'))->required()->date();

            if ($this->amount <= 0) {
                throw new \Exception('O valor do rendimento mensal deve ser maior que zero.', 7400);
            }

            if (!$this->validateMonth()) {
                throw new \Exception('O mês de referência deve ser o mês atual.', 7400);
            }
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

    public function getId(): string
    {
        return $this->monthly_income_id;
    }

    public function getUserId(): string
    {
        return $this->user_id;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getReferenceMonth(): string
    {
        return $this->reference_month;
    }

    private function validateMonth(): bool
    {
        $date = new DateTime($this->reference_month);
        $now  = new DateTime();

        $year_month_date = $date->format('Y-m');
        $year_month_now  = $now->format('Y-m');

        return $year_month_date === $year_month_now;
    }
}
