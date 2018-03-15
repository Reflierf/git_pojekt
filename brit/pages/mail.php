<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */

protect_page();
admin_protect($dbc);

if (isset($_POST['btn_send']) === true) {
    if (empty($_POST['subject'])) {
        $errors[] = "A levél témáját meg kell adni!";
    } else if (empty($_POST['body']) === true) {
        $errors[] = "Eddig még nem írtál semmit sem, amit elküldhetnénk.";
    }
}

?>

<!-- 
Ez itt az első hivatalos üzenet. Mostantól kezdve a fontosabb változtatásokról, fejlesztésekről és egyéb, a weboldallal kapcsolatos információkat fogja tartalmazni.
 -->

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
    if (isset($_GET['success']) === true && empty($_GET['success']) === true) {
        echo '<div class="post_success pt-center">';
        echo '<pre>';
        echo '<p>A levél sikeresen el lett küldve minden regisztrált felhasználónak!</p>';
        echo '</pre>';
        echo '</div>';
    } else {
        if (empty($_POST) === false && empty($errors) === true) {
            email_for_all_users($dbc, $_POST['subject'], $_POST['body'], $session_user_id);
            header("Location: index.php?page=mail&success");
            exit();
    } else {
        ?>
    
        <header>
                <p class="page_title">E-mail minden feljhasználónak</p>
            </header>
            <form action="" method="post">
            
                <section>
                    <ul class="bevitel">
                        <li>
                            <!-- <label class="cimke" for=""></label> -->
                            <input type="text" class="sor" id="subject" name="subject" value="<?php echo(!empty($_POST['subject'])? $_POST['subject'] : '') ?>" placeholder="Levél témája...*"/>
                        </li>
                        <li>
                            <textarea id="body" name="body" placeholder="Levél tartalma...*"><?php echo(!empty($_POST['body'])? $_POST['body'] : '') ?></textarea>
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
        <?php
        }
    }
    ?>