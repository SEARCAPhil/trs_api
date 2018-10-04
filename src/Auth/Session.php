<?php
namespace Auth;
class Session{
	public function __construct(\PDO $DB_CONNECTION){
		$this->DB = $DB_CONNECTION;
		$this->DB->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_WARNING); 
	}
	public function generate_token($text,$salt){
		return sha1($text.''.md5($salt));
	}
	public function set($token,$uid,$user_agent){
		$SQL = 'INSERT INTO account_session(token,account_id,user_agent) values(:token,:uid,:user_agent)';
		$sth = $this->DB->prepare($SQL);
		$sth->bindParam(':token',$token,\PDO::PARAM_STR);
		$sth->bindParam(':uid',$uid,\PDO::PARAM_STR);
		$sth->bindParam(':user_agent',$user_agent,\PDO::PARAM_STR);
		$sth->execute();
		return $this->DB->lastInsertId();
	}
	public function get($token){
		$SQL = 'SELECT account_session.* , account_profile.id as pid FROM account_session LEFT JOIN account_profile on account_session.account_id = account_profile.uid WHERE token =:token ORDER BY account_profile.id DESC LIMIT 1';
		$sth = $this->DB->prepare($SQL);
		$sth->bindParam(':token',$token,\PDO::PARAM_STR);
		$sth->execute();
		$result = [];
		while ($row = $sth->fetch(\PDO::FETCH_OBJ)) {
			$result[] = $row;
		}

		return $result;
	}
}
?>