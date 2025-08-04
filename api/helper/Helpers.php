<?php

require_once HOME . 'api/model/CustomFilter.php';

function dd(...$data): void
{
    header('Content-Type: text/html; charset=utf-8');

    if (!in_array(\PHP_SAPI, ['cli', 'phpdbg'], true) && !headers_sent()) {
        header('HTTP/1.1 500 Internal Server Error');
    }

    echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>
        body {
            background-color: #5a5858ff;
            color: #3ecc22ff;
            font-family: Consolas, Menlo, Monaco, monospace;
            padding: 2px;
        }
        .dump-container {
            background-color: #2d2d2d;
            padding: 15px;
            margin-bottom: 20px;
        }
        .type-string  { color: #3ecc22ff; }
        .type-int     { color: #3ecc22ff; }
        .type-float   { color: #3ecc22ff; }
        .type-bool    { color: #3ecc22ff; }
        .type-null    { color: #3ecc22ff; font-style: italic; }
        .type-array   { color: #3ecc22ff; }
        .type-object  { color: #3ecc22ff; }
        .key          { color: #3ecc22ff; }
    </style></head><body>';

    foreach ($data as $item) {
        echo '<div class="dump-container"><pre>' . highlightDump($item) . '</pre></div>';
    }

    echo '</body></html>';
    die();
}

function highlightDump($value): string
{
    ob_start();
    return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function ffilter(
    array $data,
    $input,
    $default = null,
    string $msg = null
): CustomFilter {
    return (new CustomFilter(
        $data,
        $input,
        $default,
        $msg
    ));
}

function sanitizeString(string $text): string
{
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    return preg_replace('/[^A-Za-z0-9 ]/', '', $text);
}

function getAmountInFloat(int $amount): float
{
    return (float) $amount / 100;
}

function getAmountInInteger(int|float $amount): int
{
    return (int) floor($amount * 100);
}
