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
$cursor = $collection->find([]);
foreach ($cursor as $doc) {

    echo 'Start--'.date('Y-m-d H:i:s').'<br/>';
    $Market = $doc->MarketName;

    $result = $bittrex->getTicker($Market);

    $bid = number_format($result->Bid,8);
    $ask = number_format($result->Ask,8);
    $last = number_format($result->Last,8);

    if( $bid == 0 || $ask == 0){
        echo $Market.' cancelled.';
        continue;
    }

    $spread = number_format($ask-$bid,8);
    $spreadperc = ($ask-$bid)/$ask;
    $buy = number_format($bid+$increment,8);
    $sell = number_format($ask-$increment,8);
    $aspread = $sell-$buy;
    $aspreadperc = $aspread/($ask-$increment);

    $qty = round($btc / $buy,8);

    $estcost = number_format($qty*$buy*(1+$TX),8);
    $estreturn = number_format($qty*$sell*(1-$TX),8);
    $profit = number_format($estreturn-$estcost,8);


    if ($aspreadperc > $from && $aspreadperc < $to && $aspread > $minspread && $profit > 0){
        echo $Market;
        echo '<br>Spread: '.(string)number_format($spread,8);
        echo '<br>Spread%: '.(string)number_format($spreadperc,8);
        echo '<br>ASpread: '.(string)number_format($aspread,8);
        echo '<br>ASpread%: '.(string)number_format($aspreadperc,8);
        echo '<br>Buy@'.$buy;
        echo '<br>EstCost@'.$estcost;
        echo '<br>Sell@'.$sell;
        echo '<br>EstReturn@'.$estreturn;
        echo '<br>EstProfit@'.$profit;
    }
    else{
        echo $Market.' skiped';
    }

    echo '<br>End--'.date('Y-m-d H:i:s').'<br/><br/>';
    //echo '<br><br>';

}






?>