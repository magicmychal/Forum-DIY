<?php
// for testing this class we need to include the Model.php
// require_once 'Model.php';
class Users extends Model {
    public function read_users() {
        $sQuery = $this->db->prepare('SELECT * FROM users');
        $sQuery->execute();
        $users = $sQuery->fetchAll();
        if( count($users) ){
            echo print_r($users);
            exit;
        }
    }
    //prepared insert stament, whcih is used in sign_up controller
    public function sign_up_user($username, $hashed_pass, $email ){
        $sQuery = $this->db->prepare('INSERT INTO users
         VALUES(null, :userName, :hashed_password, :email, :date_created, :user_role, :active )');
        $sQuery->bindValue(':userName',$username );
        $sQuery->bindValue(':hashed_password',$hashed_pass );
        $sQuery->bindValue(':email',$email );
        $sQuery->bindValue(':date_created',date('Y/m/d H:i:s') );
        $sQuery->bindValue(':user_role', 4 );
        $sQuery->bindValue(':active', 0 );
        $sQuery->execute();
       if( $sQuery->rowCount() ){
           echo '{"message":"success", "text":"user created"}';
           exit;
       }
       echo '{ "message":"error"}';
    }

    // :MORTIMUS the results of function  will be used to check if the username exist or not
    // the function takes the username from table
    public function select_username($username){
        $sQuery = $this->db->prepare('SELECT username from users WHERE username = :usersname');
        $sQuery->bindValue(':usersname', $username);
        $sQuery->execute();
        $aUser = $sQuery->fetchAll();
       
        return $aUser;
    }

    public function select_username_and_password($username){
        $sQuery = $this->db->prepare('SELECT id,username,password_hashed,email  from users WHERE username = :usersname');
        $sQuery->bindValue(':usersname', $username);
        $sQuery->execute();
        $aUser = $sQuery->fetchAll();
       
        return $aUser;
    }
}
// for testing
// $modeltest = new Users;
// $modeltest->read_users();
