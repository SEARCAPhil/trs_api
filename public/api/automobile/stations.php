<?php
header('Access-Control-Allow-Origin: *');
require_once('../../../src/Directory.php');
require_once('../../../config/database.php');

use TRSAPI\Directory;

$method = $_SERVER['REQUEST_METHOD'];

// show all drivers
if($method === 'GET') {
	$dr = new Directory($DB);
	echo @json_encode($dr->gasoline_station());
}

?>