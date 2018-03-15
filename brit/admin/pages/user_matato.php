<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */



?>
            <?php include "includes/widgets/asside.php"; ?>
            <article id="user_matato">
                <p class="page_title">
                    <strong>Ez itt  a User-matató.</strong>
                </p>
                <?php 
                $users = get_users($dbc);
                while ($row = $users->fetch()) {
                    //debug_r($row);
                    ?>
                    <form action="" method="post" enctype="multipart/form-data">
                        <div id="uid_<?php echo $row['user_id']; ?>" class="border_out">
                            <div class="user_picture">
                                <?php 
                                if (file_exists($row['user_profile'])) {
                                    $user_picture = $row['user_profile'];
                                } else {
                                    $user_picture = 'img/profile/no_profile.jpg';
                                }
                                ?>
                                <img src="<?php echo $user_picture; ?>"/>
                            </div>
                            <div class="user_data">
                                <div class="first_data">
                                    <span class="user_id">Id: <?php echo $row['user_id']; ?></span>
                                    <span class="user_name">
                                        Név: 
                                        <input type="text" id="username" name="username" value="<?php echo $row['user_name']; ?>" title="Felhasználó neve" style=""/>
                                    </span>
                                    <span class="user_rank">
                                        <?php
                                        $rank = $row['user_type'];
                                        $getranks = get_ranks($dbc);
                                        ?><select name="rank" size="1" style="width: 20%;"><?php
            								while ($row2 = $getranks->fetch()) {
            								    ?><option value="<?php echo $row2['rank_type']; ?>" <?php if ($row2['rank_type'] == $rank) {echo ' selected="selected"';} ?> >
                                                    <?php echo $row2['rank_name']; ?>
                                                </option><?php
            								}
                                        ?></select>
                                        <?php //$user_type = ($row['user_type'] == 0)? ?>
                                    </span>                               
                                </div><!-- end first_data -->
                                
                                <div class="secund_data">
                                     <div class="user_email">Email: <input type="text" id="useremail" name="useremail" value="<?php echo $row['user_email']; ?>" title="Email cím"/></div>
                                     <div class="user_firstname">Keresztnév: <?php echo $row['user_firstname']; ?></div>
                                     <div class="user_lastname">Vezetéknév: <?php echo $row['user_lastname']; ?></div>
                                </div><!-- end secund data -->
                                <!-- <div class="third_data">
                                    <span>Aktív: <input type="checkbox" id="active/not_banned" name="active/not_banned" <?php if ($row['user_active'] == 1) {echo 'checked="checked"';}?>/></span>
                                </div><!-- end third_data -->
                            </div><!-- end user_data -->
                            <div class="clear"></div>
                            
                        </div><!-- end uid_ -->
                    </form>
                    <?php
                }
                ?>
            </article>