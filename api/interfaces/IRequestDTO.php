<?php

interface IRequestDTO
{
    /**
     * Transforms the data from the DTO to an object.
     *
     * @return object
     */
    public function transformToObject(): object;

    public static function fromArray(array $data): self;
}