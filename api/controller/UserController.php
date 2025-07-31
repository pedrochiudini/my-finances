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
        Route::post("/api/users/create", "$path::createUser");
    }

    public function createUser(Request $request, Response $response)
    {
        try {
            $dto = $request::body(UserRequestDTO::class);

            $user = $dto->transformToObject();

            $user->validateData();

            return UserResponseDTO::transformToDTO($user);
        } catch (\Throwable $th) {
            Functions::isCustomThrow($th);
            throw new \Exception('Erro ao criar usuário', 7400, $th);
        }
    }

    public function getUsers(Request $request, Response $response)
    {
        return [
            UserResponseDTO::transformToDTO(new User(1, 'John Doe', 'john@example.com', 'password123')),
            UserResponseDTO::transformToDTO(new User(2, 'Jane Smith', 'jane@example.com', 'password456'))
        ];
    }
}
