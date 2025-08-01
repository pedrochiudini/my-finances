<?php

require_once HOME . 'api/model/User.php';

class UserResponseDTO implements JsonSerializable
{
    public function __construct(
        private string $user_id,
        private string $name,
        private string $email
    ) {}

    public static function transformToDTO(User $user): self
    {
        return new self(
            user_id: $user->getId(),
            name: $user->getName(),
            email: $user->getEmail()
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'id'    => $this->user_id,
            'name'  => $this->name,
            'email' => $this->email,
        ];
    }
}
