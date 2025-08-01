<?php

require_once HOME . 'api/model/MonthlyIncome.php';

class MonthlyIncomeResponseDTO implements JsonSerializable
{
    public function __construct(
        private string $monthly_income_id,
        private string $user_id,
        private float $amount,
        private string $reference_month
    ) {}

    public static function transformToDTO(MonthlyIncome $monthly_income): self
    {
        return new self(
            monthly_income_id: $monthly_income->getId(),
            user_id: $monthly_income->getUserId(),
            amount: $monthly_income->getAmount(),
            reference_month: $monthly_income->getReferenceMonth()
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'monthly_income_id' => $this->monthly_income_id,
            'user_id'           => $this->user_id,
            'amount'            => $this->amount,
            'reference_month'   => $this->reference_month,
        ];
    }
}
