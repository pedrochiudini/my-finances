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

        Route::get("/api/users", "$path::getUsers");
        Route::post("/api/users/create", "$path::create");
    }

    private RepositoryInterface $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getUserById(Request $request)
    {
        try {
            $dto  = $request::body(UserRequestDTO::class);
            $user = $dto->transformToObject();

            $user->validateUserId();

            $user = $this->repository->findById($user->getId());

            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'Usuário não encontrado.'
                ];
            }

            return UserResponseDTO::transformTo DTO($user);
        } catch (\Throwable $th) {
            Functions::isCustomThrow($th);
            throw new \Exception('Erro ao buscar usuário', 7401, $th);
        }
    }

    public function create(Request $request)
    {
        try {
            $dto  = $request::body(UserRequestDTO::class);
            $user = $dto->transformToObject();

            $user->validateData();

            if ($this->repository->save($user)) {
                return [
                    'success' => true,
                    'message' => 'Usuário criado com sucesso.',
                ];
            }
        } catch (\Throwable $th) {
            Functions::isCustomThrow($th);
            throw new \Exception('Erro ao criar usuário', 7400, $th);
        }
    }

    public function getUsers()
    {
        return [
            UserResponseDTO::transformToDTO(new User(1, 'John Doe', 'john@example.com', 'password123')),
            UserResponseDTO::transformToDTO(new User(2, 'Jane Smith', 'jane@example.com', 'password456'))
        ];
    }
}
