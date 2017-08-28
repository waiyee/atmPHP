<?php
include('app.php');
require_once 'library/devbittrexapi.php';
use atm\devbittrex\DevClient;
$devbittrex = new DevClient();
$oid = $_POST['oid'];
$return='';
$OB = $dbclient->coins->OwnOrderBook;

$order = $OB->findOne(array('_id'=>  new \MongoDB\BSON\ObjectID($oid)));

if ($order->Status == 'buying') {
    $uuid = $order->BuyOrder->uuid;
    $type = 'buy';
}
else{
    $uuid = $order->SellOrder->uuid;
    $type = 'sell';
}

// Call order API to check complete buy order or not
$buy_result = $devbittrex->getOrder($uuid);

if (!$buy_result->IsOpen) {

    if (boughtOrder(new ObjectId($oid), $api_status, $dbclient)>0 )
        $return = $oid.' '.'Brought at rate '.$order->BuyOrder->Rate.'<br/>';


    $uuid = hash("md5", time());
    // Call API
    // Insert order into db
    sellLimitDB(new ObjectId($oid), $uuid, $api_status, $dbclient);
    $return = $return.'Place sell order at rate '.$order->SellOrder->Rate.'<br/>';
}
else
{
    $return = $oid.' '.'Not yet '.$type.'<br/>';
}

echo $return;