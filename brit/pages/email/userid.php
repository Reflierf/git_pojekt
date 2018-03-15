<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */



?>

    <header>
        <p class="page_title">Neki küldesz levelet: <?php echo $cimzett['user_name']; ?></p>
    </header>
    
    <form action="" method="post">
        <section>
            <input type="hidden" name="user" value="<?php echo $userid; ?>"/>
            <ul class="bevitel">
                <li>
                    <input type="text" class="sor" id="subject" name="subject" value="<?php echo(!empty($_POST['subject'])? $_POST['subject'] : '') ?>" placeholder="Levél témája (max. 30 karakter)..."/>
                </li>
                <li>
                    <textarea id="body" name="body" placeholder="Levél tartalma..."><?php echo(!empty($_POST['body'])? $_POST['body'] : '') ?></textarea>
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
    