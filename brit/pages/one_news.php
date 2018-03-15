<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */

if (isset($_GET['newsid']) && !empty($_GET['newsid'])) {
    $news_id = $_GET['newsid'];
    $news = getOneNews($dbc, $news_id);
    if ($news['post_modify_date'] == $news['post_create_date']) {
        $echo_modify_date = '  --------------  ';
    } else {
        $echo_modify_date = $news['post_modify_date'];
    }
    //debug_r($news);
    $user = user_data($dbc, $news['post_author'], 'user_name');
}


?>
                <header>
                    <p class="page_title"><?php echo $news['post_title']; ?></p>
                </header>
                
                <section>
                    <div class="alap" style="">
                        <div id="news_data">
                            <div class="user_img_box">
                                <img src="img/nopicture.jpg"/>
                            </div>
                            <div class="ndatas">
                                <span>Hír szerzője: <a href="<?php echo $user['user_name']; ?>"><?php echo $user['user_name']; ?></a></span>
                                <span>Hír kiírva: <?php echo $news['post_create_date']; ?></span>
                                <span>Hír módosítva: <?php echo $echo_modify_date; ?></span>
                            </div>
                            <div class="nkw">
                                <p><?php echo $news['post_keywords']; ?></p>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div id="news_pictures">
                            <div>
                                <img src="<?php echo $news['post_image']; ?>"/>
                            </div>
                        </div>
                        <div id="news_content">
                            <p><?php echo $news['post_content']; ?></p>
                        </div>
                    </div><!-- end alap -->
                </section>
                
                <section class="nobck">
                    <p class="hd3 bd">Kommentáld...</p>
                    <?php include 'pages/comment.php'; ?>
                </section>
                <section class="nobck">
                    <?php
                    //debug_r($_POST);
                    $_POST[] = null; 
                    $getcomments = getCommentForNews($dbc, $news_id);
                    if ($getcomments != false) {
                        while ($row = $getcomments->fetch()) {
                            $user_name = user_data($dbc, $row['nwc_user'], 'user_name');
                            ?>
                           	<div class="view_comment">
                                <div class="head">
                                    <div class="comment_user"><a href="<?php echo $user_name['user_name']; ?>"><?php echo $user_name['user_name']; ?></a></div>
                                    <div class="comment_date"><?php echo $row['nwc_date']; ?></div>
                                    <div class="clear"></div>
                                </div>
                                <div class="comment_text">
                                    <?php echo $row['nwc_comment']; ?>
                                </div>
                            </div>
                            <?php
                            
                        }
                    } else {
                        echo '<p class="hd5 pt-center">Még senki nem kommentálta ezt a hírt!</p>';
                    }
                    
                    ?>
                </section>
                
                <footer>
                    
                    <p class="footer_text"><i class="fa fa-bullseye" style="font-size: 14px;"></i> Brittany Bod Fansite</p>
                    <p class="more_text"><a href="index.php?page=news">Vissza a hírekhez</a></p>
                    <div class="clear"></div>
                </footer>