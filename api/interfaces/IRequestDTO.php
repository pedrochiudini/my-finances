<?php

interface IRequestDTO
{
    /**
     * Transforms the data from the DTO to an object.
     *
     * @return object
     */
    public function transformToObject(): object;

    /**
     * Creates an instance of the DTO from an array.
     *
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self;
}