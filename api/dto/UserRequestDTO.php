<?php

require_once HOME . 'api/model/User.php';
require_once HOME . 'api/interfaces/IRequestDTO.php';

class UserRequestDTO implements IRequestDTO
{
    public function __construct(
        private string $user_id,
        private string $name,
        private string $email,
        private string $password,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            user_id: $data['user_id'] ?? '',
            name: $data['name'] ?? '',
            email: $data['email'] ?? '',
            password: $data['password'] ?? '',
        );
    }

    public function transformToObject(): User
    {
        return new User(
            user_id: $this->user_id,
            name: $this->name,
            email: $this->email,
            password: $this->password
        );
    }
}
