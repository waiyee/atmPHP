<?php
include('app.php');
require_once 'library/devbittrexapi.php';
use atm\devbittrex\DevClient;
$devbittrex = new DevClient();
$oid = $_POST['oid'];
//$oid = '59a57bda44cf484900006a9f';
$return='';
$OB = $dbclient->coins->OwnOrderBook;

$order = $OB->findOne(array('_id'=>  new \MongoDB\BSON\ObjectID($oid)));

if ($order->Status == 'buying') {
    $uuid = $order->BuyOrder->uuid;
    $type = 'buy';
    $print_rate = number_format($order->BuyOrder->Rate,8);
}
else{
    $uuid = $order->SellOrder->uuid;
    $type = 'sell';
    $print_rate = number_format($order->BuyOrder->Rate,8);
}

// Call order API to check complete buy order or not
$buy_result = $devbittrex->getOrder($uuid);


if (!$buy_result->IsOpen) {
    $api_status ='';

    if (boughtOrder( new \MongoDB\BSON\ObjectID($oid), $api_status, $dbclient)>0 )
        $return = '['.date('Y-m-d H:i:s').'] '.$order->MarketName.' '.'Brought at rate '.number_format($order->BuyOrder->Rate, 8).'<br/>';


    //    $uuid = hash("md5", time());
    // Call API
    $sell_result = $devbittrex->sellLimit($order->MarketName,$order->SellOrder->Quantity, $order->SellOrder->Rate);
    if ($sell_result->uuid) {
        // Insert order into db
        sellLimitDB(new \MongoDB\BSON\ObjectID($oid), $sell_result->uuid, $api_status, $dbclient);
        $return = $return . '['.date('Y-m-d H:i:s').'] '.'Place sell order at rate ' . number_format($order->SellOrder->Rate ,8). '<br/>';
    }
}
else
{
    $return = '['.date('Y-m-d H:i:s').'] '.$order->MarketName.' '.'Not yet '.$type.' at '.$print_rate.' Market price : '.number_format($buy_result->PricePerUnit, 8).'<br/>';
}

echo $return;