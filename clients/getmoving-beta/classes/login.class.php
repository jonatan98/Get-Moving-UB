<?php
if(!class_exists("GM_Login")){
class GM_Login{
    function __construct(){
        require CONNECT_TO_DB;
        $this->db = $db;
        $this->tbl = $tbl;
        
        $this->error = array();
        $this->new_user = false;
    }
    
    function fb(){
        //Get info from user account and then register / login
        $code = $_GET['code'];
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'].'/';
        $currentUrl = $protocol.$domainName."?pid=".get_pid($this->db, $this->tbl, "handle_login");
        
        //Redirect if the code is not sent
        if(empty($code)) {
            $_SESSION['state'] = md5(uniqid(rand(), TRUE)); //CSRF protection
            $dialogUrl = "https://www.facebook.com/dialog/oauth?client_id=" 
            . FB_APP_ID . "&redirect_uri=" . urlencode($currentUrl) . "&state="
            . $_SESSION['state'];

            die("<script> top.location.href='" . $dialogUrl . "'</script>");
        }
        
        //Get user data from code
        /*if($_REQUEST['state'] == $_SESSION['state']) {*/
            //Fetch data
            $tokenUrl = "https://graph.facebook.com/oauth/access_token?"
            . "client_id=" . FB_APP_ID . "&redirect_uri=" . urlencode($currentUrl)
            . "&client_secret=" . FB_APP_SECRET . "&code=" . $code . "&scope=public_profile,email";

            $response = @file_get_contents($tokenUrl);
            $params = null;
            parse_str($response, $params);

            $graphUrl = "https://graph.facebook.com/me?access_token=" 
            . $params['access_token'];

            $user = json_decode(file_get_contents($graphUrl));
            
            //Store the fetched info in the database
            $this->fb_name = $user->name;
            $this->email = isset($user->email) ? $user->email : '';
            $this->facebook_id = $user->id;
            
            //Sjekk om brukeren er registrert
            $stmt = $this->db->prepare("SELECT userID FROM `".$this->tbl['getmoving_user']."` WHERE facebookID = :facebookID");
            if($stmt->execute(array(
                'facebookID' => $this->facebook_id
            ))){
                return $this->login();
            }
            return $this->register();
        /*}else{
            echo("The state does not match. You may be a victim of CSRF.");
        }*/
        return false;
    }
    
    function login(){
        //Check if session has been set and is valid
        
        //Login user with username/pass or fb code
        if(isset($this->facebook_id)){
            //Facebook login
            $stmt = $this->db->prepare("SELECT userID FROM `".$this->tbl['getmoving_user']."` WHERE facebookID = :facebookID");
            $stmt->execute(array(
                'facebookID' => $this->facebook_id
            ));
            if($res = $stmt->fetch(PDO::FETCH_ASSOC)){
                //Make some kind of session system
                $_SESSION['userID'] = $res['userID'];
                return true;
            }
            //Troubleshooting
            $this->error[] = "Didn't find user in database";
        }else if(isset($_POST['username']) && (isset($_POST['password']) || isset($_POST['pass1']))){
            $username = $_POST['username'];
            $password = isset($_POST['password']) ? $_POST['password'] : $_POST['pass1'];
            if($username === '' || $password === ''){
                $this->error[] = "Du må fylle inn begge feltene.";
                return false;
            }
            //Normal login
            $stmt = $this->db->prepare("SELECT userID, password FROM `".$this->tbl['getmoving_user']."` WHERE LOWER(username) = LOWER(:username) AND password != ''");
            $stmt->execute(array(
                'username' => $username
            ));
            if($user = $stmt->fetch(PDO::FETCH_ASSOC)){
                if(password_verify($password, $user['password'])){
                    //success
                    $_SESSION['userID'] = $this->generateSession($user['userID']);
                    return true;
                }
            }
            $this->error[] = "Feil brukernavn eller passord.";
        }
        return false;
    }
    
    function register(){
        $this->new_user = true;
        //Register new user
        $datetime = (new DateTime())->format('Y-m-d H:i:s');
        if(isset($this->fb_name) && isset($this->email) && isset($this->facebook_id)){
            //Facebook register
            $nme = explode(" ", $this->fb_name);
            $lastname = count($nme) > 1 ? $nme[count($nme) - 1] : '';
            if(count($nme) > 1){ unset($nme[count($nme) - 1]); }
            $firstname = implode(' ', $nme);
            
            $stmt = $this->db->prepare("INSERT INTO `".$this->tbl['getmoving_user']."` 
                (facebookID, username, password, firstname, lastname, email, register_datetime)
                VALUES
                (:facebookID, :username, :password, :firstname, :lastname, :email, :register_datetime)");
            if($stmt->execute(array(
                'facebookID' => $this->facebook_id,
                'username' => '',
                'password' => '',
                'firstname' => $firstname,
                'lastname' => $lastname,
                'email' => $this->email,
                'register_datetime' => $datetime
            ))){
                //Logg inn the user
                return $this->login();
            }
            //Troubeshoot
            $this->error[] = "Failed to save user in db";
        }else if(isset($_POST['username']) && isset($_POST['pass1'])){
            //Verify variables
            $username = isset($_POST['username']) ? $_POST['username'] : '';
            $pass1 = isset($_POST['pass1']) ? $_POST['pass1'] : '';
            $pass2 = isset($_POST['pass2']) ? $_POST['pass2'] : '';
            $firstname = isset($_POST['firstname']) ? $_POST['firstname'] : '';
            $lastname = isset($_POST['lastname']) ? $_POST['lastname'] : '';
            $email = isset($_POST['email']) ? $_POST['email'] : '';
            
            //Check if passwords match
            if($pass1 !== $pass2){
                $this->error[] = "Passordene må være like";
                return false;
            }
            //Check if username is taken
            $stmt = $this->db->prepare("SELECT userID FROM `".$this->tbl['getmoving_user']."` WHERE LOWER(username) = LOWER(:username)");
            if($stmt->execute(array(
                'username' => $username
            ))){
                $this->error[] = "Brukernavnet er allerede tatt";
                return false;
            }
            //Hash password
            $options = [
              'cost' => 11
            ];
            $password = password_hash($pass1, PASSWORD_BCRYPT, $options);
            //Normal register
            $stmt = $this->db->prepare("INSERT INTO `".$this->tbl['getmoving_user']."` 
                (facebookID, username, password, firstname, lastname, email, register_datetime)
                VALUES
                (:facebookID, :username, :password, :firstname, :lastname, :email, :register_datetime)");
            if($stmt->execute(array(
                'facebookID' => '',
                'username' => $username,
                'password' => $password,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'email' => $email,
                'register_datetime' => $datetime
            ))){
                //Logg inn the user
                return $this->login();
            }
            $this->error[] = "Failed to save user in db";
            $this->error[] = json_encode($this->db->errorInfo());
        }
        return false;
    }
    
    function generateSession($userID){
        
        return $userID;
    }
}
}