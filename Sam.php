<?php
include('app.php');
/*require_once 'library/devbittrexapi.php';
use atm\devbittrex\DevClient;
$devbittrex = new DevClient();*/
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

// $opening_orders = $bittrex->getOpenOrders();
$api_status = '';

$return='';


$OOB = $dbclient->coins->OwnOrderBook;

//$order = $OB->findOne(array('_id'=>  new \MongoDB\BSON\ObjectID($oid)));

$bought_orders = $OOB->find(array('Status'=>'bought'));

foreach ($bought_orders as $bought_order)
{
    $uuid = $bought_order->BuyOrder->uuid;
    $type = 'buy';
    $print_rate = number_format($bought_order->BuyOrder->Rate, 8);

    //empty the wallet, config SELLALL
    $Qty = $bought_order->SellOrder->Quantity;

    if (SELLREMAIN == 1){
        $balance = $bittrex->getBalance($bought_order->MarketCurrency);
        $Qty = $balance->Available;

        //UPDATE OwnOrderBook
        $OOB->UpdateOne(array('_id'=>$bought_order->_id), array('$set'=>array(
            'SellOrder.Quantity' => round($Qty, 8),
            'SellOrder.Total' => round($bought_order->SellOrder->Rate * $Qty, 8),
            'SellOrder.Fee' => round(( $bought_order->SellOrder->Rate * $Qty ) * TXFEE, 8)
        )));
    }

    //API
    $sell_result = $bittrex->sellLimit($bought_order->MarketName, $Qty, $bought_order->SellOrder->Rate);
    if (!empty($sell_result->uuid)) {
        // Insert order into db
        sellLimitDB($bought_order->_id, $sell_result->uuid, $api_status, $dbclient);

        $return = '[' . date('Y-m-d H:i:s') . '] ' . $bought_order->MarketName . ' Place sell order at rate ' . number_format($bought_order->SellOrder->Rate, 8) . '<br/>';
    }

    echo $return;
}

/*
if($opening_orders) {

    $opening_uuid = array();

    foreach ($opening_orders as $opening_order) {
        $dump = array_push($opening_uuid, $opening_order->OrderUuid);
    }

    $buying_orders = $OOB->find(

        array('Status' => array('$in' =>array('bought', 'buying'))),
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

            //empty the wallet, config SELLALL
            $Qty = $handling_order->SellOrder->Quantity;

            if (SELLREMAIN == 1){
                $balance = $bittrex->getBalance($handling_order->MarketCurrency);
                $Qty = $balance->Available;

                //UPDATE OwnOrderBook
                $OOB->UpdateOne(array('_id'=>$handling_order->_id), array('$set'=>array(
                    'SellOrder.Quantity' => round($Qty, 8),
                    'SellOrder.Total' => round($handling_order->SellOrder->Rate * $Qty, 8),
                    'SellOrder.Fee' => round(( $handling_order->SellOrder->Rate * $Qty ) * TXFEE, 8)
                )));
            }

            //API
            $sell_result = $bittrex->sellLimit($handling_order->MarketName, $Qty, $handling_order->SellOrder->Rate);
            if ($sell_result->uuid) {
                // Insert order into db
                sellLimitDB($handling_order->_id, $sell_result->uuid, $api_status, $dbclient);

                $return = '[' . date('Y-m-d H:i:s') . '] ' . $handling_order->MarketName . ' Place sell order at rate ' . number_format($handling_order->SellOrder->Rate, 8) . '<br/>';
            }

            echo $return;
        }
    }
}
*/
// Get selling orders that can't match rate for already TIMETOSELL hours


echo 'End: '.date('Y-m-d H:i:s').'<br/>';