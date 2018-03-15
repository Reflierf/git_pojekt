<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */

function modify_news($dbc, $news_data) {
    $return_error = array();
    //debug_r($news_data); die();
    $destination_path = 'img/news_uploaded/';
    if (isset($news_data['post_imgisset']) === true) {
        unlink('../'.$news_data['post_old_img']);
        $image_tmp = $news_data['post_imgtmp'];
        $image_name = $news_data['post_title_alias'].'.'.$news_data['post_imgext'];
        //$image_name = iconv('utf-8', 'windows-1250', $image_name); //die($image_name);
        $destination = $destination_path.$image_name;
        $return_error['upload_error'] =  (save_newspic($image_tmp, $destination_path, $image_name))? true : false;
    } else {
        $destination = $news_data['post_old_img'];
    }
    
    try {
         $q = "UPDATE `brit_post_2` SET `post_title`=:title, `post_title_alias`=:title_alias, `post_modify_date`=:mddate, `post_modify_author`=:mauthor, 
            `post_keywords`=:keywords, `post_content`=:content, `post_image`=:image, `post_visible`=:visible WHERE `post_id` = :id";
        if ($sql = $dbc->prepare($q)) {
            
            $sql->bindParam(':id', $news_data['post_id']);
            $sql->bindParam(':title', $news_data['post_title']);
            $sql->bindParam(':title_alias', $news_data['post_title_alias']);
            $sql->bindParam(':mddate', $news_data['post_modify_date']);
            $sql->bindParam(':mauthor', $news_data['post_modífy_author']);
            $sql->bindParam(':keywords', $news_data['post_keywords']);
            $sql->bindParam(':content', $news_data['post_content']);
            $sql->bindParam(':image', $destination);
            $sql->bindParam(':visible', $news_data['post_visible']);
                        
            $return_error['sql_error'] = ($sql->execute()) ? true : false;
        }
        
    } catch(PDOexception $e) {
        file_put_contents('PDOErrors.txt', $e->getMessage()."\n", FILE_APPEND);
        echo 'Adatbázis-kezelési hiba!<br />';
        echo $e->getMessage();
    }
    return $return_error;
}

function delete_news($dbc, $delete_news) {
    //debug_r($delete_news); die();
    $id = (int)$delete_news['delete_id'];
    
    try {
        $q = "DELETE FROM `brit_post_2` WHERE `post_id` = :id";
        if ($sql = $dbc->prepare($q)) {
            $sql->bindParam(':id', $id);
            $return_error['sql_error'] = ($sql->execute()) ? true : false;
            if (file_exists($delete_news['delete_img'])) {
                unlink('../'.$delete_news['delete_img']);
            }
        }
        
    } catch(PDOexception $e) {
        file_put_contents('PDOErrors.txt', $e->getMessage()."\n", FILE_APPEND);
        echo 'Adatbázis-kezelési hiba!<br />';
        echo $e->getMessage();
    }
    return $return_error;
}

function getOneNews($dbc, $id) {
    $id = (int)$id;
    try {
        $q = "SELECT * FROM `brit_post_2` WHERE `post_id` = :id";
        if ($sql = $dbc->prepare($q)) {
            $sql->bindParam(':id', $id);
            $sql->execute();
            if ($sql->rowCount() > 0) {
                $sql->setFetchMode(PDO::FETCH_ASSOC);
                return $sql;
            } else {
                return false;
            }
        }
    } catch(PDOexception $e) {
        file_put_contents('PDOErrors.txt', $e->getMessage()."\n", FILE_APPEND);
        echo 'Adatbázis-kezelési hiba!<br />';
        echo $e->getMessage();
    }
}

function getAllNews($dbc) {
    try {
        $q = "SELECT * FROM `brit_post_2`";
        if ($sql = $dbc->query($q)) {
            if ($sql->rowCount() > 0) {
                $sql->setFetchMode(PDO::FETCH_ASSOC);
                return $sql;
            } else {
                return false;
            }
        }
    } catch(PDOexception $e) {
        file_put_contents('PDOErrors.txt', $e->getMessage()."\n", FILE_APPEND);
        echo 'Adatbázis-kezelési hiba!<br />';
        echo $e->getMessage();
    }
}

function save_news($dbc, $news_data) {
    //debug_r($news_data); die();
    $return_error = array();
    $destination_path = 'img/news_uploaded/';
    if (isset($news_data['post_imgisset']) === true) {
        $image_tmp = $news_data['post_imgtmp'];
        $image_name = $news_data['post_title_alias'].'.'.$news_data['post_imgext'];
        //$image_name = iconv('utf-8', 'windows-1250', $image_name); //die($image_name);
        $destination = $destination_path.$image_name;
        $return_error['upload_error'] =  (save_newspic($image_tmp, $destination_path, $image_name))? true : false;
    } else {
        $destination = 'img/nopicture.jpg';
    }
    
    try {
        
         $q = "INSERT INTO `brit_post_2` (`post_title`, `post_title_alias`, `post_create_date`, `post_modify_date`, `post_author`, `post_modify_author`, `post_keywords`, `post_content`, 
            `post_image`, `post_visible`) VALUES (:title, :title_alias, :crdate, :mddate, :author, :mauthor, :keywords, :content, :image, :visible)";
            
            if ($sql = $dbc->prepare($q)) {
                
                $sql->bindParam(':title', $news_data['post_title']);
                $sql->bindParam(':title_alias', $news_data['post_title_alias']);
                $sql->bindParam(':crdate', $news_data['post_create_date']);
                $sql->bindParam(':mddate', $news_data['post_modify_date']);
                $sql->bindParam(':author', $news_data['post_author']);
                $sql->bindParam(':mauthor', $news_data['post_modify_author']);
                $sql->bindParam(':keywords', $news_data['post_keywords']);
                $sql->bindParam(':content', $news_data['post_content']);
                $sql->bindParam(':image', $destination);
                $sql->bindParam(':visible', $news_data['post_visible']);
                
                $return_error['sql_error'] = ($sql->execute()) ? true : false;
            }
            
    } catch(PDOexception $e) {
        file_put_contents('PDOErrors.txt', $e->getMessage()."\n", FILE_APPEND);
        echo 'Adatbázis-kezelési hiba!<br />';
        echo $e->getMessage();
    }
    //debug_r($return_error); 
    //die();
    return $return_error;
}

?>