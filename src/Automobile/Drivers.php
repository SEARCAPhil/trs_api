<?php
namespace Automobile;

class Drivers {
	public function __construct(\PDO $DB_CONNECTION){
		$this->DB=$DB_CONNECTION;
	}

	public function lists($page=0,$limit=50){
		$results=[];
		$page=$page<2?0:$page-1;
		$SQL='SELECT * FROM account_profile WHERE position = "driver" ORDER BY profile_name ASC LIMIT :offset,:lim';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':lim',$limit,\PDO::PARAM_INT);
		$sth->bindParam(':offset',$page,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}
		return $results;
	}
}

?>