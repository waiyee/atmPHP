<?php
include('app.php');

function checkCandle($dbclient, $Markets,$bittrex)
{
//$Market = 'BTC-NEO';
    //$Markets = array('BTC-NEO', 'BTC-ETH', 'BTC-BCC', 'BTC-LSK', 'BTC-XMR', 'BTC-OMG');
    $interval = 14;
    $sum = 0;
    //$collection = $dbclient->coins->MarketTicks_30min;
    $collection = $dbclient->coins->MarketDayTicks;

    foreach ($Markets as $Market) {
        $ops =
            array(
                array('$match' => array('MarketName' => $Market)),
                array('$unwind' => '$Ticks'),
                array('$sort' => array('Ticks.T' => -1)),
                array('$limit' => $interval + 1),
                array('$sort' => array('Ticks.T' => 1))
            );
        $result = $collection->aggregate($ops);
//print_r($result);

        $O = array();
        $H = array();
        $L = array();
        $C = array();
        foreach ($result as $item) {
            $O[] = $item->Ticks->O;
            $H[] = $item->Ticks->H;
            $L[] = $item->Ticks->L;
            $C[] = $item->Ticks->C;
        }

        $cdl2crows = trader_cdl2crows($O, $H, $L, $C);
        $sum += $cdl2crows[$interval];
        $cdl3blackcrows = trader_cdl3blackcrows($O, $H, $L, $C);
        $sum += $cdl3blackcrows[$interval];
        $cdl3inside = trader_cdl3inside($O, $H, $L, $C);
        $sum += $cdl3inside[$interval];
        $cdl3linestrike = trader_cdl3linestrike($O, $H, $L, $C);
        $sum += $cdl3linestrike[$interval];
        $cdl3outside = trader_cdl3outside($O, $H, $L, $C);
        $sum += $cdl3outside[$interval];
        $cdl3starsinsouth = trader_cdl3starsinsouth($O, $H, $L, $C);
        $sum += $cdl3starsinsouth[$interval];
        $cdl3whitesoldiers = trader_cdl3whitesoldiers($O, $H, $L, $C);
        $sum += $cdl3whitesoldiers[$interval];
        $cdlabandonedbaby = trader_cdlabandonedbaby($O, $H, $L, $C);
        $sum += $cdlabandonedbaby[$interval];
        $cdladvanceblock = trader_cdladvanceblock($O, $H, $L, $C);
        $sum += $cdladvanceblock[$interval];
        $cdlbelthold = trader_cdlbelthold($O, $H, $L, $C);
        $sum += $cdlbelthold[$interval];
        $cdlbreakaway = trader_cdlbreakaway($O, $H, $L, $C);
        $sum += $cdlbreakaway[$interval];
        $cdlclosingmarubozu = trader_cdlclosingmarubozu($O, $H, $L, $C);
        $sum += $cdlclosingmarubozu[$interval];
        $cdlconcealbabyswall = trader_cdlconcealbabyswall($O, $H, $L, $C);
        $sum += $cdlconcealbabyswall[$interval];
        $cdlcounterattack = trader_cdlcounterattack($O, $H, $L, $C);
        $sum += $cdlcounterattack[$interval];
        $cdldarkcloudcover = trader_cdldarkcloudcover($O, $H, $L, $C);
        $sum += $cdldarkcloudcover[$interval];
        $cdldoji = trader_cdldoji($O, $H, $L, $C);
        $sum += $cdldoji[$interval];
        $cdldojistar = trader_cdldojistar($O, $H, $L, $C);
        $sum += $cdldojistar[$interval];
        $cdldragonflydoji = trader_cdldragonflydoji($O, $H, $L, $C);
        $sum += $cdldragonflydoji[$interval];
        $cdlengulfing = trader_cdlengulfing($O, $H, $L, $C);
        $sum += $cdlengulfing[$interval];
        $cdleveningdojistar = trader_cdleveningdojistar($O, $H, $L, $C);
        $sum += $cdleveningdojistar[$interval];
        $cdleveningstar = trader_cdleveningstar($O, $H, $L, $C);
        $sum += $cdleveningstar[$interval];
        $cdlgapsidesidewhite = trader_cdlgapsidesidewhite($O, $H, $L, $C);
        $sum += $cdlgapsidesidewhite[$interval];
        $cdlgravestonedoji = trader_cdlgravestonedoji($O, $H, $L, $C);
        $sum += $cdlgravestonedoji[$interval];
        $cdlhammer = trader_cdlhammer($O, $H, $L, $C);
        $sum += $cdlhammer[$interval];
        $cdlhangingman = trader_cdlhangingman($O, $H, $L, $C);
        $sum += $cdlhangingman[$interval];
        $cdlharami = trader_cdlharami($O, $H, $L, $C);
        $sum += $cdlharami[$interval];
        $cdlharamicross = trader_cdlharamicross($O, $H, $L, $C);
        $sum += $cdlharamicross[$interval];
        $cdlhighwave = trader_cdlhighwave($O, $H, $L, $C);
        $sum += $cdlhighwave[$interval];
        $cdlhikkake = trader_cdlhikkake($O, $H, $L, $C);
        $sum += $cdlhikkake[$interval];
        $cdlhikkakemod = trader_cdlhikkakemod($O, $H, $L, $C);
        $sum += $cdlhikkakemod[$interval];
        $cdlhomingpigeon = trader_cdlhomingpigeon($O, $H, $L, $C);
        $sum += $cdlhomingpigeon[$interval];
        $cdlidentical3crows = trader_cdlidentical3crows($O, $H, $L, $C);
        $sum += $cdlidentical3crows[$interval];
        $cdlinneck = trader_cdlinneck($O, $H, $L, $C);
        $sum += $cdlinneck[$interval];
        $cdlinvertedhammer = trader_cdlinvertedhammer($O, $H, $L, $C);
        $sum += $cdlinvertedhammer[$interval];
        $cdlkicking = trader_cdlkicking($O, $H, $L, $C);
        $sum += $cdlkicking[$interval];
        $cdlkickingbylength = trader_cdlkickingbylength($O, $H, $L, $C);
        $sum += $cdlkickingbylength[$interval];
        $cdlladderbottom = trader_cdlladderbottom($O, $H, $L, $C);
        $sum += $cdlladderbottom[$interval];
        $cdllongleggeddoji = trader_cdllongleggeddoji($O, $H, $L, $C);
        $sum += $cdllongleggeddoji[$interval];
        $cdllongline = trader_cdllongline($O, $H, $L, $C);
        $sum += $cdllongline[$interval];
        $cdlmarubozu = trader_cdlmarubozu($O, $H, $L, $C);
        $sum += $cdlmarubozu[$interval];
        $cdlmatchinglow = trader_cdlmatchinglow($O, $H, $L, $C);
        $sum += $cdlmatchinglow[$interval];
        $cdlmathold = trader_cdlmathold($O, $H, $L, $C);
        $sum += $cdlmathold[$interval];
        $cdlmorningdojistar = trader_cdlmorningdojistar($O, $H, $L, $C);
        $sum += $cdlmorningdojistar[$interval];
        $cdlmorningstar = trader_cdlmorningstar($O, $H, $L, $C);
        $sum += $cdlmorningstar[$interval];
        $cdlonneck = trader_cdlonneck($O, $H, $L, $C);
        $sum += $cdlonneck[$interval];
        $cdlpiercing = trader_cdlpiercing($O, $H, $L, $C);
        $sum += $cdlpiercing[$interval];
        $cdlrickshawman = trader_cdlrickshawman($O, $H, $L, $C);
        $sum += $cdlrickshawman[$interval];
        $cdlrisefall3methods = trader_cdlrisefall3methods($O, $H, $L, $C);
        $sum += $cdlrisefall3methods[$interval];
        $cdlseparatinglines = trader_cdlseparatinglines($O, $H, $L, $C);
        $sum += $cdlseparatinglines[$interval];
        $cdlshootingstar = trader_cdlshootingstar($O, $H, $L, $C);
        $sum += $cdlshootingstar[$interval];
        $cdlshortline = trader_cdlshortline($O, $H, $L, $C);
        $sum += $cdlshortline[$interval];
        $cdlspinningtop = trader_cdlspinningtop($O, $H, $L, $C);
        $sum += $cdlspinningtop[$interval];
        $cdlstalledpattern = trader_cdlstalledpattern($O, $H, $L, $C);
        $sum += $cdlstalledpattern[$interval];
        $cdlsticksandwich = trader_cdlsticksandwich($O, $H, $L, $C);
        $sum += $cdlsticksandwich[$interval];
        $cdltakuri = trader_cdltakuri($O, $H, $L, $C);
        $sum += $cdltakuri[$interval];
        $cdltasukigap = trader_cdltasukigap($O, $H, $L, $C);
        $sum += $cdltasukigap[$interval];
        $cdlthrusting = trader_cdlthrusting($O, $H, $L, $C);
        $sum += $cdlthrusting[$interval];
        $cdltristar = trader_cdltristar($O, $H, $L, $C);
        $sum += $cdltristar[$interval];
        $cdlunique3river = trader_cdlunique3river($O, $H, $L, $C);
        $sum += $cdlunique3river[$interval];
        $cdlupsidegap2crows = trader_cdlupsidegap2crows($O, $H, $L, $C);
        $sum += $cdlupsidegap2crows[$interval];
        $cdlxsidegap3methods = trader_cdlxsidegap3methods($O, $H, $L, $C);
        $sum += $cdlxsidegap3methods[$interval];

        echo $Market;
        echo ': ';
        echo $sum;
        echo ' Price: ';
        $gettick = $bittrex->getTicker($Market);
        $json = json_decode(json_encode($gettick),true);
        echo $json['Last'];
        echo ' Time: ';
        echo date("Y-m-d H:i:s");
        echo '<br>';
    }
}
?>