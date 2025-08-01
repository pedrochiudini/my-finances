<?php

require_once HOME . 'api/model/Model.php';
require_once HOME . 'api/interfaces/ModelInterface.php';

class Expense extends Model implements ModelInterface
{
    public const FIXO     = 'FI'; // Despesas fixas, como aluguel, contas de serviços públicos, etc.
    public const DESEJO   = 'DE'; // Despesas de desejo, como entretenimento, viagens, etc.
    public const POUPANCA = 'PO'; // Despesas de poupança, como investimentos, economias, saúde, etc.

    public static array $categories = [
        self::FIXO,
        self::DESEJO,
        self::POUPANCA
    ];

    public static string $name_table = 'expenses';
    public static array $fields_db   = [
        'expense_id',
        'monthly_income_id', // foreign key
        'amount', // no banco é salvo como decimal (float)
        'category', // no banco é salvo como enum (['fixo', 'desejo', 'poupanca'])
        'date', // no banco é salvo como date ('Y-m-d')
    ];

    protected string $expense_id;
    protected string $monthly_income_id;
    protected float $amount;
    protected string $category;
    protected string $date;

    public function __construct(string $expense_id, string $monthly_income_id, float $amount, string $category, string $date)
    {
        $this->expense_id = $expense_id;
        $this->monthly_income_id = $monthly_income_id;
        $this->amount = $amount;
        $this->category = $category;
        $this->date = $date;
    }

    public function validateData()
    {
        try {
            $data = $this->getData();

            $this->monthly_income_id = (ffilter($data, 'monthly_income_id'))->required()->string();
            $this->amount            = (ffilter($data, 'amount'))->required()->float();
            $this->category          = (ffilter($data, 'category'))->required()->string();
            $this->date              = (ffilter($data, 'date'))->required()->date();

            if ($this->amount <= 0) {
                throw new \Exception('O valor da despesa deve ser maior que zero.', 7400);
            }

            if (!in_array($this->category, self::$categories)) {
                throw new \Exception('Categoria inválida. Deve ser "fixo", "desejo" ou "poupanca".', 7400);
            }
        } catch (\Throwable $th) {
            Functions::isCustomThrow($th);
            throw new \Exception('Erro ao validar dados da despesa.', 7400, $th);
        }
    }

    public function validateExpenseId()
    {
        try {
            $data = $this->getData();

            $this->expense_id = (ffilter($data, 'expense_id'))->required()->string();
        } catch (\Throwable $th) {
            Functions::isCustomThrow($th);
            throw new \Exception('Erro ao validar ID da despesa.', 7401, $th);
        }
    }

    public function getId(): string
    {
        return $this->expense_id;
    }

    public function getMonthlyIncomeId(): string
    {
        return $this->monthly_income_id;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getDate(): string
    {
        return $this->date;
    }
}