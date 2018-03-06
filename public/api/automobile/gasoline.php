<?php
header('Access-Control-Allow-Origin: *');
require_once('../../../src/Automobile/Gasoline.php');
require_once('../../../config/database.php');
include_once(dirname(__FILE__).'/../../../src/Auth/Session.php');

use Automobile\Gasoline;
use Auth\Session;

$Gas = new Gasoline($DB);
$Sess = new Session($DB);

$method = $_SERVER['REQUEST_METHOD'];

// show all drivers
if($method === 'POST') {
	$input=file_get_contents("php://input");
	$data=(@json_decode($input));
	$res = [];

	$action = isset($data->action) ? $data->action : null;
	$tr_number = isset($data->tr_number) ? $data->tr_number : null;
	$automobile_id = isset($data->automobile_id) ? $data->automobile_id : null;
	$amount = isset($data->amount) ? $data->amount : 0;
	$liters = isset($data->liters) ? $data->liters : 0;
	$receipt = isset($data->receipt) ? $data->receipt : null;
	$station = isset($data->station) ? $data->station : null;
	$driver_id = isset($data->driver_id) ? $data->driver_id : null;
	$date_received = isset($data->date_received) ? $data->date_received : null;
	$token = isset($data->token) ? $data->token : null;
	

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


	//get token
	$tok = $Sess->get($token);

	//if not exist
	if(!isset($tok[0])) return 0;

	$encoded_by = $tok[0]->pid;


	// add new gasoline record
	if ($action == 'create') {
		$lastId = $Gas->create($tr_number,$automobile_id,$amount,$liters,$receipt,$station,$driver_id,$encoded_by,$received_day,$received_month,$received_year);

		if($lastId > 0) {
			$res['data'] = $lastId;
			echo json_encode($res);
		}
	}
	// add new gasoline record
	if ($action == 'remove') {
		$id = (int) @isset($data->id) ? $data->id : null;

		if (!empty($id)) {
			echo (int) @$Gas->remove($id);	
			return 0;
		}
	}
	// update
	if ($action == 'update') {
		$id = isset($data->id) ? $data->id : null;
		if (!empty($id)) {
			$lastId = $Gas->update($id,$tr_number,$automobile_id,$amount,$liters,$receipt,$station,$driver_id,$encoded_by,$received_day,$received_month,$received_year);
			
			if($lastId > 0) {
				$res['data'] = $lastId;
				echo json_encode($res);
			}
		}

	}
	
}

// get gasoline information
if($method === 'GET') {

		$id = (int) @htmlentities(htmlspecialchars($_GET['id']));
		$month = (int) @htmlentities(htmlspecialchars($_GET['month']));


		if(!empty($month)) {
			// get data per month
			$data = $Gas->lists($month);
		}else{
			// view details
			$data = $Gas->info($id);
		}
		
		$res['data'] = $data;
		echo json_encode($res);
	}

?>