<?php
include('app.php');
// Use algo to select market + quntity + rate
$market = 'BTC-LTC';
$quantity = 0.0000131;
$buy_rate = 0.47;

// Suppose Call API to get uuid + api status
$uuid = hash("md5", time());
$api_status = '';
$sell_rate = 99999999999;

// Insert order into db
$insert_id = buyLimitDB($uuid, $market, $quantity, $buy_rate, $api_status, $dbclient, $sell_rate);

// Call order API to check complete buy order or not

// if buy = yes,  update order to db then, place sell order to API
boughtOrder($insert_id, $api_status, $dbclient);
$uuid = hash("md5", time());

// Insert order into db
sellLimitDB($insert_id, $uuid, $dbclient);

// Call order API to check complete sell order or not

// if sell = yes, update order to db
SoldOrder($insert_id, $api_status, $dbclient);