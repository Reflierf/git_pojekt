<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */

session_start();
//error_reporting(0);

include 'database/connection.php';
include 'function/general.php';
include 'function/users.php';
include 'function/menu.php';
include 'function/mail.php';
include 'function/news.php';
include 'function/article.php';
require 'function/message.php';

//echo $current_file = $_SERVER['SCRIPT_NAME'].$_SERVER['REQUEST_URI'];
//echo $current_file = $_SERVER['REQUEST_URI'].' ';
//var_dump(parse_url($current_file));
$current_web = $_SERVER['SCRIPT_NAME'];
$current_web = explode('/', $_SERVER['SCRIPT_NAME']); //debug_r($current_web);
$current_file = explode('/',$_SERVER['REQUEST_URI']); //debug_r($current_file);
$current_file = end($current_file);
//debug_r($current_file);

if (logged_in() === true) {
    $session_user_id = $_SESSION['user_id'];
    $user_data = user_data($dbc, $session_user_id, 'user_id', 'user_name', 'user_password', 'user_firstname', 'user_lastname', 'user_email', 'user_secret_mail',  
                        'user_active', 'user_password_recover', 'user_type', 'user_allow_email', 'user_profile');
    if (user_active($dbc, $user_data['user_name']) === false) {
        session_destroy();
        header("Location: index.php");
        exit();
    }
    if ($current_file !== 'index.php?page=changepassword' && $current_file !== 'index.php?page=logout' && $user_data['user_password_recover'] == 1) {
        header("Location: index.php?page=changepassword");
        exit();
    }
}

if (isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])) {
    unset($_SESSION['admin_id']);
}

$errors = array();

$corepath = dirname(__DIR__);
//debug_r(__FILE__);



?>