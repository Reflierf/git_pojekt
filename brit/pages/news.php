<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */



?>

                <header>
                    <p class="page_title">Hírek</p>
                </header>
                
                <section>
                    
                    <?php
                    
                    $allnews = getAllNews($dbc);
                    if ($allnews != false) {
                        while ($row = $allnews->fetch()) {
                            if ($row['post_visible'] == 1) {
                                $news_post_user = user_data($dbc, $row['post_author'], 'user_name');
                                ?>
                                <div class="news_box">
                                    <div class="news_data">
                                        <ul>
                                            <!-- <li>Hír sorszáma: <?php //echo $row['post_id']; ?></li>
                                            <li>Láthatóság: <?php //echo $row['post_visible']; ?></li>
                                            <li>Fontos: <?php //echo $row['post_important']; ?></li> -->
                                            
                                            <div class="tsec"> <!-- Fejléc a hír címével, a kulcsszavakkal, szerzővel és létrehozás dátumával -->
                                                <li class="title"><a href="index.php?page=one_news&newsid=<?php echo $row['post_id']; ?>"><?php echo $row['post_title']; ?></a></li>
                                                <li class="kw"><?php echo $row['post_keywords']; ?></li>
                                                <li class="ndate">
                                                    <span style="margin-right: 10px;">Hír szerzője: <a href="<?php echo $news_post_user['user_name']; ?>"><?php echo $news_post_user['user_name']; ?></a></span>
                                                    <span>Hír létrehozva: <?php echo $row['post_create_date']; ?></span>
                                                    <?php 
                                                    if ($row['post_create_date'] !== $row['post_modify_date']) {
                                                        ?><span>Hír modosítva: <?php echo $row['post_modify_date']; ?></span><?php
                                                    }
                                                    ?>
                                                </li>
                                            </div><!-- end tsec -->
                                            
                                            <li class="ncontent"> <!-- Innét megy a hír tartalma. Ha hosszabb 200 karakternél, elvágva az utolsó szóköznél. -->
                                                <?php
                                                if (strlen($row['post_content']) > 200) {
                                                    $prew_content = substr($row['post_content'], 0, 200);
                                                    $last_space = strrpos($prew_content, ' ');
                                                    $prew_content = substr($row['post_content'], 0, $last_space);
                                                    echo $prew_content.'...';
                                                } else {
                                                    echo $row['post_content'];
                                                }
                                                
                                                ?>
                                            </li>
                                            
                                            <li><a href="index.php?page=one_news&newsid=<?php echo $row['post_id']; ?>"><i>olvasd tovább...</i></a></li>
                                        </ul>
                                    </div><!-- end news_data -->
                                    <div class="news_pic">
                                        <img src="<?php echo $row['post_image']; ?>" style=""/>
                                    </div><!-- end news_pic -->
                                    <div class="clear"></div>
                                </div><!-- end news_box -->
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