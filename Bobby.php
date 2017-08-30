<?php
include('app.php');
require_once 'library/devbittrexapi.php';
use atm\devbittrex\DevClient;
$devbittrex = new DevClient();

/******** FOLLOWING CODE NOT YET TEST ********/

/** Buyer
 *   1. Use API to check selling order finish or not and count for opening orders
 *   2. Update BTC wallet
 *   3. Search for Analyser provide non-used and valid record or not
 *   4. If opening order not exceed limit, place buy order via API
 *   5. Store place buy order in DB
 **/

$OOB = $dbclient->coins->OwnOrderBook;
$temp =$dbclient->coins->tempOpeningOrders;
$temp->drop();

// API get all opening orders
$opening_orders = $devbittrex->getOpenOrders();

$api_status = '';
if($opening_orders) {
    $temp->InsertMany($opening_orders);
    $selling_orders = $OOB->find(array('Status'=>'selling'), array('Projection'=>array('SellOrder.uuid'=>1, '_id'=>0)));
    $sold_orders = $temp->find(array('SellOrder.uuid'=>$selling_orders), array('Projection'=>array('_id'=>1)));
    SoldOrders($sold_orders, $api_status, $dbclient);
}

updateWallet($bittrex,$dbclient);

// Search Analyser for valid record to place buy order
$analyser = $dbclient->coins->Analyser;
$valid_mkt = $analyser->find([]); // Valid time + non-used + no buying/selling market in OrderBook
$valid_mkt_count = $valid_mkt.count();
$count_op_order = count($opening_orders);

//

