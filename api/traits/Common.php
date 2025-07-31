<?php

trait Common
{
    public function splitUuid($string, bool $unico = true): array
    {
        $split = preg_split("/\s*[;,']\s*/", $string, -1, PREG_SPLIT_NO_EMPTY);

        if ($unico) {
            return array_unique($split);
        }

        return $split;
    }

    public function uuid(): string
    {
        return sha1(uniqid(rand(), true));
    }

    public function now(): string
    {
        $t     = microtime(true);
        $micro = sprintf("%06d", ($t - floor($t)) * 1000000);

        return date("Y-m-d H:i:s") . ".{$micro}";
    }

    public function hash($pass, $salt = null, $custo = '5000')
    {
        if (!$salt) {
            $salt = $this->uuid();
            $salt = base64_encode($salt);
            $salt = str_replace('+', '.', $salt);
            $salt = substr($salt, 0, 22);
        }

        return crypt($pass, '$6$rounds=' . $custo . '$' . $salt . '$');
    }

    public function checkHash($pass, $hash): bool
    {
        if (hash_equals($hash, crypt($pass, $hash))) {
            return true;
        }

        return false;
    }

    /**
     * Formatar CPF, CNPJ, Telefone ou qualquer coisa
     * Ex: ***.***.***-** //159.558.502-87
     */
    public function addMask($mask, $str)
    {
        $str = str_replace(" ", "", $str);

        for ($i = 0; $i < strlen($str); $i++) {
            $mask[strpos($mask, "*")] = $str[$i];
        }

        return $mask;
    }
}
