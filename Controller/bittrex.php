<?php 

function refreshMarkets($b, $dbc)
{
	try {	
	$obj = $b->getMarkets();
	//var_dump($obj);
	$collection = $dbc->coins->markets;
	
	$collection->drop();
	$result = $collection->insertMany($obj );
	return true;
	}
	catch(Exception $e)
	{
		trigger_error(sprintf('Refresh with error #%d: %s;', 
			$e->getCode(), $e->getMessage()), 
			E_USER_ERROR
			);
	}
	
}

function refreshCurrencies($b, $dbc)
{
	try {
		$obj = $b->getCurrencies();
		$collection = $dbc->coins->currencies;
		$collection->drop();
		$result = $collection->insertMany($obj );
		return true;
	}
	catch(Exception $e)
	{
		trigger_error(sprintf('Refresh with error #%d: %s;', 
			$e->getCode(), $e->getMessage()), 
			E_USER_ERROR
			);
	}
}

function refreshMarketSummaries($b, $dbc)
{
    try {
        $obj = $b->getMarketSummaries();
        //var_dump($obj);
        $collection = $dbc->coins->MarketSummaries;

        $collection->drop();
        $result = $collection->insertMany($obj );
        return true;
    }
    catch(Exception $e)
    {
        trigger_error(sprintf('Refresh with error #%d: %s;',
            $e->getCode(), $e->getMessage()),
            E_USER_ERROR
        );
    }

}

function updateWallet($b, $dbc)
{
    //try{
        $obj = $b->getbalances();
        $collection = $dbc->coins->WalletBalance;
        $collection->drop();
        $result = $collection->insertMany($obj );
        return true;
    /*}
    catch(Exception $e)
    {
        trigger_error(sprintf('Refresh with error #%d: %s;',
            $e->getCode(), $e->getMessage()),
            E_USER_ERROR
        );
    }*/
}