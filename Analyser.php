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
        $Markets[] = $item->_id;
        //echo $item->_id;
        //echo '<br>';
        //TODO: CHECK TRENDS
    }
checkCandle($dbclient,$Markets);

//}



?>