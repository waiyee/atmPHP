<?php
include('app.php');
require_once 'library/devbittrexapi.php';
use atm\devbittrex\DevClient;
$devbittrex = new DevClient();
echo 'Start: '.date('Y-m-d H:i:s').'<br/>';
/** Seller
 *  1. Get non-complete buying orders from DB
 *  2. Check order complete or not via API
 *  3. If completed, update DB for buy order
 *      3.1. Use API to place sell limit order
 *      3.2. Update place sell order in DB
 *  4. Get selling orders that can't match rate for already TIMETOSELL hours and sell at market price
 */


//$oid = $_POST['oid'];
//$oid = '59a589df44cf4836100034ff';
$temp =$dbclient->coins->tempOpeningOrders;
$temp->drop();
$opening_orders = $devbittrex->getOpenOrders();
$api_status = '';

$return='';


$OOB = $dbclient->coins->OwnOrderBook;

//$order = $OB->findOne(array('_id'=>  new \MongoDB\BSON\ObjectID($oid)));


if($opening_orders) {
    $opening_uuid = array();
    foreach ($opening_orders as $opening_order) {
        $dump = array_push($opening_uuid, $opening_order->OrderUuid);
    }

    $buying_orders = $OOB->find(
        array('Status' => 'buying'),
        array('BuyOrder.uuid' => 1, '_id' => 0)
    );

    $buying_uuid = array();
    foreach ($buying_orders as $buying_order) {
        $dump = array_push($buying_uuid, $buying_order->BuyOrder->uuid);
    }

    $bought_orders = array_diff($buying_uuid, $opening_uuid);

    if ($bought_orders) {
        BoughtOrders($bought_orders, $api_status, $dbclient);
        foreach ($bought_orders as $order) {

            $handling_order = $dbclient->coins->OwnOrderBook->findOne(array('BuyOrder.uuid' => $order));

            $uuid = $handling_order->BuyOrder->uuid;
            $type = 'buy';
            $print_rate = number_format($handling_order->BuyOrder->Rate, 8);
            //API
            $sell_result = $devbittrex->sellLimit($handling_order->MarketName, $handling_order->SellOrder->Quantity, $handling_order->SellOrder->Rate);
            if ($sell_result->uuid) {
                // Insert order into db
                sellLimitDB($handling_order->_id, $sell_result->uuid, $api_status, $dbclient);

                $return = '[' . date('Y-m-d H:i:s') . '] ' . $handling_order->MarketName . ' Place sell order at rate ' . number_format($handling_order->SellOrder->Rate, 8) . '<br/>';
            }

            echo $return;
        }
    }
}

// Get selling orders that can't match rate for already TIMETOSELL hours
$time_orders = $OOB->find(
    array('Status'=>'selling'),
    array("SellOrder.OrderTime" => array('$lte'=>new MongoDB\BSON\UTCDateTime(microtime(true) * 1000 - (TIMETOSELL*60*60*1000))))
);

foreach($time_orders as $time_order){
    /*
     * check time
check status
cancel opened sell
place new market sell
Record data
     */
    var_dump($time_order->SellOrder);
    $cancel_result = $devbittrex->cancel($time_order->SellOrder->uuid);

    echo 'Cancel order'  . $time_order->MarketName;
        cancelSellDB($time_order->SellOrder->uuid, $api_status, $dbclient);
        $market_price = $devbittrex->getTicker($time_order->MarketName);
        if($market_price)
        {
            $new_uuid = $devbittrex->buyLimit($time_order->MarketName, $time_oder->SellOrder->Quantity, $market_price->last);
            updateSellDB ($time_order->_id, $new_uuid->uuid, $api_status, $market_price->last, $time_oder->SellOrder->Quantity, $dbclient);
        }
}

echo 'End: '.date('Y-m-d H:i:s').'<br/>';