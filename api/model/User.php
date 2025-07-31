<?php

require_once HOME . 'api/model/Model.php';
require_once HOME . 'api/interfaces/ModelInterface.php';

class User extends Model implements ModelInterface
{
    public static string $name_table = 'users';
    public static array $fields_db = [
        'id',
        'name',
        'email',
        'password'
    ];

    protected int $id;
    protected string $name;
    protected string $email;
    protected string $password;

    public function __construct(int $id, string $name, string $email, string $password)
    {
        $this->id = $id;
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
        } catch (\Throwable $th) {
            Functions::isCustomThrow($th);
            throw new \Exception('Erro ao validar dados do usuário', 7400, $th);
        }
    }

    public function getId(): int
    {
        return $this->id;
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
