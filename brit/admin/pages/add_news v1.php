<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */

?>
    <article id="add_news">
        <?php
        
        //$title_regex = '([a-zA-Z0-9áéíóöőúüűÁÉÍÓÖŐÚÜŰ\!\?\:\(\)\-\_\ ])';
        
        if (isset($_POST) && !empty($_POST)) {
            //debug_r($_POST); //die();
            $required_fields = array('news_title', 'acontent');
            foreach ($_POST as $key => $value) {
                if ((empty($value) && in_array($key, $required_fields)) /*|| (!isset($_POST['news_visible']) === true)*/ ) {
                    $errors[] = 'A csillaggal megjelölt mezőket kötelező kitölteni!';
                    break(1);
                }
            }
            /*if (preg_match($title_regex, $_POST['news_title']) == false) {
                $errors[] = 'A cím nem tartalmazhat speciális karaktereket(#, +, &, stb...)!';
            }*/
        }
        
        if (isset($_FILES['npic']['name']) && !empty($_FILES['npic']['name']) && empty($errors)) {
            $image_isset = true;
            $image_pathinfo = pathinfo($_FILES['npic']['name']);
            $image_filename = $image_pathinfo['basename'];
            $image_origname = $image_pathinfo['filename'];
            $image_extension = $image_pathinfo['extension'];
            $image_tmp_name = $_FILES['npic']['tmp_name'];
            $image_dimensions = getimagesize($image_tmp_name);
            $image_mimetype = $image_dimensions['mime'];
            $allowed_extensions = array(
                'image/jpeg'    => 'jpeg',
                'image/jpg'     => 'jpg',
                'image/png'     => 'png',
                'image/gif'     => 'gif'
            );
            if (is_uploaded_file($image_tmp_name) === true) {
                if (!in_array($image_pathinfo['extension'], $allowed_extensions) || !isset($allowed_extensions[$image_mimetype])) {
                    $errors[] = 'Nem engedélyezett fileformátum!';
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
        
        <?php
        if (isset($_GET['success'])&& $_GET['success'] == 'ok') {
            ?>
        	<div><script>alert("A hír kiposztolva!");</script></div>
            <?php
            header("Location: index.php?page=add_news");
            exit();
            /*?>
            <div class="post_success pt-center">
                <?php
                $errors[] = "A hír kiposztolva!";
                    echo '<pre>';
                    echo output_errors($errors);
                    echo '</pre>';
                ?><!-- end post_error -->
            </div>
            <?php*/

        } else if (!empty($_POST) && empty($errors)) {
            //$_POST['news_title'] = iconv('windows-1250', 'utf-8', $_POST['news_title']); 
            //die($_POST['news_title']);
            $convert_title = conv_ekezet($_POST['news_title']);
            $create_date = date('Y-m-d H:m:s');
            $news_data = array(
                'post_title'        => $_POST['news_title'],
                'post_title_alias'  => $convert_title,
                'post_create_date'  => $create_date,
                'post_modify_date'  => $create_date,
                'post_author'       => $user_data['user_id'],
                'post_keywords'     => $_POST['news_label'],
                'post_content'      => $_POST['acontent'],
                'post_visible'      => isset($_POST['news_visible']) == 'on' ? 1 : 0,
            );
            
            //debug_r($news_data); die();
            if (isset($image_isset) && !empty($image_isset)) {
               $news_data['post_imgname'] = $image_origname;
               $news_data['post_imgext'] = $image_extension;
               $news_data['post_imgtmp'] = $image_tmp_name;
               $news_data['post_imgisset'] = $image_isset;
            }
            
            $returned_error = save_news($dbc, $news_data);
            
            if ((isset($returned_error['upload_error']) != 1) || (isset($returned_error['sql_error']) != 1)) {
                ?><div><script>alert("Baj van!");</script></div><?php
            } else {
                header("Location: index.php?page=add_news&success=ok");
                exit();
            }
        }
        ?>
        
        <p class="page_title push_obj">
            <strong>Új hír hozzáadása</strong>
        </p>
        <form action="" method="post" enctype="multipart/form-data" class="top_line">
            <ul class="bevitel">
                <li>
                    <label class="cimke" for="news_title">Hír címe:*</label>
                    <input type="text" class="sett" id="news_title" name="news_title" value="<?php echo ((isset($_POST['news_title'])) ? $_POST['news_title'] : ''); ?>" placeholder="Hír címe...*"/>
                </li>
                <li>
                    <label class="cimke" for="news_label">Cimkék:</label>
                    <input type="text" class="sett" id="news_label" name="news_label" value="<?php echo ((isset($_POST['news_label'])) ? $_POST['news_label'] : ''); ?>" placeholder="Cimkék..."/>
                </li>
                <li>
                    <label class="cimke" for="news_picture">Kép hozzáadása:</label>
                    <input type="file" class="sett pictures" id="npic" name="npic" value="<?php echo ((isset($_POST['npic'])) ? $_POST['npic'] : ''); ?>" placeholder="Kép hozzádaása..."/>
                </li>
                <li>
                    <div style="margin: 10px 170px; height: 100px; background: #fff; border: 1px solid #000; width: 69%; display: none;">
                    valami
                    
                    </div>
                </li>
                <!-- <li>
                    <label class="cimke" for="news_content">Tartalom:*</label>
                    <textarea class="sett" id="news_content" name="news_content" placeholder="Tartalom..."><?php //echo ((isset($_POST['news_content'])) ? $_POST['news_content'] : ''); ?></textarea>
                </li> -->
                <li>
                    <!-- <label class="cimke" for="acontent">Cikk tartalma</label> -->
                    <textarea class="sett" id="acontent" name="acontent">
                        <?php echo (isset($adata['acontent'])) ? $adata['acontent'] : ''; ?>
                    </textarea>
                </li>
                <li style="margin: 10px 0 0;">
                    <label class="chkb_cimke" for="news_visible">Láthatóság:* </label>
                    <input type="checkbox" id="news_visible" name="news_visible" <?php echo (isset($_POST['news_visible']) == 1) ? 'checked="checked"' : '';?>/>
                </li>
                
            </ul><!-- end bevitel -->
            <div class="top_line"></div>
            <ul class="bevitel">
                <li class="">
                    <button class="button buttonLong" id="btn_addnews" name="btn_addnews"><i class="fa fa-refresh" aria-hidden="true"></i> Hír posztoláása</button>
                </li>
            </ul><!-- end bevitel -->
                
        </form>

    </article>