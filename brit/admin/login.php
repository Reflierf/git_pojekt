<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */
session_start();

include 'core/database/connection.php';
include 'core/function/general.php';
include 'core/function/users.php';
include 'core/function/menu.php';
include 'core/function/mail.php';

if (isset($_SESSION['user_id'])) {
   $user = user_data($dbc, $_SESSION['user_id'], 'user_name', 'user_id', 'user_firstname');  //die();
}
//if (isset($user)) { debug_r($user);}

//echo "Login.php";
if (isset($_POST['userpage']) === true) {
    header("Location: http://localhost/brit/index.php");
    exit();
}

if (isset($_POST['admin_login']) === true) {
    //$email = $_POST['email'];
    $password = $_POST['password'];
    //debug_r($_POST);
    if (empty($password) === true) {
        $errors[] = "Nincs jogod belépni!";
    } else {
        //echo "nanakaxa";
        $login = login_admin($dbc, $password);
        //debug_r($login); die();
        if ( $login === false) {
            $errors[] = "Nincs jogod belépni!";
        } else {
            //echo "KAKAK";
            $_SESSION['admin_id'] = $login; //die();
            header("Location: index.php");
            exit();
        }
    }
    /*if (empty($email) === true || empty($password) === true) {
        $errors[] = "Hibás vagy hiányzó adatok!";
    } else {
        if (email_exists($dbc, $email) === false) {
            $errors[] = "Nincs jogod belépni!";
        } else {
            //echo "nanakaxa";
            $login = login_admin($dbc, $email, $password);
            if ( $login === false) {
                $errors[] = "Nincs jogod belépni!";
            } else {
                //echo "KAKAK";
                $_SESSION['admin_id'] = $login;
                header("Location: index.php");
                exit();
            }
        }
    }*/
}

?>

<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8"/>
        <link rel="stylesheet" href="css/login.css" type="text/css"/>
        <link rel="stylesheet" href="css/stdinput.css" type="text/css"/>
        <link rel="stylesheet" href="css/font.css" type="text/css"/>
        <link rel="Shortcut Icon" type="image/ico" href="img/login.ico"/>
        <title>B. B. Admin - Login</title>
    </head>
    <body>
        <section>
            <div class="errors pt-center">
                <?php if (!empty($errors) === true) {
                    echo '<pre>';
                    echo output_errors($errors);
                    echo '</pre>';
                } ?>
                </p>
            </div>
            <header class="login_title">
                Login
            </header>
            <img src="img/0052.jpg"/>
            <div>
                <form action="" method="post">
                    <ul>
                        <!-- <li>
                            <input type="text" id="email" name="email" style="width: 99.5%!important" placeholder="Admin email..." autofocus="1"/>
                        </li> -->
                        <li>
                            <input type="text" id="user" name="user" style="width: 99.5%!important" value="<?php echo $user['user_name']; ?>" readonly=""/>
                        </li>
                        <li>
                            <input type="password" id="password" name="password" style="width: 99.5%!important" placeholder="Admin jelszó..."/>
                        </li>
                        <div>
                            <li>
                                <input type="submit" class="button buttonLong" id="admin_login" name="admin_login" value="Belépés" style="float: right;" autofocus="on"/>
                            </li>
                            <li>
                                <input type="submit" class="button buttonLong" id="userpage" name="userpage" value="Vissza" style="float: left;"/>
                            </li>
                            <div class="clear"></div>
                        </div>
                        
                    </ul>
                </form>
            </div>
        </section>
    </body>
</html>