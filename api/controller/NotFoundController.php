<?php

require_once __DIR__ . '/../http/Response.php';

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
