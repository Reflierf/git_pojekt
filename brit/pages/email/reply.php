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

if (isset($_POST['btn_send'])) {
    //debug_r($_POST);
    //$email = get_email($dbc, $_POST['messageid']);
    if (empty($_POST['subject']) === true || empty($_POST['body_o']) === true  || empty($_POST['body_a']) === true) {
        $errors[] = 'Minden mezőt ki kell tölteni!';
    } else {
        $email_text = $user_data['user_name'].' üzenetet küldött neked a Britanny Bod fan site oldalon keresztűl, ebben az időpontban: '.date('Y-m-d H:i').',';
        $email_text .= "\r\n ezzel a témával: <a href='http://localhost/brit/index.php'><b>".$_POST['subject']."</b></a>.";

        $email_data = array(
            'send_email_hash'       => mt_rand(1, 100000),
            'send_email_from'       => $user_data['user_id'],
            'send_email_to'         => $email['send_email_from'],
            'send_email_date'       => date('Y-m-d H:i'),
            'send_email_subject'    => $_POST['subject'],
            'send_email_message'    => $_POST['body_a'],
            'send_email_adress'     => $email['user_email'],
        );
        //debug_r($email_data); die();
        html_mail($email_data['send_email_adress'], 'Leveled érkezett!', $email_text);
        $result = email_for_user($dbc, $email_data);
        //debug_r($result);
        header("Location: index.php?page=email&email=$sender");
        exit();
    }
}


$lr = chr(012);
$orig_message = 'Eredeti üzenet: '.$lr;
$orig_message .= $email['send_email_date'].$lr;
$orig_message .= $email['send_email_message'];

?>

<header>
    <p class="page_title">Neki küldesz levelet: <?php echo $email['user_name']; ?></p>
</header>

<form action="" method="post">

    <section>
        <input type="hidden" name="messageid" value="<?php echo $email['send_email_id']; ?>"/>
        <ul class="bevitel">
            <li>
                <input type="text" class="sor" id="subject" name="subject" value="Válasz erre: <?php echo $email['send_email_subject']; ?>" readonly=""/>
            </li>
            <li>
                <textarea id="body" name="body_o" placeholder="Levél tartalma..." readonly=""><?php echo $orig_message; ?></textarea>
            </li>
            <li>
                <textarea id="body" name="body_a" placeholder="válaszolj rá..." ></textarea>
            </li>
        </ul>
    </section>
    
    <footer>
        <ul class="bevitel">
            <button class="button buttonLong" type="submit" id="btn_send" name="btn_send"><i class="fa fa-envelope" aria-hidden="true"></i> E-mail küldése</button>
        </ul>
        <p class="footer_text">&copy; 2017, Relierf</p>
        <div class="clear"></div>
    </footer>
    
</form>