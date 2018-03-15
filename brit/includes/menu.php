<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */


$menu = array();
$q = "SELECT * FROM `brit_menu` ORDER BY `menu_sort` ASC";
if ($sql = $dbc->query($q)) {
    if ($sql->rowCount() > 0) {
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        
        while (($row =  $sql->fetch()) != false) {
            //print_r($row); echo '<br />';
            if ($row['menu_admin'] != 1) {
                $menu[] = array(
                'id' => $row['menu_id'],
                'title' => $row['menu_title'],
                'anchor' => $row['menu_anchor'],
                'parent_id' => $row['menu_parent_id'],
                'admin' => $row['menu_admin']
                );
            } else {
                if (logged_in() && $user_data['user_type'] == 1) {
                    $menu[] = array(
                    'id' => $row['menu_id'],
                    'title' => $row['menu_title'],
                    'anchor' => $row['menu_anchor'],
                    'parent_id' => $row['menu_parent_id'],
                    'admin' => $row['menu_admin']
                    );
                }
            }
            //debug_r($row); echo '<br />';
        }
    }
}
//debug_r($menu);
?>

                <nav class="my_panel">
                    <div class="navbar">
                        <div class="menu-toggle"><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></div><!-- end menu-toggle -->
                        <div class="menu_nav">
                            <?php echo build_menu($menu); ?>
                        </div>
                    </div>
                </nav>