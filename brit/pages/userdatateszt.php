<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */

//print_r($errors); echo '<br />';

?>
    
    <p style="font-size: 18pt!important; font-weight: bold!important;">User Data teszt</p>
    
    <?php
    if (isset($_SESSION['user_id'])) {
   
        //$petra = user_data($dbc, $session_user_id, 'user_name');
        //debug_r($petra);
        update_user($dbc, array('user_password_recover' => '1'), 2);
    }
    ?>