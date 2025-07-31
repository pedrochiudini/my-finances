<?php

require_once HOME . 'api/model/User.php';

class UserResponseDTO implements JsonSerializable
{
    public function __construct(
        private string $name,
        private string $email,
    ) {}

    public static function transformToDTO(User $user): self
    {
        return new self(
            name: $user->getName(),
            email: $user->getEmail()
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'name'  => $this->name,
            'email' => $this->email,
        ];
    }
}
