<?php

class CustomFilter
{
    private $data;

    private $msg;

    private $input;

    private $default;

    /**
     * @param array $data Request data
     * @param mixed $input Value to be filtered
     * @param mixed $default Default value if input is empty
     * @param string $msg Message to be displayed if input is invalid
     */
    public function __construct(array $data, $input, $default = null, string $msg = null)
    {
        $this->data    = $data;
        $this->msg     = $msg;
        $this->input   = $input;
        $this->default = $default;
    }

    public function required()//: static
    {
        if (!array_key_exists($this->input, $this->data) || $this->data[$this->input] === '') {
            throw new \Exception($this->msg ?? "Field ({$this->input}) is required!", 4600);
        }

        return $this;
    }

    private function raw()
    {
        return $this->data[$this->input] ?? $this->default;
    }

    private function filter(int $filter = FILTER_DEFAULT)
    {
        return filter_var($this->raw(), $filter);
    }

    public function boolean(): bool
    {
        return (bool) $this->filter(FILTER_VALIDATE_BOOLEAN);
    }

    public function string(): string
    {
        $value = $this->filter(FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if ($value !== null && trim($value) === '') {
            throw new \Exception($this->msg ?? "Field ({$this->input}) is not a valid string.", 4601);
        }

        if (empty($value) && !empty($this->default)) {
            $value = $this->default;
        }

        return (string) $value;
    }

    public function int(): int
    {
        $value = $this->filter(FILTER_VALIDATE_INT);

        if (is_int($value)) {
            return $value;
        }

        throw new \Exception($this->msg ?? "Field ({$this->input}) is not an integer.", 4602);
    }

    public function float(): float
    {
        $value = $this->filter(FILTER_VALIDATE_FLOAT);

        if (is_float($value) || is_numeric($value)) {
            return (float) $value;
        }

        throw new \Exception($this->msg ?? "Field ({$this->input}) is not a float.", 4603);
    }

    public function isArray(): array
    {
        $value = $this->raw();

        if (is_array($value)) {
            return $value;
        }

        throw new \Exception($this->msg ?? "Field ({$this->input}) is not a valid array.", 4604);
    }

    public function arrayHeader(array $headers): array
    {
        $value = $this->isArray();

        foreach ($headers as $key => $field) {
            if (is_array($field)) {
                if (!isset($value[$key]) || !is_array($value[$key])) {
                    throw new \Exception($this->msg ?? "Field ({$key}) must be a nested array.", 4604);
                }
                (new self($value[$key], '', null, $this->msg))->arrayHeader($field);
            } else {
                if (!array_key_exists($field, $value)) {
                    throw new \Exception($this->msg ?? "Field ({$field}) is missing in array.", 4604);
                }
            }
        }

        return $value;
    }

    public function url(): string
    {
        $value = $this->filter(FILTER_VALIDATE_URL);

        if ($value) {
            return $value;
        }

        throw new \Exception($this->msg ?? "Field ({$this->input}) is not a valid URL.", 4605);
    }

    public function mac(): string
    {
        $value = $this->filter(FILTER_VALIDATE_MAC);

        if ($value) {
            return $value;
        }

        throw new \Exception($this->msg ?? "Field ({$this->input}) is not a valid MAC address.", 4606);
    }

    public function ip(): string
    {
        $value = $this->filter(FILTER_VALIDATE_IP);

        if ($value) {
            return $value;
        }

        throw new \Exception($this->msg ?? "Field ({$this->input}) is not a valid IP address.", 4607);
    }

    public function inAList(array $list): string
    {
        $value = $this->raw();

        if (in_array($value, $list, true)) {
            return $value;
        }

        throw new \Exception($this->msg ?? "Field ({$this->input}) must be one of [" . implode(', ', $list) . "].", 4611);
    }

    public function between(float $min, float $max): float
    {
        $value = $this->float();

        if ($value >= $min && $value <= $max) {
            return $value;
        }

        throw new \Exception($this->msg ?? "Field ({$this->input}) must be between {$min} and {$max}.", 4612);
    }

    public function maior(float $val): float
    {
        $value = $this->float();

        if ($value > $val) {
            return $value;
        }

        throw new \Exception($this->msg ?? "Field ({$this->input}) must be greater than {$val}.", 4613);
    }

    public function maiorIgual(float $val): float
    {
        $value = $this->float();

        if ($value >= $val) {
            return $value;
        }

        throw new \Exception($this->msg ?? "Field ({$this->input}) must be greater or equal to {$val}.", 4613);
    }

    public function menor(float $val): float
    {
        $value = $this->float();

        if ($value < $val) {
            return $value;
        }

        throw new \Exception($this->msg ?? "Field ({$this->input}) must be less than {$val}.", 4614);
    }

    public function menorIgual(float $val): float
    {
        $value = $this->float();

        if ($value <= $val) {
            return $value;
        }

        throw new \Exception($this->msg ?? "Field ({$this->input}) must be less or equal to {$val}.", 4614);
    }

    public function date(string $format = 'Y-m-d'): string
    {
        $value = $this->raw();

        $dt = \DateTime::createFromFormat($format, $value);
        if ($dt && $dt->format($format) === $value) {
            return $value;
        }

        throw new \Exception($this->msg ?? "Field ({$this->input}) must match date format {$format}.", 4615);
    }
}
