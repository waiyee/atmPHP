<?php
/** DEV
 * Bittrex API wrapper class
 */
namespace atm\devbittrex;
class DevClient
{
	private $baseUrl;
	private $apiVersion = 'v1.1';
	private $apiKey ='b16a576da6ce4c98a228a9c8fb358a9d';
	private $apiSecret = '311cf541a233410c8f8d84d1a0e03d96';
	private $dbclient;

	public function __construct ()
	{
		$this->baseUrl   = 'https://bittrex.com/api/'.$this->apiVersion.'/';
        $this->dbclient = new \MongoDB\Client("mongodb://localhost:27017");
	}
	/**
	 * Invoke API
	 * @param string $method API method to call
	 * @param array $params parameters
	 * @param bool $apiKey  use apikey or not
	 * @return object
	 */
	private function call ($method, $params = array(), $apiKey = false)
	{
		try
		{
             /*   $uri  = $this->baseUrl.$method;
                if ($apiKey == false) {
                    $params['apikey'] = $this->apiKey;
                    $params['nonce']  = time();
                }

                if (!empty($params)) {
                    $uri .= '?'.http_build_query($params);
                }

                $sign = hash_hmac ('sha512', $uri, $this->apiSecret);

                $ch = curl_init ($uri);

                curl_setopt ($ch, CURLOPT_HTTPHEADER, array('apisign: '.$sign));

                curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);

                $result = curl_exec($ch);
                $answer = json_decode($result);
            */
         $uuid = hash("md5", time().rand(10,9999999999));

        switch ($method)
        {
            case 'market/buylimit':
                $result = '{
                                "success" : true,
                                "message" : "",
                                "result" : {
                                        "uuid" : "'.$uuid.'"
                                    }
                            }';
                break;
            case 'market/selllimit':
                $result = '{
                                    "success" : true,
                                    "message" : "",
                                    "result" : {
                                            "uuid" : "'.$uuid.'"
                                        }
                                   }';

                break;
            case 'account/getorder':
                $OD = $this->dbclient->coins->OwnOrderBook;

                $ownOrder = $OD->findOne(
                    array('$or'=>
                        array(array('BuyOrder.uuid'=>$params['uuid']), array('SellOrder.uuid'=>$params['uuid']))
                ));

                if ($ownOrder->Status == 'buying' or $ownOrder->Status == 'selling')
                {
                    $uri  = $this->baseUrl.'public/getorderbook';
                    $params['market'] = $ownOrder->MarketName;
                    if ($ownOrder->Status == 'buying')
                        $params['type'] = 'sell';
                    else
                        $params['type'] = 'buy';
                    if (!empty($params)) {
                        $uri .= '?'.http_build_query($params);
                    }

                    $sign = hash_hmac ('sha512', $uri, $this->apiSecret);
                    $ch = curl_init ($uri);
                    curl_setopt ($ch, CURLOPT_HTTPHEADER, array('apisign: '.$sign));
                    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
                    $result = curl_exec($ch);
                    $answer = json_decode($result);
                    $success = false;
                    $quantity = 0;
                    $rate = 0;

                    if ($answer->success == true) {
                        $closest_rate = $answer->result[0]->Rate;

                        if ($ownOrder->Status =='buying' && $ownOrder->BuyOrder->Rate >= $closest_rate )
                        {
                            $success = true;
                            $quantity = $answer->result[0]->Quantity;
                            $rate = $answer->result[0]->Rate;
                        }

                        if ($ownOrder->Status =='selling' && $ownOrder->SellOrder->Rate <= $closest_rate )
                        {
                            $success = true;
                            $quantity = $answer->result[0]->Quantity;
                            $rate = $answer->result[0]->Rate;
                        }


                        if ($success)
                        {
                            $result = '{
                                                        "success" : true,
                                                        "message" : "",
                                                        "result" : {
                                                            "AccountId" : null,
                                                            "OrderUuid" : "'.$params['uuid'].'",
                                                            "Exchange" : "'.$params['market'].'",
                                                            "Type" : "LIMIT_'.strtoupper($params['type']).'",
                                                            "Quantity" : '.$quantity.',
                                                            "QuantityRemaining" : 0.00000000,
                                                            "Limit" : 0.00000001,
                                                            "Reserved" : 0.00001000,
                                                            "ReserveRemaining" : 0.00001000,
                                                            "CommissionReserved" : 0.00000002,
                                                            "CommissionReserveRemaining" : 0.00000002,
                                                            "CommissionPaid" : 0.00000000,
                                                            "Price" : '.$rate.',
                                                            "PricePerUnit" : null,
                                                            "Opened" : "2014-07-13T07:45:46.27",
                                                            "Closed" : "2014-07-13T07:45:46.27",
                                                            "IsOpen" : false,
                                                            "Sentinel" : "6c454604-22e2-4fb4-892e-179eede20972",
                                                            "CancelInitiated" : false,
                                                            "ImmediateOrCancel" : false,
                                                            "IsConditional" : false,
                                                            "Condition" : "NONE",
                                                            "ConditionTarget" : null
                                                        }
                                                    }';
                        }
                        else
                        {
                            $result = '{
                                                        "success" : true,
                                                        "message" : "",
                                                        "result" : {
                                                            "AccountId" : null,
                                                            "OrderUuid" : "'.$params['uuid'].'",
                                                            "Exchange" : "'.$params['market'].'",
                                                            "Type" : "LIMIT_'.strtoupper($params['type']).'",
                                                            "Quantity" : '.$quantity.',
                                                            "QuantityRemaining" : 0.00000000,
                                                            "Limit" : 0.00000001,
                                                            "Reserved" : 0.00001000,
                                                            "ReserveRemaining" : 0.00001000,
                                                            "CommissionReserved" : 0.00000002,
                                                            "CommissionReserveRemaining" : 0.00000002,
                                                            "CommissionPaid" : 0.00000000,
                                                            "Price" : '.$rate.',
                                                            "PricePerUnit" : '.$closest_rate.',
                                                            "Opened" : "2014-07-13T07:45:46.27",
                                                            "Closed" : null,
                                                            "IsOpen" : true,
                                                            "Sentinel" : "6c454604-22e2-4fb4-892e-179eede20972",
                                                            "CancelInitiated" : false,
                                                            "ImmediateOrCancel" : false,
                                                            "IsConditional" : false,
                                                            "Condition" : "NONE",
                                                            "ConditionTarget" : null
                                                        }
                                                    }';

                        }
                    }
                }
                break;
        }

        if (strlen($result)>0)
            $answer = json_decode($result);

		return $answer->result;
		}catch(Exception $e)
		{
			trigger_error(sprintf('Curl failed with error #%d: %s;', 
			$e->getCode(), $e->getMessage()), 
			E_USER_ERROR
			);
			
		}
	}
	/**
	 * Get the open and available trading markets at Bittrex along with other meta data.
	 * @return array
	 */
	public function getMarkets ()
	{
		return $this->call ('public/getmarkets');
	}
	/**
	 * Get all supported currencies at Bittrex along with other meta data.
	 * @return array
	 */
	public function getCurrencies ()
	{
		return $this->call ('public/getcurrencies');
	}
    /**
     * Get the current tick values for a market.
     * @param string $market	literal for the market (ex: BTC-LTC)
     * @return array
     */
    public function getTicker ($market)
    {
        return $this->call ('public/getticker', array('market' => $market));
    }
	/**
	 * Get the current tick values for a market.
	 * @param string $market	literal for the market (ex: BTC-LTC)
	 * @param string $tickInterval ["oneMin", "fiveMin", "thirtyMin", "hour", "day"]
	 * @return array
	 */
	public function getTicks ($market, $tickInterval)
	{
		$params = array (
			'marketName' => $market,
			'tickInterval'   => $tickInterval			
		);
		$this->apiVersion = 'v2.0'; //Only v2.0 suuport
		$this->baseUrl   = 'https://bittrex.com/api/'.$this->apiVersion.'/';	
		$temp =  $this->call ('pub/market/getticks', $params);
		$this->apiVersion = 'v1.1';
		$this->baseUrl   = 'https://bittrex.com/api/'.$this->apiVersion.'/';	
		return $temp;
	}
	/**
	 * Get the last 24 hour summary of all active exchanges
	 * @return array
	 */
	public function getMarketSummaries ()
	{
		return $this->call ('public/getmarketsummaries');
	}
	/**
	 * Get the last 24 hour summary of all active exchanges
	 * @param string $market literal for the market (ex: BTC-LTC)
	 * @return array
	 */
	public function getMarketSummary ($market)
	{
		return $this->call ('public/getmarketsummary', array('market' => $market));
	}
	/**
	 * Get the orderbook for a given market
	 * @param string $market  literal for the market (ex: BTC-LTC)
	 * @param string $type	  "buy", "sell" or "both" to identify the type of orderbook to return
	 * @param integer $depth  how deep of an order book to retrieve. Max is 50.
	 * @return array
	 */
	public function getOrderBook ($market, $type, $depth = 20)
	{
		$params = array (
			'market' => $market,
			'type'   => $type,
			'depth'  => $depth
		);
		return $this->call ('public/getorderbook', $params);
	}
	/**
	 * Get the latest trades that have occured for a specific market
	 * @param string $market  literal for the market (ex: BTC-LTC)
	 * @param integer $count  number of entries to return. Max is 50.
	 * @return array
	 */
	public function getMarketHistory ($market, $count = 20)
	{
		$params = array (
			'market' => $market,
			'count'  => $count
		);
		return $this->call ('public/getmarkethistory', $params);
	}
	/**
	 * Place a limit buy order in a specific market. 
	 * Make sure you have the proper permissions set on your API keys for this call to work
	 * @param string $market  literal for the market (ex: BTC-LTC)
	 * @param float $quantity the amount to purchase
	 * @param float $rate     the rate at which to place the order
	 * @return array
	 */
	public function buyLimit ($market, $quantity, $rate)
	{
		$params = array (
			'market'   => $market,
			'quantity' => $quantity,
			'rate'     => $rate
		);
		return $this->call ('market/buylimit', $params, true);
	}

    /**
     * Place a buy order in a specific market.
     * Make sure you have the proper permissions set on your API keys for this call to work
     * @param string $market  literal for the market (ex: BTC-LTC)
     * @param float $quantity the amount to purchase
     * @return array
     */
	public function buyMarket ($market, $quantity)
	{
		$params = array (
			'market'   => $market,
			'quantity' => $quantity
		);
		return $this->call ('market/buymarket', $params, true);
	}
	/**
	 * Place a limit sell order in a specific market. 
	 * Make sure you have the proper permissions set on your API keys for this call to work
	 * @param string $market  literal for the market (ex: BTC-LTC)
	 * @param float $quantity the amount to sell
	 * @param float $rate     the rate at which to place the order
	 * @return array
	 */
	public function sellLimit ($market, $quantity, $rate)
	{
		$params = array (
			'market'   => $market,
			'quantity' => $quantity,
			'rate'     => $rate
		);
		return $this->call ('market/selllimit', $params, true);
	}

	/**
	 * Place a sell order in a specific market. 
	 * Make sure you have the proper permissions set on your API keys for this call to work
	 * @param string $market  literal for the market (ex: BTC-LTC)
	 * @param float $quantity the amount to sell
	 * @return array
	 */
	public function sellMarket ($market, $quantity)
	{
		$params = array (
			'market'   => $market,
			'quantity' => $quantity
		);
		return $this->call ('market/sellmarket', $params, true);
	}
	/**
	 * Cancel a buy or sell order 
	 * @param string $uuid id of sell or buy order
	 * @return array
	 */
	public function cancel ($uuid)
	{
		$params = array ('uuid' => $uuid);
		return $this->call ('market/cancel', $params, true);
	}
	/**
	 * Get all orders that you currently have opened. A specific market can be requested
	 * @param string $market  literal for the market (ex: BTC-LTC)
	 * @return array
	 */
	public function getOpenOrders ($market = null)
	{
		$params = array ('market' => $market);
		return $this->call ('market/getopenorders', $params, true);
	}
	/**
	 * Retrieve all balances from your account
	 * @return array
	 */
	public function getBalances ()
	{
		return $this->call ('account/getbalances', array(), true);
	}
	/**
	 * Retrieve the balance from your account for a specific currency
	 * @param string $currency literal for the currency (ex: LTC)
	 * @return array
	 */
	public function getBalance ($currency)
	{
		$params = array ('currency' => $currency);
		return $this->call ('account/getbalance', $params, true);
	}
	/**
	 * Retrieve or generate an address for a specific currency. If one 
	 * does not exist, the call will fail and return ADDRESS_GENERATING 
	 * until one is available.
	 * @param string $currency literal for the currency (ex: LTC)
	 * @return array
	 */
	public function getDepositAddress ($currency)
	{
		$params = array ('currency' => $currency);
		return $this->call ('account/getdepositaddress', $params, true);
	}
	/**
	 * Withdraw funds from your account. note: please account for txfee.
	 * @param string $currency  literal for the currency (ex: LTC)
	 * @param float $quantity   the quantity of coins to withdraw
	 * @param float $address    the address where to send the funds
	 * @param float $paymentid  (optional) used for CryptoNotes/BitShareX/Nxt optional field (memo/paymentid)
	 * @return array
	 */
	public function withdraw ($currency, $quantity, $address, $paymentid = null)
	{
		$params = array (
			'currency' => $currency,
			'quantity' => $quantity,
			'address'  => $address,
		);
		
		if ($paymentid) {
			$params['paymentid'] = $paymentid;
		}
		
		return $this->call ('account/withdraw', $params, true);
	}
	/**
	 * Retrieve a single order by uuid
	 * @param string $uuid 	the uuid of the buy or sell order
	 * @return array
	 */
	public function getOrder ($uuid)
	{
		$params = array ('uuid' => $uuid);
		return $this->call ('account/getorder', $params, true);
	}

	/**
	 * Retrieve your order history
	 * @param string $market  (optional) a string literal for the market (ie. BTC-LTC). If ommited, will return for all markets
	 * @param integer $count  (optional) the number of records to return
	 * @return array
	 */
	public function getOrderHistory ($market = null, $count = null)
	{
		$params = array ();
		if ($market) {
			$params['market'] = $market;
		}
		if ($count) {
			$params['count'] = $count;
		}
		return $this->call ('account/getorderhistory', $params, true);
	}
	/**
	 * Retrieve your withdrawal history
	 * @param string $currency  (optional) a string literal for the currecy (ie. BTC). If omitted, will return for all currencies
	 * @param integer $count    (optional) the number of records to return
	 * @return array
	 */
	public function getWithdrawalHistory ($currency = null, $count = null)
	{
		$params = array ();
		if ($currency) {
			$params['currency'] = $currency;
		}
		if ($count) {
			$params['count'] = $count;
		}
		return $this->call ('account/getwithdrawalhistory', $params, true);
	}
	/**
	 * Retrieve your deposit history
	 * @param string $currency  (optional) a string literal for the currecy (ie. BTC). If omitted, will return for all currencies
	 * @param integer $count    (optional) the number of records to return
	 * @return array
	 */
	public function getDepositHistory ($currency = null, $count = null)
	{
		$params = array ();
		if ($currency) {
			$params['currency'] = $currency;
		}
		if ($count) {
			$params['count'] = $count;
		}
		return $this->call ('account/getdeposithistory', $params, true);
	}
}
