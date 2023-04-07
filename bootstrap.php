<?php
require_once 'vendor/autoload.php';
use Dotenv\Dotenv;
use Src\System\DatabaseConnector;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$dbConnection = (new DatabaseConnector())->getConnection();
