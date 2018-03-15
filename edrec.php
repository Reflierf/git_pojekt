<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */
if (isset($_GET['id']) && !empty($_GET['id'])) {
    //debug_r($_GET);
    $id = $_GET['id'];
    $q = "SELECT * FROM `movie` WHERE `movie_id` = $id";
    $result = getPDOquery($dbc, $q, PDO::FETCH_ASSOC);
}

if (isset($_POST['saveup'])) {
    //debug_r($_POST);
    $title = $_POST['title'];
    $genre = (empty($_POST['new_genre'])) ? (int)$_POST['genre'] : $_POST['new_genre'];
    $type =  (empty($_POST['new_type']))  ? (int)$_POST['type'] :  $_POST['new_type'];
    $media = (empty($_POST['new_media'])) ? (int)$_POST['media'] : $_POST['new_media'];
    $pack =  (empty($_POST['new_pack']))  ? (int)$_POST['pack'] :  $_POST['new_pack'];
    $note = $_POST['note'];
    $links = $_POST['links'];
    $criticism = $_POST['criticism'];
    if (empty($title) || empty($genre) || empty($type) || empty($media) || empty($pack)) {
        $errors[] = "A csillagokkal jelölt mezőket ki kell tölteni!";
    }
}



?>
            <div class="post_error pt-center">
                <?php
                if (!empty($errors) === true) {
                    echo '<pre>';
                    echo output_errors($errors);
                    echo '</pre>';
                }
                ?><!-- end post_error -->
            </div>
            
            <?php 
            if (isset($_POST['saveup']) && empty($errors)) {
                $title_alias = conv_ekezet($title);
                $movie_data = array(
                    'movie_id'          => $id,
                    'movie_title'       => $title,
                    'movie_title_alias' => $title_alias,
                    'movie_genre'       => $genre,
                    'movie_type'        => $type,
                    'movie_pack'        => $pack,
                    'movie_media'       => $media,
                    'movie_note'        => $note,
                    'movie_links'       => $links,
                    'movie_criticism'     => $criticism
                );
                //debug_r($movie_data);
                $result = updateMovie($dbc, $movie_data);
                if ($result != false) {
                    header('Location: index.php?page=movielist');
                    exit();
                } else {
                    ?> <script>alert("Nem történt semmi.")</script> <?php
                }
            }
            ?>
            <?php 
            $movie = $result->fetch();
            //debug_r($movie);
            ?>
             
            <form action="" method="post">
                <div class="input_panel" style="">
                    <ul>
                        <li>
                            <p class="fejlec" style="">Rekord szerkesztése</p>
                        </li>
                        <li>
                            <label class="cimke" for="title">Cím:*</label>
                            <input type="text" id="title" name="title" value="<?php echo $movie['movie_title']; ?>" placeholder="cím..."/>
                        </li>
                        <li>
                            <label class="cimke" for="genre">Műfaj:*</label>
                            <select id="genre" name="genre">
                                <?php 
                                $list = getPDOquery($dbc, "SELECT * FROM `genre`", PDO::FETCH_ASSOC);
                                while ($row = $list->fetch()) {
                                    //debug_r($row);
                                    ?>
                                   	<option value="<?php echo $row['gid']; ?>" <?php echo ($movie['movie_genre'] == $row['gid'])? 'selected=""' : ''; ?>><?php echo $row['gitem']; ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                            <input class="short_text" type="text" name="new_genre" placeholder="új műfaj felvétele..."/>
                        </li>
                        <li>
                            <label class="cimke" for="type">Megjelenés:*</label>
                            <select id="type" name="type">
                                <?php 
                                $list = getPDOquery($dbc, "SELECT * FROM `type`", PDO::FETCH_ASSOC);
                                while ($row = $list->fetch()) {
                                    //debug_r($row);
                                    ?>
                                   	<option value="<?php echo $row['tid']; ?>" <?php echo ($movie['movie_type'] == $row['tid'])? 'selected=""' : ''; ?>><?php echo $row['titem']; ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                            <input class="short_text" type="text" name="new_type" placeholder="új megjelenés felvétele..."/>
                        </li>
                        <li>
                            <label class="cimke" for="pack">Kiszerelés:*</label>
                            <select id="pack" name="pack">
                                <?php 
                                $list = getPDOquery($dbc, "SELECT * FROM `pack`", PDO::FETCH_ASSOC);
                                while ($row = $list->fetch()) {
                                    //debug_r($row);
                                    ?>
                                   	<option value="<?php echo $row['pid']; ?>" <?php echo ($movie['movie_pack'] == $row['pid'])? 'selected=""' : ''; ?>><?php echo $row['pitem']; ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                            <input class="short_text" type="text" name="new_pack" placeholder="új tároló felvétele..."/>
                        </li>
                        <li>
                            <label class="cimke" for="media">Média:*</label>
                            <select id="media" name="media">
                                <?php 
                                $list = getPDOquery($dbc, "SELECT * FROM `media`", PDO::FETCH_ASSOC);
                                while ($row = $list->fetch()) {
                                    //debug_r($row);
                                    ?>
                                   	<option value="<?php echo $row['mid']; ?>" <?php echo ($movie['movie_media'] == $row['mid'])? 'selected=""' : ''; ?>><?php echo $row['mitem']; ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                            <input class="short_text" type="text" name="new_media" placeholder="új adathordozó felvétele..."/>
                        </li>
                       
                        <li>
                            <label class="cimke" for="note" style="vertical-align: top;">Megjegyzés</label>
                            <textarea class="text_body" name="note" placeholder="írj megjegyzést..."><?php echo $movie['movie_note']; ?></textarea>
                        </li>
                        <li>
                            <label class="cimke" for="links" style="vertical-align: top;">Linkek</label>
                            <textarea class="text_body short_area" name="links" placeholder="linkek..."></textarea>
                        </li>
                        <li>
                            <label class="cimke" for="criticism" style="vertical-align: top;">Kritikák</label>
                            <textarea class="text_body short_area" name="criticism" placeholder="kritikák..."></textarea>
                        </li>
                        <br />
                        <li>
                            <button type="submit" class="button buttonLong" id="saveup" name="saveup">Módosít</button>
                            <button class="button buttonLong" type="submit" id="ok" name="ok" style="display: inline;">Valami</button>
                        </li>
                    </ul>
                </div>
                
            </form>