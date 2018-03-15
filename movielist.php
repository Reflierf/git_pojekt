<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */



if (isset($_POST['btnEdit'])) {
    //debug_r($_POST);
    header("Location: index.php?page=edrec&id='$_POST[movie_id]'");
    exit();
}

if (isset($_POST['btnDelete'])) {
    //debug_r($_POST);
    header("Location: index.php?page=delmovie&id='$_POST[movie_id]'");
    exit();
}

if (isset($_POST['btnsearch'])) {
    //debug_r($_POST); die();
    if (empty($_POST['mtitle']) && $_POST['mgenre'] == 0 && $_POST['mtype'] == 0 && $_POST['mpack'] == 0 && $_POST['mmedia'] == 0) {
        $errors[] = 'A szűréshez legaláb egy feltétel szükséges!';
    }
    
    $is_find = false;
    
    if (empty($errors)) {
        $q = "SELECT * FROM `movie` WHERE ";
        
        if (!empty($_POST['mtitle'])) {
            if ($is_find == true) {
                $q = $q."AND ";
            }
            $q = $q."`movie_title` LIKE '%$_POST[mtitle]%' ";
            $is_find = true;
        }
        
        if (!empty($_POST['mgenre'])) {
            if ($is_find == true) {
                $q = $q."AND ";
            }
            $q = $q."`movie_genre` = $_POST[mgenre] ";
            $is_find = true;
        }
        
        if (!empty($_POST['mtype'])) {
            if ($is_find == true) {
                $q = $q."AND ";
            }
            $q = $q."`movie_type` = $_POST[mtype] ";
            $is_find = true;
        }
        
        if (!empty($_POST['mpack'])) {
            if ($is_find == true) {
                $q = $q."AND ";
            }
            $q = $q."`movie_pack` = $_POST[mpack] ";
            $is_find = true;
        }
        
        if (!empty($_POST['mmedia'])) {
            if ($is_find == true) {
                $q = $q."AND ";
            }
            $q = $q."`movie_media` = $_POST[mmedia] ";
            $is_find = true;
        }
        
        //echo $q; die();
        //header("Location: index.php?page=searching&title='$_POST[mtitle]'&genre=$_POST[mgenre]&type=$_POST[mtype]&pack=$_POST[mpack]&media=$_POST[mmedia]");
        header("Location: index.php?page=searching&query=".$q);
        exit();
    }
    
}

if (isset($_GET['order'])) {
    //debug_r($_GET);
    /**/if (isset($_GET['order'])) {
        $order = $_GET['order'];
        switch ($order) {
            case 'ASC':
                $neworder = 'DESC';
                break;
            case 'DESC':
                $neworder = 'ASC';
                break;
        } 
    }
}else {
    $order = 'ASC';
    $neworder = 'DESC';
}
if (isset($_GET['sorting'])) {
    //debug_r($_GET);
    $sorting = $_GET['sorting'];
} else {
    $sorting = 'movie_title';
}
/**/if (isset($_GET['pnum'])) {
    //debug_r($_GET['pnum']);
}

$limit = 10; //max ennyi sort adunjk vissza
$q = "SELECT COUNT(`movie_id`) FROM `movie`";
$result = getPDOquery($dbc, $q, PDO::FETCH_NUM);
$c = $result->fetch(); //$c[0]] adja vissza az összes sort a `movie` táblában

$maxpage = ceil($c[0]/$limit); //debug_r($maxpage);összesen ennyi sór van az adattáblában
$pnum = isset($_GET['pnum']) ? abs((int)$_GET['pnum']) : 1;		//Ha a fejlécben jött 'page' adat, akkor erre az oldalra ugrunk, ellenkező esetben az első oldalra

$q = "SELECT `movie`.`movie_id`, `movie`.`movie_title`, `movie`.`movie_note`, `genre`.`gitem`, `type`.`titem`, 
    `pack`.`pitem`, `media`.`mitem` FROM `movie`, `genre`, `type`, `pack`, `media` 
    WHERE `movie`.`movie_genre` = `genre`.`gid` AND `movie`.`movie_type` = `type`.`tid` 
    AND `movie`.`movie_pack` = `pack`.`pid` AND `movie`.`movie_media` = `media`.`mid` ORDER BY $sorting $order";
$result = getPDOquery($dbc, $q, PDO::FETCH_ASSOC);
/*while ($row = $result->fetch()) {
    debug_r($row);
}*/
?>

<div class="movielist">
    <div class="title">
        <p>Filmek listája</p>
        <div class="post_error pt-center">
            <?php
            if (!empty($errors)) {
                echo '<pre>';
                echo output_errors($errors);
                echo '</pre>';
            }
            ?>
        </div>
    </div><!-- end title -->
    <div class="table">
        <div class="header">
            <!-- <span class="sid"><input type="button" class="btnhead" onclick="jump('id', 'ASC', '2', '1')" value="id"/></span> -->
            <span class="sid"><input type="button" class="btnhead" onclick="jump('movie_id', '<?php echo $neworder; ?>', <?php echo $pnum; ?>)" value="id"/></span>
            <span class="stitle"><input type="button" class="btnhead" onclick="jump('movie_title', '<?php echo $neworder; ?>', <?php echo $pnum; ?>)" value="Cím"/></span>
            <span class="sgenre"><input type="button" class="btnhead" onclick="jump('gitem', '<?php echo $neworder; ?>', <?php echo $pnum; ?>)" value="Műfaj"/></span>
            <span class="stype"><input type="button" class="btnhead" onclick="jump('titem', '<?php echo $neworder; ?>', <?php echo $pnum; ?>)" value="Megjelenés"/></span>
            <span class="spack"><input type="button" class="btnhead" onclick="jump('pitem', '<?php echo $neworder; ?>', <?php echo $pnum; ?>)" value="Kiszerelés"/></span>
            <span class="smedia"><input type="button" class="btnhead" onclick="jump('mitem', '<?php echo $neworder; ?>', <?php echo $pnum; ?>)" value="Média"/></span>
            <span class="snote"><input type="button" class="btnhead" value="Megjegyzés"/></span>
            <span><button type="submit" name="btnSearch" class="show_hidden_panel"><i class="fa fa-search" aria-hidden="true"></i></button></span>
            <span class="editors">Műveletek</span>
        </div><!-- end header -->
        <div class="body">
        
        <?php include "pages/searchblock.php"; ?>
            
            <?php
            $delete_message = 'Valóban törlöd a filmet a listából?';
            while ($row = $result->fetch()) {
                //debug_r($row);
                ?>
                <form action="" method="post">
                    <input type="hidden" name="movie_id" value="<?php echo $row['movie_id']; ?>"/>
                    <div class="rows">
                        <a href="index.php?page=edrec&id=<?php echo $row['movie_id']; ?>">
                            <div class="row_data">
                                <!--  --><span class="sid"><?php echo $row['movie_id']; ?></span>
                                <span class="stitle"><?php echo $row['movie_title']; ?></span>
                                <span class="sgenre"><?php echo $row['gitem']; ?></span>
                                <span class="stype"><?php echo $row['titem']; ?></span>
                                <span class="spack"><?php echo $row['pitem']; ?></span>
                                <span class="smedia"><?php echo $row['mitem']; ?></span>
                                <span class="snote"><?php echo $row['movie_note']; ?></span>
                                <span class="editors">
                                    <div class="btneditors">
                                        <span><button type="submit" name="btnEdit"><i class="fa fa-pencil" aria-hidden="true"></i></button></span>
                                        <span><button type="submit" name="btnDelete" onclick="return confirm('<?php echo $delete_message; ?>')"><i class="fa fa-trash-o" aria-hidden="true"></i></button></span>
                                        <!-- <span class="edit gomb"><i class="fa fa-pencil" aria-hidden="true"></i></span>
                                        <span class="delete gomb"><i class="fa fa-trash-o" aria-hidden="true"></i></span> -->
                                    </div>
                                </span>
                            </div><!-- end row_data -->   
                        </a>
                    </div><!-- end rows -->
                </form>
                <?php
            }
            ?>
            
            
        </div><!-- end body -->
    </div><!-- end table -->
</div><!-- end movielist -->