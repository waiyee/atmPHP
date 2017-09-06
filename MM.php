<?php
include('app.php');

//$Market = 'BTC-UNB';
$increment = 0.00000001;
$minspread = 0.0000001;
$from = 0.006;
$to = 0.05;
$btc = 0.0005;
$TX = 0.0025;

$collection = $dbclient->coins->markets;
$cursor = $collection->find(array('BaseCurrency'=>'BTC'));
foreach ($cursor as $doc) {

    echo 'Start--'.date('Y-m-d H:i:s').'<br/>';
    $Market = $doc->MarketName;

    //Check order exists
    $exits_now = $dbclient->coins->OwnOrderBook->findOne(
        array('$and' =>
            array(
                array('MarketName' => $Market),
                array('$or' =>
                    array(
                        array('Status' => 'buying'), array('Status' => 'selling'), array('Status' => 'bought')
                    )
                )
            ))

    );
    if (!empty($exits_now)) {
        echo $Market.' order exists.<br/>';
        echo '<br>End--'.date('Y-m-d H:i:s').'<br/><br/>';
        continue;
    }

    $result = $bittrex->getTicker($Market);

    $bid = round($result->Bid,8);
    $ask = round($result->Ask,8);
    $last = round($result->Last,8);

    if( $bid == 0 || $ask == 0){
        echo $Market.' cancelled.<br/>';
        continue;
    }

    $spread = round($ask-$bid,8);
    $spreadperc = ($ask-$bid)/$ask;
    $buy = round($bid+$increment,8); //buy rate
    $sell = round($ask-$increment,8); //sell rate
    $aspread = $sell-$buy;
    $aspreadperc = $aspread/$sell;

    $quantity = round($btc / $buy,8); //buy qty

    $estcost = number_format($quantity*$buy*(1+$TX),8);
    $estreturn = number_format($quantity*$sell*(1-$TX),8);
    $profit = number_format($estreturn-$estcost,8);


    if ($aspreadperc > $from && $aspreadperc < $to && $aspread > $minspread && $profit > 0){
        echo $Market;
        echo '<br>Spread: '.number_format($spread,8);
        echo '<br>Spread%: '.number_format($spreadperc,8);
        echo '<br>ASpread: '.number_format($aspread,8);
        echo '<br>ASpread%: '.number_format($aspreadperc,8);
        echo '<br>Buy@'.number_format($buy,8);
        echo '<br>EstCost@'.$estcost;
        echo '<br>Sell@'.number_format($sell,8);
        echo '<br>EstReturn@'.$estreturn;
        echo '<br>EstProfit@'.$profit;

        //Place buy order

        $buy_result = $bittrex->buyLimit($Market, $quantity, $buy);
        if (!empty($buy_result->uuid)) {
            echo '<br>Buy order placed.';
            //Insert DB
            $insert_id = buysellLimitDB($buy_result->uuid, $Market, $quantity, $buy, $sell, $api_status, $dbclient);
        }

    }
    else{
        echo $Market.' skiped';
    }

    echo '<br>End--'.date('Y-m-d H:i:s').'<br/><br/>';


}






?>