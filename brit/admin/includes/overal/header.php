<?php
if (!logged_in()) {
    //echo "true";
    header("Location: login.php");
    exit();
}

?>
<!DOCTYPE HTML>
<html>
    <?php 
    include 'includes/head.php';
    ?>
    <body>
        <!-- $session_admin_id: <?php //echo $_SESSION['admin_id'];?><br /> -->
        
        <?php
        include 'includes/header.php';
        ?>
        <?php include 'includes/nav.php'; ?>
        <section>
            
            
            
            <?php //include 'includes/widgets/asside.php'; ?>