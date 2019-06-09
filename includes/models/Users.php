<?php
// for testing this class we need to include the Model.php
// require_once 'Model.php';
class Users extends Model
{
    public function read_users()
    {
        try {
            $sQuery = $this->db->prepare('SELECT * FROM users');
            $sQuery->execute();
            $users = $sQuery->fetchAll();
            if (count($users)) {
                echo print_r($users);
                exit;
            }
        } catch (PDOException $error) {
            LogSaver::save_the_log($error, 'users.txt');
        }
    }

    //prepared insert statement, which is used in sign_up controller
    public function sign_up_user($username, $hashed_pass, $email, $token)
    {
        try {
            $checkUserQuery = $this->db->prepare('SELECT username, email FROM users WHERE email = :email OR username = :username');
            $checkUserQuery->bindValue(':email', $email);
            $checkUserQuery->bindValue(':username', $username);
            $checkUserQuery->execute();
            // check if the user exists in the database
            if (!empty($checkUserQuery->fetch())) {
                // return false to the controllers
                // return false;
                //PETER: the response here is sent to AJAX and AJAX expects a JSON
                // These 2 have to be separated to show something meaningful to the user
                // we gotta tell them if the email is already in use of if the username is already in use
                // specifically.
                echo '{"status":"0","message":"Duplicating username or email"}';
                exit;
            }
        } catch (PDOException $error) {
            LogSaver::save_the_log($error, 'users.txt');
        }


        try {
            $sQuery = $this->db->prepare('INSERT INTO users
         VALUES(null, :userName, :hashed_password, :email, :date_created, :user_role, :active , :token)');
            $sQuery->bindValue(':userName', $username);
            $sQuery->bindValue(':hashed_password', $hashed_pass);
            $sQuery->bindValue(':email', $email);
            $sQuery->bindValue(':date_created', date('Y/m/d H:i:s'));
            $sQuery->bindValue(':user_role', 4);
            $sQuery->bindValue(':active', 1);
            $sQuery->bindValue(':token', $token);
            $sQuery->execute();
            $returnedID = $this->db->lastInsertId();
            if (!$sQuery->rowCount()) {
                echo '{"status":"0","message":"User was not created"}';
                exit;
            }
            echo '{"status":"1","message":"User was created"}';
            mailer::sent_mail($_POST['txtEmail'], $token , $returnedID , $username);
        } catch (PDOException $error) {
            LogSaver::save_the_log($error, 'users.txt');
        }
    }

    // :MORTIMUS the results of function  will be used to check if the username exist or not
    // the function takes the username from table
    public function select_username($username)
    {
        try {
            $sQuery = $this->db->prepare('SELECT username from users WHERE username = :username AND active = 1');
            $sQuery->bindValue(':username', $username);
            $sQuery->execute();
            $aUser = $sQuery->fetchAll();

            return $aUser;
        } catch (PDOException $error) {
            LogSaver::save_the_log($error, 'users.txt');
        }
    }

    public function select_username_and_password($username)
    {
        try {
            $sQuery = $this->db->prepare('SELECT id,username,password_hashed,email  from users WHERE username = :usersname ');
            $sQuery->bindValue(':usersname', $username);
            $sQuery->execute();
            $aUser = $sQuery->fetchAll();

            return $aUser;
        } catch (PDOException $error) {
            LogSaver::save_the_log($error, 'users.txt');
        }
    }

   public  function  activate_user($token , $user_id){
        try{
            $sQuery = $this->db->prepare('UPDATE users SET active = 1 WHERE id = :ID AND activation_token = :token ');
            $sQuery->bindValue(':ID' , $user_id);
            $sQuery->bindValue(':token' , $token);
            $sQuery->execute();
            if(!$sQuery->rowCount()){
               return false;
                exit;
            }
           return true;
        }catch(PDOException $e){
            echo '{"status":"0","message":"Something went wrong, please contact the support"}';
            LogSaver::save_the_log($e, 'verify.txt');
        }


   }

    public function select_user_role_by_id($userId){
        try {
            $sQuery = $this->db->prepare('SELECT user_role_id from users WHERE id=:userId');
            $sQuery->bindValue(':userId', $userId);
            $sQuery->execute();
            $user = $sQuery->fetch();
            return $user;
        } catch (PDOException $error) {
            LogSaver::save_the_log($error, 'select-user-role.txt');
            die();
        }
    }

    public function get_users($searchBy)
    {
        try {
            $sQuery = $this->db->prepare('SELECT u.id, u.username, u.email, u.date_createad, u.user_role_id, u.active
                                                    FROM users u
                                                    WHERE u.username REGEXP :searchTerm OR u.username REGEXP :searchTerm');
            $sQuery->bindValue(':searchTerm', $searchBy);
            $sQuery->execute();
            $result = $sQuery->fetchAll();
            return $result;
        } catch (PDOException $error) {
            LogSaver::save_the_log($error, 'get-user.txt');
            die();
        }
    }

    public function update_user_basics($userId, $iActive, $iRole)
    {
        /*
         * Michal: Admin can't grant another admin! At least for now.
         * Therefore system won't allow to change role to 6
         */
        if ($iRole == 6) {
            return false;
            die();
        }
        /*
         * So now we cannot gain the admin rights. But we also don't want to
         * denied admin rights. Therefore the idea is to check the user rights beforehand again
         */
        try {
            $sQuery = $this->db->prepare('SELECT user_role_id
                                                    FROM users
                                                    WHERE id = :iUserId');
            $sQuery->bindValue(':iUserId', $userId);
            $sQuery->execute();
            $userRole = $sQuery->fetch();
            if($userRole['user_role_id'] == 6){
                return false;
                die();
            }
        } catch (PDOException $error) {
            LogSaver::save_the_log($error, 'update-user-basics.txt');
            return false;
            die();
        }
        try {
            $sQuery = $this->db->prepare('UPDATE users
                                                    SET active = :iActive, user_role_id = :iRole
                                                    WHERE id = :iUserId');
            $sQuery->bindValue(':iActive', $iActive);
            $sQuery->bindValue(':iRole', $iRole);
            $sQuery->bindValue(':iUserId', $userId);
            $sQuery->execute();
            if (!$sQuery->rowCount()) {
                return false;
                exit;
            }
            return true;
        } catch (PDOException $error) {
            LogSaver::save_the_log($error, 'update-user-basics.txt');
            return false;
            die();
        }
    }
}
// for testing
// $modeltest = new Users;
// $modeltest->read_users();
