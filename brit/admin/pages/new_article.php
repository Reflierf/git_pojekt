<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */


?>

<?php include 'includes/widgets/asside_pages.php'; ?>
<article id="new_article">
    
    <?php
    $adata = array();
    $aid = "";
    //$adata['atitle'] = "Szex mindenkinek!";
    ?>
    
    <?php
    //Új cikk létrehozásánál ellenőrzi, hogy mindent kitöltöttünk-e?
    if (isset($_POST['btn_addarticle'])) {
        //debug_r($_POST); die();
        $required_fields = array('atitle', 'acontent');
        foreach ($_POST as $key => $value) {
            if ((empty($value) && in_array($key, $required_fields)) /*|| (!isset($_POST['news_visible']) === true)*/ ) {
                $errors[] = 'A csillaggal megjelölt mezőket kötelező kitölteni!';
                break(1);
            }
        }
        $adata['atitle'] = $_POST['atitle'];
        $adata['acontent'] = $_POST['acontent'];
        $adata['avisible'] = isset($_POST['avisible']) == 'on' ? 1 : 0;
        //$adata['avisible'] = $_POST['avisible'];
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
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $aid = (int)$_GET['id'];
        $get_article = getArticle($dbc, $aid);
        if ($get_article != false) {
            $row = $get_article->fetch();
            //debug_r($row);
            $adata['atitle'] = $row['cikk_title'];
            $adata['acontent'] = $row['cikk_content'];
            $adata['avisible'] = $row['cikk_visible'];
        }     
    }
    
    if (isset($_GET['success']) && $_GET['success'] == 'ok') {
        ?>
    	<div><script>alert("A cikk sikeresen elmentve!");</script></div>
        <?php
        header("Location: index.php?page=pages");
        exit();
    } else if (!empty($_POST) && empty($errors)){
        //debug_r($_POST); die();
        $convert_title = conv_ekezet($_POST['atitle']);
        if (isset($row['cikk_createdate']) == true) {
            $create_date = $row['cikk_createdate'];
            $modify_date = date('Y-m-d H:m:s');
        } else {
            $create_date = date('Y-m-d H:m:s');
            $modify_date = $create_date;
        }
        if (isset($row['cikk_author'])) {
            $author = $row['cikk_author'];
            $modify_author = $user_data['user_id'];
        } else {
            $author = $user_data['user_id'];
            $modify_author = $author;
        }
        
        $article_data = array(
            'cikk_id'           => $aid,
            'cikk_title'        => $_POST['atitle'],
            'cikk_titlealias'   => $convert_title,
            'cikk_createdate'   => $create_date,
            'cikk_modifydate'   => $modify_date,
            'cikk_author'       => $author,
            'cikk_modífyauthor' => $modify_author,
            'cikk_content'      => $_POST['acontent'],
            'cikk_visible'      => isset($_POST['avisible']) == 'on' ? 1 : 0,
        );
        //debug_r($article_data); die();
        
        if (!empty($article_data['cikk_id']) == true) {
            $returned_error = modifyArticle($dbc, $article_data);
        } else {
            $returned_error = saveArticle($dbc, $article_data);
        }
        
        if ((isset($returned_error['upload_error']) != 1) || (isset($returned_error['sql_error']) != 1)) {
            ?><div><script>alert("Baj van!");</script></div><?php
        } else {
            header("Location: index.php?page=new_article&success=ok");
            exit();
        }/**/
    }
    ?>
    <p class="page_title">
        <strong>Új cikk</strong>
    </p>
    <form action="" method="post" enctype="multipart/form-data" class="top_line">
        <ul class="editor">
            <li class="">
                <button class="button buttonLong" id="btn_addarticle" name="btn_addarticle"><i class="fa fa-refresh" aria-hidden="true"></i> Cikk mentése</button>
            </li>
        </ul><!-- end button -->
        <div class="top_line"></div>
        <ul class="editor">
            <li>
                <label class="cimke" for="atitle">Cikk címe:</label>
                <input type="text" class="sett" id="atitle" name="atitle" value="<?php echo (isset($adata['atitle'])) ? $adata['atitle'] : ''; ?>"/>
            </li>
            <li>
                <!-- <label class="cimke" for="acontent">Cikk tartalma</label> -->
                <textarea class="sett" id="acontent" name="acontent">
                    <?php echo (isset($adata['acontent'])) ? $adata['acontent'] : ''; ?>
                </textarea>
            </li>
            <li style="margin: 10px 0 0;">
                <label class="chkb_cimke" for="avisible">Láthatóság:* </label>
                <input type="checkbox" id="avisible" name="avisible" <?php echo (isset($adata['avisible']) == 1) ? 'checked="checked"' : '';?>/>
            </li>
        </ul>
        <div class="top_line"></div>
        <!--<ul class="editor">
            <li class="">
                <button class="button buttonLong" id="btn_addarticle" name="btn_addarticle"><i class="fa fa-refresh" aria-hidden="true"></i> Cikk mentése</button>
            </li>
        </ul><!-- end bevitel -->
    </form>
</article>