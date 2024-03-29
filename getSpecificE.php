<?php
	include('app.php');
$collection = $dbclient->coins->MarketSummaries;

/*

db.MarketDayTicks.aggregate([
{$unwind:"$Ticks"},
     {$match:{ "Ticks.T":{$gte:  "2017-08-09T00:00:00.000",
        $lte: "2017-08-12T00:00:00.000" }}}
])
db.MarketSummaries.aggregate([
	{ $unwind: '$Summaries' },
	{
       $group:
         {
           _id: "$MarketName",
           Summaries: { $last: "$Summaries" }
         }
     },
     {$match: {_id: /^BTC/}},
 {
   $sort : {"Summaries.OpenBuyOrders":-1}
 }
])

				   */

$ops =  array(
    array(
        '$unwind' => '$Summaries'
    ),
    array(
        '$group' => array('_id'=>'$MarketName', 'Summaries'=>array('$last'=>'$Summaries')),
    ),
    array(
      '$match' => array('_id' =>array('$regex' => 'BTC-'))
    ),
    array(
        '$sort' => array('Summaries.OpenBuyOrders' => -1)
    )

);

$result = $collection->aggregate($ops);
foreach ($result as $doc) {
    echo 'Start--dump'.date('Y-m-d H:i:s').'<br/>';
    var_dump($doc);
    echo 'End--dump'.date('Y-m-d H:i:s').'<br/>';
}
/* //must use single quote
$ops =  array(
	array(
		'$unwind' => '$Ticks'
	),
	array(
		'$match' => array('Ticks.T'=>array('$gte'=> '2017-08-19T00:00:00.000', '$lte'=>'2017-08-24T00:00:00.000')),
	)
		
		);

echo 'Start--running '.date('Y-m-d H:i:s').'<br/>';
$result = $collection->aggregate($ops);
echo 'End--running '.date('Y-m-d H:i:s').'<br/>';
foreach ($result as $doc) {
	echo 'Start--dump'.date('Y-m-d H:i:s').'<br/>'; 
	var_dump($doc);
	echo 'End--dump'.date('Y-m-d H:i:s').'<br/>'; 
}*/