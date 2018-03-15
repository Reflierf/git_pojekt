<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */

$pages = getAllArticles($dbc);

?>
<article id="articles">
    <header>
        <p class="page_title">Cikkek listája</p>
    </header>
    <section>
        
        <?php 
            if ($pages === false) {
                echo "Nincs megjeleníthető tartalom.";
            } else {
                while ($row = $pages->fetch()) {
                    if ($row['cikk_visible'] == 1) {
                        $auser = user_data($dbc, $row['cikk_author'], 'user_name');
                        ?>
                        <div class="article_box">
                            <div class="article_data">
                                <ul>
                                    <div class="tsec">
                                        <li class="title"><a href="index.php?page=read_article&aid=<?php echo $row['cikk_id']; ?>"><?php echo $row['cikk_title']; ?></a></li>
                                        <li class="ndate">
                                            <span style="margin-right: 10px;">Szerző: <a href="<?php echo $auser['user_name']; ?>"><?php echo $auser['user_name']; ?></a></span>
                                            <span>Létrehozva: <?php echo $row['cikk_createdate']; ?></span>
                                            <?php 
                                            if ($row['cikk_createdate'] !== $row['cikk_modifydate']) {
                                                ?><span>Modosítva: <?php echo $row['cikk_modifydate']; ?></span><?php
                                            }
                                            ?>
                                        </li>
                                    </div><!-- end tsec -->
                                    <li class="ncontent"> <!-- Innét megy a hír tartalma. Ha hosszabb 200 karakternél, elvágva az utolsó szóköznél. -->
                                        <?php
                                        if (strlen($row['cikk_content']) > 200) {
                                            $prew_content = substr($row['cikk_content'], 0, 200);
                                            $last_space = strrpos($prew_content, ' ');
                                            $prew_content = substr($row['cikk_content'], 0, $last_space);
                                            echo $prew_content.'...';
                                        } else {
                                            echo $row['cikk_content'];
                                        }
                                        
                                        ?>
                                    </li>
                                    <li class="alink"><a href="index.php?page=read_article&aid=<?php echo $row['cikk_id']; ?>"><i>olvasd tovább...</i></a></li>
                                </ul>
                            </div>
                        </div>
                        <?php
                    }
                }
            }
        ?>
    </section>
    <footer>
        <p class="footer_text"><i class="fa fa-bullseye" style="font-size: 14px;"></i> Brittany Bod Fansite</p>
        <div class="clear"></div>
    </footer>
</article>