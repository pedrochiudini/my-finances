<?php

require_once HOME . 'api/model/User.php';
require_once HOME . 'api/dto/UserResponseDTO.php';
require_once HOME . 'api/dto/UserRequestDTO.php';
require_once HOME . 'api/http/Route.php';
require_once HOME . 'api/helper/Helpers.php';

class UserController
{
    public static function getRoutes()
    {
        $path = self::class;

        Route::post("/api/users/create", "$path::createUser");
        Route::get("/api/users/all", "$path::getAllUsers");
        Route::get("/api/users/{id}", "$path::getUserById");
    }

    private RepositoryInterface $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getUserById(Request $request, $user_id)
    {
        try {
            $dto  = UserRequestDTO::fromArray(["user_id" => $user_id]);
            $user = $dto->transformToObject();

            $user->validateUserId();

            $user = $this->repository->findById($user->getId());

            if (!$user) {
                throw new \Exception('Erro ao buscar usuário.', 7401);
            }

            return UserResponseDTO::transformToDTO($user);
        } catch (\Throwable $th) {
            Functions::isCustomThrow($th);
            throw new \Exception('Erro ao buscar usuário.', 7401, $th);
        }
    }

    public function getAllUsers()
    {
        try {
            $users = $this->repository->findAll();

            if (empty($users)) {
                throw new \Exception('Nenhum usuário encontrado.', 7401);
            }

            $func_map_users = (function ($user) {
                return UserResponseDTO::transformToDTO($user);
            });

            return array_map($func_map_users, $users);
        } catch (\Throwable $th) {
            Functions::isCustomThrow($th);
            throw new \Exception('Erro ao buscar usuários.', 7401, $th);
        }
    }

    public function createUser(Request $request)
    {
        try {
            $dto  = $request::body(UserRequestDTO::class);
            $user = $dto->transformToObject();

            $user->validateData();

            if (!$this->repository->save($user)) {
                throw new \Exception('Erro ao criar usuário.', 7402);
            }

            return [
                'success' => true,
                'message' => 'Usuário criado com sucesso.',
            ];
        } catch (\Throwable $th) {
            Functions::isCustomThrow($th);
            throw new \Exception('Erro ao criar usuário.', 7402, $th);
        }
    }
}
