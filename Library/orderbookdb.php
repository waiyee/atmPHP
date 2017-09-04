<?php
/**
 * Place a limit buy order in a specific market.
 * Update DB buy order, calculate sell price
 * @param  string $uuid uuid from API order ID
 * @param string $market  literal for the market (ex: BTC-LTC)
 * @param float $quantity the amount to purchase
 * @param float $buy_rate     the rate at which to place the order
 * @param DB Client
 * @return boolean
 */
function buyLimitDB ($uuid, $market, $quantity, $buy_rate, $api_status, $db)
{
    $exploeMrt = explode('-', $market);

    $basecur = $exploeMrt[0];
    $marketcur= $exploeMrt[1];

    $buy_fee = ($quantity * $buy_rate) * TXFEE;
    $buy_total = round( ($quantity * $buy_rate) * (1+TXFEE) , 8);

    $expected_sell_total = $buy_total * (1+PROFIT +TXFEE);
    $sell_rate = round( $expected_sell_total / $quantity, 8);
    $sell_fee = ( $sell_rate * $quantity ) * TXFEE;
    $sell_total = round(($sell_rate*$quantity) * (1-TXFEE),8);

    $params = array (
        'MarketName'   => $market,
        'BaseCurrency' => $basecur,
        'MarketCurrency' => $marketcur,
        'Status'   => 'buying',
        'BuyOrder' => array(
            'uuid'       => $uuid,
            'Quantity' => $quantity,
            'Rate'     => $buy_rate,
            'Fee'      => $buy_fee,
            'Total'    => $buy_total,
            'Status'   => $api_status,
            'OrderTime' => new \MongoDB\BSON\UTCDateTime(),
            'CompleteTime' => null
        ),
        'SellOrder' => array(
            'uuid'       => null,
            'Quantity' => $quantity,
            'Rate'     => $sell_rate,
            'Fee'      => $sell_fee,
            'Total'    => $sell_total,
            'Status'    => null,
            'OrderTime' => null,
            'CompleteTime' => null
        ),
        'created_at' => new \MongoDB\BSON\UTCDateTime(),
        'updated_at'=> new \MongoDB\BSON\UTCDateTime()
    );
    $OB = $db->coins->OwnOrderBook;
    $result = $OB->InsertOne($params);

    return $result->getInsertedId();
}

/**
 *  Settle buy orders
 * @param $uuid  Bittrex sell order uuid
 * @param $db DB Client
 * @return mixed
 */
function BoughtOrders($uuid, $api_status, $db)
{
    $OB = $db->coins->OwnOrderBook;


    foreach ($uuid as $i)
    {
        $result = $OB->UpdateOne(array('BuyOrder.uuid'=>$i), array('$set'=>array('Status'=>'bought', 'updated_at'=> new \MongoDB\BSON\UTCDateTime(), 'BuyOrder.CompleteTime'=>  new \MongoDB\BSON\UTCDateTime(), 'BuyOrder.Status'=>$api_status)));

    }

    return $result->getModifiedCount();
}


/**
 *  Settle buy order
 * @param $id   string own DB ID
 * @param $db DB Client
 * @return mixed
 */
function boughtOrder($id, $api_status, $db, $PricePerUnit, $Commission, $Total)
{
    $OB = $db->coins->OwnOrderBook;
    $result = $OB->UpdateOne(array('_id'=>$id), array('$set'=>array(
        'Status'=>'bought',
        'updated_at'=> new \MongoDB\BSON\UTCDateTime(),
        'BuyOrder.CompleteTime'=>  new \MongoDB\BSON\UTCDateTime(),
        'BuyOrder.Status'=>$api_status,
        'BuyOrder.Rate'=>$PricePerUnit,
        'BuyOrder.Fee'=>$Commission,
        'BuyOrder.Total'=>$Total
    )));

    return $result->getModifiedCount();
}

/**
 *  Settle sell orders
 * @param $uuid  Bittrex sell order uuid
 * @param $db DB Client
 * @return mixed
 */
function SoldOrders($uuid, $api_status, $db)
{
    $OB = $db->coins->OwnOrderBook;
    $wallet = $db->coins->WalletBalance->findOne(array('Currency' => 'BTC'));

    foreach ($uuid as $i)
    {
        $result = $OB->UpdateOne(array('SellOrder.uuid'=>$i), array('$set'=>array('Status'=>'sold', 'updated_at'=> new \MongoDB\BSON\UTCDateTime(), 'SellOrder.CompleteTime'=>  new \MongoDB\BSON\UTCDateTime(), 'SellOrder.Status' => $api_status)));
        $order = $OB->findOne(array('SellOrder.uuid'=>$i));
        $db->coins->WalletBalance->UpdateOne(array('Currency' => 'BTC'), array('$set' => array('Available' => $wallet->Balance + $order->SellOrder->Total)));
    }

    return $result->getModifiedCount();
}

/**
 *  Settle sell order
 * @param $id   string own DB ID
 * @param $db DB Client
 * @return mixed
 */
function SoldOrder($id, $api_status, $db, $PricePerUnit, $Commission, $Total)
{
    $OB = $db->coins->OwnOrderBook;

    $result = $OB->UpdateOne(array('_id'=>$id), array('$set'=>array(
        'Status'=>'sold',
        'updated_at'=> new \MongoDB\BSON\UTCDateTime(),
        'SellOrder.CompleteTime'=>  new \MongoDB\BSON\UTCDateTime(),
        'SellOrder.Status' => $api_status,
        'SellOrder.Rate'=>$PricePerUnit,
        'SellOrder.Fee'=>$Commission,
        'SellOrder.Total'=>$Total
    )));
    return $result->getModifiedCount();
}

/**
 * Place a limit sell order in a specific market.
 * Update DB seel order with own ID
 * @param  string $_id own DB ID
 * @param  string $uuid uuid from API order ID
 * @param DB Client
 * @return string
 */
function sellLimitDB ($_id, $uuid, $api_status, $db)
{
    $OB = $db->coins->OwnOrderBook;

    $result = $OB->UpdateOne(array('_id'=>$_id), array('$set'=>array('SellOrder.uuid'=>$uuid,'SellOrder.OrderTime'=>new \MongoDB\BSON\UTCDateTime(), 'SellOrder.Status' => $api_status,
        'Status'=>'selling', 'updated_at' => new \MongoDB\BSON\UTCDateTime()
        )));

    return $result->getModifiedCount();
    //return $this->call ('market/buylimit', $params, true);
}

/*
 * Cancel can't sell order
 */
function cancelSellDB ($uuid, $api_status, $db)
{
    $OB = $db->coins->OwnOrderBook;

    $result = $OB->UpdateOne(array('SellOrder.uuid'=>$uuid), array('$set'=>array(
        'Status'=>'cancel', 'updated_at' => new \MongoDB\BSON\UTCDateTime()
    )));

    return $result->getModifiedCount();
    //return $this->call ('market/buylimit', $params, true);
}

/*
 * Cancel can't buy order
 */
function cancelBuyDB ($uuid, $api_status, $db)
{
    $OB = $db->coins->OwnOrderBook;

    $result = $OB->UpdateOne(array('BuyOrder.uuid'=>$uuid), array('$set'=>array(
        'Status'=>'cancel', 'updated_at' => new \MongoDB\BSON\UTCDateTime()
    )));

    return $result->getModifiedCount();
    //return $this->call ('market/buylimit', $params, true);
}

/*
 * update sell order
 */
function updateSellDB ($_id, $uuid, $api_status, $rate, $quantity, $db)
{
    $OB = $db->coins->OwnOrderBook;

    $sell_fee = ($quantity * $rate) * TXFEE;
    $sell_total = round( ($quantity * $rate) * (1+TXFEE) , 8);

    $result = $OB->UpdateOne(array('_id'=>$_id), array('$set'=>array('SellOrder.uuid'=>$uuid,'SellOrder.OrderTime'=>new \MongoDB\BSON\UTCDateTime(), 'SellOrder.Status' => $api_status,'SellOrder.Rate' => $rate, 'SellOrder.Quantity' => $quantity,
        'SellOrder.Fee'=>$sell_fee, 'SellOrder.Total'=> $sell_total, 'Status'=>'selling', 'updated_at' => new \MongoDB\BSON\UTCDateTime()
    )));

    return $result->getModifiedCount();
    //return $this->call ('market/buylimit', $params, true);
}

/**
 * Update order price only
 */
function updatePriceDB ($_id, $buy_rate, $buy_fee, $buy_total, $sell_rate,$sell_fee, $sell_total, $db)
{
    $OB = $db->coins->OwnOrderBook;
    $result = $OB->UpdateOne(array('_id'=>$_id), array('$set'=>array('BuyOrder.Rate'=> $buy_rate, 'BuyOrder.Fee'=>$buy_fee, 'BuyOrder.Total'=>$buy_total,'SellOrder.Rate' => $sell_rate, 'SellOrder.Fee' => $sell_fee,
        'SellOrder.Total'=> $sell_total,  'updated_at' => new \MongoDB\BSON\UTCDateTime()
    )));

    return $result->getModifiedCount();
}
