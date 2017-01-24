<?php
//Hack to logout
if(isset($_GET['logout'])){
    unset($_SESSION['userID']);
}

/*
 * Fetch user info if logged in
 */
$vars['user_userID'] = 0; $vars['user_name'] = ''; $vars['user_username'] = '';
if(isset($_SESSION['userID']) && is_numeric($_SESSION['userID'])){
    $stmt = $db->prepare("SELECT userID, username, firstname, lastname, email, register_datetime FROM `".$tbl['getmoving_user']."` WHERE userID = :userID");
    $stmt->execute(array(
        'userID' => $_SESSION['userID']
    ));
    if($userinfo = $stmt->fetch(PDO::FETCH_ASSOC)){
        foreach($userinfo as $k => $v){ if($k == 'password'){continue;}
            $vars['user_'.$k] = $v;
        }
    }
    //Format registered date
    $e_r = explode(" ", $vars['user_register_datetime']); $e_rd = explode("-", $e_r[0]); $e_rt = explode(":", $e_r[1]);
    $m = array('','Januar', 'Februar', 'Mars', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Desember');
    $vars['user_registered'] = "$e_rd[2]. {$m[intval($e_rd[1])]} $e_rd[0] $e_rt[0]:$e_rt[1]";
}
 
switch($page['type']){
    case "map":
        //Display intro popup
        $vars['display_intro_popup'] = isset($_COOKIE['seen_intro']) ? 'none' : 'block';
        setcookie('seen_intro', true);
        //Display error
        $vars['error'] = isset($_SESSION['error']) ? $_SESSION['error'] : ''; unset($_SESSION['error']);
        //Get all locations
        $variables['locations'] = array();
        $stmt = $db->query("SELECT locationID, lat, lng, name, description, icon_type FROM `".$tbl['getmoving_location']."` WHERE active = 1");
        $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($locations as $location){
            //Get the areas of the location
            $areas = array();
            $stmt = $db->prepare("SELECT a.areaID FROM `".$tbl['getmoving_location_area']."` AS la
                INNER JOIN `".$tbl['getmoving_area']."` AS a ON la.areaID = a.areaID
                WHERE la.locationID = :locationID");
            $stmt->execute(array(
                'locationID' => $location['locationID']
            ));
            $_areas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach($_areas as $a){ $areas[] = $a['areaID']; }
            //Get the sports of the locations
            $activities = array();
            $stmt = $db->prepare("SELECT a.activityID FROM `".$tbl['getmoving_location_activity']."` AS la
                INNER JOIN `".$tbl['getmoving_activity']."` AS a ON la.activityID = a.activityID
                WHERE la.locationID = :locationID");
            $stmt->execute(array(
                'locationID' => $location['locationID']
            ));
            $_activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach($_activities as $a){ $activities[] = $a['activityID']; }
            
            //Get all the active users
            $stmt = $db->prepare("SELECT userID AS id, DATE_FORMAT(`start`,'%H:%i') AS start_time, DATE_FORMAT(`stop`,'%H:%i') AS stop_time FROM `".$tbl['getmoving_user_location']."` WHERE ((NOW() BETWEEN  start AND stop) OR (start BETWEEN NOW() AND :later_dt AND stop > NOW())) AND locationID = :locationID AND cancelled = 0 AND left_early = 0");
            $stmt->execute(array(
                'locationID' => $location['locationID'],
                'later_dt' => ((new DateTime())->modify("+120 minute"))->format("Y-m-d H:i:s")
            ));
            $user_locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $active_users = array();
            $soon_active_users = array();
            foreach($user_locations as $ul){
                if(new DateTime() > new DateTime($ul['start_time'])){
                    $active_users[] = $ul;
                }else{
                    $soon_active_users[] = $ul;
                }
            }
            //Makes icon red if there are no active users
            $icon_type = 'active';
            if(count($soon_active_users) === 0 && count($active_users) === 0){
                $icon_type = 'inactive';
            }
            //Parse data as JSON
            $active_users = json_encode($active_users);
            $soon_active_users = json_encode($soon_active_users);
            
            $variables['locations'][] = array(
                'location_id' => $location['locationID'],
                'location_lat' => $location['lat'],
                'location_lng' => $location['lng'],
                'location_type' => $icon_type,
                'location_name' => $location['name'],
                'location_description' => $location['description'],
                'location_areas' => implode(', ', $areas),
                'location_activities' => implode(', ', $activities),
                'location_users' => $active_users,
                'location_soon_users' => $soon_active_users,
                'location_separator' => ','
            );
        }
        $variables['locations'][count($variables['locations']) - 1]['location_separator'] = '';
        //Get all areas
        $variables['areas'] = array();
        $stmt = $db->query("SELECT areaID, name FROM `".$tbl['getmoving_area']."` ORDER BY name ASC");
        $areas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($areas as $area){
            $variables['areas'][] = array(
                'area_id' => $area['areaID'],
                'area_name' => $area['name']
            );
        }
        //Get all sports
        $variables['activities'] = array();
        $stmt = $db->query("SELECT activityID, name FROM `".$tbl['getmoving_activity']."` ORDER BY name ASC");
        $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($activities as $activity){
            $variables['activities'][] = array(
                'activity_id' => $activity['activityID'],
                'activity_name' => $activity['name']
            );
        }
        break;
    case "handle_active_user":
        //Kick user out if not logged in
        if(!isset($_SESSION['userID'])){
            $_SESSION['error'] = 'Ikke logget inn';
            header("Location: /" . get_pname($db, $tbl, 'map') . ".html#error");
        }
        //Check for special actions
        if(isset($_POST['action']) && isset($_POST['locationID']) && $_POST['action'] !== 0){
            if($_POST['action'] == 'left'){
                if(!isset($_POST['arrival_time'])){
                    $_SESSION['error'] = '#2 Mangler data';
                    header("Location: /" . get_pname($db, $tbl, 'map') . ".html#error");
                }
                //User has left before estimated
                $now = (new DateTime())->format("Y-m-d H:i:s");
                $stmt = $db->prepare("UPDATE `".$tbl['getmoving_user_location']."` SET `left_early` = 1, `stop` = :now WHERE locationID = :locationID AND userID = :userID AND DATE_FORMAT(`start`,'%H:%i') = :start");
                if($stmt->execute(array(
                    'now' => $now,
                    'userID' => $_SESSION['userID'],
                    'locationID' => $_POST['locationID'],
                    'start' => $_POST['arrival_time']
                ))){
                    //Success
                    header("Location: /" . get_pname($db, $tbl, 'map') . ".html#success");
                }else{
                    //Failed
                    $_SESSION['error'] = 'Klarte ikke oppdatere';
                    header("Location: /" . get_pname($db, $tbl, 'map') . ".html#error");
                }
                die();
            }else if($_POST['action'] == 'cancel'){
                if(!isset($_POST['arrival_time'])){
                    $_SESSION['error'] = 'Mangler data';
                    header("Location: /" . get_pname($db, $tbl, 'map') . ".html#error");
                }
                //User will not come :-(
                $stmt = $db->prepare("UPDATE `".$tbl['getmoving_user_location']."` SET `cancelled` = 1 WHERE locationID = :locationID AND userID = :userID AND DATE_FORMAT(`start`,'%H:%i') = :start");
                if($stmt->execute(array(
                    'userID' => $_SESSION['userID'],
                    'locationID' => $_POST['locationID'],
                    'start' => $_POST['arrival_time']
                ))){
                    //Success
                    header("Location: /" . get_pname($db, $tbl, 'map') . ".html#success");
                }else{
                    //Failed
                    $_SESSION['error'] = 'Klarte ikke oppdatere';
                    header("Location: /" . get_pname($db, $tbl, 'map') . ".html#error");
                }
                die();
            }
        }
        //Verify all info has been sent
        if(!isset($_POST['leave']) || !isset($_POST['leave_time']) || !isset($_POST['locationID']) || !isset($_POST['action'])){
            $_SESSION['error'] = '#1 Mangler data';
            header("Location: /" . get_pname($db, $tbl, 'map') . ".html#error");
        }
        //Lagre data fra bruker
        $allowed_time_shortcuts = array_map(function($piece){
            return (string) $piece;
        }, array("15", "30", "45", "60", "90", "120"));
        
        //Get the start time
        $start = new DateTime();
        if(isset($_POST['arrival'])){
            if(in_array($_POST['arrival'], $allowed_time_shortcuts)){
                //Calculate x mins from now
                $start->add(new DateInterval('PT' . $_POST['arrival'] . 'M'));
            }else if($_POST['arrival'] == "time" && preg_match("/(2[0-3]|[01][0-9]):([0-5][0-9])/", $_POST['arrival_time'])){
                $lt = explode(':', $_POST['arrival_time']);
                $start->setTime(intval($lt[0]), intval($lt[1]));
            }else{
                //Invalid start time
                echo "Invalid start time<br>";
            }
        }
        
        //Get the duration of stay
        $stop = clone $start;
        if(in_array($_POST['leave'], $allowed_time_shortcuts)){
            //Calculate x mins from now
            $stop->add(new DateInterval('PT' . $_POST['leave'] . 'M'));
        }else if($_POST['leave'] == "time" && preg_match("/(2[0-3]|[01][0-9]):([0-5][0-9])/", $_POST['leave_time'])){
            $lt = explode(':', $_POST['leave_time']);
            $stop->setTime(intval($lt[0]), intval($lt[1]));
        }else{
            //Invalid start time
            echo "Invalid start time<br>";
        }
        
        //Check if the user has already registered activity within the timeframe
        $stmt = $db->prepare("SELECT user_locationID FROM `".$tbl['getmoving_user_location']."` WHERE ((NOW() BETWEEN  start AND stop) OR (start BETWEEN NOW() AND :later_dt AND stop > NOW())) AND locationID = :locationID AND userID = :userID AND cancelled = 0 AND left_early = 0");
        $stmt->execute(array(
            'locationID' => $_POST['locationID'],
            'userID' => $_SESSION['userID'],
            'later_dt' => ((new DateTime())->modify("+120 minute"))->format("Y-m-d H:i:s")
        ));
        if($activity = $stmt->fetch(PDO::FETCH_ASSOC)){
            //User is already active
            $stmt = $db->prepare("UPDATE `".$tbl['getmoving_user_location']."` SET start = :start, stop = :stop WHERE user_locationID = :luid");
            $res = $stmt->execute(array(
                'luid' => $activity['user_locationID'],
                'start' => ($start->format("Y-m-d H:i:s")),
                'stop' => ($stop->format("Y-m-d H:i:s"))
            ));
            if($res){
                header("Location: /" . get_pname($db, $tbl, 'map') . ".html#success");
            }else{
                header("Location: /" . get_pname($db, $tbl, 'map') . ".html#error");
            }
        }else{
            //User has no active record
            $stmt = $db->prepare("INSERT INTO `".$tbl['getmoving_user_location']."` (userID, locationID, start, stop, registered) VALUES (:uid, :lid, :start, :stop, :registered)");
            $res = $stmt->execute(array(
                'uid' => $_SESSION['userID'],
                'lid' => $_POST['locationID'],
                'start' => ($start->format("Y-m-d H:i:s")),
                'stop' => ($stop->format("Y-m-d H:i:s")),
                'registered' => (new DateTime())->format("Y-m-d H:i:s")
            ));
            if($res){
                header("Location: /" . get_pname($db, $tbl, 'map') . ".html#success");
            }else{
                header("Location: /" . get_pname($db, $tbl, 'map') . ".html#error");
            }
        }
        die();
        break;
    case "login":
        //Redirect user to map page if already logged in
        if(isset($_SESSION['userID']) && is_numeric($_SESSION['userID'])){
            header("Location: /".get_pname($db, $tbl, "map").".html");
        }
        //Display error
        $vars['error'] = isset($_SESSION['error']) ? $_SESSION['error'] : ''; unset($_SESSION['error']);
        //Display domain name for fb login
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'].'/';
        $vars['base_url'] = $protocol.$domainName;
        break;
    case "handle_login":
        require CLIENT_CLASS_DIR.'login.class.php';
        $login = new GM_Login();
        $success = false;
        if(isset($_GET['code'])){
            //Facebook login
            $success = $login->fb();
        }else if(isset($_POST['username']) && !isset($_POST['pass2'])){
            //Login
            $success = $login->login();
        }else if(isset($_POST['pass2'])){
            //Register
            $success = $login->register();
        }
        
        if($success){
            $url = "/".get_pname($db, $tbl, "map").".html";
        }else{
            $_SESSION['error'] = implode(', ', $login->error);
            $url = "/".get_pname($db, $tbl, "login").".html";
        }
        header("Location: $url");
        die("<script>window.location.href = '$url';</script>");
    case "profile_update":
        //Update profile data
        if(isset($_POST['username'])){
            $username = isset($_POST['username']) ? $_POST['username'] : "";
            $firstname = isset($_POST['firstname']) ? $_POST['firstname'] : "";
            $lastname = isset($_POST['lastname']) ? $_POST['lastname'] : "";
            $email = isset($_POST['email']) ? $_POST['email'] : "";
            if($username == '' || $firstname == '' || $lastname == '' || $email == ''){
                //Error
                $_SESSION['error'] = 'Alle felter må være fylt ut';
                header("Location: /" . get_pname($db, $tbl, 'profile_update') . ".html#error");
                die("Mangler variabler");
            }
            //Oppdater informasjon
            $stmt = $db->prepare("UPDATE `".$tbl['getmoving_user']."` SET username = :username, firstname = :firstname, lastname = :lastname, email = :email WHERE userID = :userID");
            if($stmt->execute(array(
                'username' => $username,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'email' => $email,
                'userID' => $_SESSION['userID']
            ))){
                //Success
                header("Location: /" . get_pname($db, $tbl, 'profile_update') . ".html#success");
                die("<script>window.location.href = '/" . get_pname($db, $tbl, 'profile_update') . ".html#success';</script>");
            }else{
                //Fail
                $_SESSION['error'] = 'Klarte ikke lagre informasjon';
                header("Location: /" . get_pname($db, $tbl, 'profile_update') . ".html#error");
                die("<script>window.location.href = '/" . get_pname($db, $tbl, 'profile_update') . ".html#error';</script>");
            }
        }
        //Display error
        $vars['error'] = isset($_SESSION['error']) ? $_SESSION['error'] : ''; unset($_SESSION['error']);
        break;
    case "profile_settings":
        //Kick if not logged in
        
        $vars['checked_anonymous_active'] = 'checked="checked"';
        $vars['checked_anonymous_chat'] = 'checked="checked"';
        $stmt = $db->prepare("SELECT * FROM getmoving_Setting WHERE userID = :userID");
        $stmt->execute(array(
            'userID' => $_SESSION['userID']
        ));
        if($usr = $stmt->fetch(PDO::FETCH_ASSOC)){
            $user_is_found = true;
            $vars['checked_anonymous_active'] = $usr['anonymous_active'] === 1 ? 'checked="checked"' : '';
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
            //Check if exists
            if(isset($user_is_found)){
                //Update
                $stmt = $db->prepare("UPDATE getmoving_Setting SET anonymous_active = :aa, anonymous_chat = :ac WHERE userID = :userID");
                if($stmt->execute(array(
                    'userID' => $_SESSION['userID'],
                    'aa' => isset($_POST['anonymous_active']) ? 1 : 0,
                    'ac' => isset($_POST['anonymous_chat']) ? 1 : 0
                ))){
                    $vars['checked_anonymous_active'] = isset($_POST['anonymous_active']) ? 'checked="checked"' : '';
                }else{
                    $this->error = "Klarte ikke lagre";
                }
            }else{
                //Create
                $stmt = $db->prepare("INSERT INTO getmoving_Setting (userID, anonymous_active) VALUES (:userID, :aa)");
                if($stmt->execute(array(
                    'userID' => $_SESSION['userID'],
                    'aa' => isset($_POST['anonymous_active']) ? 1 : 0
                ))){
                    $vars['checked_anonymous_active'] = isset($_POST['anonymous_active']) ? 'checked="checked"' : '';
                }else{
                    $this->error = "Klarte ikke lagre";
                }
            }
        }
        
        break;
}

//Print header
$header = $template->getTemplateFile('header.html');
$template->printTemplate($header, $perm_vars, $variables, $vars);

//Print menu
if(isset($_SESSION['userID'])){
    $menu = $template->getTemplateFile('menu_loggedin.html');
}else{
    $menu = $template->getTemplateFile('menu.html');
}
$template->printTemplate($menu, $perm_vars, $variables, $vars);

//Print page content
$template->printTemplate($content, $perm_vars, $variables, $vars);

//Print footer
$footer = $template->getTemplateFile('footer.html');
$template->printTemplate($footer, $perm_vars, $variables, $vars);
?>