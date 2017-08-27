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
function buyLimitDB ($uuid, $market, $quantity, $buy_rate, $db)
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
            'OrderTime' => new \MongoDB\BSON\UTCDateTime(),
            'CompleteTime' => null
        ),
        'SellOrder' => array(
            'uuid'       => null,
            'Quantity' => $quantity,
            'Rate'     => $sell_rate,
            'Fee'      => $sell_fee,
            'Total'    => $sell_total,
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
 *  Settle buy order
 * @param $id   string own DB ID
 * @param $db DB Client
 * @return mixed
 */
function boughtOrder($id, $db)
{
    $OB = $db->coins->OwnOderBook;

    $result = $OB->UpdateOne(array('_id'=>$id), array('$set'=>array('Status'=>'bought', 'update_at'=> new \MongoDB\BSON\UTCDateTime(), 'BuyOrder.CompleteTIme'=>  new \MongoDB\BSON\UTCDateTime())));
    return $result->getModifiedCount();
}

/**
 *  Settle sell order
 * @param $id   string own DB ID
 * @param $db DB Client
 * @return mixed
 */
function SoldOrder($id, $db)
{
    $OB = $db->coins->OwnOderBook;

    $result = $OB->UpdateOne(array('_id'=>$id), array('$set'=>array('Status'=>'sold', 'update_at'=> new \MongoDB\BSON\UTCDateTime(), 'SellOrder.CompleteTIme'=>  new \MongoDB\BSON\UTCDateTime())));
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
function sellLimitDB ($_id, $uuid, $db)
{
    $OB = $db->coins->OwnOderBook;


    $result = $OB->UpdateOne(array('_id'=>$_id), array('$set'=>array('SellOrder.uuid'=>$uuid,'SellOrder.OrderTime'=>new \MongoDB\BSON\UTCDateTime(),
        'status'=>'selling', 'updated_at' => new \MongoDB\BSON\UTCDateTime()
        )));
    return $result->getModifiedCount();
    //return $this->call ('market/buylimit', $params, true);
}