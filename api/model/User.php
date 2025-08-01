<?php

require_once HOME . 'api/model/Model.php';
require_once HOME . 'api/interfaces/ModelInterface.php';

class User extends Model implements ModelInterface
{
    public static string $name_table = 'users';
    public static array $fields_db = [
        'user_id',
        'name',
        'email',
        'password'
    ];

    protected string $user_id;
    protected string $name;
    protected string $email;
    protected string $password;

    public function __construct(string $user_id, string $name, string $email, string $password)
    {
        $this->user_id = $user_id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
    }

    public function validateData()
    {
        try {
            $data = $this->getData();

            $this->name     = (ffilter($data, 'name'))->required()->string();
            $this->email    = (ffilter($data, 'email'))->required()->string();
            $this->password = (ffilter($data, 'password'))->required()->string();

            $this->name = sanitizeString($this->name);
        } catch (\Throwable $th) {
            Functions::isCustomThrow($th);
            throw new \Exception('Erro ao validar dados do usuário', 7400, $th);
        }
    }

    public function validateUserId()
    {
        try {
            $data = $this->getData();

            $this->user_id = (ffilter($data, 'user_id'))->required()->string();
        } catch (\Throwable $th) {
            Functions::isCustomThrow($th);
            throw new \Exception('Erro ao validar ID do usuário', 7401, $th);
        }
    }

    public function getId(): string
    {
        return $this->user_id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
