<?php
include('app.php');
$OB = $dbclient->coins->OwnOrderBook;
$result = $OB->find( array('Status' => array('$in' =>array('selling', 'buying'))));
$IDArray = array();

foreach ($result as $doc) {
    $IDArray = array_merge($IDArray, array($doc['_id']));
    //echo $doc['_id'];
}
echo json_encode($IDArray);