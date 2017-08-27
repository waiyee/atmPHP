<?php
    include('app.php');

    //echo "test";
    $today = date('Y-m-d');
    echo 'Today: '.$today;
    echo '<br>';

    $collection = $dbclient->coins->MarketDayTicks;
    $cursor = $collection->find([]);

    foreach ($cursor as $doc) {
        $MarketName = (string)$doc->MarketName;
        echo $MarketName .'<br>';
        $O = array();
        $H = array();
        $L = array();
        $C = array();
        $T = array();
        foreach($doc->Ticks as $Ticks){
            $tdate = substr((string)$Ticks->T,0,10);
            $d1 = (string)$tdate;
            $d2 = (string)$today;
            $date1=date_create($d1);
            $date2=date_create($d2);
            $diff=date_diff($date1,$date2);
            $dif = $diff->format("%a");
            //echo $dif.'<br>';

            if((int)$dif<31) {
                //echo $dif;
                $O[] = $Ticks->O;
                $H[] = $Ticks->H;
                $L[] = $Ticks->L;
                $C[] = $Ticks->C;
                $T[] = $Ticks->T;
                //echo '<br>';
            }
            //echo '<br>';
        }
        //print_r($O);
        //echo '<br>';
        //print_r($H);
        //echo '<br>';
        //print_r($L);
        //echo '<br>';
        //print_r($C);
        //echo '<br>';
        //print_r($T);
        //echo '<br>';

        echo 'Three Line Strike: ';
        $cdl3linestrike = trader_cdl3linestrike($O,$H,$L,$C);
        echo $cdl3linestrike[30];
        echo '<br>';

        echo 'Three Outside Up/Down: ';
        $cdl3outside = trader_cdl3outside($O,$H,$L,$C);
        echo $cdl3outside[30];
        echo '<br>';

        echo 'Thrusting: ';
        $cdlthrusting = trader_cdlthrusting($O,$H,$L,$C);
        echo $cdlthrusting[30];
        echo '<br>';

        echo 'Tristar: ';
        $cdltristar = trader_cdltristar($O,$H,$L,$C);
        echo $cdltristar[30];
        echo '<br>';

        echo 'Abandoned Baby: ';
        $cdlabandonedbaby = trader_cdlabandonedbaby($O,$H,$L,$C);
        echo $cdlabandonedbaby[30];
        echo '<br>';

        echo 'Belt-hold: ';
        $cdlbelthold = trader_cdlbelthold($O,$H,$L,$C);
        echo $cdlbelthold[30];
        echo '<br>';

        echo 'Breakaway: ';
        $cdlbreakaway = trader_cdlbreakaway($O,$H,$L,$C);
        echo $cdlbreakaway[30];
        echo '<br>';

        echo 'Doji Star: ';
        $cdldojistar = trader_cdldojistar($O,$H,$L,$C);
        echo $cdldojistar[30];
        echo '<br>';

        echo 'Dragonfly Doji: ';
        $cdldragonflydoji = trader_cdldragonflydoji($O,$H,$L,$C);
        echo $cdldragonflydoji[30];
        echo '<br>';

        echo 'Engulfing Pattern: ';
        $cdlengulfing = trader_cdlengulfing($O,$H,$L,$C);
        echo $cdlengulfing[30];
        echo '<br>';

        echo 'Gravestone Doji: ';
        $cdlgravestonedoji = trader_cdlgravestonedoji($O,$H,$L,$C);
        echo $cdlgravestonedoji[30];
        echo '<br>';

        echo 'Hammer: ';
        $cdlhammer = trader_cdlhammer($O,$H,$L,$C);
        echo $cdlhammer[30];
        echo '<br>';

        echo 'Inverted Hammer: ';
        $cdlinvertedhammer = trader_cdlinvertedhammer($O,$H,$L,$C);
        echo $cdlinvertedhammer[30];
        echo '<br>';

        echo 'Ladder Bottom: ';
        $cdlladderbottom = trader_cdlladderbottom($O,$H,$L,$C);
        echo $cdlladderbottom[30];
        echo '<br>';





        echo '<br>';
    }

    function allCandle($market, $interval){
        //getTicks($market, $interval)
        //getDBTicks($market, $interval)
    }

?>