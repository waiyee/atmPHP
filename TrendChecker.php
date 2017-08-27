<?php
include('app.php');

$Market = 'BTC-NEO';
$days = 14;
$collection = $dbclient->coins->MarketTicks_30min;
$ops =
    array(
        array('$match' => array('MarketName' => $Market)),
        array('$unwind' => '$Ticks'),
        array('$sort' => array('Ticks.T' => -1)),
        array('$limit' => $days+1),
        array('$sort' => array('Ticks.T' => 1))
    );
$result = $collection->aggregate($ops);
//print_r($result);

$O = array();
$H = array();
$L = array();
$C = array();
foreach ($result as $item){
    $O[] = $item->Ticks->O;
    $H[] = $item->Ticks->H;
    $L[] = $item->Ticks->L;
    $C[] = $item->Ticks->C;
}
/*
print_r($O);
echo '<br>';
print_r($H);
echo '<br>';
print_r($L);
echo '<br>';
print_r($C);
echo '<br>';
*/
echo 'Three Line Strike: ';
$cdl3linestrike = trader_cdl3linestrike($O,$H,$L,$C);
echo $cdl3linestrike[$days];
echo '<br>';

echo 'Three Outside Up/Down: ';
$cdl3outside = trader_cdl3outside($O,$H,$L,$C);
echo $cdl3outside[$days];
echo '<br>';


?>