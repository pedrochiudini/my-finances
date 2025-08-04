<?php

require_once HOME . 'api/model/Expense.php';
require_once HOME . 'api/interfaces/IRequestDTO.php';

class ExpenseRequestDTO implements IRequestDTO
{
    public function __construct(
        private string $expense_id,
        private string $user_id,
        private float $amount,
        private string $category,
        private string $reference_month
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            expense_id: $data['expense_id'] ?? '',
            user_id: $data['user_id'] ?? '',
            amount: $data['amount'] ?? 0.0,
            category: $data['category'] ?? '',
            reference_month: $data['reference_month'] ?? ''
        );
    }

    public function transformToObject(): Expense
    {
        return new Expense(
            expense_id: $this->expense_id,
            user_id: $this->user_id,
            amount: $this->amount,
            category: $this->category,
            reference_month: $this->reference_month
        );
    }
}
