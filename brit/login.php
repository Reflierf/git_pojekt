<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */

include 'core/init.php';


//if (empty($_POST) === false)
if (empty($_POST) === false) {
    //print_r($_POST); die();
    $username = $_POST['user_name'];
    $password = $_POST['user_password'];
    //echo $username.', '.$password;
    
    if (empty($username) === true || empty($password) === true) {
        $errors[] = "A belépéshez szükség van egy névre és egy jelszóra!";
    } else if (user_exists($dbc, $username) === false) {
        $errors[] = "Nem találtam ilyen felhasználó nevet. Valóban regisztráltál?";
    } else if (user_active($dbc, $username) === false) {
        $errors[] = "Ez a felhasználónév még nem lett aktíválva!";
    } else {
        
        if (strlen($password) > 32) {
            $errors[] = 'A jelszó túl hosszú!'; 
        }
        //print_r($errors); die();
        
        $login = login($dbc, $username, $password);
        if ($login == false) {
            $errors[] = "A felhasználónév vagy a jelszó nem helyes (esetleg mindkettő...)!";
        } else {
           //die($login);
           $_SESSION['user_id'] = $login;
           header("Location: index.php");
           exit();
        }
    }
    
} else {
    $errors[] = "Nincs érvényes adat!";
}

include ('includes/overal/header.php');
if (empty($errors) === false) {
    ?>
   	<p class="pt-center" style="font-size: 1.5rem; color: red; font-weight: bold;">A bejelentkezés nem sikerült! Oka: <br /></p>
    
    <?php
    echo output_errors($errors);
}
include ('includes/overal/footer.php');
?>