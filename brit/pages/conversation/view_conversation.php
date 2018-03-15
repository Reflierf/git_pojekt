<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */

protect_page();

if (isset($_POST['btn_send'])) {
    //debug_r($_POST);
    if (empty($_POST['message'])) {
		//echo 'valami gáz';
		$errors[] = 'Még nem írtál választ.';
	} else {
		//print_r($_POST['text_body']);
		add_conversation_message($dbc, $_GET['conversation_id'], $_POST['message']);
	}
}

if (isset($_GET['conversation_id'])) {
    $conversation_id = $_GET['conversation_id'];
    $valid_conversation = validate_conversation_id($dbc, $conversation_id);
    if ($valid_conversation == false) {
        $errors[] = "Érvénytelen üzenet azonosító!";
        $conv_subject = "Hiba!";
    }
    
    if ($valid_conversation === true) {
        $conv_subject = $_GET['subject'];
        if (isset($_POST['message'])) {
            update_conversation_last_view($dbc, $conversation_id);
            $messages = fetch_conversation_messages($dbc, $conversation_id);
        } else {
            $messages = fetch_conversation_messages($dbc, $conversation_id);
            update_conversation_last_view($dbc, $conversation_id);
        }
        $data = fetch_conversation_users($dbc, $conversation_id);
    }
}




?>



<header>
    <p class="page_title"><?php echo $conv_subject; ?></p>
</header>

<section>

    <div class="post_error">
    <?php
        if (!empty($errors)) {
            echo '<pre>';
            echo output_errors($errors);
            echo '</pre>';
        }
    ?>
    </div><!-- end post_error -->
    
    
    <div class="partners">
            <p style="font-size: 14pt;">Beszélgető partnerek:</p>
            <div class="partners_galeria">
                <div class="left_indent">
                    <?php 
                    while ($row = $data->fetch()) {
                        //debug_r($row);
                        ?>
                       	<div style="float: left; margin-right: 20px; width: 6%;">
                            <p style="margin-bottom: 5px;"><a href="<?php echo $row['user_name']; ?>"><?php echo $row['user_name']; ?></a></p>
                            <a href="<?php echo $row['user_name']; ?>"><img src="<?php echo $row['user_profile']; ?>" style="width: 100%; border: 1px solid #777; padding: 3px;"/></a>
                        </div>
                        <?php
                    }
                    ?>
                <div class="clear"></div>
                </div>
            </div>
            
    </div><!-- end partners -->
    
    <div class="more_things">
        <div class="send_message">
            <button class="button buttonLong show_hidden_panel" type="submit" id="vez<?php echo $conversation_id; ?>" name="returmaessage">Írj választ...</button>
            <form id="anim_vez<?php echo $conversation_id; ?>" class="animate_td hide_form" action="" method="post">
                <ul>
					<li>
                        <label class="cimke" for="message">Vélemény...</label>
                        <textarea class="text_body" id="message" name="message" placeholder="hozzászólás tartalma..."></textarea>
                    </li>
                    <li>
                        <button class="button buttonLong" type="submit" id="btn_send" name="btn_send"><i class="fa fa-commenting-o" aria-hidden="true"></i> Küldés</button>
                    </li>
				</ul>
            </form>
        </div><!-- end of send_message -->
    </div><!-- end of more_things -->
    
    <div class="view_message">
		<?php 
			foreach ($messages as $message) {
			?>
				<div class="view_comment<?php if ($message['unread']) echo ' unread'; ?>">
					<div><p class="move-left light_color"><a href="<?php echo $user_data['user_name']; ?>"><?php echo $message['user_name']; ?></a></p></div>
					<div><p class="move-right light_color"><?php echo '('.date('Y/m/d H:t', $message['date']).')'; ?></p></div>
					<div class="clear"></div><br />
					<div class="text"><p><?php echo $message['text']; ?></p></div>
				</div>
			<?php
			}
		?>
	</div><!-- end of view_message -->
    
</section>

<footer>
    <p class="footer_text">&copy; 2017, Relierf</p>
    <div class="clear"></div>
</footer>