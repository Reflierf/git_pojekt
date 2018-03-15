<?php
//$url = $_SERVER['REQUEST_URI'];
//var_dump(parse_url($url)); 
if (!empty($_GET)) {
    @$file_name = $_GET['page'].'.php';
    //print_r($file_name);
    if (is_file('pages/'.$file_name)) {
        include("pages/$file_name");
    } else if (is_file('includes/system/'.$file_name)){
        include("includes/system/$file_name");
    } else if (is_file('includes/widgets/modules/'.$file_name)) {
        include("includes/widgets/modules/$file_name");
    } else if (is_file('admin/'.$file_name)) {
        include("admin/$file_name");
    } else {
        echo 'Nemlétező oldal!';
    }
} else {
    include 'pages/main.php';
}






 //include ('pages/main.php');
 
 
 
?> 