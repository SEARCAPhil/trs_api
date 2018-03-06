<?php
namespace Auth;
class Account{
	public function __construct($DB_CONNECTION){
		$this->DB = $DB_CONNECTION;
		$this->DB->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_WARNING); 
	}
	public function login($username, $password){
		$SQL = 'SELECT account.username,account.id as uid,account_profile.* FROM account LEFT JOIN account_profile on account_profile.uid = account.id WHERE username = :username and password = :password LIMIT 1';
		$sth = $this->DB->prepare($SQL);
		$sth->bindParam(':username',$username,\PDO::PARAM_STR);
		$sth->bindParam(':password',$password,\PDO::PARAM_STR);
		$sth->execute();
		$result = [];
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$result = $row;
		}
		return $result;
	}
	public function view($id){
		$results=[];

		$SQL='SELECT * FROM account_profile WHERE id = :id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id', $id, \PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}
		return $results;
	}
}
?>