<?php 
namespace TRSAPI;

require_once('Account.php');
require_once('Sessions.php');
require_once('Directory.php');

use TRSAPI\Accounts;
use TRSAPI\Sessions;
use TRSAPI\Directory;


#start session
@session_start(); 

class Authentication
{

    public function __construct($DB_CONNECTION){
        $this->DB = $DB_CONNECTION;
        $this->DB->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_WARNING); 
    }

    public function login($request) {
        $Ses = new Sessions($this->DB);
        $Acc = new Accounts($this->DB);
        $Dir = new Directory($this->DB);

        //browsers , curl, etc...
        $agent = isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:null;
        // token, salt
        $token = ($Ses->generate_token(date('y-m-d h:i:s'),'bms-2/26/2018'));
        $result = [];

        $input = @$request->data;
        $credential = $Acc->loginO365($input->mail,$input->id);
        $department_list = $Dir->departments_list();
        $dept_id = self::map_department($credential->department);
        $department_alias = explode(' ', @$input->department);
        $dept_alias = '';
        
        foreach($department_alias as $key => $value) {
            $dept_alias.=  strtoupper(substr($value, 0, 1));
        }
    

        // set OPENID
        if(is_null($credential->openID) || empty($credential->openID)) $Acc->setOpenID($credential->uid, $input->id);

        // register
        if(!isset($credential->uid)) {
            // create($company_id, $username, $password, $uid)
            // This is for creating Office365 account
            /*------------------------------------------------------
            // username = @email
            -------------------------------------------------------*/
            $accountId = (int) @$Acc->create(isset($input->mail) ? $input->mail : null, null, $input->id);

            // if account successfully created, save profile to DB
            if($accountId > 0) {

                // create_profile($id, $profile_name, $last_name, $first_name, $middle_name, $email, $department, $department_alias, $position)
                $profileId = (int) @$Acc->create_profile($accountId, $input->displayName, $input->surname, $input->givenName, $input->givenName, $input->mail, $input->department, $dept_alias, $input->jobTitle, $dept_id);
                $sessionId = $Ses->set($token,$accountId,$agent);
                
                if($sessionId) {
                    $result['token'] = $token;
                    $result['role'] = @$credential->role;
                    $result['id'] = $accountId;
                    $result['profile_id'] = $profileId;
                    $result['fields'] = $input;
                    $result['dept_id'] = $dept_id;
                }

                return $result;

            }
        } else {
        // proceed to login
        // no need to register again
        $sessionId = $Ses->set($token,$credential->uid,$agent);

        // update profile
        $isUpdated = (int) $Acc->update_profile($credential->profile_id, $input->displayName, $input->surname, $input->givenName, $input->givenName, $input->mail, $input->department, $dept_alias, $input->jobTitle, $dept_id, $input->id);

        if($sessionId) {
            $result['token'] = $token;
            $result['role'] = $credential->role;
            $result['fields'] = $input;
            $result['id'] = $credential->uid;
            $result['profile_id'] = $credential->profile_id;
            $result['dept_id'] = $dept_id;
        }	

            return $result;
        }

    // $res=json_encode(array('id'=>(integer)$row->id,'priv'=>$row->priv,'dept'=>$row->dept_id,'dept_name'=>$row->dept_name,'dept_alias'=>$row->dept_alias,'profile_image'=>$row->profile_image,'profile_name'=>$row->profile_name,'last_name'=>$row->last_name,'first_name'=>$row->first_name,'position'=>$row->position,'date_modified'=>$row->date_modified,'token'=>$hash));
    }

    public function map_department ($dept_name) {
        $dept_id = null;
        $Dir = new Directory($this->DB);
        $department_list = $Dir->departments_list();
        foreach($department_list as $key => $value) {
            # compare department assigned in OWA against in TRS
            if($value->dept_name === $dept_name) $dept_id = $value->dept_id;
        }
        return $dept_id;
    }


    public function isAdmin(){
      return ($_SESSION['priv'] ==='admin');
    }


    public function logout(){
        $_SESSION = null;
        unset($_SESSION);
        session_destroy();
    }


    private function session($data) {
       #set session manually
       $_SESSION['id'] = $data['id'];
       $_SESSION['token'] = $data['token'];
       $_SESSION['profile_id'] = $data['profile_id'];
       $_SESSION['uid'] = $data['profile_id'];
       $_SESSION['dept'] = $data['fields']->department;
       $_SESSION['priv'] = $data['role'];
       $_SESSION['position'] = $data['fields']->jobTitle;
       $_SESSION['unit'] = $data['fields']->department;
       $_SESSION['name'] = $data['fields']->displayName;
       $_SESSION['dept_id'] = $data['dept_id'];
    }

}

