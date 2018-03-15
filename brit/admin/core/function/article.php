<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */

function deleteArticle($dbc, $article_id) {
    $article_id = (int)$article_id;
    try {
        $q = "DELETE FROM `brit_articles_2` WHERE `cikk_id` = :id";
        if ($sql = $dbc->prepare($q)) {
            $sql->bindParam(':id', $article_id);
            $return_error['sql_error'] = ($sql->execute()) ? true : false;
        }
    } catch(PDOexception $e) {
        file_put_contents('PDOErrors.txt', $e->getMessage()."\n", FILE_APPEND);
        echo 'Adatbázis-kezelési hiba!<br />';
        echo $e->getMessage();
    }
    return $return_error;
}

function getArticle($dbc, $aid) {
    $aid = (int)$aid;
    try {
         $q = "SELECT * FROM `brit_articles_2` WHERE `cikk_id` = $aid";
        if ($sql = $dbc->query($q)) {
            if ($sql->rowCount() > 0) {
                $sql->setFetchMode(PDO::FETCH_ASSOC);
                //$data = $sql->fetch();
                //debug_r($data); die();
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

function modifyArticle($dbc, $article_data) {
    //echo "modify";
    //debug_r($article_data); die();
    $return_error = array();
    try {
        
         $q = "UPDATE `brit_articles_2` SET `cikk_title`=:title,`cikk_titlealias`=:title_alias,`cikk_createdate`=:crdate,`cikk_modifydate`=:mddate,
            `cikk_author`=:author,`cikk_modifyauthor`=:mdauthor,`cikk_content`=:content,`cikk_visible`=:visible WHERE`cikk_id` = :user_id";
            
            if ($sql = $dbc->prepare($q)) {
                $sql->bindParam(':title', $article_data['cikk_title']);
                $sql->bindParam(':title_alias', $article_data['cikk_titlealias']);
                $sql->bindParam(':crdate', $article_data['cikk_createdate']);
                $sql->bindParam(':mddate', $article_data['cikk_modifydate']);
                $sql->bindParam(':author', $article_data['cikk_author']);
                $sql->bindParam(':mdauthor', $article_data['cikk_modifyauthor']);
                $sql->bindParam(':content', $article_data['cikk_content']);
                $sql->bindParam(':visible', $article_data['cikk_visible']);
                $sql->bindParam(':user_id', $article_data['cikk_id']);
                
                $return_error['sql_error'] = ($sql->execute()) ? true : false;
                //debug_r($return_error); die();
            }
            
    } catch(PDOexception $e) {
        file_put_contents('PDOErrors.txt', $e->getMessage()."\n", FILE_APPEND);
        echo 'Adatbázis-kezelési hiba!<br />';
        echo $e->getMessage();
    }
}

function saveArticle($dbc, $article_data) {
    //debug_r($article_data); die();
    $return_error = array();
    try {
        
         $q = "INSERT INTO `brit_articles_2` (`cikk_title`, `cikk_titlealias`, `cikk_createdate`, `cikk_modifydate`, `cikk_author`, `cikk_content`, 
         `cikk_visible`) VALUES (:title, :title_alias, :crdate, :mddate, :author, :content, :visible)";
            
            if ($sql = $dbc->prepare($q)) {
                
                $sql->bindParam(':title', $article_data['cikk_title']);
                $sql->bindParam(':title_alias', $article_data['cikk_titlealias']);
                $sql->bindParam(':crdate', $article_data['cikk_createdate']);
                $sql->bindParam(':mddate', $article_data['cikk_modifydate']);
                $sql->bindParam(':author', $article_data['cikk_author']);
                
                $sql->bindParam(':content', $article_data['cikk_content']);
               
                $sql->bindParam(':visible', $article_data['cikk_visible']);
                
                $return_error['sql_error'] = ($sql->execute()) ? true : false;
                //debug_r($return_error); die();
            }
            
    } catch(PDOexception $e) {
        file_put_contents('PDOErrors.txt', $e->getMessage()."\n", FILE_APPEND);
        echo 'Adatbázis-kezelési hiba!<br />';
        echo $e->getMessage();
    }
}

function getAllArticles($dbc) {
    try {
        $q = "SELECT * FROM `brit_articles_2`";
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

?>