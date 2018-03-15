<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */

function getCommentForNews($dbc, $news_id) {
    $news_id =(int)$news_id;
    
    try {
        $q = "SELECT * FROM `brit_news_comment` WHERE `nwc_news` = $news_id";
        if ($sql = $dbc->query($q)) {
            if($sql->rowCount() > 0) {
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
    
    //return $data;
}

function insertNewsComment($dbc, $comment_data) {
    array_walk($comment_data, 'array_sanitize');
    $return_error = array();
    try {
        $q = "INSERT INTO `brit_news_comment` (`nwc_news`, `nwc_user`, `nwc_date`, `nwc_comment`) 
        VALUE (:news_id, :user_id, :news_date, :news_comment)";
        //die ($q);
        if ($sql = $dbc->prepare($q)) {
            $sql->bindParam(':news_id', $comment_data['comment_news']);
            $sql->bindParam(':user_id', $comment_data['comment_user']);
            $sql->bindParam(':news_date', $comment_data['comment_date']);
            $sql->bindParam(':news_comment', $comment_data['comment_text']);
        }
        $return_error['sql_error'] = ($sql->execute()) ? true : false;
    } catch(PDOexception $e) {
        file_put_contents('PDOErrors.txt', $e->getMessage()."\n", FILE_APPEND);
        echo 'Adatbázis-kezelési hiba!<br />';
        echo $e->getMessage();
    }
    //debug_r($return_error); die();
    return $return_error;
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

function getOneNews($dbc, $id) {
    $id = (int)$id;
    try {
        $q = "SELECT * FROM `brit_post_2` WHERE `post_id` = :id";
        if ($sql = $dbc->prepare($q)) {
            $sql->bindParam(':id', $id);
            $sql->execute();
            if ($sql->rowCount() > 0) {
                $sql->setFetchMode(PDO::FETCH_ASSOC);
                return $sql->fetch();
            }
        }
    } catch(PDOexception $e) {
        file_put_contents('PDOErrors.txt', $e->getMessage()."\n", FILE_APPEND);
        echo 'Adatbázis-kezelési hiba!<br />';
        echo $e->getMessage();
    }
}

?>