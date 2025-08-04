<?php

interface RepositoryInterface
{
    /**
     * Find an entity by its ID.
     *
     * @return object Returns the entity if found, null otherwise.
     * @throws \Exception If an error occurs during the find operation.
     */
    public function findById(string $id);

    /**
     * Find all entities.
     *
     * @return array Returns an array of entities.
     * @throws \Exception If an error occurs during the find operation.
     */
    public function findAll(?string $user_id = null);

    /**
     * Save an entity to the database.
     *
     * @return bool Returns true on success, false on failure.
     * @throws \Exception If an error occurs during the save operation.
     */
    public function save(object $entity);

    public function update(object $entity);

    public function delete(string $id);
}