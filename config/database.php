<?php
/*DATABASE CONFIGURATION*/

$_host='localhost';
$_username='root';
$_password='';
$_dbname='trs';

try{
	$db=new \PDO('mysql:host='.$_host.';dbname='.$_dbname.';',$_username);
	$db->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e){
	echo $e->getMessage();
}

// set alias
$DB = $db;


?>