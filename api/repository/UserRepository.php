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

            $qb->select(User::$fields_db)
                ->from(User::$name_table)
                ->where('user_id', '=', ':id')
                ->setParameter(':id', $user_id);

            $query  = $qb->build();
            $params = $query['params'] ?? [];

            $stmt = $this->connection->prepare($query['sql']);
            $stmt->execute($params);

            $data = $stmt->fetch();

            if (!$data) {
                throw new \Exception("Usuário não encontrado.", 7402);
            }

            return new User(
                $data['user_id'],
                $data['name'],
                $data['email'],
                $data['password']
            );
        } catch (\Throwable $th) {
            Functions::isCustomThrow($th);
            throw new \Exception("Erro ao buscar usuário por ID. $th", 7402, $th);
        }
    }

    public function findAll()
    {
        try {
            $qb = new QueryBuilder();

            $qb->select(User::$fields_db)
                ->from(User::$name_table);

            $query  = $qb->build();
            $params = $query['params'] ?? [];

            $stmt = $this->connection->prepare($query['sql']);
            $stmt->execute($params);

            $data = $stmt->fetchAll();

            if (empty($data)) {
                return [];
            }

            $func_map_users = (function ($user) {
                return new User(
                    $user['user_id'],
                    $user['name'],
                    $user['email'],
                    $user['password']
                );
            });

            return array_map($func_map_users, $data);
        } catch (\Throwable $th) {
            Functions::isCustomThrow($th);
            throw new \Exception("Erro ao buscar todos os usuários.", 7402, $th);
        }
    }

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
            throw new \Exception("Erro ao salvar usuário.", 7403, $th);
        }
    }

    public function update(object $user) {}

    public function delete(string $user_id) {}
}
