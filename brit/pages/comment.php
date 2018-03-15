<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */


if (isset($_POST{'btn_comment'})) {
    if (!empty($_POST['comment_body'])) {
        //debug_r($_POST);
        $comment = array(
            'comment_news'  => $news_id,
            'comment_user'  => $user_data['user_id'],
            'comment_date'  => date('Y-m-d H:i:s'),
            'comment_text'  => $_POST['comment_body']
        );
        $sql_error = insertNewsComment($dbc, $comment);
        //debug_r($sql_error); 
        if ($sql_error != true) {
            $errors[] = "SQL hiba történt!";
        } else {
            $_POST['comment_body'] = "";
            ?><div><script>alert("A hozzászólás sikeresen hozzáfűzve.");</script></div><?php
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
                    <form action="" method="post">
                        <ul class="bevitel">
                            <li>
                                <textarea id="comment_body" name="comment_body" placeholder="Komment..."></textarea>
                            </li>
                            <li>
                                <button class="button buttonLong" type="submit" id="btn_comment" name="btn_comment"><i class="fa fa-envelope" aria-hidden="true"></i> Komment küldése</button>
                            </li>
                        </ul>
                    </form>