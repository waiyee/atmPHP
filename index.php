<html>
<head>
</head>

<body>
<?php 

	include('app.php');

	if (refreshCurrencies($bittrex,$dbclient) )
		echo 'Update Currencies Successfully <br/>';
	else
		echo 'Error in update Currencies <br/>';
	if (refreshMarkets($bittrex,$dbclient) )
		echo 'Update Markets Successfully <br/>';
	else
		echo 'Error in update Markets <br/>';
	
	
	


?>


</body>

</html>