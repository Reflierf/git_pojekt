<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */

protect_page();

if (!empty($user_data['user_profile']) && file_exists($user_data['user_profile'])){
    $img_avatar_tag = '<img class="avatar" src="'.$user_data['user_profile'].'" alt="'.$user_data['user_name'].' profilja"/>';
} else {
    $img_avatar_tag = '<img class="avatar" src="img/profile/no_profile.jpg" alt="'.$user_data['user_name'].' profilja"/>';
}

$email_regex = '/^([A-z0-9\_\.\-]+)@([a-z09\_\.\-]+).([a-z]{2,})$/';
$is_set_pictures = false;

if (isset($_POST['btn_settings']) === true) {
    //debug_r($_POST); die();
    if (empty($_POST['password']) === true) {
        $errors[] = 'A változtatások megerősítéséhez add meg a jelszavad!';
    } else {
        if (password_correct($dbc, $_POST['password']) == false){
            $errors[] = "Nincs jogod az adatokat megváltoztatni!";
        }
        
        if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) == false) {
            $errors[] = "Valós e-mail címet kell megadni!";
        } else if (email_exists($dbc, $_POST['email']) === true && $user_data['user_email'] !== $_POST['email']) {
            $errors[] = 'Ez az e-mail cím már használatban van, válassz másikat!';
        }
    }
    
    if (isset($_FILES['userprofile']['name']) == true ){
        //debug_r($_FILES);
        $is_set_pictures = true;
        $old_user_profile = $user_data['user_profile'];
        $image_pathinfo = pathinfo($_FILES['userprofile']['name']);
        $image_origname = $image_pathinfo['filename'];
        $image_extension = $image_pathinfo['extension'];
        $image_tmp_name = $_FILES['userprofile']['tmp_name'];
        $image_dimensions = getimagesize($image_tmp_name);
        $image_mimetype = $image_dimensions['mime'];
        $allowed_extensions = array(
            'image/jpeg'    => 'jpeg',
            'image/jpg'     => 'jpg',
            'image/png'     => 'png',
            'image/gif'     => 'gif'
        );
        if (is_uploaded_file($image_tmp_name) === true){
            if ($_FILES['userprofile']['size'] > 200000) {
                $errors[] = "A file méret nem lehet nagyobb, mint 200 kbyte!";
            } else if (!in_array($image_extension, $allowed_extensions) === true){
                $errors[] = "Nem engedélyezett formátum!<br />Csak képfile-t lehet feltölteni (jpg, jpeg, png, gif).";
            }
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
                ?>
            </div><!-- end post_error -->
            
            <?php
            if (isset($_GET['success']) && $_GET['success'] === 'ok') {
                //minden rendben
                echo '<div class="post_success pt-center">';
                echo '<pre><ul>';
                echo '<li>A beállítások sikeresen megváltoztak!</li>';
                echo '</ul></pre>';
                echo '</div>';
            } else if (empty($_POST) === false && empty($errors) === true) {
                //adat feldolgozás
                $update_data = array(
                    'user_firstname'    => $_POST['firstname'],
                    'user_lastname'     => $_POST['lastname'],
                    'user_email'        => $_POST['email'],
                    'user_allow_email'  => $allow_email = (isset($_POST['allow_email']))? 1 : 0,
                    'file_isset'        => $is_set_pictures
                );
                
                if ($is_set_pictures === true) {
                    $update_data['tmpname'] = $image_tmp_name;
                    $update_data['file_name'] = conv_ekezet($image_origname);
                    $update_data['old_profile'] = $user_data['user_profile'];
                    $update_data['user_profile'] = "img/profile/".$update_data['file_name'].'.'.$image_extension;
                }
                
                //debug_r($update_data);
                update_user($dbc, $update_data, $user_data['user_id']);
                header("Location: index.php?page=settings&success=ok");
                exit();
            } else {
                //debug_r($user_data['user_allow_email']);
                ?>
                
                <header>
                    <p class="page_title">Beállítások</p>
                </header>
                <form action="" method="post" enctype="multipart/form-data">
                
                    <section>
                        <ul class="bevitel">
                            <li>
                                <label class="cimke" for="firstname">Keresztnév:* </label>
                                <input type="text" class="sett" id="firstname" name="firstname" value="<?php echo $user_data['user_firstname']; ?>" placeholder="Keresztnév...*"/>
                            </li>
                            <li>
                                <label class="cimke" for="lastname">Vezetéknév: </label>
                                <input type="text" class="sett" id="lastname" name="lastname" value="<?php echo $user_data['user_lastname']; ?>" placeholder="Vezetéknév..."/>
                            </li>
                            <li>
                                <label class="cimke" for="email">E-mail:* </label>
                                <input type="text" class="sett" id="email" name="email" value="<?php echo $user_data['user_email']; ?>" placeholder="E-mail...*"/>
                            </li>
                            <li>
                                <div class="edit_pic">
                                    <div class="minpic_div">
                                        <?php echo $img_avatar_tag; ?>
                                    </div>
                                    <div class="input_div">
                                        <label style="display: block; margin-bottom: 5px;" for="userprofile">Profilkép</label>
                                        <input type="file" id="userprofile" name="userprofile" onchange=""/>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                            </li>
                            <li style="margin: 10px 0 20px;">
                                <label class="chkb_cimke" style="margin-right: 10px;" for="allow_email">Szeretne kapni e-mailt tőlünk?</label>
                                <input  type="checkbox" id="allow_email" name="allow_email" <?php echo ($user_data['user_allow_email'] == 1) ? 'checked="checked"' : '';?> />
                            </li>
                            <li>
                                <label class="cimke" for="password">Jelszó*</label>
                                <input type="password" class="sett" id="password" name="password" value="" placeholder="jelszó..."/>
                                
                            </li>
                            <li><p style="margin-left: 70px;">A változtatások megerősítéséhez add meg a jelszavad.</p></li>
                            <li></li>
                        </ul>
                    </section>
                    
                    <footer>
                        <ul class="bevitel">
                            <button class="button buttonLong" type="submit" id="btn_settings" name="btn_settings"><i class="fa fa-refresh" aria-hidden="true"></i> Frissítés</button>
                        </ul>
                        <p class="footer_text">&copy; 2017, Relierf</p>
                        <div class="clear"></div>
                    </footer>
                
                </form>
                
            <?php 
            }
            ?>