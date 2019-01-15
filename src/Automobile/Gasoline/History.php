<?php
namespace Automobile\Gasoline;

class History {
	public function __construct(\PDO $DB_CONNECTION){
		$this->DB=$DB_CONNECTION;
	}

	public function create($id,$account_id,$message,$action){
		$results=[];

		$SQL='INSERT INTO gasoline_history(gasoline_id,account_profile_id,message,action) VALUES (:gasoline_id,:account_profile_id,:message,:action)';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':gasoline_id', $id);
		$sth->bindParam(':account_profile_id', $account_id);
		$sth->bindParam(':action', $action);
		$sth->bindParam(':message', $message);

		$sth->execute();
		
		return $this->DB->lastInsertId();
	}

	public function lists($id){
		$results=[];

		$SQL='SELECT gasoline_history.*, account_profile.profile_name FROM gasoline_history LEFT JOIN account_profile on account_profile.id = gasoline_history.account_profile_id where gasoline_history.gasoline_id = :gasoline_id ORDER BY gasoline_history.id DESC';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':gasoline_id', $id);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}
		return $results;
	}

}

?>