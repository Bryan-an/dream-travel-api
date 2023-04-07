<?php

use Src\Controllers\CustomerController;
use Src\Controllers\EmployeeController;
use Src\Controllers\TravelController;

require_once "../bootstrap.php";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode("/", $uri);

define("CUSTOMER_ENDPOINT", "customer");
define("EMPLOYEE_ENDPOINT", "employee");
define("TRAVEL_ENDPOINT", "travel");

$isCustomerEnpoint = $uri[1] === CUSTOMER_ENDPOINT;
$isEmployeeEndpoint = $uri[1] === EMPLOYEE_ENDPOINT;
$isTravelEndpoint = $uri[1] === TRAVEL_ENDPOINT;

if (
    !$isCustomerEnpoint
    && !$isEmployeeEndpoint
    && !$isTravelEndpoint
) {
    header("HTTP/1.1 404 Not Found");
    exit();
}

$id = null;

if (isset($uri[2])) {
    $id = $uri[2];
}

$requestMethod = $_SERVER['REQUEST_METHOD'];

$controller = null;

if ($isCustomerEnpoint) {
    $controller = new CustomerController($dbConnection, $requestMethod, $id);
} else if ($isEmployeeEndpoint) {
    $controller = new EmployeeController($dbConnection, $requestMethod, $id);
} else if ($isTravelEndpoint) {
    $controller = new TravelController($dbConnection, $requestMethod, $id);
}

$controller->processRequest();
