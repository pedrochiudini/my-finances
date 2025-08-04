<?php

require_once HOME . 'api/controller/UserController.php';
require_once HOME . 'api/controller/MonthlyIncomeController.php';
require_once HOME . 'api/controller/ExpenseController.php';
require_once HOME . 'api/controller/DashboardController.php';

UserController::getRoutes();
MonthlyIncomeController::getRoutes();
ExpenseController::getRoutes();
DashboardController::getRoutes();