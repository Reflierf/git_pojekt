<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */

protect_page();

if (isset($_GET['username']) && !empty($_GET['username'])) {
    $username = $_GET['username'];
} else {
    header("Location: index2.php");
}
?>

<?php 
if (empty($errors) == true) {
    if (user_exists($dbc, $username) == true) {
        $uid = user_id_from_username($dbc, $username);
        $profile_data = user_data($dbc, $uid, 'user_name', 'user_password', 'user_firstname', 'user_lastname', 'user_email', 
        'user_secret_mail', 'user_profile');
        ?>
        
        <header>
            <p class="page_title"><?php echo $profile_data['user_name']; ?> profilja</p>
        </header>
        
        <section>
            <?php 
            if ($profile_data['user_profile'] != '') {
                	$profile_image = $profile_data['user_profile'];
                } else {
                	$profile_image = 'img/profile/no_profile.jpg';
                }
                
                $ipcim = $_SERVER['REMOTE_ADDR'];
	            $felhaszn = $_SERVER['HTTP_USER_AGENT'];
	            $nyelv = $_SERVER['HTTP_ACCEPT_LANGUAGE']
            ?>
            <?php //echo $username.' -> '.$uid; ?>
            <div class="profile_box">
                <!-- Felhasználó portréjának megjelenítése -->
                <div class="profile_image">
                    <img src="<?php echo $profile_image; ?>" alt="<?php echo $profile_data['user_name'] ?> képe"/>
                </div><!-- end of profile_image -->
                <!-- adatok megjelenítése -->
                <div class="profile_datas">
                    <ul>
                        <li><p>Név: <?php echo $profile_data['user_firstname'].' '.$profile_data['user_lastname']; ?></p></li>
                        <li>
                        <?php
                            //Megjeleníthető e-mail cím
                            echo "<p>E-mail cím: ".(($profile_data['user_secret_mail'] != 1)? $profile_data['user_email'] : "Nem publikus")."</p>";
                        ?>
                        </li>
                        <?php
                        //Adatok a rendszergazdának
                        if ($user_data['user_type'] == 1) {
                            ?>
                            <br />
                           	<li><p>Ip cím? <?php echo get_ip(); ?></p></li>
                            <li><p>Böngésző: <?php echo $felhaszn; ?></p></li>
                            <li><p>Nyelv: <?php echo $nyelv; ?></p></li>
                            <br />
                            <?php
                        }
                        ?>
                    </ul>
                </div><!-- end of profile_datas -->
                 <div class="clear"></div>
                 <div class="messages">
                    <hr style=""/>
                    <?php
                    if ($uid == $session_user_id) {
                        ?>
                       	<a class="btnspan pt-center" href="index.php?page=email&email=received">Nekem küldött levelek</a>
                        <a class="btnspan pt-center" href="index.php?page=email&email=sentmail">Eddig elküldött leveleim</a>
                        <a class="btnspan pt-center" href="index.php?page=inbox">Megkezdett társalgásaim</a>
                        <?php
                    } else {
                        if ($profile_data['user_secret_mail'] > 0) {
                            ?>
                           	<a class="btnspan pt-center" href="index.php?page=email&userid=<?php echo $uid; ?>">Küldj neki levelet</a>
                            <a class="btnspan pt-center" href="index.php?page=new_conversation&id=<?php echo $uid; ?>">Meginvitálás társalgásra</a>
                            <?php
                        } else {
                            ?>
                           	<a class="btnspan pt-center" href="mailto:<?php echo $profile_data['user_email'];?>?subject=Hello,%20<?php echo $profile_data['user_name']; ?> ">Küldj neki levelet</a>
                            <a class="btnspan pt-center" href="index.php?page=new_conversation&id=<?php echo $uid; ?>">Meginvitálás társalgásra</a>
                            <?php
                        }
                    }
                    ?>
                </div><!-- end of messages -->
            </div><!-- end of profile_box -->
            
        </section>
        
        <footer>
            <p class="footer_text">&copy; 2017, Relierf</p>
            <div class="clear"></div>
        </footer>
        
        <?php
    } else {
        //hiba van
        ?>
        <header>
            <p class="page_title">Hiba!</p>
        </header>
        <section>
            <?php
        $errors[] = 'Ezzel a névvel nem rigisztráltak a Britanny Bod oldalon!';
        ?>
        <div class="post_error pt-center">
            <?php
            if (!empty($errors) === true) {
                echo '<pre>';
                echo output_errors($errors);
                echo '</pre>';
            }
            ?>
        </div><!-- end post_error -->
        </section>
        <footer>
            <p class="footer_text">&copy; 2017, Relierf</p>
            <div class="clear"></div>
        </footer>
        <?php
    } 
}
?>
