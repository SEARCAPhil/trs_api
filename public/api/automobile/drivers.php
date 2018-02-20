<?php
header('Access-Control-Allow-Origin: *');
require_once('../../../src/Automobile/Drivers.php');
require_once('../../../config/database.php');

use Automobile\Drivers;

$method = $_SERVER['REQUEST_METHOD'];

// show all drivers
if($method === 'GET') {
	$dr = new Drivers($DB);
	echo @json_encode($dr->lists());
}

?>