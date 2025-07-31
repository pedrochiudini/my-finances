<?php

interface ModelInterface
{
    public function validateData();

    public function getData();

    public function setData(array $data);
}
