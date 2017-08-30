<?php
include('app.php');
require_once 'library/devbittrexapi.php';
use atm\devbittrex\DevClient;
$devbittrex = new DevClient();

$analyser = $dbclient->coins->Analyser;
$wallet = $dbclient->coins->WalletBalance;
$btcAva = $wallet->findOne(array('Currency'=>'BTC'));
$ops =
    array(
        array(
            '$sort' => array('Time' => 1, 'Score'=>1)
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
        )
    );
$markets = $analyser->aggregate($ops);
/*
$markets = $analyser->find(
  array('Score'=>array('$gt'=>0)),
  array('sort'=>array('Score'=>-1))
);
*/

foreach ($markets as $market) {

    if ($market->doc->Score > 0 ) {
        $rate = round($market->doc->Rate, 8);

        $quantity = round(($btcAva->Balance * BTCUSAGE) / $rate, 8);

        $buy_result = $devbittrex->buyLimit($market->doc->MarketName, $quantity, $rate);
        if ($buy_result->uuid) {
            $api_status = '';
            // Insert order into db
            $insert_id = buyLimitDB($buy_result->uuid, $market->doc->MarketName, $quantity, $rate, $api_status, $dbclient);
        }
        if ($insert_id)
            echo '[' . date('Y-m-d H:i:s') . '] ' . $market->doc->MarketName . ' Buy at ' . number_format($rate, 8) . ' for ' . number_format($quantity, 9) . ' placed.<br/>Score: ' . $market->doc->Score . '<br/>';
    }
}