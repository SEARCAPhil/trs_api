<?php
header('Access-Control-Allow-Origin: *');
require_once('../../../src/Automobile/Vehicle.php');
require_once('../../../config/database.php');

use Automobile\Vehicle;

$method = $_SERVER['REQUEST_METHOD'];

// show all drivers
if($method === 'GET') {
	$Veh = new Vehicle($DB);
	echo @json_encode($Veh->lists());
}

?>