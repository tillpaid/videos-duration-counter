<?php

define('ROOT_PATH', dirname(__FILE__));

require_once 'vendor/autoload.php';

use Classes\App\DurationCounter;
use Symfony\Component\Dotenv\Dotenv;


try {
	$dotenv = new Dotenv();
	$dotenv->load(__DIR__ . '/.env');
} catch (Exception $exception) {
	die("ERROR: Cannot load .env file");
}

$counter = new DurationCounter();
$counter->process();
