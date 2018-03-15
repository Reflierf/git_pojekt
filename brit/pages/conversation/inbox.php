<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */

if (isset($_POST['del_msg']) === true) {
    debug_r($_POST);
    if (validate_conversation_id($dbc, $_POST['conv_id']) === false) {
        $errors[] = 'Érvénytelen üzenet azonosító!';
    } else {
        delete_conversation($dbc, $_GET['delete_conversation'], $session_user_id);
    }
}

$conversations = fetch_conversation_summery($dbc);

if (empty($conversations) === true) {
    $errors[] = "Nincs új üzeneted.";
}

$delete_message = 'Valóban törlöd a társalgást?';
?>

<header>
    <p class="page_title">Inbox</p>
</header>

<section class="bckconv">
    <!-- Ha van hiba -->
    <div class="post_error pt-center">
        <?php
        if (!empty($errors) === true) {
            echo '<pre>';
            echo output_errors($errors);
            echo '</pre>';
        }
        ?>
    </div><!-- end post_error -->
    
    <div class="btnnewconv">
        <a class="btnspan pt-center" href="index.php?page=new_conversation">Új társalgás indítása</a>
    </div>
    
    <div class="conversation_list">
        
            <?php if (empty($conversations) === true) {
                echo "Nincsenek megkezdett társalgásaid.";
            } else {
                echo "Megkezdett társalgásaid: <br />";
                
                ?><form action="" method="post"><!-- form! --><?php
                foreach ($conversations as $conversation) {
                    //debug_r($conversation);
                    ?>
                    <input type="hidden" id="conv_id" name="conv_id" value="<?php echo $conversation['id']; ?>"/>
                   	<div class="conversation">
                        <div class="conv_row">
                            <!-- <button class="button btn-small" type="submit" id="del_msg" name="del_msg" onclick="return confirm('<?php echo $delete_message; ?>')" style="display: inline; width: 25px;"><i class="fa fa-trash-o" aria-hidden="true"></i></button> -->
                            <a class="button btn-small btn_del" href="index.php?page=delete_conversation&conversation_id=<?php echo $conversation['id']; ?>" onclick="return confirm('<?php echo $delete_message; ?>')"><i class="fa fa-trash-o" aria-hidden="true" style="margin-top: 4px;"></i></a>
                            <a class="conv_title" href="index.php?page=view_conversation&conversation_id=<?php echo $conversation['id']; ?>&subject=<?php echo $conversation['subject']; ?>"><?php echo $conversation['subject']; ?></a>
                            <p>Utolsó hozzászólás: <?php echo date('Y/m/d H:t', $conversation['last_replay']); ?></p>
                        </div><!-- end of conv_row -->
                        <div class="conv_users">
                            <?php 
                            $conv_users = fetch_conversation_users($dbc, $conversation['id']);
                            while ($row = $conv_users->fetch()) {
                                //debug_r($row);
                                if ($row['user_profile'] !== "") {
                                    $img_path = $row['user_profile'];
                                } else {
                                    $img_path = 'img/profile/no_profile.jpg';
                                }
                                ?>
                                <div style="display: inline;">
                                    <a href="<?php echo $row['user_name']; ?>">
                                        <img class="conversation_users" src="<?php echo $img_path; ?>" title="<?php echo $row['user_name']; ?>"/>
                                    </a>
                                </div>
                               	
                                <?php
                            }
                            ?>
                        </div><!-- end of conv_users -->
                        <div class="clear"></div>
                    </div><!-- end of conversation -->
                    <?php
                }
                ?></form><!-- end of form! --><?php
            } ?>
        
    </div>
</section>

<footer>
    <p class="footer_text">&copy; 2017, Relierf</p>
    <div class="clear"></div>
</footer>