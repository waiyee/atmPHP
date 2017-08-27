<?php
require_once 'vendor/autoload.php'; // include Composer's autoloader
require_once 'library/bittrexapi.php';
require_once 'library/orderbookdb.php';
require_once 'controller/bittrex.php';

define('PROFIT','0.02');
define('TXFEE','0.0025');
use atm\bittrex\Client;

set_time_limit(0);
$bittrex = new Client();

$dbclient = new MongoDB\Client("mongodb://localhost:27017");