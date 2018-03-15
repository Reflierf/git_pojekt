<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */



?>

            <aside class="col-lg-3">
                <?php
                if (logged_in()) {
                    include 'modules/loggedin.php';
                } else {
                    include 'modules/login.php';
                }
                include 'modules/colorselector.php';
                include 'modules/user_count.php';
                include 'modules/kakuk.php';
                ?>
                
            </aside>
            