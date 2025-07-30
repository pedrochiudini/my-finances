<?php

require_once HOME . 'api/http/Response.php';
require_once HOME . 'api/helper/QueryBuilder.php';

class NotFoundController
{
    public function index(Response $response): void
    {
        $response::json([
            'success' => false,
            'message' => 'Rota não encontrada.'
        ], 404);
        return;
    }
}
