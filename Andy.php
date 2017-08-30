<?php
include('app.php');
include('TrendChecker.php');
echo 'Start: '.date('Y-m-d H:i:s').'<br/>';

/** GET MarketSummaries**/
echo 'SelectMarkets: '.date('Y-m-d H:i:s').'<br/>';
include('getMarketSummaries.php');
//GetHighestVolumeMarkets($dbclient);

$SelectedMarkets = $dbclient->coins->SelectedMarkets;
$SelectedMarkets->drop();
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

        $SelectedMarkets = $dbclient->coins->SelectedMarkets;
        $SelectedMarkets->InsertOne(array('MarketName'=>$MarketName));
    }
echo 'GetTicks: '.date('Y-m-d H:i:s').'<br/>';
include('getSelectedMarketTicks.php');
echo 'CheckTrends: '.date('Y-m-d H:i:s').'<br/>';
checkCandle($dbclient,$Markets,$bittrex);
echo 'Finished: '.date('Y-m-d H:i:s').'<br/>';
//}



?>