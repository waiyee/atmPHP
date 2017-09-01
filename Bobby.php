<?php
include('app.php');
require_once 'library/devbittrexapi.php';
use atm\devbittrex\DevClient;
$devbittrex = new DevClient();

/** Buyer
 *   1. Use API to check selling order finish or not and count for opening orders
 *   2. Update BTC wallet
 *   3. Search for Analyser provide non-used and valid record or not
 *   4. If opening order not exceed limit, place buy order via API
 *   5. Store place buy order in DB
 **/
echo 'Start: '.date('Y-m-d H:i:s').'<br/>';
$OOB = $dbclient->coins->OwnOrderBook;
$temp =$dbclient->coins->tempOpeningOrders;
$temp->drop();
/*
$selling_orders = $dbclient->coins->OwnOrderBook->find(
 [
        array('Status' => 'selling'),
        array('SellOrder.uuid' => 1, '_id' => 0)
    ]
);
var_dump($selling_orders);
print_r(count($selling_orders));
foreach($selling_orders as $selling_order)
{
    var_dump($selling_order);
}*/

// API get all opening orders
$opening_orders = $devbittrex->getOpenOrders();

$api_status = '';

if($opening_orders) {
    $opening_uuid = array();
    foreach ($opening_orders as $opening_order)
    {
        $dump = array_push($opening_uuid, $opening_order->OrderUuid);
    }

    $selling_orders = $OOB->find(
        array('Status' => 'selling'),
        array('SellOrder.uuid' => 1, '_id' => 0)
    );

    $selling_uuid = array();
    foreach($selling_orders as $selling_order)
    {
        $dump = array_push($selling_uuid, $selling_order->SellOrder->uuid);
    }

    $sold_orders = array_diff($selling_uuid,$opening_uuid);

    if ($sold_orders)
        SoldOrders($sold_orders, $api_status, $dbclient);
}

//updateWallet($bittrex,$dbclient);

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

$wallet = $dbclient->coins->WalletBalance;
$btcAva = $wallet->findOne(array('Currency'=>'BTC'));
$btc_balance = $btcAva->Balance;

foreach ($valid_mkt as $market) {
    $rate = round($market->doc->Rate, 8);

    $quantity = round(( $btc_balance * BTCUSAGE) / $rate, 8);
    if ($quantity > 0 ) {
        $btc_balance = round($btc_balance - ($rate * $quantity), 8);
        $exits_now = $dbclient->coins->OwnOrderBook->findOne(
            array('$and' =>
                array(
                    array('MarketName' => $market->doc->MarketName),
                    array('$or' =>
                        array(
                            array('Status' => 'buying'), array('Status' => 'selling')
                        )
                    )
                ))

        );
        if (!$exits_now) {
            $buy_result = $devbittrex->buyLimit($market->doc->MarketName, $quantity, $rate);
            if ($buy_result->uuid) {
                $api_status = '';
                // Insert order into db

                $wallet->UpdateOne(array('Currency' => 'BTC'), array('$set' => array('Available' => $btc_balance)));

                $insert_id = buyLimitDB($buy_result->uuid, $market->doc->MarketName, $quantity, $rate, $api_status, $dbclient);
            }
            if ($insert_id)
                echo '[' . date('Y-m-d H:i:s') . '] ' . $market->doc->MarketName . ' Buy at ' . number_format($rate, 8) . ' for ' . number_format($quantity, 9) . ' placed.<br/>Score: ' . $market->doc->Score . '<br/>';
        }
    }
}



echo 'End: '.date('Y-m-d H:i:s').'<br/>';