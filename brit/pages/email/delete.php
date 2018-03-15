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

if (isset($_POST["btn_delete"])) {
    delete_email($dbc, $_POST['emailid']);
    header("Location: index.php?page=email&email=$sender");
    exit();
}

$delete_message = 'Valóban törlöd a levelet?';
?>

<header>
    <p class="page-title">Törlés</p>
</header>
<form action="" method="post">
    <input type="hidden" name="emailid" value="<?php echo $email['send_email_id']; ?>"/>
    <section>
        <div class="letter_box">
            <div class="letter_data">
                <span class="sender">Küldő: <?php echo $email['user_name']; ?></span>
                <span class="date">Érkezett: <?php echo date('Y/m/d H:t', strtotime($email['send_email_date'])); ?></span>
            </div>
            <div class="clear"></div>
            <hr />
            <div class="letter_subject"><?php echo $email['send_email_subject']; ?></div>
            <div id="anim_vez<?php echo $row['send_email_id']; ?>" class="letter_message"><?php echo $email['send_email_message']; ?></div>
        </div>
    </section>
    
    <footer>
        <ul class="bevitel" style="width: 100%;">
            <button class="button buttonLong" type="submit" id="btn_delete" name="btn_delete" onclick="return confirm('<?php echo $delete_message; ?>')">
                <i class="fa fa-trash" aria-hidden="true"></i> Törlés
            </button>
        </ul>
        <p class="footer_text">&copy; 2017, Relierf</p>
        <div class="clear"></div>
    </footer>

</form>