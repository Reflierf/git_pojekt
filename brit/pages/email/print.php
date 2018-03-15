<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */

if ((isset($_GET['id']) && !empty($_GET['id']))) {
    //debug_r($_GET);
    $email = get_email($dbc, $_GET['id']);
    $sender = $_GET['sender'];
    //debug_r($email);    
}

?>
<header>
    <p class="page_title">Nyomtatás: <?php echo $email['send_email_subject']; ?></p>
</header>

<section>

    <div id="printableArea" style="color: black; font-size: 12pt;">
          <h2 style="text-align: center;"><?php echo $email['send_email_subject']; ?></h2>
          <br />
          <p>Küldte: <?php echo $email['user_name']; ?></p>
          <p>Ekkor: <?php echo $email['send_email_date']; ?></p>
          <br />
          <p style="text-align: justify;"><?php echo $email['send_email_message']; ?></p>
          <br />
    </div>
    
</section>

<footer>
    <ul class="bevitel" style="width: 100%;">
        <button class="button buttonLong" type="submit" id="btn_send" name="btn_send" onclick="printDiv('printableArea')"><i class="fa fa-print" aria-hidden="true"></i> Nyomtatás</button>
    </ul>
    <p class="footer_text">&copy; 2017, Relierf</p>
    <div class="clear"></div>
</footer>