<?php

require_once HOME . 'api/model/Expense.php';

class ExpenseResponseDTO implements JsonSerializable
{
    public function __construct(
        private string $expense_id,
        private string $user_id,
        private float $amount,
        private string $category,
        private string $reference_month
    ) {}

    public static function transformToDTO(Expense $expense): self
    {
        return new self(
            expense_id: $expense->getId(),
            user_id: $expense->getUserId(),
            amount: $expense->getAmount(),
            category: $expense->getCategory(),
            reference_month: $expense->getReferenceMonth()
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'expense_id'      => $this->expense_id,
            'user_id'         => $this->user_id,
            'amount'          => $this->amount,
            'category'        => $this->category,
            'reference_month' => $this->reference_month,
        ];
    }
}
