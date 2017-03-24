<?php 

switch($page['type']){
    case "start":
        //Code for the start page
        
        break;
    case "reklamefilm":
        $file = __DIR__ . '/click.log';
        // Open the file to get existing content
        $current = file_get_contents($file);
        // Append a new person to the file
        $_SERVER['HTTP_REFERER'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        $current .= date("Y-m-d H:i:s") . " " . $_SERVER['REMOTE_ADDR'] . " " . $_SERVER['HTTP_REFERER'] . "\n";
        // Write the contents back to the file
        file_put_contents($file, $current);
        $url = "https://youtu.be/Xgs9RPmDJUE";
        header("Location: $url");
        die("<script>window.location.href = '$url';</script>");
        break;
}

//Print header
//$header = $template->getTemplateFile('header.html');
//$template->printTemplate($header, $perm_vars, $variables, $vars);

//Print menu
//$menu = $template->getTemplateFile('menu.html');
//$template->printTemplate($menu, $perm_vars, $variables, $vars);

//Print page content
$template->printTemplate($content, $perm_vars, $variables, $vars);

//Print footer
//$footer = $template->getTemplateFile('footer.html');
//$template->printTemplate($footer, $perm_vars, $variables, $vars);

?>