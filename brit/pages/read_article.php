<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */


?>
<article id="read_article">
<?php

    if (isset($_GET['aid']) && !empty($_GET['aid'])) {
        $article_id = $_GET['aid'];
        $get_article = getArticle($dbc, $article_id);
        if ($get_article != false) {
            $row = $get_article->fetch();
            //debug_r($row);
            $author = user_data($dbc, $row['cikk_author'], 'user_name');
            if ($row['cikk_modifydate'] == $row['cikk_createdate']) {
                $echo_modify_date = '  --------------  ';
            } else {
                $echo_modify_date = $row['cikk_modifydate'];
            }
            ?>
            
           	<header>
                <p class="page_title"><?php echo $row['cikk_title']; ?></p>
            </header>
            
            <section>
                <div class="alap">
                    <div id="article_data">
                        <div class="user_img_box">
                            <img src="img/nopicture.jpg"/>
                        </div>
                        <div class="ndatas">
                            <span>Cikk szerzője: <a href="<?php echo $author['user_name']; ?>"><?php echo $author['user_name']; ?></a></span>
                            <span>Cikk kiírva: <?php echo $row['cikk_createdate']; ?></span>
                            <span>Cikk módosítva: <?php echo $echo_modify_date; ?></span>
                        </div>
                        <div class="clear"></div>
                    </div><!-- end news_data -->
                    <div id="article_content">
                        <p><?php echo $row['cikk_content']; ?></p>
                    </div><!-- end news_content -->
                </div><!-- end alap -->
            </section>
            
            <footer>
                
                <p class="footer_text"><i class="fa fa-bullseye" style="font-size: 14px;"></i> Brittany Bod Fansite</p>
                <div class="clear"></div>
            </footer>
            
            <?php
            
        }
    } else {
        echo "Hiányos adat, a cikket nem lehet megjeleníteni!";
    }

?>
    


    
    
</article>