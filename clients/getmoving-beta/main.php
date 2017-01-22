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
    $stmt = $db->prepare("SELECT userID, username, name, email FROM `".$tbl['getmoving_user']."` WHERE userID = :userID");
    $stmt->execute(array(
        'userID' => $_SESSION['userID']
    ));
    if($userinfo = $stmt->fetch(PDO::FETCH_ASSOC)){
        foreach($userinfo as $k => $v){ if($k == 'password'){continue;}
            $vars['user_'.$k] = $v;
        }
    }
}
 
switch($page['type']){
    case "map":
        //Get all locations
        $variables['locations'] = array();
        $stmt = $db->query("SELECT locationID, lat, lng, name, description, icon_type FROM `".$tbl['getmoving_location']."`");
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
            $stmt = $db->prepare("SELECT userID AS id, DATE_FORMAT(`start`,'%H:%i') AS start_time, DATE_FORMAT(`stop`,'%H:%i') AS stop_time FROM `".$tbl['getmoving_user_location']."` WHERE NOW() BETWEEN  start AND stop AND locationID = :locationID");
            $stmt->execute(array(
                'locationID' => $location['locationID']
            ));
            $user_locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $active_users = json_encode($user_locations);
            
            $variables['locations'][] = array(
                'location_id' => $location['locationID'],
                'location_lat' => $location['lat'],
                'location_lng' => $location['lng'],
                'location_type' => $location['icon_type'],
                'location_name' => $location['name'],
                'location_description' => $location['description'],
                'location_areas' => implode(', ', $areas),
                'location_activities' => implode(', ', $activities),
                'location_users' => $active_users,
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
        //Verify all info has been sent
        if(!isset($_POST['leave']) || !isset($_POST['leave_time']) || !isset($_POST['locationID'])){
            $_SESSION['error'] = 'Mangler data';
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
        die();
        break;
    case "login":
        //Redirect user to map page if already logged in
        if(isset($_SESSION['userID']) && is_numeric($_SESSION['userID'])){
            header("Location: /".get_pname($db, $tbl, "map").".html");
        }
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
        }else if(isset($_POST['username'])){
            //Login
        }else if(isset($_POST['password2'])){
            
        }
                
        if($success){
            $url = "/".get_pname($db, $tbl, "map").".html";
        }else{
            $url = "/".get_pname($db, $tbl, "login")."/error=".implode(",", $login->error).".html";
        }
        header("Location: $url");
        die("<script>window.location.href = '$url';</script>");
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