<?php

abstract class Model
{
    public static string $name_table;
    public static array $fields_db;

    abstract public function validateData();

    public function castAtributo($type, $value)
    {
        if ($type != "NULL") {
            #Só realiza casting para variaveis definidas com tipagem
            settype($value, $type);
        }

        return $value;
    }

    public function getData(): array
    {
        $data = [];
        foreach (get_called_class()::$fields_db as $field) {
            $data[$field] = $this->$field;
        }

        return $data;
    }

    public function setData(array $data, bool $validate = true): self
    {
        $class      = get_called_class();
        $reflection = new \ReflectionClass($class);

        foreach ($class::$fields_db as $field) {
            if (isset($data[$field])) {
                //Realiza o casting para o formato correto.
                $type = $reflection->getProperty($field)->getType();

                if (is_null($type)) {
                    // Se a property não estiver definida na classe, não realiza o casting
                    $this->$field = $data[$field];
                    continue;
                }

                $this->$field = $this->castAtributo($type, $data[$field]);
            }
        }

        if ($validate) {
            $this->validateData();
        }

        return $this;
    }
}
