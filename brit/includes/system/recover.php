<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */

logged_in_redirect();


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
    
    
    <?php
    if (isset($_GET['success']) === true && $_GET['success'] === 'ok') {
        echo '<div class="post_success pt-center">';
        echo '<pre>';
        //echo '<p>A visszaállítás sikeresen lezajlott!</p>';
        echo '<p>Az e-mail címedre elküldtünk egy levelet, ellenőrizd a postádat és kövesd az útmutatást!</p>';
        echo '</pre>';
        echo '</div>';
    } else {
        $mode_allowed = array('username', 'password');
        if (isset($_GET['mode']) === true && in_array($_GET['mode'], $mode_allowed)) {
            $mode_text = ($_GET['mode'] === 'username') ? "felhasználó név" : "jelszó";
            if (isset($_POST['email']) === true && empty($_POST['email']) === false) {
                if (email_exists($dbc, $_POST['email'])) {
                    recover($dbc, $_GET['mode'], $_POST['email']);
                    header("Location: index.php?page=recover&success=ok");
                    exit();
                } else {
                    $errors[] = "Sajnos nem találtunk ilyen e-mail címet. Valóban regisztráltál?.";
                    echo '<div class="post_error pt-center">';
                    echo '<pre>';
                    echo output_errors($errors);
                    echo '</pre>';
                    echo "</div>";
                }
            }
            ?>
           	<header>
                <p class="page_title">Elfelejtett <?php echo $mode_text; ?></p>
            </header>
            <form action="" method="post">
            
                <section>
                    <ul class="bevitel">
                        <li>
                            <!-- <label class="cimke" for=""></label> -->
                            <input type="hidden" class="sor" id="mode" name="mode" value="<?php echo $_GET['mode']; ?>"/>
                        </li>
                        <li>
                            <input type="text" class="sor" id="email" name="email" value="" placeholder="E-mail...*"/>
                        </li>
                    </ul>
                </section>
                
                <footer>
                    <ul class="bevitel">
                        <button class="button buttonLong" type="submit" id="btn_register" name="btn_register"><i class="fa fa-recycle" aria-hidden="true"></i> Visszaállítás</button>
                    </ul>
                    <p class="footer_text">&copy; 2017, Relierf</p>
                    <div class="clear"></div>
                </footer>
            
            </form>
            <?php
        } else {
            header("Location: index.php");
            exit();
        }
    }
    ?>
    
   	


    
    
