<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */

if (isset($_GET) && !empty($_GET)) {
    $newsid = (int)$_GET['id'];
    $getnews =getOneNews($dbc, $newsid);
    if ($getnews != false) {
        $row = $getnews->fetch();
    }
    
    //$oldtitle = $row['post_title'];
    $old_keyword =  $row['post_keywords'];
}

if (isset($_POST['btn_editnews'])) {
    //debug_r($_POST);
    //debug_r($convert_title);
    //debug_r($row);
    $required_fields = array('news_title', 'news_content');
    foreach ($_POST as $key => $value) {
        if ((empty($value) && in_array($key, $required_fields)) /*|| (!isset($_POST['news_visible']) === true)*/ ) {
            $errors[] = 'A csillaggal megjelölt mezőket kötelező kitölteni!';
            break(1);
        }
    }
}

if (isset($_FILES['npic']['name']) && !empty($_FILES['npic']['name']) && empty($errors)) {
    //debug_r($_FILES);
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
        
        <article id="edit_news">
        
        <p class="page_title push_obj">
            <strong>Hír szerkesztése</strong>
        </p>
        
        <div class="post_error pt-center">
            <?php
            if (!empty($errors) === true) {
                echo '<pre>';
                echo output_errors($errors);
                echo '</pre>';
            }
            ?><!-- end post_error -->
        </div>
        
<?Php
        if (isset($_GET['success'])&& $_GET['success'] == 'ok') {
            ?>
            <div><script>alert("A hír kiposztolva!");</script></div>
            <?php
            header("Location: index.php?page=news_list");
            exit();
        } else if (!empty($_POST) && empty($errors)) {
            //debug_r($_POST);
            //debug_r($_FILES);
            //debug_r($row);
            /*if ($oldtitle != $_POST['news_title']) {
                $row['post_oldtitle'] = $row['post_oldtitle'].'/'.$oldtitle;
            }*/
            $convert_title = conv_ekezet($_POST['news_title']);
            $modify_date = date('Y-m-d H:m');
            //echo $old_keyword.'   '.$_POST['news_label']; die();
            if ($old_keyword !== $_POST['news_label']) {
                $news_keywords = $old_keyword.' '.$_POST['news_label'];
            }
            $news_content = $row['post_content'].'<br /><br />'.'Hozzáfúzve: '.$modify_date.'<br />'.$_POST['news_content'];
            $news_data = array(
                //'post_title'        => $_POST['news_title'],
                //'post_oldtitle'     => $row['post_oldtitle'],
                //'post_title_alias'  => $convert_title,
                'post_id'           => $row['post_id'],
                'post_keywords'     => $news_keywords,
                'post_modify_date'  => $modify_date,
                'post_content'      => $news_content,
                'post_visible'      => isset($_POST['news_visible']) == 'on' ? 1 : 0,
            );
            //debug_r($news_data);
            $returned_error = modify_news($dbc, $news_data);
            
            if ( /*(isset($returned_error['upload_error']) != 1) || */(isset($returned_error['sql_error']) != 1)) {
                ?><div><script>alert("Baj van!");</script></div><?php
            } else {
                header("Location: index.php?page=edit_news&success=ok");
                exit();
            }
        }
        
        
        
        //$row = getOneNews($dbc, $newsid);
        
        $nuser = user_data($dbc, $row['post_author'], 'user_name');
        /*if (strlen($row['post_content']) > 80) {
            $prew_content = substr($row['post_content'], 0, 80);
            $last_space = strrpos($prew_content, ' ');
            $prew_content = substr($row['post_content'], 0, $last_space);
        } else {
            $prew_content = $row['post_content'];
        }*/
        //debug_r($row);
        ?>
       	<!-- <div class="nrow">
            <span style="width: 8%; color: #fafafa; display: block;">Valami</span>
            <span class="ntitle"><?php echo $row['post_title']; ?></span>
            <span class="ndate"><?php echo $row['post_create_date']; ?></span>
            <span class="ndate"><?php echo $row['post_modify_date']; ?></span>
            <span class="nauthor"><?php echo $nuser['user_name']; ?></span>
            <span class="ncont"><?php echo $prew_content.'...'; ?></span>
            <span class="nimg">
                <img src="../<?php echo $row['post_image']; ?>"/>
            </span>
            
            <div class="clear"></div>
        </div> -->
        <div><?php echo $row['post_id']; ?></div>
        <form action="" method="post" enctype="multipart/form-data" class="top_line">
            <ul class="bevitel">
                <li>
                    <label class="cimke" for="news_title">Hír címe:*</label>
                    <input type="text" class="sett" id="news_title" name="news_title" value="<?php echo ((isset($row['post_title'])) ? $row['post_title'] : ''); ?>" readonly/>
                </li>
                <?php 
                /*if (!empty($row['post_oldtitle'])) {
                    ?>
                   	<li>
                        <label class="cimke" for="news_title">Hír régi címe:*</label>
                        <input type="text" class="sett" id="news_title" name="news_oldtitle" value="<?php echo ((isset($row['post_oldtitle'])) ? $row['post_oldtitle'] : ''); ?>"/>
                    </li>
                    <?php
                }*/
                ?>
                 <li>
                    <label class="cimke" for="news_label">További cimkék:</label>
                    <input type="text" class="sett" id="news_label" name="news_label" value="<?php //echo ((isset($row['post_keywords'])) ? $row['post_keywords'] : '' ); ?>" placeholder="Cimkék..."/>
                </li>
                <li>
                    <div class="editpic">
                        <div class="minpic_div">
                            <img src="../<?php echo $row['post_image']; ?>" class="minpic"/>
                        </div>
                        <div class="input_div">
                            <label class="cimke2" for="news_picture">Kép hozzáadása:</label>
                            <input type="file" class="pictures" id="npic" name="npic" onchange="zab()" value="<?php echo ((isset($row['post_image'])) ? $row['post_image'] : ''); ?>" placeholder="Kép hozzádaása..."/>
                        </div>
                        <div class="clear"></div>
                    </div>
                </li>
                <li>
                    <div style="margin: 10px 170px; height: 100px; background: #fff; border: 1px solid #000; width: 69%; display: none;">
                    <!-- valami -->
                    
                    </div>
                </li>
                <li>
                    <label class="cimke" for="news_content">Tartalom:*</label>
                    <p style="display: inline-block; margin-bottom: 20px; text-align: justify; width: 66%;"><?php echo $row['post_content']; ?></p>
                </li>
                <li>
                    <textarea class="sett" style="margin-left: 20%;" id="news_content" name="news_content" placeholder="További tartalom hozzáfűzése..."><?php echo ((isset($_POST['news_content'])) ? $_POST['news_content'] : ''); ?></textarea> 
                </li>
                <li style="margin: 10px 0 0;">
                    <label class="chkb_cimke" for="news_visible">Láthatóság:* </label>
                    <input type="checkbox" id="news_visible" name="news_visible" <?php echo (isset($row['post_visible']) == 1) ? 'checked="checked"' : '';?>/>
                </li>
                
            </ul><!-- end bevitel -->
            <div class="top_line"></div>
            <ul class="bevitel">
                <li class="">
                    <button class="button buttonLong" id="btn_editnews" name="btn_editnews"><i class="fa fa-refresh" aria-hidden="true"></i> Hír posztoláása</button>
                </li>
            </ul><!-- end bevitel -->
            </ul>
        </form>
        
</article>