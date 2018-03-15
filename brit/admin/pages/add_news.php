<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */



?>

<?php include "includes/widgets/asside_news.php"; ?>
<article id="add_news">

    <!-- Feldolgozó rész indul -->
    <?php
    $nid = 0;
    $ndata = array();
    $image_isset = false;
    
    //Úrlap adatok feldolgozása
    if (isset($_POST['btnAddNews_top']) == true || isset($_POST['btnAddNews_bottom']) == true) {
        //debug_r($_POST);
        $required_fields = array('news_title', 'acontent');
        foreach ($_POST as $key => $value) {
            if (empty($value) && in_array($key, $required_fields)) {
                $errors[] = "A csillaggal megjelölt mezőket kötelező kitölteni!";
                break(1);
            }
        }
        $ndata['news_title'] = $_POST['news_title'];
        $ndata['news_keywords'] = $_POST['news_keywords'];
        $ndata['acontent'] = $_POST['acontent'];
        (isset($_POST['news_visible'])) ? $ndata['news_visible'] = $_POST['news_visible'] : $ndata['news_visible'] = 0;
        (isset($_POST['is_news_image'])) ? $ndata['newspicture'] = $_POST['is_news_image'] : $ndata['newspicture'] = '';
        
        //képadtok feldolgozása
        if (isset($_FILES['news_pic']['name']) && !empty($_FILES['news_pic']['name']) && empty($errors)) {
            //debug_r($_FILES['news_pic']);
            $image_isset = true;
            $image_pathinfo = pathinfo($_FILES['news_pic']['name']);
            $image_origname = $image_pathinfo['filename'];
            $image_extension = $image_pathinfo['extension'];
            $image_tmp_name = $_FILES['news_pic']['tmp_name'];
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
            
            $ndata['newspicture'] = 'img/'.$image_pathinfo['basename'];
            
        }
        
    } else if (isset($_GET['id']) && !empty($_GET['id'])) {        //Ha szerkesztésre lett küldve egy cikk
        //debug_r($_GET);
        $nid = (int)$_GET['id'];
        $getnews = getOneNews($dbc, $nid);
        $row = $getnews->fetch();
        //debug_r($row);
        $ndata['news_title'] = $row['post_title'];
        $ndata['news_keywords'] = $row['post_keywords'];
        $ndata['newspicture'] = $row['post_image'];
        $ndata['acontent'] = $row['post_content'];
        $ndata['news_visible'] = $row['post_visible'];
        //debug_r($ndata);
        
    }
    
    ?>
    <!-- end Feldolgozó -->
    
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
    //Mentés és eredményfeldolgozás
    if (isset($_GET['success']) && $_GET['success'] == 'ok') {
        //eredmény feldolgozás
        ?><div><script>alert("A hír kiposztolva!");</script></div><?php
        header("Location: index.php?page=news_list");
        exit();
    } else if (!empty($_POST) && empty($errors)) {
        //mentés
        if ($_POST['news_nid'] != 0) {
            $convert_title = conv_ekezet($_POST['news_title']);
            $getnews = getOneNews($dbc, $_POST['news_nid']);
            $modify_date = date('Y-m-d H:m:s');
            $row = $getnews->fetch();
            $news_data = array(
                'post_id'               => $_POST['news_nid'],
                'post_title'            => $_POST['news_title'],
                'post_title_alias'      => $convert_title,
                'post_create_date'      => $row['post_create_date'],
                'post_modify_date'      => $modify_date,
                'post_author'           => $row['post_author'],
                'post_modify_author'    => $user_data['user_id'],
                'post_keywords'         => $_POST['news_keywords'],
                'post_content'          => $_POST['acontent'],
                'post_visible'          => isset($_POST['news_visible']) == 'on' ? 1 : 0,
                'post_old_img'          => $row['post_image'],
            );
            
        } else {
            //debug_r($_POST);
            $convert_title = conv_ekezet($_POST['news_title']);
            $create_date = date('Y-m-d H:m:s');
            $news_data = array(
                'post_title'            => $_POST['news_title'],
                'post_title_alias'      => $convert_title,
                'post_create_date'      => $create_date,
                'post_modify_date'      => $create_date,
                'post_author'           => $user_data['user_id'],
                'post_modify_author'    => $user_data['user_id'],
                'post_keywords'         => $_POST['news_keywords'],
                'post_content'          => $_POST['acontent'],
                'post_visible'          => isset($_POST['news_visible']) == 'on' ? 1 : 0,
            );
        }
        if ($image_isset == true) {
            $news_data['img_name'] = $image_origname;
            $news_data['post_imgext'] = $image_extension;
            $news_data['post_imgtmp'] = $image_tmp_name;
            $news_data['post_imgisset'] = $image_isset;
        }
        //debug_r($news_data);
        if ($_POST['news_nid'] != 0) {
            $returned_error = modify_news($dbc, $news_data);
        } else {
            $returned_error = save_news($dbc, $news_data);
        }
        
        if ((isset($returned_error['upload_error']) != 1) || (isset($returned_error['sql_error']) != 1)) {
            ?><div><script>alert("Baj van!");</script></div><?php
        } else {
            header("Location: index.php?page=add_news&success=ok");
            exit();
        }
        
    }
    ?>
    
    <p class="page_title">
        <strong>Új cikk</strong>
    </p>
    
    <form action="" enctype="multipart/form-data" method="post" class="top_line">
        <input type="hidden" id="news_nid" name="news_nid" value="<?php echo (isset($nid)) ? $nid : 0; ?>"/>
        <!--  --><ul class="editor">
            <button class="button buttonLong" id="btnAddNews_top" name="btnAddNews_top" ><i class="fa fa-refresh" aria-hidden="true"></i> Hír posztoláása</button>
        </ul>
        <div class="top_line"></div>
        <ul class="editor">
            <li>
                <label class="cimke" for="news_title">Hír címe:*</label>
                <input type="text" class="sett" id="news_title" name="news_title" placeholder="hír címe..." 
                    value="<?php echo ((isset($ndata['news_title'])) ? $ndata['news_title'] : ''); ?>" placeholder="hír címe...*"/>
            </li>
            <li>
                <label class="cimke" for="news_keywords">Cimkék:</label>
                <input type="text" class="sett" id="news_keywords" name="news_keywords" placeholder="cimké..." 
                    value="<?php echo ((isset($ndata['news_keywords'])) ? $ndata['news_keywords'] : ''); ?>"/>
            </li>
            <li>
                <div class="editpic">
                    <div class="minpic_div">
                        <?php 
                        if (isset($ndata['newspicture'])) {
                            ?>
                           	<img  id="newspicture" src="<?php echo '../'.$ndata['newspicture']; ?>" class="minpic"/>
                            <!--  --><input type="hidden" id="is_news_image" name="is_news_image" value="<?php echo $ndata['newspicture']; ?>"/>
                            <?php
                        } else {
                            ?>
                           	<img id="newspicture" src="../img/nopicture.jpg" class="minpic"/>
                            <?php
                        }
                        ?>
                        
                    </div>
                    <div class="input_div">
                        <label class="cimke2" for="news_pic">Kép hozzáadása</label>
                        <input type="file" class="pictures" id="news_pic" name="news_pic" placeholder="kép hozzáadása..." onchange="this.form.newspicture.src='../img/'+this.value" 
                            value="<?php //echo (isset($ndata['newspicture'])) ? $ndata['newspicture'] : ''; ?>"/>
                    </div>
                    <div class="clear"></div>
                </div>
            </li>
            <li>
                <label class="cimke" for="acontent">Tartalom:*</label>
                <textarea class="sett" id="acontent" name="acontent">
                    <?php echo (isset($ndata['acontent'])) ? $ndata['acontent'] : ''; ?>
                </textarea>
            </li>
            <li style="margin: 10px 0 0;">
                <label class="chkb_cimke" for="news_visible">Láthatóság:* </label>
                <input type="checkbox" id="news_visible" name="news_visible" 
                    <?php echo (isset($ndata['news_visible']) == 1) ? 'checked="checked"' : '';?>/>
            </li>
        </ul>
        <div class="top_line"></div>
        <!--  --><ul class="editor">
            <button class="button buttonLong" id="btnAddNews_bottom" name="btnAddNews_bottom" ><i class="fa fa-refresh" aria-hidden="true"></i> Hír posztoláása</button>
        </ul>
    </form>
</article>