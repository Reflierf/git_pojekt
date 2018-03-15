<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */

session_start();
session_destroy();
header("Location: index.php");

?>