<?php

require_once HOME . 'api/http/Route.php';
require_once HOME . 'api/interfaces/IRequestDTO.php';

class Request
{
    public static function getMethod(): string
    {
        return $_SERVER["REQUEST_METHOD"];
    }

    /**
     * Lê o corpo da requisição e instancia o DTO informado
     *
     * @param class-string<IRequestDTO> $dtoClass
     * @return IRequestDTO
     */
    public static function body(string $dtoClass): IRequestDTO
    {
        if (!in_array(self::getMethod(), [
            Route::METHOD_POST,
            Route::METHOD_PUT,
            Route::METHOD_DELETE,
        ])) {
            throw new \Exception("Método inválido para corpo de requisição", 400);
        }

        $json = file_get_contents('php://input');

        if (!$json) throw new \Exception("Corpo da requisição vazio", 400);

        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) throw new \Exception("JSON inválido: " . json_last_error_msg(), 400);

        if (!class_exists($dtoClass)) throw new \Exception("Classe DTO {$dtoClass} não existe", 500);

        return $dtoClass::fromArray($data);
    }

    public static function authorization(): array
    {
        $authorization = getallheaders();

        if (!isset($authorization['Authorization'])) throw new \Exception("Autorização não encontrada.", 401);

        $authorization_partials = explode(' ', $authorization['Authorization']);

        if (count($authorization_partials) != 2) throw new \Exception("Autorização inválida.", 401);

        return ['authorization' => $authorization_partials[1] ?? ''];
    }
}
