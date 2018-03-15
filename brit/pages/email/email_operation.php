<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */

if (isset($_POST['id']) && !empty($_POST['id']) && isset($_POST['operation'])) {
    //debug_r($_POST); die();
    //$email = get_email($dbc, $_POST['id']);
    $operation = $_POST['operation']; $id = $_POST['id'];
    $sender = $_POST['sender'];
}

switch ($operation) {
    case 'ansver':
        header("Location: index.php?page=reply&id=$id&sender=$sender");
        exit();
        break;
    case 'forward':
        header("Location: index.php?page=forward&id=$id&sender=$sender");
        exit();
        break;
    case 'print':
        header("Location: index.php?page=print&id=$id&sender=$sender");
        exit();
        break;
    case 'delete':
       	header("Location: index.php?page=delete&id=$id&sender=$sender");
        exit();
        break;
        
}

?>