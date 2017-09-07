<?php

include('app.php');

$OOB = $dbclient->coins->OwnOrderBook;

$sell_time_orders = $OOB->find(
    array('$and'=>
        array(
            array('Status'=>'selling')
        ))
);


foreach($sell_time_orders as $time_order){

    $cancel_result = $bittrex->cancel($time_order->SellOrder->uuid);
    $cancel_result = 'cancelled';
    if ($cancel_result != 'ERROR') {
        echo 'Cancel order ' . $time_order->MarketName;
        cancelSellDB($time_order->SellOrder->uuid, 'cancel', $dbclient);

        $market_price = $bittrex->getTicker($time_order->MarketName);

        if (!empty($market_price)) {

            if ($time_order->SellOrder->Quantity * $market_price->Last * (1+TXFEE) >=  BRMINTRADESIZE) {

                echo 'at market rate '.$market_price->Last.'<br/>';
                $new_uuid = $bittrex->sellLimit($time_order->MarketName, $time_order->SellOrder->Quantity, $market_price->Last);
            }
            else{

                $rate = BRMINTRADESIZE * (1+TXFEE) / $time_order->SellOrder->Quantity;
                echo 'at min rate '.$rate.'<br/>';
                $new_uuid = $bittrex->sellLimit($time_order->MarketName, $time_order->SellOrder->Quantity, $rate);
            }

            updateSellDB($time_order->_id, $new_uuid->uuid, 'selling', $market_price->last, $time_oder->SellOrder->Quantity, $dbclient);
        }

    }
}

