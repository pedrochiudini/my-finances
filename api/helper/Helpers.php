<?php

require_once HOME . 'api/model/CustomFilter.php';

function dd(mixed ...$data): void
{
    header('Content-Type: text/html; charset=utf-8');

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
    print_r($value);
    $output = ob_get_clean();

    // Realce básico por tipo usando regex
    $output = htmlspecialchars($output, ENT_QUOTES, 'UTF-8');

    // Tipos primitivos
    $output = preg_replace('/string\((\d+)\) "(.*?)"/', '<span class="type-string">string($1) "$2"</span>', $output);
    $output = preg_replace('/int\((\d+)\)/', '<span class="type-int">int($1)</span>', $output);
    $output = preg_replace('/float\((.*?)\)/', '<span class="type-float">float($1)</span>', $output);
    $output = preg_replace('/bool\((true|false)\)/', '<span class="type-bool">bool($1)</span>', $output);
    $output = preg_replace('/NULL/', '<span class="type-null">NULL</span>', $output);
    $output = preg_replace('/array\((\d+)\)/', '<span class="type-array">array($1)</span>', $output);
    $output = preg_replace('/object\((.*?)\)#(\d+)/', '<span class="type-object">object($1)#$2</span>', $output);

    // Chaves
    $output = preg_replace('/\[\"(.*?)\"\]=>/', '<span class="key">["$1"]</span> =>', $output);
    $output = preg_replace('/\[(\d+)\]=>/', '<span class="key">[$1]</span> =>', $output);

    return $output;
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
