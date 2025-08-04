<?php

require_once HOME . 'api/interfaces/RepositoryInterface.php';
require_once HOME . 'api/model/MonthlyIncome.php';
require_once HOME . 'api/helper/QueryBuilder.php';
require_once HOME . 'api/helper/Functions.php';
require_once HOME . 'api/traits/Common.php';
require_once HOME . 'api/helper/Helpers.php';

class MonthlyIncomeRepository implements RepositoryInterface
{
    use Common;

    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function findById(string $monthly_income_id)
    {
        try {
            $qb = new QueryBuilder();

            $qb->select(MonthlyIncome::$fields_db)
                ->from(MonthlyIncome::$name_table)
                ->where('monthly_income_id', '=', ':id')
                ->setParameter(':id', $monthly_income_id);

            $query  = $qb->build();
            $params = $query['params'] ?? [];

            $stmt = $this->connection->prepare($query['sql']);
            $stmt->execute($params);

            $data = $stmt->fetch();

            if (!$data) {
                throw new \Exception("Rendimento mensal não encontrado.", 7401);
            }

            return new MonthlyIncome(
                $data['monthly_income_id'],
                $data['user_id'],
                $data['amount'],
                $data['reference_month']
            );
        } catch (\Throwable $th) {
            Functions::isCustomThrow($th);
            throw new \Exception("Erro ao buscar rendimento mensal por ID.", 7401, $th);
        }
    }

    public function findAll(?string $user_id = null)
    {
        try {
            $qb = new QueryBuilder();

            $qb->select(MonthlyIncome::$fields_db)
                ->from(MonthlyIncome::$name_table);

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

            $func_map_monthly_incomes = (function ($monthly_income) {
                return new MonthlyIncome(
                    $monthly_income['monthly_income_id'],
                    $monthly_income['user_id'],
                    $monthly_income['amount'],
                    $monthly_income['reference_month']
                );
            });

            return array_map($func_map_monthly_incomes, $data);
        } catch (\Throwable $th) {
            Functions::isCustomThrow($th);
            throw new \Exception("Erro ao buscar todos os rendimentos mensais.", 7402, $th);
        }
    }

    public function save(object $monthly_income)
    {
        try {
            $qb = new QueryBuilder();

            $qb->insert(MonthlyIncome::$name_table)

                ->setValues([
                    'monthly_income_id' => ':id',
                    'user_id'           => ':user_id',
                    'amount'            => ':amount',
                    'reference_month'   => ':reference_month',
                ])

                ->setParameters([
                    ':id'              => $this->uuid(),
                    ':user_id'         => $monthly_income->getUserId(),
                    ':amount'          => $monthly_income->getAmount(),
                    ':reference_month' => $monthly_income->getReferenceMonth(),
                ]);

            $query  = $qb->build();
            $params = $query['params'] ?? [];

            $stmt = $this->connection->prepare($query['sql']);
            $stmt->execute($params);

            return $stmt->rowCount() > 0;
        } catch (\Throwable $th) {
            Functions::isCustomThrow($th);
            throw new \Exception("Erro ao salvar rendimento mensal.", 7403, $th);
        }
    }

    public function update(object $monthly_income) {}

    public function delete(string $monthly_income_id) {}

    public function getAllFromCurrentMonth(string $current_month, string $user_id)
    {
        try {
            $qb = new QueryBuilder();

            $qb->select(MonthlyIncome::$fields_db)
                ->from(MonthlyIncome::$name_table)
                ->where('user_id', '=', ':user_id')
                ->where("TO_CHAR(reference_month, 'YYYY-MM')", "=", ":reference_month")
                ->setParameters([
                    ':user_id'         => $user_id,
                    ':reference_month' => $current_month
                ]);

            $query  = $qb->build();
            $params = $query['params'] ?? [];

            $stmt = $this->connection->prepare($query['sql']);
            $stmt->execute($params);

            $data = $stmt->fetchAll();

            if (empty($data)) {
                return [];
            }

            $func_map_monthly_incomes = (function ($monthly_income) {
                return new MonthlyIncome(
                    $monthly_income['monthly_income_id'],
                    $monthly_income['user_id'],
                    $monthly_income['amount'],
                    $monthly_income['reference_month']
                );
            });

            return array_map($func_map_monthly_incomes, $data);
        } catch (\Throwable $th) {
            Functions::isCustomThrow($th);
            throw new \Exception("Erro ao buscar rendimentos mensais do mês atual.", 7404, $th);
        }
    }
}
