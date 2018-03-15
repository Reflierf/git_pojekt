<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */

if (!empty($_POST)) {
    if (isset($_POST['btn_deletarticle'])) {
        //debug_r($_POST);
        $sqlerrors = deleteArticle($dbc, $_POST['articleid']);
        //debug_r($sqlerrors); die();
        if ($sqlerrors['sql_error'] <= 0) {
            ?><div><script>alert("Hiba a gépezetben! Próbáld újra!");</script></div><?php
        } else if ($sqlerrors['sql_error'] >= 1) {
            ?><div><script>alert("Cikk törölve!");</script></div><?php
        }
    }
    if (isset($_POST['btn_edit'])) {
        header("Location: index.php?page=new_article&id=".$_POST['articleid']);
        exit();
    }
}

?>
<?php include 'includes/widgets/asside_pages.php'; ?>
<article id="edit_news">
    <p class="page_title">
        <strong>Cikkek listája</strong>
    </p>
    <div class="nhead">
            <span class="ntitle">Cím</span>
            <span class="ndate">Létrehozva</span>
            <span class="ndate">Módosítva</span>
            <span class="nauthor">Szerző</span>
            <span class="nimg" style="text-align: center;">Tartalom</span>
            <div class="clear"></div>
        </div>
    <?php
        $pages = getAllArticles($dbc);
        if ($pages === false) {
            echo "Nincs megjeleníthető tartalom.";
        } else {
            while ($row = $pages->fetch()) {
                $auser = user_data($dbc, $row['cikk_author'], 'user_name');
                if (strlen($row['cikk_content']) > 80) {
                    $prew_content = substr($row['cikk_content'], 0, 80);
                    $last_space = strrpos($prew_content, ' ');
                    $prew_content = substr($row['cikk_content'], 0, $last_space);
                } else {
                    $prew_content = $row['cikk_content'];
                }
                ?>
               	<div class="arow">
                    <span class="atitle"><a href="index.php?page=new_article&id=<?php echo $row['cikk_id']; ?>"><?php echo $row['cikk_title']; ?></a></span>
                    <span class="adate"><?php echo $row['cikk_createdate']; ?></span>
                    <span class="adate"><?php echo $row['cikk_modifydate']; ?></span>
                    <span class="aauthor"><?php echo $auser['user_name']; ?></span>
                    <span class="acont"><?php echo htmlspecialchars(strip_tags(trim($prew_content))).'...'; ?></span>
                    <?php //debug_r($row); ?>
                    <span class="edit_article">
                        <div class="btn_container">
                            <form action="" method="post" style="margin: 0; padding: 0;">
                                <input type="hidden" id="articleid" name="articleid" value="<?php echo $row['cikk_id']; ?>"/>
                                <!-- <input type="hidden" id="newstitle" name="newstitle" value="<?php //echo $row['cikk_title']; ?>"/> -->                       
                                <button class="button buttonBox" title="Szerkesztés" name="btn_edit" value="btn_edit">
                                    <i class="fa fa-pencil-square-o"></i>
                                </button>
                                <button onclick="return confirm('<?php echo 'Valóban törlöd a '.$row['cikk_title'].' című cikket?'; ?>')" class="button buttonBox" title="Törlés" id="btn_deletarticle" name="btn_deletarticle">
                                    <i class="fa fa-trash-o"></i>
                                </button>
                            </form>
                        </div>
                    </span>  
                </div>
                <?php
                
            }
        }
    ?>
</article>