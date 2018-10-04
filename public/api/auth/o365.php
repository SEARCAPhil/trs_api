<?php 
header('Access-Control-Allow-Origin: *');
require_once('../../../src/Auth/Account.php');
require_once('../../../src/Sessions.php');
require_once('../../../src/Authentication.php');
require_once('../../../config/database.php');

use TRSAPI\Accounts as Account;
use TRSAPI\Sessions as Session;
use TRSAPI\Authentication as Authentication;

$method= ($_SERVER['REQUEST_METHOD']);
$input = @json_decode(file_get_contents("php://input"));

$acc = new Account($DB);
$Ses = new Session($DB);
$auth = new Authentication($DB);


//browsers , curl, etc...
$agent = isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:null;

// token, salt
$token = ($Ses->generate_token(date('y-m-d h:i:s'),'trs-2/26/2018'));

if($method=="POST"){
	echo json_encode($auth->login($input));
}
?>