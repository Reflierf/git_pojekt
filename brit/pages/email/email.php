<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */

if (isset($_GET['email'])) {
    if (!empty($_GET['email'])) {
        if ($_GET['email'] == 'received') {
            $email = 'received';
        } else {
            $email = 'sentmail';
        }
    } else {
        $errors[] = 'Valami hiba van, mert nincsenek se bejövő, se kimenő levelek.';
    }  
} else if (isset($_GET['userid'])) {
    if (!empty($_GET['userid'])) {
        $userid = $_GET['userid'];
        $cimzett = user_data($dbc, $userid, 'user_name', 'user_email');
    } else {
        $errors[] = 'Nem valós felhasználó azonosító.';
    }
} else if (isset($_GET['ansver'])) {
    $ansver = $_GET['ansver'];
} else if (isset($_GET['success']) && !empty($_GET['success'])) {
    $result = get_last_email($dbc, $_GET['success'], $_GET['to']);
    if ($result !== false) {
        //$timestamp = strtotime($result['send_email_date']);
        //debug_r($result);
        ?>
       	<header>
            <p class="page_title">Elküldtél egy levelet</p>
        </header>
        <section>
            Címzett: <?php echo $result['user_name']; ?><br />
            Ideje: <?php echo date('Y-m-d H:i', strtotime($result['send_email_date'])); ?><br /><br />
            Témája: <?php echo $result['send_email_subject']; ?><br />
            Tartalma: <?php echo $result['send_email_message']; ?><br />
            <br />
            <a class="btnspan pt-center" href="index.php?page=profile&username=<?php echo $result['user_name']; ?>">Vissza a Profil oldalra.</a><br />
        </section>
        <footer>
            <p class="footer_text">&copy; 2017, Relierf</p>
            <div class="clear"></div>
        </footer>
        <?php
    } else {
        $errors[] = "Sikertelen levélküldés.";
    }
} else {
    $errors[] = 'Súlyos hiba! Nincs mgjeleníthető elem.';
}


if (isset($_POST['btn_send'])) {
    //debug_r($_POST);
    if (empty($_POST['subject']) === true || empty($_POST['body']) === true) {
        $errors[] = 'Minden mezőt ki kell tölteni!';
    } else if (strlen($_POST['subject']) > 30) {
        $errors[] = "Legfeljebb 30 karakter hosszú legyen!";
    } else {
        $email_text = $user_data['user_name'].' üzenetet küldött neked a Britanny Bod fan site oldalon keresztűl, ebben az időpontban: '.date('Y-m-d H:i');
        $email_text .= "\r\nElolvashatod, ha <a href='http://localhost/brit/index.php'><b>ide</b></a> kattintasz.";

        $email_data = array(
            'send_email_hash'       => mt_rand(1, 100000),
            'send_email_from'       => $user_data['user_id'],
            'send_email_to'         => $_POST['user'],
            'send_email_date'       => date('Y-m-d H:i'),
            'send_email_subject'    => $_POST['subject'],
            'send_email_message'    => $_POST['body'],
            'send_email_adress'      => $cimzett['user_email'],
        );
        //debug_r($email_data);
        html_mail($email_data['send_email_adress'], 'Leveled érkezett!', $email_text);
        $result = email_for_user($dbc, $email_data);
        //debug_r($result);
        header("Location: index.php?page=email&success=$email_data[send_email_hash]&to=$email_data[send_email_to]");
        exit();
    }
}
?>



<div class="post_error">
<?php
    if (!empty($errors)) {
        echo '<pre>';
        echo output_errors($errors);
        echo '</pre>';
    }
?>
</div><!-- end post_error -->

<?php 
if (isset($email) && empty($errors)) {
    if ($email == 'received') {
        ?>
       	<?php include 'pages/email/received.php'; ?>
        <?php
    } else if ($email == 'sentmail') {
        ?>
       	<?php include 'pages/email/sentmail.php'; ?>
        <?php
    }
} else if (isset($userid)) {
    ?>
   	<?php include 'pages/email/userid.php'; ?>
    <?php
} else if (isset($ansver)) {
    debug_r($ansver);
}
?>