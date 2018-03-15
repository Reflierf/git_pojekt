<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */

?>
        <header>
            <p class="page_title">Elküldött leveleim</p>
        </header>
        
        <section>
        <?php 
        $sentmail = get_sentmail_email($dbc, $session_user_id);
        if ($sentmail !== false) {
            while ($row = $sentmail->fetch()) {
                //debug_r($row);
                ?>
                
               	<div class="letter_box">
                    <div class="letter_data">
                        <span class="sender">Címzett: <?php echo $row['user_name']; ?></span>
                        <span class="date">Küldted: <?php echo date('Y/m/d H:t', strtotime($row['send_email_date'])); ?></span>
                    </div>
                    <div class="clear"></div>
                    <hr />
                    <div class="letter_subject"><?php echo $row['send_email_subject']; ?></div>
                    
                    <div style="float: left;">
                        <span class="btncontroll">
                            <button id="vez<?php echo $row['send_email_id']; ?>" class="button buttonFlex show_hidden_panel" type="submit" name="viewmessage" title="megjelenítés">
                                <i class="fa fa-arrow-down" aria-hidden="true"></i>
                            </button>
                        </span>
                    </div>
                    
                    <form action="index.php?page=email_operation" method="post">
                        <input type="hidden" id="id" name="id" value="<?php echo $row['send_email_id']; ?>"/>
                        <input type="hidden" id="sender" name="sender" value="sentmail"/>
                        <div style="float: right;">
                            <span class="btncontroll">
                                <button class="button buttonFlex" type="submit" id="operation" name="operation" title="válasz" value="ansver">
                                    <i class="fa fa-reply" aria-hidden="true"></i>
                                </button>
                            </span>
                            <span class="btncontroll">
                                <button class="button buttonFlex" type="submit" id="operation" name="operation" title="továbbítás" value="forward">
                                    <i class="fa fa-forward" aria-hidden="true"></i>
                                </button>
                            </span>
                            <span class="btncontroll">
                                <button class="button buttonFlex" type="submit" id="operation" name="operation" title="nyomtatás" value="print">
                                    <i class="fa fa-print" aria-hidden="true"></i>
                                </button>
                            </span>
                            <span class="btncontroll">
                                <button class="button buttonFlex" type="submit" id="operation" name="operation" title="törlés" value="delete">
                                    <i class="fa fa-trash-o" aria-hidden="true"></i>
                                </button>
                            </span>
                        </div>
                    </form>
                    
                    <div class="clear"></div>
                    <div id="anim_vez<?php echo $row['send_email_id']; ?>" class="letter_message animate_td hide_form" style="display: none;"><?php echo $row['send_email_message']; ?></div>
                    
                </div>
                
                <?php
            }
        } else {
            echo "<p class='pt-center'>Még senkinek sem írtál levelet.</p>";
        }
        ?>
        </section>
        
        <footer>
            <p class="footer_text">&copy; 2017, Relierf</p>
            <div class="clear"></div>
        </footer>
        