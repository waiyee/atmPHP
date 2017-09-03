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
            $status->PricePerUnit,$status->Commission,$status->Commission+$status->Price);
    }
}

$selling_orders = $OOB->find(
    array('Status' => 'selling')
);
foreach ($selling_orders as $selling_order){
    $status = $bittrex->getOrder($selling_order->SellOrder->uuid);
    if (!empty($status->Closed)){
        soldOrder($selling_order->_id,'sold',$dbclient,
            $status->PricePerUnit,$status->Commission,$status->Commission+$status->Price);
    }
}




?>