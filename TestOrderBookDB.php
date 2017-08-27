<?php
include('app.php');
$market = 'BTC-LTC';
$quantity = 0.0000131;
$buy_rate = 0.47;
$uuid = hash("md5", time());


$insert_id = buyLimitDB($uuid, $market, $quantity, $buy_rate, $dbclient);

$uuid = hash("md5", time());

sellLimitDB($insert_id, $uuid, $dbclient);

