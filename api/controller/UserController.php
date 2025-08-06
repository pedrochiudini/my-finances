<?php

require_once HOME . 'api/model/User.php';
require_once HOME . 'api/dto/UserResponseDTO.php';
require_once HOME . 'api/dto/UserRequestDTO.php';
require_once HOME . 'api/http/Route.php';
require_once HOME . 'api/helper/Helpers.php';
require_once HOME . 'api/interfaces/UserRepositoryInterface.php';
require_once HOME . 'api/traits/Common.php';
require_once HOME . 'api/auth/JWT.php';
require_once HOME . 'api/http/Cors.php';

class UserController
{
    use Common;

    public static function getRoutes()
    {
        $path = self::class;

        Route::post("/api/users/create", "$path::createUser");
        Route::post("/api/users/login", "$path::login");
        Route::get("/api/users/all", "$path::getAllUsers");
        Route::get("/api/users/{id}", "$path::getUserById");
    }

    private UserRepositoryInterface $repository;

    public function __construct(UserRepositoryInterface $repository)
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
        protectedRoute(function ($payload) {
            if (empty($payload['user_id'])) {
                throw new \Exception('Usuário não autenticado.', 7401);
            }
        });

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

    public function login(Request $request)
    {
        try {
            $dto  = $request::body(UserRequestDTO::class);
            $user = $dto->transformToObject();

            $user->validateLoginData();

            $user_db = $this->repository->findByEmail($user->getEmail());

            if (!$user_db || !$this->checkHash($user->getPassword(), $user_db->getPassword())) {
                throw new \Exception('Credenciais inválidas.', 7403);
            }

            $user_id = $user_db->getId();

            return [
                'success' => true,
                'token'   => JWT::encode(['user_id' => $user_id]),
                'user'    => UserResponseDTO::transformToDTO($user_db),
            ];
        } catch (\Throwable $th) {
            Functions::isCustomThrow($th);
            throw new \Exception('Erro ao realizar login.', 7403, $th);
        }
    }
}
