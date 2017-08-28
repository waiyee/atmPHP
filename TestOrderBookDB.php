<?php
include('app.php');
require_once 'library/devbittrexapi.php';
use atm\devbittrex\DevClient;
$devbittrex = new DevClient();
// Use algo to select market + quntity + rate
$market = 'BTC-LTC';
$quantity = 0.0011111;
$buy_rate = 0.47;

// Suppose Call API to get uuid + api status
$uuid = hash("md5", time());
$api_status = '';
$sell_rate = 99999999999;



// Insert order into db
$insert_id = buyLimitDB($uuid, $market, $quantity, $buy_rate, $api_status, $dbclient, $sell_rate);

// Call order API to check complete buy order or not
$buy_result = $devbittrex->getOrder($uuid);

sleep(1);

// if buy = yes,  update order to db then, place sell order to API

if (!$buy_result->IsOpen) {
    if (boughtOrder($insert_id, $api_status, $dbclient) > 0 )
        echo 'Bought<br/>';

    $uuid = hash("md5", time());

    // Insert order into db
    sellLimitDB($insert_id, $uuid, $api_status, $dbclient);

    // Call order API to check complete sell order or not
    $sell_result = $devbittrex->getOrder($uuid);

    // if sell = yes, update order to db
    if (!$sell_result->IsOpen)
    {
        if (SoldOrder($insert_id, $api_status, $dbclient) > 0 )
            echo 'Sold<br/>';
    }
}