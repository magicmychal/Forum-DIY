<?php
// Used when the submitted login password doesn't match the username.
class FailedLogin {

    public static function save_attempt($username){
        $ipAddress = self::get_ip_address();
        $ipLocation = self::get_ip_location($ipAddress);
        $newToken = bin2hex(openssl_random_pseudo_bytes(16));

        $model = new Users;
        $queryResponse = $model->select_user_by_username($username);
        if($queryResponse){
            $email = $queryResponse['email'];
            $userId = $queryResponse['id'];
            try {
                $sQuery = $model->db->prepare('INSERT INTO failed_login 
                VALUES(null, :username, null , :ip, :location, 0)');
                $sQuery->bindValue(':username', $username);
                $sQuery->bindValue(':ip', $ipAddress);
                $sQuery->bindValue(':location', $ipLocation);
                $sQuery->execute();
    
                if (!$sQuery->rowCount()) {
                    echo '{"status":"0","message":"Wrong username or password"}';
                    exit;
                }
    
                self::count_attempts($username, $newToken, $email, $userId, $ipLocation);
    
                
            } catch (PDOException $error) {
                LogSaver::save_the_log($error, 'failed-login.txt');
                exit;
            }
        }else{
            echo '{"status":"0","message":"Wrong username or password"}';
            exit;
        }
        
    }

    public static function count_attempts($username, $newToken, $email, $userId, $ipLocation){
        $model = new Model;
        try {
            $sQuery = $model->db->prepare('SELECT COUNT(username) AS failed_attempts
            FROM failed_login WHERE username = :username AND archived = 0');
            $sQuery->bindValue(':username', $username);
            $sQuery->execute();
            $result = $sQuery->fetch();
            if($result['failed_attempts']>4){
               self::deactivate_user($username, $newToken);
               mailer::sent_mail($email, $newToken , $userId , $username, $ipLocation);
            }else{
                echo '{"status":"0","message":"Wrong user name or password"}';
            }

        } catch (PDOException $error) {
            LogSaver::save_the_log($error, 'failed-login.txt');
            exit;
        }
    }

    public static function deactivate_user($username, $newToken){
        $model = new Model;
        try{
            $sQuery = $model->db->prepare('UPDATE users SET active = 0, activation_token = :token WHERE username = :username;');
            $sQuery->bindValue(':username', $username);
            $sQuery->bindValue(':token', $newToken);
            $sQuery->execute();
            if(!$sQuery->rowCount()){
                echo '{"status":"0","message":"User was not deactivated "';
                    exit;
                }
                echo '{"status":"0","message":"User was deactivated "}';
        }catch(PDOException $e){
            LogSaver::save_the_log($e, 'failed-login.txt');
            exit;
        }
    }
    // source: https://stackoverflow.com/questions/13646690/how-to-get-real-ip-from-visitor
    public static function get_ip_address(){
        if( array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
            if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',')>0) {
                $addr = explode(",",$_SERVER['HTTP_X_FORWARDED_FOR']);
                return trim($addr[0]);
            } else {
                return $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
        }
        else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }
    // source: https://stackoverflow.com/questions/409999/getting-the-location-from-an-ip-address
    public static function get_ip_location($ip){
        // TODO: get rid of placeholder, Peter: localhost ip is ::1 and doesnt return any location I added an ip here for testing
        if($ip= '::1'){
            return 'localhost';
        }
        $details = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));

        return $details->city.', '.$details->country;
    }

    public static function reactivate_user($token, $userId){
        $model = new Users;
        if(!$model->activate_user($token, $userId)){
            
            $tittle = "Failure";
            $message = "User was not activated";
            require_once("./includes/views/verify_user.php");
            exit;
        }

        if(!self::archive_attempts($userId)){
            
            $tittle = "Failure";
            $message = "User was not activated";
            require_once("./includes/views/verify_user.php");
            exit;
        } else {

            $tittle = "Success";
            $message = "User was activated";
            require_once("./includes/views/verify_user.php");
        }
        

    }
    
    public static function archive_attempts($userId){
        $model = new Users;
        $queryResponse = $model->select_username_by_id($userId);
        if($queryResponse){
            $username = $queryResponse['username'];
            try{
                $sQuery = $model->db->prepare('UPDATE failed_login SET archived = 1 WHERE username = :username;');
                $sQuery->bindValue(':username', $username);
                $sQuery->execute();
                if(!$sQuery->rowCount()){
                    
                    return false;
                }
                
                return true;
            }catch(PDOException $e){
                LogSaver::save_the_log($e, 'failed-login.txt');
                exit;
            }
        }else{
            return false;
        }

    }

    public static function report(){
        
        $tittle = "Thank you for your response";
        $message = "We are investigating the situation and 
        we will notify you as soon as we have more details.";
        require_once("./includes/views/verify_user.php");
        exit;       
        
    }
}