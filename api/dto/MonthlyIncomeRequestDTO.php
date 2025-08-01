<?php

require_once HOME . 'api/model/MonthlyIncome.php';
require_once HOME . 'api/interfaces/IRequestDTO.php';

class MonthlyIncomeRequestDTO implements IRequestDTO
{
    public function __construct(
        private string $monthly_income_id,
        private string $user_id,
        private float $amount,
        private string $reference_month
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            monthly_income_id: $data['monthly_income_id'] ?? '',
            user_id: $data['user_id'] ?? '',
            amount: $data['amount'] ?? 0.0,
            reference_month: $data['reference_month'] ?? ''
        );
    }

    public function transformToObject(): MonthlyIncome
    {
        return new MonthlyIncome(
            monthly_income_id: $this->monthly_income_id,
            user_id: $this->user_id,
            amount: $this->amount,
            reference_month: $this->reference_month
        );
    }
}
