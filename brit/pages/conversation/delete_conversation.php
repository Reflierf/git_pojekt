<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */

if (isset($_GET)) {
    $paraszt = validate_conversation_id($dbc, $_GET['conversation_id']);
    if ($paraszt == false) {
        $errors[] = 'Érvénytelen üzenet azonosító!';
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
        <?php
    } else {
        delete_conversation($dbc, $_GET['conversation_id'], $session_user_id);

        header("Location: index.php?page=inbox");
        exit();
    }
}
?>
