<?php

	include('app.php');
	
	$collection = $dbclient->coins->markets; //Database market
	$sum = $dbclient->coins->MarketSummaries; //Database Summary 
	

	$MarkSum = $bittrex->getMarketSummaries();
	$i = 1;
	foreach ($MarkSum as $doc) {
		$MarketName = $doc->MarketName; // Each market @ API
		echo 'Start[.'.$i.']--'.$MarketName.' '.date('Y-m-d H:i:s').'<br/>';
		
		$market = $collection->findone(['MarketName'=>$MarketName]); //use API market name to get a id
		
		if ($market)
		{

			$dbMarkSum = $sum->FindOne(array('MarketName'=>$MarketName));
			if (!$dbMarkSum)
			{
				
				$result = $sum->InsertOne(array('_id'=>$market->_id,'MarketName'=>$MarketName, 'Summaries'=>array($doc)));
				
			}
			else
			{
				if (isset($dbMarkSum['Summaries'])) {
					$summaries = $dbMarkSum['Summaries'];
				}else{
					$summaries = array();
				}	
				
				
				$temp = json_decode(json_encode($summaries));

                $t= array_merge($temp, array($doc));

				$sum->updateOne(array('_id'=>$market->_id), array('$set'=>array('Summaries'=>$t)));
			}
			
		//$MarkGrp = array('Summaries'=>$MarkSum, 'Histories'=>$MarkHist, 'OrderBookBuy'=>array_slice($MarkOB->buy,0,100), 'OrderBookSell'=>array_slice($MarkOB->sell,0,100));
			
			//$result = $c2->insertMany($MarkGrp);
			echo 'End[.'.$i++.']-- '.date('Y-m-d H:i:s').'<br/><br/>';
		}
		else
		{
			echo 'Market '.$MarketName.' Not Exsit<br/>';
		}
		
	}