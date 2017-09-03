<?php
include('app.php');

$OOB = $dbclient->coins->OwnOrderBook;



$orders = $OOB->find();
foreach ($orders as $order){
    $buy_status = $bittrex->getOrder($order->BuyOrder->uuid);
    $sell_status = $bittrex->getOrder($order->SellOrder->uuid);

    if (!empty($buy_status) && !empty($sell_status)){

        if (!empty($buy_status->Closed)) {
            $buy_rate = $buy_status->PricePerUnit;
            $buy_fee = $buy_status->CommissionPaid;
            $buy_total = $buy_status->Price +$buy_status->CommissionPaid;
        }
        else{
            $buy_rate = $buy_status->Limit;
            $buy_fee = round($buy_status->Limit  * $buy_status->Quantity * TXFEE , 8) ;
            $buy_total = round ($buy_status->Limit  * $buy_status->Quantity  + $buy_fee, 8);
        }

        if (!empty($sell_status->Closed)) {
            $sell_rate = $sell_status->PricePerUnit;
            $sell_fee = $sell_status->CommissionPaid;
            $sell_total = $sell_status->Price +$sell_status->CommissionPaid;
        }
        else{
            $sell_rate = $sell_status->Limit;
            $sell_fee =round( $sell_status->Limit  * $sell_status->Quantity * TXFEE, 8);
            $sell_total = round ($sell_status->Limit  * $sell_status->Quantity  + $sell_fee ,8);
        }


        updatePriceDB($order->_id,
            $buy_rate,$buy_fee,$buy_total,
            $sell_rate,$sell_fee,$sell_total,
            $dbclient);

    }
}
