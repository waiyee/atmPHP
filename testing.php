<?php
include('app.php');

$OB = $dbclient->coins->OwnOrderBook;

$orders = $OB->find(array('Status'=>'sold'));
$net_profit = 0;
foreach ($orders as $order)
{
    $net_profit = $net_profit + ($order->SellOrder->Total - $order->BuyOrder->Total);
}

echo $net_profit;