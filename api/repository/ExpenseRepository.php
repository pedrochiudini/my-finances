<?php

require_once HOME . 'api/interfaces/RepositoryInterface.php';
require_once HOME . 'api/model/Expense.php';
require_once HOME . 'api/helper/QueryBuilder.php';
require_once HOME . 'api/helper/Functions.php';
require_once HOME . 'api/traits/Common.php';
require_once HOME . 'api/helper/Helpers.php';

class ExpenseRepository implements RepositoryInterface
{
    use Common;

    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function findById(string $expense_id)
    {
        try {
            $qb = new QueryBuilder();

            $qb->select(Expense::$fields_db)
                ->from(Expense::$name_table)
                ->where('expense_id', '=', ':id')
                ->setParameter(':id', $expense_id);

            $query  = $qb->build();
            $params = $query['params'] ?? [];

            $stmt = $this->connection->prepare($query['sql']);
            $stmt->execute($params);

            $data = $stmt->fetch();

            if (!$data) {
                throw new \Exception("Despesa não encontrada.", 7401);
            }

            return new Expense(
                $data['expense_id'],
                $data['user_id'],
                $data['amount'],
                $data['category'],
                $data['reference_month']
            );
        } catch (\Throwable $th) {
            Functions::isCustomThrow($th);
            throw new \Exception("Erro ao buscar despesa por ID.", 7401, $th);
        }
    }

    public function findAll(?string $user_id = null)
    {
        try {
            $qb = new QueryBuilder();

            $qb->select(Expense::$fields_db)
                ->from(Expense::$name_table);

            if ($user_id) {
                $qb->where('user_id', '=', ':user_id')
                    ->setParameter(':user_id', $user_id);
            }

            $query  = $qb->build();
            $params = $query['params'] ?? [];

            $stmt = $this->connection->prepare($query['sql']);
            $stmt->execute($params);

            $data = $stmt->fetchAll();

            if (empty($data)) {
                return [];
            }

            $func_map_expenses = (function ($expense) {
                return new Expense(
                    $expense['expense_id'],
                    $expense['user_id'],
                    $expense['amount'],
                    $expense['category'],
                    $expense['reference_month']
                );
            });

            return array_map($func_map_expenses, $data);
        } catch (\Throwable $th) {
            Functions::isCustomThrow($th);
            throw new \Exception("Erro ao buscar todas as despesas.", 7402, $th);
        }
    }

    public function save(object $expense)
    {
        try {
            $qb = new QueryBuilder();

            $qb->insert(Expense::$name_table)

                ->setValues([
                    'expense_id'      => ':id',
                    'user_id '        => ':user_id',
                    'amount'          => ':amount',
                    'category'        => ':category',
                    'reference_month' => ':reference_month',
                ])

                ->setParameters([
                    ':id'              => $this->uuid(),
                    ':user_id'         => $expense->getUserId(),
                    ':amount'          => $expense->getAmount(),
                    ':category'        => $expense->getCategory(),
                    ':reference_month' => $expense->getReferenceMonth(),
                ]);

            $query  = $qb->build();
            $params = $query['params'] ?? [];

            $stmt = $this->connection->prepare($query['sql']);
            $stmt->execute($params);

            return $stmt->rowCount() > 0;
        } catch (\Throwable $th) {
            Functions::isCustomThrow($th);
            throw new \Exception("Erro ao salvar despesa.", 7403, $th);
        }
    }

    public function update(object $expense) {}

    public function delete(string $expense_id) {}

    public function getAllFromCurrentMonth(string $current_month, string $user_id)
    {
        try {
            $qb = new QueryBuilder();

            $qb->select(Expense::$fields_db)
                ->from(Expense::$name_table)
                ->where('user_id', '=', ':user_id')
                ->where("TO_CHAR(reference_month, 'YYYY-MM')", "=", ":reference_month")
                ->setParameters([
                    ':user_id'         => $user_id,
                    ':reference_month' => $current_month,
                ]);

            $query  = $qb->build();
            $params = $query['params'] ?? [];

            $stmt = $this->connection->prepare($query['sql']);
            $stmt->execute($params);

            $data = $stmt->fetchAll();

            if (empty($data)) {
                return [];
            }

            $func_map_expenses = (function ($expense) {
                return new Expense(
                    $expense['expense_id'],
                    $expense['user_id'],
                    $expense['amount'],
                    $expense['category'],
                    $expense['reference_month']
                );
            });

            return array_map($func_map_expenses, $data);
        } catch (\Throwable $th) {
            Functions::isCustomThrow($th);
            throw new \Exception("Erro ao buscar despesas do mês atual. $th", 7404, $th);
        }
    }
}
