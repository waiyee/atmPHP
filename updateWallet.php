<?php
include('app.php');

if (updateWallet($bittrex,$dbclient))
    echo 'Updated Wallet Balance';
