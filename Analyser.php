<?php
include('app.php');
/** GET MarketSummaries**/
//require_once('getMarketSummaries.php');
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
            array('$sort' => array('Summaries.Volume' => -1))
        ,
        array('$limit' => 15)
    );
$result = $collection->aggregate($ops);
    foreach ($result as $item) {
        //var_dump($item);
        //print_r($item);
        echo $item->_id;
        echo '<br>';

        //TODO: CHECK TRENDS
    }


//}



?>