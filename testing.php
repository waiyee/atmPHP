<?php
include('app.php');

$OB = $dbclient->coins->OwnOrderBook;

$orders = $OB->find([]);
$net_profit = 0;
$buying_btc = 0;
$selling_btc = 0;
foreach ($orders as $order)
{
    if ($order->Status == 'selling' )
        $selling_btc += $order->BuyOrder->Total;
    if ($order->Status == 'buying')
        $buying_btc += $order->BuyOrder->Total;
    if ($order->Status == 'sold') {
        $selling_btc += $order->SellOrder->Total;
        $net_profit = $net_profit + ($order->SellOrder->Total - $order->BuyOrder->Total);
    }
}

echo 'Buying hold ' . $buying_btc . ' BTC<br/>';
echo 'Selling hold ' . $selling_btc . ' BTC <br/>';
echo 'Net Profit ' . $net_profit . ' BTC<br/>';

