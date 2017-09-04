<?php

include('app.php');

$OOB = $dbclient->coins->OwnOrderBook;

$sell_time_orders = $OOB->find(
    array('$and'=>
        array(
            array('Status'=>'selling'),
            array("SellOrder.OrderTime" => array('$lt'=>new MongoDB\BSON\UTCDateTime(microtime(true) * 1000 - (TIMETOSELL*60*60*1000))))
        ))
);


foreach($sell_time_orders as $time_order){

    $cancel_result = $bittrex->cancel($time_order->SellOrder->uuid);
    if ($cancel_result != 'ERROR') {
        echo 'Cancel order ' . $time_order->MarketName;
        cancelSellDB($time_order->SellOrder->uuid, $api_status, $dbclient);

        $market_price = $bittrex->getTicker($time_order->MarketName);
        if ($market_price) {
            $new_uuid = $bittrex->sellLimit($time_order->MarketName, $time_order->SellOrder->Quantity, $market_price->last);
            updateSellDB($time_order->_id, $new_uuid->uuid, $api_status, $market_price->last, $time_oder->SellOrder->Quantity, $dbclient);
        }
    }
}

