<?php
require_once 'config.php';
require_once 'vendor/autoload.php'; // include Composer's autoloader
require_once 'library/bittrexapi.php';
require_once 'library/orderbookdb.php';
require_once 'controller/bittrex.php';

use atm\bittrex\Client;

set_time_limit(0);
$bittrex = new Client();

$dbclient = new MongoDB\Client("mongodb://localhost:27017");