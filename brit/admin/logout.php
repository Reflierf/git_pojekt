<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */

session_start();
//session_destroy();
unset($_SESSION['admin_id']);
header("Location: index.php");

?>