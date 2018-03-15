<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */

protect_page();

if (isset($_GET['username']) == true && empty($_GET['username']) == false) {
    $username = $_GET['username'];
    if (user_exists($dbc, $username) === true) {
        $user_id = user_id_from_username($dbc, $username);
        $profile_data = user_data($dbc, $user_id, 'user_name', 'user_firstname', 'user_lastname', 'user_email', 'user_secret_mail');
        if (empty($profile_data['user_profile']) == true) {
            $profile_image = 'img/profile/no_profile.jpg';
        } else {
            $profile_image = $profile_data["user_profile"];
        }
        
        $ipcim = $_SERVER['REMOTE_ADDR'];
        $felhaszn = $_SERVER['HTTP_USER_AGENT'];
        $nyelv = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
		//debug_r($ipcim);
		//debug_r($felhaszn);
		//debug_r($nyelv);
        ?>
        <header>
            <p class="page_title"><?php echo $profile_data['user_name']; ?> profilja</p>
        </header>
        
        <section>
            <!-- <div>
            <p>Felhasználónév: <?php echo $profile_data['user_name']; ?></p>
            <p>Keresztnév: <?php echo $profile_data['user_firstname']; ?></p>
            <p>Vezetéknév: <?php echo $profile_data['user_lastname']; ?></p>
            <p>E-mail cím: <?php echo $profile_data['user_email']; ?></p>
            </div> -->
            <div class="profile_box">
                <div class="profile_image">
                    <img src="<?php echo $profile_image; ?>" alt="felhasználó képe"/>
                </div>
                <div class="profile_datas">
                    <div>Felhasználónév: <?php echo $profile_data['user_name']; ?></div>
                    <div>
                        <p>Név: <?php echo $profile_data['user_firstname'].' '.$profile_data['user_lastname']; ?></p>
                        <!--  --><p>
                            <?php 
                            if ($profile_data['user_secret_mail'] == 0){
                                echo 'E-mail cím: '.$profile_data['user_email'];
                            } else {
                                echo 'E-mail cím: Nem publikus';
                            }
                            ?>
                        </p>
                        <p>
                            <?php
                            if ($user_data['user_type'] == 1) {
	                    	?>
    	                        <br />
    	                        <p>Ip cím: <?php echo get_ip(); ?></p>
    	                        <p>Böngésző: <?php echo $felhaszn; ?></p>
    	                        <p>Nyelv: <?php echo $nyelv; ?></p>
    	                        <br />
    	                    <?php } ?>
						
                        </p>    
                    </div>
                </div>
                <div class="clear"></div>
                <div class="messages">
                    <hr style="height: 2px; background: #555; margin: 5px 0;"/>
                    <?php 
                    if ($user_id == $session_user_id) {
                        ?>
                       	<div>
                            <span class="btnspan pt-center">Neked küldött levelek</span><!-- <span class="separatorspan"> | </span> -->
                            <span class="btnspan pt-center">Eddig elküldött leveleim</span>
                            <a href="index2.php?page=inbox"><span class="btnspan pt-center">Megkezdett társalgásaim</span></a>
                            <a href="index2.php?page=show_user_img"><span class="btnspan pt-center">Feltöltött képeid</span></a>
                        </div>
                        <?php
                    } else if ($user_id !== $session_user_id) {
                        if ($profile_data['user_secret_mail'] > 0) {
                            ?> <p><a href="index2.php?page=email&userid=<?php echo $user_id; ?>">Küldj neki levelet</a> || <a href="index2.php?page=new_conversation&id=<?php echo $user_id; ?>">Kezdeményez vele társalgást</a></p> <?php
                        } else if ($profile_data['user_secret_mail'] == 0) {
                            ?> <p><a href="mailto:<?php echo $profile_data['user_email']; ?>?Subject=Hello%20again" target="_top">Küldj neki levelet</a> || <a href="index2.php?page=new_conversation&id=<?php echo $user_id; ?>">Kezdeményez vele társalgást</a></p> <?php
                        }
                    }
                    ?>
                    <a href="index2.php">Otthon</a>
                    
                </div>
            </div><!-- end of profile_box -->
        </section>
        
        <footer>
            <p class="footer_text">&copy; 2017, Relierf</p>
            <div class="clear"></div>
        </footer>
        <?php
        
    } else {
        ?>
       	<header>
            <p class="page_title">Hiba!</p>
        </header>
        <section>
        <?php
        $errors[] = 'Ezzel a névvel nem rigisztráltak a Britanny Bod weboldalon!';
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
    
} else {
    header("Location: index.php");
    exit();
}

?>

            
            
            
            
            
            
            
                
            
            
            
            