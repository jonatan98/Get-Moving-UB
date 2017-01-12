<?php
if(!class_exists("GM_Login")){
class GM_Login{
    function __construct(){
        require CONNECT_TO_DB;
        $this->db = $db;
        $this->tbl = $tbl;
        
        $this->error = array();
    }
    
    function fb(){
        //Get info from user account and then register / login
        $code = $_GET['code'];
        $app_id = "553540261437395";
        $app_secret = "c1773e735cf65bcf837b0286267744bd";
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'].'/';
        $my_url = $protocol.$domainName."?pid=".get_pid($this->db, $this->tbl, "handle_login");
        
        //Redirect if the code is not sent
        if(empty($code)) {
            $_SESSION['state'] = md5(uniqid(rand(), TRUE)); //CSRF protection
            $dialog_url = "https://www.facebook.com/dialog/oauth?client_id=" 
            . $app_id . "&redirect_uri=" . urlencode($my_url) . "&state="
            . $_SESSION['state'];

            die("<script> top.location.href='" . $dialog_url . "'</script>");
        }
        
        //Get user data from code
        /*if($_REQUEST['state'] == $_SESSION['state']) {*/
            //Fetch data
            $token_url = "https://graph.facebook.com/oauth/access_token?"
            . "client_id=" . $app_id . "&redirect_uri=" . urlencode($my_url)
            . "&client_secret=" . $app_secret . "&code=" . $code . "&scope=public_profile,email";

            $response = @file_get_contents($token_url);
            $params = null;
            parse_str($response, $params);

            $graph_url = "https://graph.facebook.com/me?access_token=" 
            . $params['access_token'];

            $user = json_decode(file_get_contents($graph_url));
            
            //Store the fetched info in the database
            $this->fb_name = $user->name;
            if(isset($user->email)){
                $this->email = $user->email;
            }else{
                $this->email = '';
            }
            $this->facebook_id = $user->id;
            
            //Sjekk om brukeren er registrert
            $stmt = $this->db->prepare("SELECT userID FROM `".$this->tbl['getmoving_user']."` WHERE facebookID = :facebookID");
            $stmt->execute(array(
                'facebookID' => $this->facebook_id
            ));
            if($res = $stmt->fetch(PDO::FETCH_ASSOC)){
                return $this->login();
            }
            return $this->register();
        /*}else{
            echo("The state does not match. You may be a victim of CSRF.");
        }*/
        return false;
    }
    
    function login(){
        //Login user with username/pass or fb code
        if(isset($this->facebook_id)){
            //Facebook login
            echo "Facebook login";
            $stmt = $this->db->prepare("SELECT userID FROM `".$this->tbl['getmoving_user']."` WHERE facebookID = :facebookID");
            $stmt->execute(array(
                'facebookID' => $this->facebook_id
            ));
            if($res = $stmt->fetch(PDO::FETCH_ASSOC)){
                //Make some kind of session system
                $_SESSION['userID'] = $res['userID'];
                return true;
            }else{
                //Troubleshooting
                $this->error[] = "Didn't find user in database";
            }
        }else if(isset($_POST['username']) && isset($_POST['password'])){
            echo "Normal login";
        }
        return false;
    }
    
    function register(){
        //Register new user
        if(isset($this->fb_name) && isset($this->email) && isset($this->facebook_id)){
            //Facebook register
            echo "Facebook register";
            
            $datetime  = date('Y-m-d h:i:s');
            
            $stmt = $this->db->prepare("INSERT INTO `".$this->tbl['getmoving_user']."` 
                (facebookID, username, name, email, register_datetime)
                VALUES
                (:facebookID, :username, :name, :email, :register_datetime)");
            $ex = $stmt->execute(array(
                'facebookID' => $this->facebook_id,
                'username' => '',
                'name' => $this->fb_name,
                'email' => $this->email,
                'register_datetime' => $datetime
            ));
            
            if($ex){
                //Logg inn the user
                return $this->login();
            }else{
                //Troubeshoot
                $this->error[] = "Failed to save user in db";
            }
        }else if(isset($_POST['username']) && isset($_POST['password'])){
            echo "Normal register";
        }
        return false;
    }
}
}