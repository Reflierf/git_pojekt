<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */

if (isset($_GET['emadili']) && !empty($_GET['emadili'])) {
    debug_r($_GET);
    $received = get_email($dbc, $_GET['emadili']);
    debug_r($received);
}

?>

<header>
    <p class="page_title"><?php echo $received['send_email_subject']; ?></p>
</header>

<section>
    <?php 
    echo $received['send_email_message'];
    ?>
</section>

<footer>
    <p class="footer_text">&copy; 2017, Relierf</p>
    <div class="clear"></div>
</footer>