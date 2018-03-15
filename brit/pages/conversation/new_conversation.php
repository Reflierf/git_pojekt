<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */

/*if (isset($_GET['id']) == true && empty($_GET['id']) == false ) {
    //debug_r($_GET);
    $user = user_data($dbc, $_GET['id'], 'user_name');
    $_POST['to'] = $user['user_name'];
    //debug_r($_POST['to']);
} else {
    $_POST['to'] = "";
}
//debug_r($_GET);
//debug_r($_POST);*/

$username_regex = '/^[a-z0-9\_\-\.]{3,30}$/';

if (isset($_GET['id']) && !empty($_GET['id'])) {
	$addressee_id = $_GET['id'];
    $addressee_name = user_data($dbc, $addressee_id, 'user_name');
} else {
	$addressee_id = '';
    $addressee_name['user_name'] = "";
}

if (isset($_POST['btn_send']) === true) {
    //debug_r($_POST);
    if (empty($_POST['to'])) {
		$errors[] = 'Legaláb egy nevet meg kell adni!';
	} else if (preg_match('#^[a-z, 0-9, ]+$#i', $_POST['to']) === 0) {
        $errors[] = "A név nem tartalmazhat nagy betűt, speciális karaktereket, szóközt,<br />nem lehet rövidebb, mint 3 karakter és nem lehet hosszabb, mint 30 karakter!";
    } else {
        $user_names = explode(',', $_POST['to']);
        //print_r($user_names);
        foreach ($user_names as &$name) {
			$name = trim($name);
		}
        $user_ids = fetch_user_ids($dbc, $user_names);
		if (count($user_ids) != count($user_names)) {
			$errors[] = 'A következő felhasználók nem találhatóak: '.implode(', ', array_diff($user_names, array_keys($user_ids)));
		}
    }
    if (isset($_POST['subject']) && empty($_POST['subject'])) {
		$errors[] = 'Meg kell adni a társalgás témáját!';
	}
	
	if (isset($_POST['message_body']) && empty($_POST['message_body'])) {
		$errors[] = 'Még nem írtál semmit sem!';
	}
}

?>



<header>
    <p class="page_title">Új társalgás kezdeményezése</p>
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
<?php 
if (isset($_GET['success']) && $_GET['success'] == "ok"){
    header ("Location: index.php?page=inbox");
    exit();
} else if (isset($_POST['btn_send']) === true && empty($errors) === true) {
    create_conversation($dbc, array_unique($user_ids), $_POST['subject'], $_POST['message_body']);
    header("Location: index.php?page=new_conversation&success=ok");
    exit();
} else {
    ?>
    <form action="" method="post">
        
        <section>
            <ul class="bevitel">
                <p style="margin: 10px auto;">Adj meg egy vagy több partnert, vesszővel elválasztva.</p>
                <li>
                    <label class="cimke" for="to">Címzett:* </label>
                    <input type="text" class="sett" id="to" name="to" value="<?php echo (isset($addressee_name['user_name']))? $addressee_name['user_name'] : ''; ?>" placeholder="címzett...*"/>
                </li>
                <li>
                    <label class="cimke" for="subject">Téma*: </label>
                    <input type="text" class="sett" id="subject" name="subject" value="<?php echo (isset($_POST['subject']))? htmlentities($_POST['subject']) : ''; ?>" placeholder="téma..."/>
                </li>
                <li>
                    <label class="cimke" for="message_body">Üzenet:* </label>
                    <textarea id="message_body" name="message_body" placeholder="üzenet..."><?php echo (isset($_POST['message_body']))? htmlentities($_POST['message_body']) : ''; ?></textarea>
                </li>
            </ul>
        </section>
    
        <footer>
            <ul class="bevitel">
                <button class="button buttonLong" type="submit" id="btn_send" name="btn_send">Küldés</button>
            </ul>
            <p class="footer_text">&copy; 2017, Relierf</p>
            <div class="clear"></div>
        </footer>
    
    </form>
<?php } ?>