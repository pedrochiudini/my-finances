<?php

require_once HOME . 'api/model/User.php';
require_once HOME . 'api/interfaces/IRequestDTO.php';

class UserRequestDTO implements IRequestDTO
{
    public function __construct(
        private int $id,
        private string $name,
        private string $email,
        private string $password,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            email: $data['email'],
            password: $data['password'],
        );
    }

    public function transformToObject(): User
    {
        return new User(
            id: $this->id,
            name: $this->name,
            email: $this->email,
            password: $this->password
        );
    }
}
