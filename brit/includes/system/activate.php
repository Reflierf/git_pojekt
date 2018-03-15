<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */

logged_in_redirect();

if (isset($_GET['success']) === true && $_GET['success'] == 'ok') {
    echo '<div class="post_success pt-center">';
    echo '<pre>';
    echo '<p style="font-size: 18pt; font-weight: bold;">Sikers aktíválás!</p>';
    echo '<p>Most már be tudsz lépni.</p>';
    echo '</pre>';
    echo '</div>';
} else if (isset($_GET['email']) === true && isset($_GET['email_code']) === true) {
    $email      = trim($_GET['email']);
    $email_code = trim($_GET['email_code']);
    if (email_exists($dbc, $email) === false) {
        $errors[] = "Nem találtam ilyen e-mail címet, próbáld meg újra!"; 
    } else if (activate($dbc, $email, $email_code) === false) {
        $errors[] = "Hiba az aktivációs kódban, az aktiváció nem lehetséges!";
        $errors[] = "Próbáld meg újra, és ha továbbra sem megy, írj levelet a <strong>terb98@gmail.comra</strong>";
    }
    if (empty($errors) === false) {
        ?> <div class="post_error"> <?php
        echo '<pre>';
        echo output_errors($errors);
        echo '</pre>';
        ?> </div><!-- end post_error --> <?php
    } else {
        header("Location: index.php?page=activate&success=ok");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>