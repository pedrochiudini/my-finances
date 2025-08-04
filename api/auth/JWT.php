<?php

require_once HOME . 'api/config/Config.php';

class JWT
{
    private static string $secret = SECRET_KEY;

    public static function encode(array $payload, int $expSeconds = 3600): string
    {
        $header = ['alg' => 'HS256', 'typ' => 'JWT'];

        $payload['exp'] = time() + $expSeconds;

        $base64Header  = self::base64UrlEncode(json_encode($header));
        $base64Payload = self::base64UrlEncode(json_encode($payload));

        $signature       = hash_hmac('sha256', "$base64Header.$base64Payload", self::$secret, true);
        $base64Signature = self::base64UrlEncode($signature);

        return "$base64Header.$base64Payload.$base64Signature";
    }

    public static function decode(string $jwt): array
    {
        $parts = explode('.', $jwt);

        if (count($parts) !== 3) throw new \Exception('JWT inválido.', 7402);

        [$base64Header, $base64Payload, $base64Signature] = $parts;

        $header = json_decode(self::base64UrlDecode($base64Header), true);
        $payload = json_decode(self::base64UrlDecode($base64Payload), true);

        if (!$header || !$payload) throw new \Exception('JWT inválido.', 7402);

        // Verifica expiração
        if (isset($payload['exp']) && time() > $payload['exp']) throw new \Exception('JWT expirado.', 7402);

        // Verifica assinatura
        $validSignature = self::base64UrlEncode(
            hash_hmac('sha256', "$base64Header.$base64Payload", self::$secret, true)
        );

        if (!hash_equals($validSignature, $base64Signature)) throw new \Exception('JWT inválido.', 7402);

        return $payload;
    }

    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }
}