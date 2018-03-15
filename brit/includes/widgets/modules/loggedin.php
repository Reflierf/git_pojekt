<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */

if (isset($user_data['user_profile']) && !empty($user_data['user_profile']) && file_exists($user_data['user_profile'])) {
	$img_avatar_tag = '<img class="profile_image" src="'.$user_data['user_profile'].'" alt="'.$user_data['user_name'].' profilja"/>';
} else {
	$img_avatar_tag = '<img class="profile_image" src="img/profile/no_profile.jpg" alt="'.$user_data['user_name'].' profilja"/>';
}

?>

                <div id="loggedin">
                    <div class="module">
                        <div class="module_head">Hello, <?php echo $user_data['user_firstname']; ?>!</div>
                        <div class="module_body">
                        
                            <div class="loggedin_profile">        
                    	        <div class="logged_in_name">
                    	            <ul>
                    	                <li>Bejelentkezve:</li>
                    	                <li><?php echo '<strong>'.$user_data['user_name'].' ('.$user_data['user_id'].')</strong><br />'; ?></li>
                    	            </ul>
                    	        </div><!-- end logged_in_name -->
                    	        
                    	        <div class="logged_in_pictures">
                    				<ul>
                    					<li><?php echo '<a href="'.$user_data['user_name'].'"> '.$img_avatar_tag.'</a>'; ?></li>
                    				</ul>
                    			</div><!-- end of logged_in_pictures -->
                    			<div class="clear"></div>
                    		</div><!-- end of loggedin_profile -->
                    		
                            <div class="clear"></div>
                            
                            <div class="unread_message">
                                <ul>
                                    <?php 
                    				$unread_messages_count = count_unread_messages($dbc, $user_data['user_id']);
                    				if ($unread_messages_count > 0) { ?>
                    					<li>Olvasatlan üzenetek száma: <a href="index.php?page=inbox"><b><span style="font-size: 18pt;"><?php echo $unread_messages_count; ?></span></b></a></li>
                    				<?php } else { ?>
                    					<li>Nincs olvasatlan üzeneted.</li>
                    				<?php }
                    				?>
                                </ul>
                            </div>
                            
                            <div class="unread_letters">
                                <ul>
                                    <?php 
                                    $unread_letters_count = count_unread_letters($dbc, $session_user_id);
                                    if ($unread_letters_count > 0) {
                                        ?><li>Olvasatlan levelek száma: <a href="index.php?page=email&email=received"><b><span style="font-size: 18pt;"><?php echo $unread_letters_count; ?></span></b></a></li><?php
                                    } else {
                                        ?><li>Nincs olvasatlan leveled.</li><?php
                                    }
                                    ?>
                                </ul>
                            </div>
                            
                            <!-- <p>Be vagy lépve!</p> -->
                            <ul>
                                <li><a href="<?php echo $user_data['user_name']; ?>"><?php echo $user_data['user_name']; ?> profilja</a></li>
                                <li><a href="index.php?page=changepassword">Jelszó cseréje</a></li>
                                <li><a href="index.php?page=settings">Beállítások</a></li>
                                <li><a href="logout.php">Kilépés</a></li>
                            </ul>
                            
                            
                        </div>
                    </div>
                </div><!-- end login -->