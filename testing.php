<?php
include('app.php');

$OB = $dbclient->coins->OwnOrderBook;
$wallet =$dbclient->coins->WalletBalance->findOne(array('Currency'=>'BTC'));

echo 'Original Balance ' . number_format($wallet->Balance, 8) . ' BTC<br/>';
echo 'Wallet Available :' . number_format($wallet->Available, 8) . ' BTC<br/><br/>';

$orders = $OB->find([]);
$net_profit = 0;
$buying_btc = 0;
$buying_cnt = 0;
$selling_btc = 0;
$selling_cnt = 0;
$sold_btc = 0;
$sold_cnt = 0;
foreach ($orders as $order)
{
    if ($order->Status == 'selling' )
    {

        $selling_cnt += 1;
        $selling_btc += $order->BuyOrder->Total;
    }

    if ($order->Status == 'buying')
    {
        $buying_cnt += 1;
        $buying_btc += $order->BuyOrder->Total;
    }

    if ($order->Status == 'sold') {
        $sold_cnt += 1;
        $sold_btc += $order->SellOrder->Total;
        $net_profit = $net_profit + ($order->SellOrder->Total - $order->BuyOrder->Total);
    }


}





echo 'Buying hold ' . number_format($buying_btc,8) . ' BTC<br/>';
echo 'Buying Count ' . $buying_cnt . '<br/>';
echo 'Selling hold ' . number_format($selling_btc,8) . ' BTC <br/>';
echo 'Selling Count ' . $selling_cnt . '<br/>';
echo 'Sold ' . number_format($sold_btc,8) . ' BTC<br/>';
echo 'Net Profit ' . number_format($net_profit,8) . ' BTC<br/>';
echo 'Sold Count ' . $sold_cnt . '<br/><br/>';

echo 'Total having :' . number_format($buying_btc + $selling_btc + $wallet->Available, 8) ;

// Search Analyser for valid record to place buy order
$analyser = $dbclient->coins->Analyser;
$now = date('Y-m-d H:i:s');

$ops =
    array(
        array(
            '$sort' => array('Time' => 1, 'Score'=>1)
        ),
        array(
            '$match' => array('Used' => 0, 'Expire' => array('$gte' => $now ), 'Score' => array('$gt' => 0))
        ),
        array(
            '$group' => array(
                '_id' => '$MarketName', 'doc' => array( '$last' => '$$ROOT')
            )
        ),
        array(
            '$project' => array(
                '_id' => -1,
                'doc' =>  '$doc'
            )
        ),
        array(
            '$sort' => array('doc.Score'=>-1)
        )
    );



$valid_mkt = $analyser->aggregate($ops); // Valid time + non-used + no buying/selling market in OrderBook


foreach ($valid_mkt as $market) {
    var_dump($market->doc);
}

