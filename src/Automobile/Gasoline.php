<?php
namespace Automobile;

include_once(dirname(__FILE__).'/../Auth/Account.php');
include_once(dirname(__FILE__).'/Vehicle.php');

use Auth\Account;
use Automobile\Vehicle;

class Gasoline {
	public function __construct(\PDO $DB_CONNECTION){
		$this->DB=$DB_CONNECTION;
	}

	
	public function create($tr_number,$automobile_id,$amount,$liters,$receipt,$station,$driver_id,$encoded_by,$received_day,$received_month,$received_year){
		$results=[];
		$SQL='INSERT INTO gasoline(tr_number,automobile_id,amount,liters,receipt,station,driver_id,encoded_by,received_day,received_month,received_year) values(:tr_number,:automobile_id,:amount,:liters,:receipt,:station,:driver_id,:encoded_by,:received_day,:received_month,:received_year)';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':tr_number',$tr_number);
		$sth->bindParam(':automobile_id',$automobile_id);
		$sth->bindParam(':amount',$amount);
		$sth->bindParam(':liters',$liters);
		$sth->bindParam(':receipt',$receipt);
		$sth->bindParam(':station',$station);
		$sth->bindParam(':driver_id',$driver_id);
		$sth->bindParam(':encoded_by',$encoded_by);
		$sth->bindParam(':received_day',$received_day);
		$sth->bindParam(':received_month',$received_month);
		$sth->bindParam(':received_year',$received_year);
		$sth->execute();
		return $this->DB->lastInsertId();
	}

	public function update($id,$tr_number,$automobile_id,$amount,$liters,$receipt,$station,$driver_id,$encoded_by,$received_day,$received_month,$received_year){
		$results=[];
		$SQL='UPDATE gasoline SET tr_number = :tr_number, automobile_id = :automobile_id,amount = :amount,liters = :liters,receipt = :receipt,station = :station,driver_id =:driver_id,encoded_by = :encoded_by ,received_day =:received_day ,received_month =:received_month,received_year = :received_year WHERE id =:id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id);
		$sth->bindParam(':tr_number',$tr_number);
		$sth->bindParam(':automobile_id',$automobile_id);
		$sth->bindParam(':amount',$amount);
		$sth->bindParam(':liters',$liters);
		$sth->bindParam(':receipt',$receipt);
		$sth->bindParam(':station',$station);
		$sth->bindParam(':driver_id',$driver_id);
		$sth->bindParam(':encoded_by',$encoded_by);
		$sth->bindParam(':received_day',$received_day);
		$sth->bindParam(':received_month',$received_month);
		$sth->bindParam(':received_year',$received_year);
		$sth->execute();
		return $sth->rowCount();
	}

	public function info($id){
		$results=[];
		$acc = new Account($this->DB);
		$auto = new Vehicle($this->DB);

		$SQL='SELECT gasoline.*, account_profile.profile_name FROM gasoline LEFT JOIN account_profile on account_profile.id = gasoline.encoded_by WHERE gasoline.id = :id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id', $id, \PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$row->driver = $acc->view($row->driver_id);
			$row->automobile = $auto->view($row->automobile_id);
			$results[]=$row;
		}
		return $results;
	}

	public function lists($month, $page=1, $limit = 50){
		$results=[];
		$page=$page<2?0:$page-1;

		$SQL='SELECT gasoline.*, account_profile.profile_name FROM gasoline LEFT JOIN account_profile on account_profile.id = gasoline.encoded_by WHERE received_month = :month and gasoline.status !=1 LIMIT :offset,:lim';
		
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':month', $month, \PDO::PARAM_INT);
		$sth->bindParam(':lim',$limit,\PDO::PARAM_INT);
		$sth->bindParam(':offset',$page,\PDO::PARAM_INT);

		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}
		return $results;
	}


	public function set_status($id,$status){
		$SQL='UPDATE gasoline set status=:status where id=:id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id);
		$sth->bindParam(':status',$status);
		$sth->execute();

		return $sth->rowCount();
	}

	public function remove($id){
		return self::set_status($id,1);
	}
}

?>