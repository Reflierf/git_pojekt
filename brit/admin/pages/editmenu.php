<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */

//admin_protect($dbc);
protect_page();

if (isset($_POST) && !empty($_POST)) {
    //print_r($_POST); die();
    if (isset($_POST['ok'])) {
        $required_fields = array('title', 'anchor');
        foreach ($_POST as $key=>$value) {
            if (empty($value) && in_array($key, $required_fields) === true) {
                //print_r($key); print_r($value);
                $errors[] = 'Minden mező kitöltése kötelező!';
                break(1);
            }
            if ($_POST['parent'] == '') {
                $errors[] = 'Minden mező kitöltése kötelező!';
                break(1);
            }
        }
        if (update_menu($dbc, $_POST)) {
            header("Location: index.php?page=editmenu");
            exit();
        } else {
            $errors[] = 'Szar van a palacsintában!';
        }
    }
    
    if (isset($_POST['del'])) {
        //print_r($_POST);
        if (dell_menu($dbc, $_POST['id'])) {
            header("Location: index.php?page=editmenu");
            exit();
        } else {
            $errors[] = 'Szar van a palacsintában!';
        }
    }
    
    if (isset($_POST['new_menu'])) {
        //print_r($_POST);
        $required_fields = array('new_menu_name', 'new_menu_anchor');
        foreach ($_POST as $key=>$value) {
            if (empty($value) && in_array($key, $required_fields) === true) {
                //print_r($key); print_r($value);
                $errors[] = 'Minden mező kitöltése kötelező!';
                break(1);
            }
        }
        
        if (empty($errors) === true) {
            if (add_menu($dbc, $_POST)) {
                header("Location: index.php?page=editmenu");
                exit();
            } else {
                $errors[] = 'Szar van a palacsintában!';
            }
        }
    }
}

if (!empty($errors) === true) {
    print_r($errors);
}

?><div id="menuedit" style="display: block; margin: 0 auto;"><?php
$i = 0;
$q = "SELECT * FROM `brit_menu`";
if ($sql = $dbc->query($q)) {
    if ($sql->rowCount() > 0) {
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        
        while (($row =  $sql->fetch()) != false) {
            $has_parent_menu = (isset($row['menu_parent_id']))? $row['menu_parent_id'] : 'none';
            //echo 'Id: '.$row['menu_id'].' -> title: '.$row['menu_title'].' -> adress: '.$row['menu_anchor'].' -> has parent: '.$has_parent_menu.'<br />';
            ?>
            <form action="" method="post">
                <input type="hidden" id="id" name="id" value="<?php echo $row['menu_id']; ?>"/>
                <span style="width: 4%; display: inline-block;"><p style="font-weight: bold;"><?php echo $row['menu_id']; ?></p></span>
                <span><input type="text" id="title" name="title" style="width: 30%;" value="<?php echo $row['menu_title']; ?>"/></span>
                <span><input type="text" id="anchor" name="anchor" style="width: 40%;" value="<?php echo $row['menu_anchor']; ?>"/></span>
                <span><input type="text" id="parent" name="parent" style="width: 5%;" value="<?php echo $has_parent_menu; ?>"/></span>
                <span><input type="text" id="sort" name="sort" style="width: 5%;" value="<?php echo $row['menu_sort']; ?>"/></span>
                <span><input type="text" id="admin" name="admin" style="width: 5%;" value="<?php echo $row['menu_admin']; ?>"/></span>
                <span><button class="button" type="submit" id="ok" name="ok" style="display: inline;"><i class="fa fa-check" aria-hidden="true"></i></button></span>
                <span><button class="button" type="submit" id="del" name="del" style="display: inline; width: 25px;"><i class="fa fa-trash-o" aria-hidden="true"></i></button><br /></span>
            </form>
            <?php
            $i++;
        }
    }
}

?>
<br />
<form action="" method="post">
    <span><input type="text" id="new_menu_name" name="new_menu_name" placeholder="új menüpont neve..."/></span>
    <span><input type="text" id="new_menu_anchor" name="new_menu_anchor" placeholder="új menüpont célja..." value="#"/></span>
    <span><label for="select2">Melyik menü almenüje?</label>
    <select name="menu_parent" size="1">
        <option value="0" selected="">0</option>
        <?php
        $q = "SELECT `menu_id` FROM `brit_menu` ORDER BY `menu_id` ASC";
        if ($sql = $dbc->query($q)) {
            if ($sql->rowCount() > 0) {
                $sql->setFetchMode(PDO::FETCH_NUM);
                while (($row =  $sql->fetch()) != false) {
                    echo'<option value="'.$row[0].'">'.$row[0].'</option>';
                }
            }
        } 
        ?>
   </select>
   </span>
    <span><button class="button buttonLong" type="submit" id="new_menu" name="new_menu" style="display: inline;"><i class="fa fa-external-link-square" aria-hidden="true"></i> Hozzáadás</button></span>
</form>
</div>

<?php
//echo 'i eredménye: '.$i;
?>