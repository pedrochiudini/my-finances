<?php

require_once HOME . 'api/interfaces/RepositoryInterface.php';
require_once HOME . 'api/model/User.php';
require_once HOME . 'api/helper/QueryBuilder.php';
require_once HOME . 'api/helper/Functions.php';
require_once HOME . 'api/traits/Common.php';
require_once HOME . 'api/helper/Helpers.php';

class UserRepository implements RepositoryInterface
{
    use Common;

    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function findById(string $user_id)
    {
        try {
            $qb = new QueryBuilder();

            $qb->select(User::$name_table)
                ->where('user_id', '=', ':id')
                ->setParameters([':id' => $user_id]);

            $query  = $qb->build();
            $params = $query['params'] ?? [];

            $stmt = $this->connection->prepare($query['sql']);
            $stmt->bindValue(':id', $params[':id']);
            $stmt->execute();

            if ($stmt->rowCount() <= 0) {
                throw new \Exception("Usuário não encontrado.", 7403);
            }

            return $stmt->fetchObject(User::class);
        } catch (\Throwable $th) {
            Functions::isCustomThrow($th);
            throw new \Exception("Erro ao buscar usuário por ID.", 7402, $th);
        }
    }

    public function findAll() {}

    public function save(object $user)
    {
        try {
            $qb = new QueryBuilder();

            $qb->insert(User::$name_table)

                ->setValues([
                    'user_id'  => ':id',
                    'name'     => ':name',
                    'email'    => ':email',
                    'password' => ':password',
                ])

                ->setParameters([
                    ':id'       => $this->uuid(),
                    ':name'     => $user->getName(),
                    ':email'    => $user->getEmail(),
                    ':password' => $this->hash($user->getPassword()),
                ]);

            $query  = $qb->build();
            $params = $query['params'] ?? [];

            $stmt = $this->connection->prepare($query['sql']);
            $stmt->execute($params);

            return $stmt->rowCount() > 0;
        } catch (\Throwable $th) {
            Functions::isCustomThrow($th);
            throw new \Exception("Erro ao salvar usuário.", 7404, $th);
        }
    }

    public function update(object $user) {}

    public function delete(int $id) {}
}
