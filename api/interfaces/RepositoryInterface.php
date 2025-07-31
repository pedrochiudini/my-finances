<?php

interface RepositoryInterface
{
    public function findById(int $id);

    public function findAll();

    /**
     * Save an entity to the database.
     *
     * @param object $entity The entity to save.
     * @return bool Returns true on success, false on failure.
     * @throws \Exception If an error occurs during the save operation.
     */
    public function save(object $entity);

    public function update(object $entity);

    public function delete(int $id);
}