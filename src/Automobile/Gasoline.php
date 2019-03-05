<?php
namespace Automobile;

include_once(dirname(__FILE__).'/../Auth/Account.php');
include_once(dirname(__FILE__).'/Vehicle.php');
include_once(dirname(__FILE__).'/Gasoline/History.php');

use Auth\Account;
use Automobile\Vehicle;
use Automobile\Gasoline\History;

class Gasoline {
	public function __construct(\PDO $DB_CONNECTION){
		$this->DB=$DB_CONNECTION;
	}

	
	public function create($tt_number,$automobile_id,$amount,$liters,$receipt,$station,$driver_id,$encoded_by,$received_day,$received_month,$received_year,$type){
		$results=[];
		$SQL='INSERT INTO gasoline(tt_number,automobile_id,amount,liters,receipt,station,driver_id,encoded_by,received_day,received_month,received_year,type) values(:tt_number,:automobile_id,:amount,:liters,:receipt,:station,:driver_id,:encoded_by,:received_day,:received_month,:received_year,:type)';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':tt_number',$tt_number);
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
		$sth->bindParam(':type',$type);
		$sth->execute();
		return $this->DB->lastInsertId();
	}

	public function update($id,$tt_number,$automobile_id,$amount,$liters,$receipt,$station,$driver_id,$encoded_by,$received_day,$received_month,$received_year,$type){
		$results=[];
		$SQL='UPDATE gasoline SET tt_number = :tt_number, automobile_id = :automobile_id,amount = :amount,liters = :liters,receipt = :receipt,station = :station,driver_id =:driver_id,encoded_by = :encoded_by ,received_day =:received_day ,received_month =:received_month,received_year = :received_year, type = :type WHERE id =:id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id',$id);
		$sth->bindParam(':tt_number',$tt_number);
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
		$sth->bindParam(':type',$type);
		$sth->execute();
		return $sth->rowCount();
	}

	public function info($id){
		$results=[];
		$acc = new Account($this->DB);
		$auto = new Vehicle($this->DB);
		$his = new History($this->DB);

		$SQL='SELECT gasoline.*, account_profile.profile_name FROM gasoline LEFT JOIN account_profile on account_profile.id = gasoline.encoded_by WHERE gasoline.id = :id';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':id', $id, \PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$row->driver = $acc->view($row->driver_id);
			$row->automobile = $auto->view($row->automobile_id);
			$row->history = $his->lists($row->id);
			$results[]=$row;
		}
		return $results;
	}

	public function lists($month, $page=1, $limit = 50){
		$results=[];
		$page=$page<2?0:$page-1;

		$SQL='SELECT gasoline.*, account_profile.profile_name FROM gasoline LEFT JOIN account_profile on account_profile.id = gasoline.encoded_by WHERE received_month = :month and gasoline.status !=1 ORDER BY received_month, received_year, received_day DESC LIMIT :offset,:lim';
		
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


	public function lists_unpaid($page=1, $limit = 50){
		$results=[];
		$page=$page<2?0:$page-1;

		$SQL='SELECT gasoline.*, account_profile.profile_name FROM gasoline LEFT JOIN account_profile on account_profile.id = gasoline.encoded_by WHERE received_month = 0 and gasoline.status !=1 ORDER BY gasoline.date_created DESC LIMIT :offset,:lim';
		
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':lim',$limit,\PDO::PARAM_INT);
		$sth->bindParam(':offset',$page,\PDO::PARAM_INT);

		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}
		return $results;
	}


	public function lists_by_date($from, $to, $driver = null, $automobile_id = null, $station = null){
		$results=[];

		$from_date = explode('-', $from);
		$to_date = explode('-', $to);
		//from
		$year = $from_date[0];
		$month = $from_date[1];
		$day = $from_date[2];

		// to
		$end_year = $to_date[0];
		$end_month = $to_date[1];
		$end_day = $to_date[2];

		$where_driver = !is_null($driver) ? "and gasoline.driver_id=:driver_id" : '';
		$where_vehicle = !is_null($automobile_id) ? "and gasoline.automobile_id=:automobile_id" : '';
		$where_station = !is_null($station) ? "and gasoline.station=:station" : '';

		$where = "(received_month >= :month and received_day >= :day and received_year >= :year) AND (received_month <= :end_month and received_day <= :end_day and received_year <= :end_year) and gasoline.status !=1 $where_driver $where_vehicle $where_station";

		$SQL="SELECT gasoline.*, account_profile.profile_name, automobile.plate_no, automobile.manufacturer FROM gasoline LEFT JOIN account_profile on account_profile.id = gasoline.driver_id LEFT JOIN automobile on automobile.automobile_id = gasoline.automobile_id WHERE $where ORDER BY received_year, received_month, received_day DESC";
		
		$sth=$this->DB->prepare($SQL);
		// from
		$sth->bindParam(':month', $month, \PDO::PARAM_INT);
		$sth->bindParam(':day', $day, \PDO::PARAM_INT);
		$sth->bindParam(':year', $year, \PDO::PARAM_INT);
		// to
		$sth->bindParam(':end_month', $end_month, \PDO::PARAM_INT);
		$sth->bindParam(':end_day', $end_day, \PDO::PARAM_INT);
		$sth->bindParam(':end_year', $end_year, \PDO::PARAM_INT);

		// additional param
		if(!empty($where_driver)) {
			$sth->bindParam(':driver_id', $driver, \PDO::PARAM_INT);
		}

		// additional param
		if(!empty($where_driver)) {
			$sth->bindParam(':driver_id', $driver, \PDO::PARAM_INT);
		}

		if(!empty($where_vehicle)) {
			$sth->bindParam(':automobile_id', $automobile_id, \PDO::PARAM_INT);
		}

		if(!empty($where_station)) {
			$sth->bindParam(':station', $station, \PDO::PARAM_STR);
		}

		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}
		return $results;
	}

	public function list_per_tt($tt){
		$results=[];
		$SQL='SELECT * FROM gasoline WHERE status=0 and tt_number =:tt ORDER BY id ASC';
		$sth=$this->DB->prepare($SQL);
		$sth->bindParam(':tt',$tt,\PDO::PARAM_INT);
		$sth->execute();
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$results[]=$row;
		}
		return $results;
	}

	public function filter($filter, $page=1){
		if ($filter == 'unpaid') {
			return self::lists_unpaid($page);
		}
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