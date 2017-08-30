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

$OOB = $dbclient->coins->OwnOrderBook;
$selling_orders = $OOB->find(array('Status'=>'selling'));

// API get all opening orders
$opening_orders = $devbittrex->getOpenOrders();

if($opening_orders) {
    $dbclient->coins->InsertMany($opening_orders);
}
