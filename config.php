<?php
//define('URL', 'http://duythien.dev/sitepoint/blog-mongodb');
define('UserAuth', 'duythien');
define('PasswordAuth', 'duythien');

$config = array(
	'username' => 'coins',
	'password' => 'rVnIAkYtnVF0tZCpqgVV',
	'dbname'   => 'coins',
	//'cn' 	   => sprintf('mongodb://%s:%d/%s', $hosts, $port,$database),
	'connection_string'=> sprintf('mongodb://%s:%d/%s','localhost','27017','coins')
);