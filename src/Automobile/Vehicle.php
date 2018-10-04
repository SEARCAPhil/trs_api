<?php
namespace Automobile;

class Vehicle {
	public function __construct(\PDO $DB_CONNECTION){
		$this->DB=$DB_CONNECTION;
	}

	public function lists($page=0,$limit=30){
		$results=[];
		$page=$page<2?0:$page-1;
		$SQL='SELECT * FROM automobile WHERE status!=1 ORDER BY manufacturer ASC LIMIT :offset,:lim';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':lim',$limit,\PDO::PARAM_INT);
		$sth->bindParam(':offset',$page,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}
		return $results;
	}

	public function view($id){
		$results=[];

		$SQL='SELECT * FROM automobile WHERE automobile_id = :id';
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