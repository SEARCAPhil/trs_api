<?php
namespace TRSAPI;

#start session
@session_start(); 

class Accounts
{

	public function __construct($DB_CONNECTION){
		$this->DB = $DB_CONNECTION;
		$this->DB->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_WARNING); 
	}
  
	public function create($username, $password, $uid){
		$SQL = 'INSERT INTO account(username, password, uid) VALUES (:username, :password, :uid)';
		$sth = $this->DB->prepare($SQL);
		$sth->bindParam(':username',$username,\PDO::PARAM_STR);
		$sth->bindParam(':password',$password,\PDO::PARAM_STR);
		$sth->bindParam(':uid',$uid,\PDO::PARAM_STR);

		$sth->execute();
		
		return $this->DB->lastInsertId();
  }
  
	public function create_profile($id, $profile_name, $last_name, $first_name, $middle_name, $email, $department, $department_alias, $position, $dept_id){
		$SQL = 'INSERT INTO account_profile(uid, profile_name, last_name, first_name, middle_name, profile_email, department, department_alias, position, dept_id) VALUES (:account_id, :profile_name, :last_name, :first_name, :middle_name, :email, :department, :department_alias, :position, :dept_id)';
    $sth = $this->DB->prepare($SQL);
    
		$sth->bindParam(':account_id',$id,\PDO::PARAM_INT);
		$sth->bindParam(':profile_name',$profile_name);
		$sth->bindParam(':last_name',$last_name);
		$sth->bindParam(':first_name',$first_name);
		$sth->bindParam(':middle_name',$middle_name);
		$sth->bindParam(':email',$email);
		$sth->bindParam(':department',$department);
		$sth->bindParam(':department_alias',$department_alias);
		$sth->bindParam(':dept_id', $dept_id, \PDO::PARAM_INT);
		$sth->bindParam(':position',$position);
		$sth->execute();
		
		return $this->DB->lastInsertId();
	}
	
	public function update_profile($id, $profile_name, $last_name, $first_name, $middle_name, $email, $department, $department_alias, $position, $dept_id, $profile_image = null){
		$SQL = 'UPDATE account_profile SET profile_name = :profile_name, last_name = :last_name, first_name = :first_name, middle_name = :middle_name, profile_email = :profile_email, department = :department, department_alias = :department_alias, position = :position, dept_id = :dept_id, profile_image = :profile_image WHERE id = :id';
    $sth = $this->DB->prepare($SQL);
    
		$sth->bindParam(':id', $id ,\PDO::PARAM_INT);
		$sth->bindParam(':profile_name',$profile_name);
		$sth->bindParam(':last_name',$last_name);
		$sth->bindParam(':first_name',$first_name);
		$sth->bindParam(':middle_name',$middle_name);
		$sth->bindParam(':profile_email',$email);
		$sth->bindParam(':department',$department);
		$sth->bindParam(':department_alias',$department_alias);
		$sth->bindParam(':dept_id', $dept_id, \PDO::PARAM_INT);
		$sth->bindParam(':position',$position);
		$sth->bindParam(':profile_image',$profile_image);
		$sth->execute();
		
		return $this->DB->lastInsertId();
  }
  
	public function login($username, $password){
		$SQL = 'SELECT account.username,account.id as uid,profile.* FROM account LEFT JOIN profile on profile.account_id = account.id WHERE username = :username and password = :password LIMIT 1';
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
  
	public function loginO365($username, $uid){
		$SQL = 'SELECT account.username,account.id as uid, account.uid as openID, account_profile.*, account_profile.id as profile_id, account_role.role FROM account LEFT JOIN account_profile on account_profile.uid = account.id LEFT JOIN account_role on account_role.account_id = account.id WHERE account.username = :username LIMIT 1';
		$sth = $this->DB->prepare($SQL);
		$sth->bindParam(':username',$username,\PDO::PARAM_STR);
		$sth->execute();
		$result = [];
		while($row=$sth->fetch(\PDO::FETCH_OBJ)) {
			$result = $row;
		}
		return $result;
  }

  public function setOpenID($id, $uid){
		$SQL = 'UPDATE account SET uid=:uid WHERE id=:id';
		$sth = $this->DB->prepare($SQL);
        $sth->bindParam(':id', $id);
        $sth->bindParam(':uid', $uid, \PDO::PARAM_STR);
        $sth->execute();
        return $sth->rowCount();
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

