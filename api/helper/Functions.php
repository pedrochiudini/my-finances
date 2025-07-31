<?php

class Functions
{
    public static function isCustomThrow(\Throwable $th)
    {
        $code = $th->getcode();
        if (($code >= 7400 && $code <= 8000) || ($code >= 4600 && $code <= 4620)) {
            throw $th;
        }
    }
}
