<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */

if ((isset($_GET['id']) && !empty($_GET['id']))) {
    $email = get_email($dbc, $_GET['id']);
    $sender = $_GET['sender'];
    //debug_r($email);    
}

if (isset($_POST['btn_send'])) {
    if (empty($_POST['to']) === true) {
        $errors[] = 'Legalább egy nevet meg kell adni.';
    } else {
        $user_names = explode(',', $_POST['to']);
        foreach ($user_names as &$name) {
            $name = trim($name);
        }
        $user_ids = fetch_user_ids($dbc, $user_names);
        //debug_r($user_ids);
		if (count($user_ids) != count($user_names)) {
			$errors[] = 'A következő felhasználók nem találhatóak: '.implode(', ', array_diff($user_names, array_keys($user_ids)));
		}
    }
}

if (isset($_POST['btn_send']) && empty($errors)) {
    foreach ($user_ids as $ids) {
        $user = user_data($dbc, $ids, 'user_name', 'user_email');
        $email_text = $user['user_name'].' üzenetet küldött neked a Britanny Bod fan site oldalon keresztűl, ebben az időpontban: '.date('Y-m-d H:i').',';
        $email_text .= " ezzel a témával: <a href='http://localhost/brit/index.php'><b>Továbbítás: ".$email['send_email_subject']."</b></a>.";
        
        $email_data = array(
            'send_email_hash'       => mt_rand(1, 100000),
            'send_email_from'       => $email['send_email_to'],
            'send_email_to'         => $ids,
            'send_email_date'       => date('Y-m-d H:i'),
            'send_email_subject'    => "Továbbítás: ".$email['send_email_subject'],
            'send_email_message'    => $email['send_email_message'],
            'send_email_adress'     => $user['user_email'],
        );
        debug_r($email_data);
        
        html_mail($email_data['send_email_adress'], 'Leveled érkezett!', $email_text);
        $result = email_for_user($dbc, $email_data);
        //debug_r($result);
    }
    header("Location: index.php?page=email&email=$sender");
    exit();
}


$lr = chr(012);
$orig_message = 'Feladó: '.$email['user_name'].'<br />';
$orig_message .= 'Küldte: '.$email['send_email_date'].'<br />';
$orig_message .= 'Eredeti üzenet: <br />'.$lr;
$orig_message .= $email['send_email_message'].'<br />';

?>

<header>
    <p class="page_title">Továbbítás</p>
</header>

<div class="post_error pt-center">
    <?php
    if (!empty($errors) === true) {
        echo '<pre>';
        echo output_errors($errors);
        echo '</pre>';
    }
    ?>
</div><!-- end post_error -->

<form action="" method="post">

    <section>
        <ul class="bevitel">
            <input type="hidden" name="messageid" value="<?php echo $email['send_email_id']; ?>"/>
            <div>
                <p>
                    <?php echo $orig_message.'<br />'; ?>
                </p>
            </div>
            <p class="pt-center">Sorold fel azoknak a nevét, vasszővel elválasztva, akiknek továbbítani szeretnéd a fenti levelet.</p>
            <li>
                <input type="text" class="sor" id="to" name="to" value="" placeholder="nekik küldöd..."/>
            </li>
            
        </ul>
    </section>
    
    <footer>
        <ul class="bevitel">
            <button class="button buttonLong" type="submit" id="btn_send" name="btn_send"><i class="fa fa-forward" aria-hidden="true"></i> Küldése</button>
        </ul>
        <p class="footer_text">&copy; 2017, Relierf</p>
        <div class="clear"></div>
    </footer>
    
</form>