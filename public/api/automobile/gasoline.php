<?php
header('Access-Control-Allow-Origin: *');
require_once('../../../src/Automobile/Gasoline.php');
require_once('../../../config/database.php');

use Automobile\Gasoline;

$method = $_SERVER['REQUEST_METHOD'];

// show all drivers
if($method === 'POST') {
	$input=file_get_contents("php://input");
	$data=(@json_decode($input));
	$res = [];

	$automobile_id = isset($data->automobile_id) ? $data->automobile_id : null;
	$amount = isset($data->amount) ? $data->amount : 0;
	$liters = isset($data->liters) ? $data->liters : 0;
	$receipt = isset($data->receipt) ? $data->receipt : null;
	$station = isset($data->station) ? $data->station : null;
	$driver_id = isset($data->driver_id) ? $data->driver_id : null;
	$date_received = isset($data->date_received) ? $data->date_received : null;
	$encoded_by = 1;

	// date in receipt
	$received_day = 00;
	$received_month = 00;
	$received_year = 0000;

	// split date
	// yy-mm-dd format
	if(!is_null($date_received)){
		
		$date = explode('-', $date_received);
		// must be complete
		if(count($date) === 3) {
			$received_year = $date[0];	
			$received_month = $date[1];
			$received_day = $date[2];
		}
	}

	$Gas= new Gasoline($DB);
	$lastId = $Gas->create($automobile_id,$amount,$liters,$receipt,$station,$driver_id,$encoded_by,$received_day,$received_month,$received_year);

	if($lastId > 0) {
		$res['data'] = $lastId;
		echo json_encode($res);
	}
}

?>