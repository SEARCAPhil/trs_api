<?php
namespace Automobile;

class Gasoline {
	public function __construct(\PDO $DB_CONNECTION){
		$this->DB=$DB_CONNECTION;
	}

	
	public function create($automobile_id,$amount,$liters,$receipt,$station,$driver_id,$encoded_by,$received_day,$received_month,$received_year){
		$results=[];
		$SQL='INSERT INTO gasoline(automobile_id,amount,liters,receipt,station,driver_id,encoded_by,received_day,received_month,received_year) values(:automobile_id,:amount,:liters,:receipt,:station,:driver_id,:encoded_by,:received_day,:received_month,:received_year)';
		$sth=$this->DB->prepare($SQL);
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
}

?>