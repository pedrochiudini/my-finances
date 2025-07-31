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

    public function findById(int $id) {}

    public function findAll() {}

    public function save(object $user)
    {
        try {
            $qb = new QueryBuilder();

            $qb->insert(User::$name_table)

                ->setValues([
                    'id'       => ':id',
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

            dd($query);

            $stmt = $this->connection->prepare($query['sql']);
            $stmt->bindValue(':id', $params[':id']);
            $stmt->bindValue(':name', $params[':name']);
            $stmt->bindValue(':email', $params[':email']);
            $stmt->bindValue(':password', $params[':password']);

            return $stmt->execute();;
        } catch (\Throwable $th) {
            Functions::isCustomThrow($th);
            throw new \Exception('Erro ao salvar usuário', 7400, $th);
        }
    }

    public function update(object $user) {}

    public function delete(int $id) {}
}
