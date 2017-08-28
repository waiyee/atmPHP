<?php
include('app.php');
include('TrendChecker.php');
/** GET MarketSummaries**/
require_once('getMarketSummaries.php');
//GetHighestVolumeMarkets($dbclient);


/** GET TOP 15 Volume Markets**/
//function GetHighestVolumeMarkets($dbclient, $top = 15){
    $collection = $dbclient->coins->MarketSummaries;
    //$condition = array('unwind' => '$Summaries');
    //$options = array('$unwind' => '$Summaries', '$sort' => array('Volume' => -1), 'limit' => 15);
$ops =
    array(
        array('$unwind' => '$Summaries'),
        array(
            '$group' => array(
                '_id' => '$MarketName', 'Summaries' => array( '$last' => '$Summaries')
            )
        ),
        array('$match' => array('_id' =>array('$regex' => 'BTC-'),
            'Summaries.BaseVolume' => array('$gt' => 600))),
        array('$sort' => array('Summaries.OpenBuyOrders' => -1)),
        array('$limit' => 15)
    );
$result = $collection->aggregate($ops);
$Markets = array();
    foreach ($result as $item) {
        //var_dump($item);
        //print_r($item);
        $MarketName = $item->_id;
        $Markets[] = $item->_id;
        //echo $item->_id;
        //echo '<br>';
        //TODO: CHECK TRENDS
        /*
        $MarkTicks = $bittrex->getTicks($MarketName, 'thirtyMin');
        $ticks = $dbclient->coins->MarketTicks_30min;
        $dbMarkSum = $ticks->FindOne(array('MarketName'=>$MarketName));
        if (!$dbMarkSum)
        {
            $result = $ticks->InsertOne(array('_id'=>$ticks->_id,'MarketName'=>$MarketName, 'Ticks'=>$MarkTicks));
        }
        else
        {
            if (isset($dbMarkSum['Ticks'])) {
                $Ticks = $dbMarkSum['Ticks'];
            }else{
                $Ticks = array();
            }
            $temp = json_decode(json_encode($Ticks));
            $t= array_replace_recursive($temp, $MarkTicks);
            $ticks->updateOne(array('_id'=>$ticks->_id), array('$set'=>array('Ticks'=>$t)));
        }*/
    }
checkCandle($dbclient,$Markets,$bittrex);

//}



?>