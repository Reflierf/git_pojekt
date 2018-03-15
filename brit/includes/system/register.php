<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */

logged_in_redirect();

$username_regex = '/^[a-z0-9\_\-\.]{3,30}$/';

if (isset($_POST['btn_register']) === true) {
    //print_r($_POST);
    $required_fields = array('name', 'password', 'password_again', 'firstname', 'email');
    foreach ($_POST as $key => $value) {
        if (empty($value) && in_array($key, $required_fields) === true) {
            $errors[] = 'A csillaggal megjelölt mezők kitöltése szükséges a regisztrációhoz!';
            break(1);
        }
    }
    
    if (empty($errors) === true) {
        if (user_exists($dbc,$_POST['name']) === true) {
            $errors[] = "A „$_POST[name]” név már foglalt, válassz másikat!";
        }
        if (preg_match('/\\s/', $_POST['name']) == true) {
            $errors[] = 'A név nem tartalmazhat szünetet!';
        }
        if (strlen($_POST['password']) < 6) {
            $errors[] = 'A jelszó nem lehet kevesebb, mint 8 karakter!';
        }
        if ($_POST['password'] != $_POST['password_again']) {
            $errors[] = 'A jelszavak nem egyeznek!';
        }
        if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false) {
            $errors[] = 'Valós e-mail címet kell megadni!';
        }
        if (email_exists($dbc, $_POST['email']) === true) {
            $errors[] = 'Ez az e-mail cím már használatban van, válassz másikat!';
        }
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
        echo '<p>A regisztráció sikeresen lezajlott!</p>';
        echo '<p>Az e-mail címedre elküldtünk egy aktivációs levelet, ellenőrizd a postádat!</p>';
        echo '</pre>';
        echo '</div>';
    } else {
        if (empty($_POST) === false && empty($errors) === true) {
            $register_data = array(
                'user_name'         => $_POST['name'],
                'user_password'     => $_POST['password'],
                'user_firstname'    => $_POST['firstname'],
                'user_lastname'     => $_POST['lastname'],
                'user_email'        => $_POST['email'],
                'user_email_code'   => sha1($_POST['name'] + microtime())
            );
            register_user($dbc, $register_data);
            header ("Location: index.php?page=register&success=ok");
            exit();
        } else {
            ?>
           	<header>
                <p class="page_title">Regisztráció</p>
            </header>
            <form action="" method="post">
            
                <section>
                    <ul class="bevitel">
                        <li>
                            <!-- <label class="cimke" for=""></label> -->
                            <input type="text" class="sor" id="name" name="name" value="<?php echo (isset($_POST['name'])? $_POST['name']: ''); ?>" placeholder="Felhasználó név...*"/>
                        </li>
                        <li>
                            <input type="password" class="sor" id="password" name="password" value="<?php echo (isset($_POST['password'])? $_POST['password']: ''); ?>" placeholder="Jelszó...*"/>
                        </li>
                        <li>
                            <input type="password" class="sor" id="password_again" name="password_again" value="<?php echo (isset($_POST['password_again'])? $_POST['password_again']: ''); ?>" placeholder="Jelszó ismét...*"/>
                        </li>
                        <li>
                            <input type="text" class="sor" id="firstname" name="firstname" value="<?php echo (isset($_POST['firstname'])? $_POST['firstname']: ''); ?>" placeholder="Keresztnév...*"/>
                        </li>
                        <li>
                            <input type="text" class="sor" id="lastname" name="lastname" value="<?php echo (isset($_POST['lastname'])? $_POST['lastname']: ''); ?>" placeholder="Vezetéknév..."/>
                        </li>
                        <li>
                            <input type="text" class="sor" id="email" name="email" value="<?php echo (isset($_POST['email'])? $_POST['email']: ''); ?>" placeholder="E-mail...*"/>
                        </li>
                    </ul>
                </section>
                
                <footer>
                    <ul class="bevitel">
                        <button class="button buttonLong" type="submit" id="btn_register" name="btn_register"><i class="fa fa-registered" aria-hidden="true"></i> Regisztráció</button>
                    </ul>
                    <p class="footer_text">&copy; 2017, Relierf</p>
                    <div class="clear"></div>
                </footer>
            
            </form>
            <?php
        }
    }
    ?>

    
    
