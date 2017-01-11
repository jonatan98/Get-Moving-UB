<?php
//Hack to logout
if(isset($_GET['logout'])){
    unset($_SESSION['userID']);
}

/*
 * Fetch user info if logged in
 */
if(isset($_SESSION['userID']) && is_numeric($_SESSION['userID'])){
    $stmt = $db->prepare("SELECT * FROM `".$tbl['getmoving_user']."` WHERE userID = :userID");
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
            
            $variables['locations'][] = array(
                'location_lat' => $location['lat'],
                'location_lng' => $location['lng'],
                'location_type' => $location['icon_type'],
                'location_name' => $location['name'],
                'location_description' => $location['description'],
                'location_areas' => implode(', ', $areas),
                'location_activities' => implode(', ', $activities),
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
    case "login":
        //Redirect user to map page if already logged in
        if(isset($_SESSION['userID']) && is_numeric($_SESSION['userID'])){
            header("Location: /".get_pname($db, $tbl, "map").".html");
        }
        break;
    case "handle_login":
        //print_r($_REQUEST);
        //$_SESSION['state'] = md5(uniqid(rand(), TRUE));  //CSRF protection
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