<?php

require_once HOME . 'api/interfaces/RepositoryInterface.php';
require_once HOME . 'api/model/User.php';

interface UserRepositoryInterface extends RepositoryInterface
{
    /**
     * Find a user by their email.
     *
     * @param string $email The email of the user to find.
     * @return User Returns a User object if found.
     * @throws \Exception If an error occurs during the find operation.
     */
    public function findByEmail(string $email);
}