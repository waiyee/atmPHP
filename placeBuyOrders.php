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
            '$sort' => array('Score'=>1,'Time' => 1)
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
    $rate = $market->doc->Rate;
    $quanity =  ( $btcAva * BTCUSAGE ) /   $rate;

    $buy_result = $devbittrex->buyLimit($market->MarketName,$quanity, $rate);
    if ($buy_result->uuid)
        // Insert order into db
        $insert_id = buyLimitDB($buy_result->uuid, $market->MarketName, $quantity, $rate, $api_status, $dbclient);
    if ($insert_id)
        echo $market->MarketName.' Buy at '.$rate.' for '.$quanity.' placed.<br/>';
}