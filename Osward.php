<?php
include('app.php');
/**
Hello I'm Osward, the orders monitor.
 **/

$OOB = $dbclient->coins->OwnOrderBook;

$buying_orders = $OOB->find(
    array('Status' => 'buying')
);
foreach ($buying_orders as $buying_order){
    $status = $bittrex->getOrder($buying_order->BuyOrder->uuid);
    if (!empty($status->Closed)){
        boughtOrder($buying_order->_id,'bought',$dbclient,
            $status->PricePerUnit,$status->CommissionPaid,$status->CommissionPaid+$status->Price);
    }
}

$selling_orders = $OOB->find(
    array('Status' => 'selling')
);
foreach ($selling_orders as $selling_order){
    $status = $bittrex->getOrder($selling_order->SellOrder->uuid);
    if (!empty($status->Closed)){
        soldOrder($selling_order->_id,'sold',$dbclient,
            $status->PricePerUnit,$status->CommissionPaid,$status->CommissionPaid+$status->Price);
    }
}

$buy_time_orders = $OOB->find(
    array('$and'=>
        array(
            array('Status'=>'buying'),
            array("BuyOrder.OrderTime" => array('$lt'=>new MongoDB\BSON\UTCDateTime(microtime(true) * 1000 - (CANCELBUY*60*1000))))
        ))
);


foreach($buy_time_orders as $time_order){

    $cancel_result = $bittrex->cancel($time_order->BuyOrder->uuid);

    if ($cancel_result != 'ERROR') {
        echo 'Cancel order ' . $time_order->MarketName;
        cancelBuyDB($time_order->BuyOrder->uuid, $api_status, $dbclient);


    }
}


updateWallet($bittrex,$dbclient);



?>