<?php
	include('app.php');
$collection = $dbclient->coins->MarketOneMinTicks;

/*

db.MarketOneMinTicks.aggregate([
                     {$unwind:"$Ticks"},
                     { $group: { _id: "$MarketName", totalH: { $sum: "$Ticks.H" }, totalL: { $sum: "$Ticks.L" } } },
                     { $sort: { totalH: -1 } }
                   ])
				   
				  db.students.aggregate( 
{$unwind:"$tests"},
{$group: { 
       _id:null,
        mathAve: { $avg: "$tests.scores.math"},
       scienceAve: { $avg: "$tests.scores.science" }
    }
})
				   
				   
				   */
 //must use single quote
$ops =  array(
	array(
		'$unwind' => '$Ticks'
	),
    array(
        '$group' => array(
            "_id" => '$MarketName',
            "AvgH" => array('$avg' => '$Ticks.H'),
			"AvgL" => array('$avg' => '$Ticks.L'),
        )
	),
	array(
		'$sort' => array("AvgH" => -1),
	)
		
		);

echo 'Start--running average'.date('Y-m-d H:i:s').'<br/>'; 
$result = $collection->aggregate($ops);
echo 'End--running average'.date('Y-m-d H:i:s').'<br/>'; 
foreach ($result as $doc) {
	echo 'Start--dump'.date('Y-m-d H:i:s').'<br/>'; 
	var_dump($doc);
	echo 'End--dump'.date('Y-m-d H:i:s').'<br/>'; 
}