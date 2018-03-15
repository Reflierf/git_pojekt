<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */

ob_start();
include 'core/init.php';

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
        <?php //include 'includes/nav.php'; ?>
        
        <section>
            
            
            
            <?php //include 'includes/widgets/asside.php';
            include 'includes/overal/navigation.php';?>


            <?php include 'pages/main.php'; ?>
            <div class="clear"></div>
        </section> <!-- -->
        
        <?php include 'includes/footer.php'; ?>
    </body>
    <?php include 'core/setup/js.php'; ?>
</html>

<?php
ob_end_flush();
?>