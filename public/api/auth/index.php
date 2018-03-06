<?php 
header('Access-Control-Allow-Origin: *');
require_once('../../../src/Auth/Account.php');
require_once('../../../src/Auth/Session.php');
require_once('../../../config/database.php');

use Auth\Account as Account;
use Auth\Session as Session;

$method=($_SERVER['REQUEST_METHOD']);
$input =@ json_decode(file_get_contents("php://input"));

$acc = new Account($DB);
$Ses = new Session($DB);


//browsers , curl, etc...
$agent = isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:null;

// token, salt
$token = ($Ses->generate_token(date('y-m-d h:i:s'),'trs-2/26/2018'));

if($method=="POST"){
	if(!isset($input->username) && !isset($input->password)) return 0;
	$credential = $acc->login($input->username, $input->password);
	if(isset($credential->uid)){
		$session = $Ses->set($token,$credential->uid,$agent);
		//return token
		if($session>0){
			$credential->token = $token;
			echo @json_encode($credential);
		}
	}
}
?>