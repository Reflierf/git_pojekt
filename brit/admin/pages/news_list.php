<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */



if (isset($_POST) && !empty($_POST)) {
    if (isset($_POST['btn_deletenews'])) {
        
        ?><!-- <div><script>alert("Most törlünk!");</script></div> --><?php
        //debug_r($_POST); die();
        $delete_news = array (
            'delete_id' => $_POST['newsid'],
            'delete_img'    => $_POST['newsimage'],
            'delete_title'  => $_POST['newstitle']
        );
        
        $sqlerrors[] = delete_news($dbc, $delete_news);
        //debug_r($sqlerrors);
        if ($sqlerrors[0] <= 0) {
            ?><div><script>alert("Hiba a gépezetben! Próbáld újra!");</script></div><?php
        } else if ($sqlerrors[0] >= 1) {
            ?><div><script>alert("Hír törölve!");</script></div><?php
        }
    }
    if (isset($_POST['btn_edit'])) {
        ?><!-- <div><script>alert("Most szerkesztünk!");</script></div> --><?php
        
        header("Location: index.php?page=edit_news&id=".$_POST['newsid']);
        exit();        
    }
    
}

?>
<?php include 'includes/widgets/asside_news.php'; ?>
<article id="edit_news">

        <p class="page_title">
            <strong>Hírek kezelése</strong>
        </p>
        <div class="nhead">
            <span class="ntitle">Cím</span>
            <span class="ndate">Létrehozva</span>
            <span class="ndate">Módosítva</span>
            <span class="nauthor">Szerző</span>
            <span class="nimg" style="text-align: center;">Kép</span>
            <div class="clear"></div>
        </div>
        <?php 
        $read_news = getAllNews($dbc);
        if ($read_news === false) {
            echo "Nincs megjeleníthető tartalom.";
        } else {
            while ($row = $read_news->fetch()) {
                $nuser = user_data($dbc, $row['post_author'], 'user_name');
                if (strlen($row['post_content']) > 40) {
                    $prew_content = substr($row['post_content'], 0, 40);
                    $last_space = strrpos($prew_content, ' ');
                    $prew_content = substr($row['post_content'], 0, $last_space);
                } else {
                    $prew_content = $row['post_content'];
                }
                //debug_r($prew_content);
                ?>
               	<div class="nrow">
                    <span class="ntitle"><a href="index.php?page=add_news&id=<?php echo $row['post_id']; ?>"><?php echo $row['post_title']; ?></a></span>
                    <span class="ndate"><?php echo $row['post_create_date']; ?></span>
                    <span class="ndate"><?php echo $row['post_modify_date']; ?></span>
                    <span class="nauthor"><?php echo $nuser['user_name']; ?></span>
                    <!-- <span class="ncont"><?php echo strip_tags(nl2br($prew_content)).'...'; ?></span>
                    <span class="ncont"><?php //echo $prew_content.'...'; ?></span> -->
                    <span class="nimg">
                        <img src="../<?php echo $row['post_image']; ?>"/>
                    </span>
                    <span class="edit_news">
                        <div class="btn_container">
                            <form action="" method="post" style="margin: 0; padding: 0;">
                                <input type="hidden" id="newsid" name="newsid" value="<?php echo $row['post_id']; ?>"/>
                                <input type="hidden" id="newsimage" name="newsimage" value="<?php echo $row['post_image']; ?>"/>
                                <input type="hidden" id="newstitle" name="newstitle" value="<?php echo $row['post_title']; ?>"/>                       
                                <button class="button buttonBox" title="Szerkesztés" name="btn_edit" value="btn_edit">
                                    <i class="fa fa-pencil-square-o"></i>
                                </button>
                                <button onclick="return confirm('<?php echo 'Valóban törlöd a '.$row['post_title'].' című hírt?'; ?>')" class="button buttonBox" title="Törlés" id="btn_deletenews" name="btn_deletenews">
                                    <i class="fa fa-trash-o"></i>
                                </button>
                                <!-- <a href="#" onclick="return confirm('<?php echo 'Valóban törlöd a '.$row['post_title'].' című hírt?'; ?>')">
                                    <input type="hidden" id="newsimage" name="newsimage" value="<?php echo $row['post_image']; ?>"/>
                                    <input type="hidden" id="newstitle" name="newstitle" value="<?php echo $row['post_title']; ?>"/>
                                    <button class="button buttonBox" title="Törlés" id="btn_deletenews" name="btn_deletenews">
                                        <i class="fa fa-trash-o"></i>
                                    </button>
                                </a> -->
                            </form>
                        </div>
                    </span>
                    <div class="clear"></div>
                </div>
    
                <?php
                //debug_r($row);
            }
        }
        ?>
        <div id="table">
            
        </div>
        
</article>