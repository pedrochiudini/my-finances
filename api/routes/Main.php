<?php

require_once __DIR__ . '/../http/Route.php';

Route::post('/users/register', 'UserController::register');
Route::post('/users/login', 'UserController::login');
Route::get('/users/{id}/search', 'UserController::search');
Route::put('/users/update', 'UserController::update');
Route::delete('/users/{id}/delete', 'UserController::delete');