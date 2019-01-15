<?php
header('Access-Control-Allow-Origin: *');
require_once('../../../src/Automobile/Gasoline.php');
require_once('../../../config/database.php');
include_once(dirname(__FILE__).'/../../../src/Auth/Session.php');
require_once('../../../src/Automobile/Gasoline/History.php');

use Automobile\Gasoline;
use Automobile\Gasoline\History;
use Auth\Session;

$Gas = new Gasoline($DB);
$Sess = new Session($DB);
$His = new History($DB);

$method = $_SERVER['REQUEST_METHOD'];

// show all drivers
if($method === 'POST') {
	$input=file_get_contents("php://input");
	$data=(@json_decode($input));
	$res = [];

	$action = isset($data->action) ? $data->action : null;
	$tt_number = isset($data->tt_number) ? $data->tt_number : null;
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
		$lastId = $Gas->create($tt_number,$automobile_id,$amount,$liters,$receipt,$station,$driver_id,$encoded_by,$received_day,$received_month,$received_year);

		if($lastId > 0) {
			// log
			$His->create($lastId,$encoded_by,'Created this record','created');

			// send to client
			$res['data'] = $lastId;
			echo json_encode($res);
		}
	}
	// add new gasoline record
	if ($action == 'remove') {
		$id = (int) @isset($data->id) ? $data->id : null;

		if (!empty($id)) {
			$is_removed = (int) @$Gas->remove($id);	

			// log
			if ($is_removed) {
				$His->create($data->id,$encoded_by,'Deleted this record','deleted');
			}

			// send to client
			echo $is_removed;
			return 0;
		}
	}
	// update
	if ($action == 'update') {
		$id = isset($data->id) ? $data->id : null;
		if (!empty($id)) {
			$lastId = $Gas->update($id,$tt_number,$automobile_id,$amount,$liters,$receipt,$station,$driver_id,$encoded_by,$received_day,$received_month,$received_year);
			
			if($lastId > 0) {
				// log
				$His->create($id,$encoded_by,'Updated this record','updated');

				$res['data'] = $lastId;
				echo json_encode($res);
			}
		}

	}
	
}

// get gasoline list per month
if($method === 'GET' && !isset($_GET['id']) && !isset($_GET['filter'])) {

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


// get gasoline info
if($method === 'GET' && isset($_GET['id'])) {

	$id = (int) @htmlentities(htmlspecialchars($_GET['id']));

	// view details
	$data = $Gas->info($id);

	
	$res['data'] = $data;
	echo json_encode($res);
}

// get gasoline info
if($method === 'GET' && isset($_GET['filter']) && !isset($_GET['id'])) {

	$filter =  @htmlentities(htmlspecialchars($_GET['filter']));
	$page =  (int) @htmlentities(htmlspecialchars($_GET['page']));

	if (empty($page)) $page = 1;

	// view details
	$data = $Gas->filter($filter,$page);

	
	$res['data'] = $data;
	echo json_encode($res);
}

?>