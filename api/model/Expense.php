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
        'user_id', // foreign key
        'amount', // no banco é salvo como decimal (float)
        'category', // no banco é salvo como enum (['fixo', 'desejo', 'poupanca'])
        'reference_month', // no banco é salvo como date ('Y-m-d')
    ];

    protected string $expense_id;
    protected string $user_id;
    protected float $amount;
    protected string $category;
    protected string $reference_month;

    public function __construct(string $expense_id, string $user_id, float $amount, string $category, string $reference_month)
    {
        $this->expense_id = $expense_id;
        $this->user_id = $user_id;
        $this->amount = $amount;
        $this->category = $category;
        $this->reference_month = $reference_month;
    }

    public function validateData()
    {
        try {
            $data = $this->getData();

            $this->user_id         = (ffilter($data, 'user_id'))->required()->string();
            $this->amount          = (ffilter($data, 'amount'))->required()->float();
            $this->category        = (ffilter($data, 'category'))->required()->string();
            $this->reference_month = (ffilter($data, 'reference_month'))->required()->date();

            if ($this->amount <= 0) {
                throw new \Exception('O valor da despesa deve ser maior que zero.', 7400);
            }

            if (!in_array($this->category, self::$categories)) {
                throw new \Exception('Categoria inválida. Deve ser "fixo", "desejo" ou "poupanca".', 7400);
            }

            if (!$this->validateMonth()) {
                throw new \Exception('O mês de referência deve ser o mês atual.', 7400);
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

    public function getUserId(): string
    {
        return $this->user_id;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCategory(): string
    {
        return $this->category;
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
