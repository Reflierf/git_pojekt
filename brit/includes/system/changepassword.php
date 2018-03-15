<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */

protect_page();

if (isset($_POST['btn_change'])) {
    $required_fields = array('old_password', 'new_password', 'new_password_again');
    foreach ($_POST as $key => $value) {
        if (empty($value) && in_array($key, $required_fields) === true) {
            $errors[] = 'Minden mezőt ki kell tőlteni!';
            break(1);
        }
    }
    
    if (sha1($_POST['old_password']) === $user_data['user_password']) {
        if (trim($_POST['new_password']) !== trim($_POST['new_password_again'])) {
            $errors[] = "Az új jelszavak nem egyeznek!";
        }
        if (strlen($_POST['new_password']) < 8) {
            $errors[] = 'A jelszó nem lehet kevesebb, mint 8 karakter!';
        }
    } else {
        $errors[] = "Nem ez a jelenlegi jelszó!!";
    }
}

?>

<div class="post_error pt-center">
    <?php
    if (!empty($errors) === true) {
        echo '<pre>';
        echo output_errors($errors);
        echo '</pre>';
    }
    ?><!-- end post_error -->
</div>


<?php 
if (isset($_GET['success']) && $_GET['success'] == 'ok') {
    echo '<div class="post_success pt-center">';
    echo '<pre>';
    echo 'A regisztráció sikeresen lezajlott!';
    echo '</pre>';
    echo '</div>';
} else {
    if (isset($_GET['must_be']) && $_GET['must_be'] == '') {
        $must_be = "Meg kell változtatni  a jelszót, amit az e-mailben kapott, mivel nemrég jelszó cserét kért.";
    } else {
        $must_be = "";
    }
    if (empty($_POST) === false && empty($errors) === true) {
        change_password($dbc, $session_user_id, $_POST['new_password']);
        header("Location: index.php?page=changepassword&success=ok");
        exit();
    } else {
        ?>
        <header>
            <p class="page_title">Jelszó cseréje</p>
        </header>
        <form action="" method="post">
        
            <section>
                <p class="pt-center" style="font-size: 15pt; font-weight: bold; margin: 0 auto 10px;"><?php echo $must_be; ?></p>
                <ul class="bevitel">
                    <li>
                        <!-- <label class="cimke" for=""></label> -->
                        <input type="password" class="sor" id="old_password" name="old_password" value="" placeholder="Jelenlegi jelszó...*"/>
                    </li>
                    <li>
                        <input type="password" class="sor" id="new_password" name="new_password" value="" placeholder="Új jelszó...*"/>
                    </li>
                    <li>
                        <input type="password" class="sor" id="_new_password_again" name="new_password_again" value="" placeholder="Új jelszó ismét...*"/>
                    </li>
                </ul>
            </section>
            
            <footer>
                <ul class="bevitel">
                    <button class="button buttonLong" type="submit" id="btn_change" name="btn_change"><i class="fa fa-refresh" aria-hidden="true"></i> Jelszó cseréje</button>
                </ul>
                <p class="footer_text">&copy; 2017, Relierf</p>
                <div class="clear"></div>
            </footer>
        
        </form>
        <?php
    }
}
?>

