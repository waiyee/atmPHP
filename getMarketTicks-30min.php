<?php

	include('app.php');
	
	$collection = $dbclient->coins->markets;
	//var_dump($collection);
	$cursor = $collection->find([]);

	/*$MarkSum = $bittrex->getMarketSummary('BTC-LTC');
	$sum = $dbclient->coins->MarketSummaries;
	var_dump(get_object_vars($MarkSum[0]));
	$result = $sum->InsertOne(array('_id'=>1,'MarketName'=>'BTC-LTC', 'Summaries'=>$MarkSum));*/
	//$MarkSum = $bittrex->getMarketSummaries();

	foreach ($cursor as $doc) {
		$MarketName = $doc->MarketName;
		echo 'Start--'.$MarketName.date('Y-m-d H:i:s').'<br/>';

		$MarkTicks = $bittrex->getTicks($MarketName, 'thirtyMin');
		//$MarkOB = $bittrex->getOrderBook($MarketName, 'both');

		$ticks = $dbclient->coins->MarketTicks_30min;
		//$hist = $dbclient->coins->MarketHistoris;
		//$OB = $dbclient->coins->MarketOrderBook;
		
		echo 'Start Insert--'.$doc->_id.$MarketName.date('Y-m-d H:i:s').'<br/>';

		$dbMarkSum = $ticks->FindOne(array('MarketName'=>$MarketName));

		if (!$dbMarkSum)
		{
			$result = $ticks->InsertOne(array('_id'=>$doc->_id,'MarketName'=>$MarketName, 'Ticks'=>$MarkTicks));
			
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
           // var_dump($t);
            $ticks->updateOne(array('_id'=>$doc->_id), array('$set'=>array('Ticks'=>$t)));
		}
		
	//$MarkGrp = array('Summaries'=>$MarkSum, 'Histories'=>$MarkHist, 'OrderBookBuy'=>array_slice($MarkOB->buy,0,100), 'OrderBookSell'=>array_slice($MarkOB->sell,0,100));
		
		//$result = $c2->insertMany($MarkGrp);
		echo 'End--'.date('Y-m-d H:i:s').'<br/><br/>';
		
	}