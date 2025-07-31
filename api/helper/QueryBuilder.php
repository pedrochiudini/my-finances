<?php

class QueryBuilder
{
    public const SELECT = 0;
    public const UPDATE = 1;
    public const INSERT = 2;
    public const DELETE = 3;

    private PDO $connection;

    private const SQL_PARTS_DEFAULTS = [
        'select' => [],
        'table'  => null,
        'where'  => null,
        'values' => [],
    ];

    private array $sql_parts = self::SQL_PARTS_DEFAULTS;

    private string $sql;

    private int $type = self::SELECT;

    private array $params = [];

    public function getSQL(): string
    {
        switch ($this->type) {
            case self::UPDATE:
                $sql = $this->buildUpdate();
                break;

            case self::INSERT:
                $sql = $this->buildInsert();
                break;

            case self::DELETE:
                $sql = $this->buildDelete();
                break;

            case self::SELECT:
            default:
                $sql = $this->buildSelect();
                break;
        }

        $this->sql = $sql;

        return $sql;
    }

    public function select(array $columns): self
    {
        $this->type = self::SELECT;

        if (empty($columns)) {
            $columns = ['*'];
        }

        $this->sql_parts['select'] = $columns;

        return $this;
    }

    public function update(string $table): self
    {
        $this->type = self::UPDATE;

        if (empty($table)) {
            return $this;
        }

        $this->sql_parts['table'] = $table;

        return $this;
    }

    public function insert(string $table): self
    {
        $this->type = self::INSERT;

        if (empty($table)) {
            return $this;
        }

        $this->sql_parts['table'] = $table;

        return $this;
    }

    public function from(string $table): self
    {
        if (empty($table)) {
            return $this;
        }

        $this->sql_parts['table'] = $table;

        return $this;
    }

    public function where(string $column, string $operator, string $param): self
    {
        if ($this->sql_parts['where']) {
            $this->sql_parts['where'] .= ' AND ';
        } else {
            $this->sql_parts['where'] = '';
        }

        $this->sql_parts['where'] .= "{$column} {$operator} :{$column}";

        return $this;
    }

    public function setParameter(string $param, $value): self
    {
        $this->params[$param] = $value;

        return $this;
    }

    public function setValue(string $column, $value): self
    {
        $this->sql_parts['values'][$column] = $value;

        return $this;
    }

    public function buildSelect(): string
    {
        $columns = implode(', ', $this->sql_parts['select']);

        $table = $this->sql_parts['table'];

        $query = "SELECT $columns FROM $table";

        if ($this->sql_parts['where']) {
            $query .= ' WHERE ' . $this->sql_parts['where'];
        }

        $query = rtrim($query) . ';';

        return $query;
    }

    public function buildUpdate(): string
    {
        $query = "";

        return $query;
    }

    public function buildInsert(): string
    {
        $query = 'INSERT INTO ' . $this->sql_parts['table'] .
        ' (' . implode(', ', array_keys($this->sql_parts['values'])) . ')' .
        ' VALUES (' . implode(', ', $this->sql_parts['values']) . ')';

        $query = rtrim($query) . ';';

        return $query;
    }

    public function buildDelete(): string
    {
        $query = "";

        return $query;
    }

    public function build(): array
    {
        if (empty($this->sql_parts['table'])) {
            throw new \Exception("Tabela não especificada.", 400);
        }

        $sql    = $this->getSQL();
        $params = $this->params;

        if (empty($params)) {
            return ['sql' => $sql];
        }

        return [
            'sql'    => $sql,
            'params' => $params
        ];
    }
}
