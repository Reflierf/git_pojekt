<?php

function get_ranks($dbc) {
	$q = "SELECT * FROM `brit_rank`";
	if ($sql = $dbc->query($q)) {
    	if ($sql->rowCount() > 0) {
    		$sql->setFetchMode(PDO::FETCH_ASSOC);
    		$data = $sql;
    	}
    }
    return $data;
}

function get_users($dbc) {
    try {
        $q = "SELECT * FROM `brit_user`";
        if ($sql = $dbc->prepare($q)) {
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

function user_data($dbc, $id) {
    $data = array();
    $id = (int)$id;
    $func_num_args = func_num_args();
    $func_get_args = func_get_args();
    if ($func_num_args > 2) {
        unset($func_get_args[0], $func_get_args[1]); //if (isset($func_get_args)) {debug_r($func_get_args);}
    }
    $fields = '`'.implode('`, `', $func_get_args).'`'; //if (isset($func_num_args)) {debug_r($func_num_args);}
    try {
        $q = "SELECT $fields FROM `brit_user` WHERE `user_id` = :user_id";
        if ($sql = $dbc->prepare($q)) {
            $sql->bindParam(':user_id', $id);
            $sql->execute();
            if ($sql->rowCount() > 0) {
                $sql->setFetchMode(PDO::FETCH_ASSOC);
                $data = $sql->fetch();
            }
        }
        return $data;
    } catch(PDOexception $e) {
        file_put_contents('PDOErrors.txt', $e->getMessage()."\n", FILE_APPEND);
        echo 'Adatbázis-kezelési hiba!<br />';
        echo $e->getMessage();
    }
}

function user_id_from_email($dbc, $emal) {
    $email=sanitize($email);
    try {
        $q = "SELECT `user_id` FROM `brit_user` WHERE `user_email` = :email ";
        if ($sql = $dbc->prepare($q)) {
            $sql->bindParam(':email', $emal);
            $sql->execute();
            if ($sql->rowCount() > 0) {
                $sql->setFetchMode(PDO::FETCH_NUM);
                $data = $sql->fetch();
            }
        }
        return $data[0];
    } catch(PDOexception $e) {
        file_put_contents('PDOErrors.txt', $e->getMessage()."\n", FILE_APPEND);
        echo 'Adatbázis-kezelési hiba!<br />';
        echo $e->getMessage();
    }
}

function login_admin($dbc, $password) {
    //Valamikor kellett az email cím is. Ha megint kell, csak meg kell adni a függvény paramétereiben.
    //$admin_id = user_id_from_email($dbc, $email);
    //$email = sanitize($email);
    $password = sha1($password);
    try {
        //$q = "SELECT `user_type` FROM `brit_user` WHERE `user_email` = :email AND `user_admin_password` = '$password'";
        $q = "SELECT `user_id` FROM `brit_user` WHERE `user_admin_password` = :password AND `user_type` = 1";
        if ($sql = $dbc->prepare($q)) {
            //$sql->bindParam(':email', $email);
            $sql->bindParam(':password', $password);
            $sql->execute();
            if ($sql->rowCount() > 0) {
                $sql->setFetchMode(PDO::FETCH_NUM);
        		$data = $sql->fetch();
            }
            
        }
        /*if (isset($data[0]) && $data[0] == 1)  {
            print_r($data);
            //return true;
        } else {
            echo "0";
            //return false;
        } die();*/
        return (isset($data[0])&& !empty($data[0])) ? $data[0] : false;
    } catch(PDOexception $e) {
        file_put_contents('PDOErrors.txt', $e->getMessage()."\n", FILE_APPEND);
        echo 'Adatbázis-kezelési hiba!<br />';
        echo $e->getMessage();
    }
}

function equal_password($dbc, $password, $email) {
    $password = sha1($password);
    try {
        $q = "SELECT `user_password`, `user_admin_password` FROM `brit_user` WHERE `user_email` = :email";
        if ($sql = $dbc->prepare($q)) {
            $sql->bindParam(':email', $email);
            $sql->execute();
            if ($sql->rowCount() > 0) {
                $sql->setFetchMode(PDO::FETCH_ASSOC);
        		$data = $sql->fetch();
            }
        }
        //print_r($data);
        if (($data['user_password'] == $password) || $data['user_admin_password'] != $password) {
            return false;
        } else {
            return true;
        }
    } catch(PDOexception $e) {
        file_put_contents('PDOErrors.txt', $e->getMessage()."\n", FILE_APPEND);
        echo 'Adatbázis-kezelési hiba!<br />';
        echo $e->getMessage();
    }
}

function email_exists($dbc, $email) {
    $email = sanitize($email);
    try {
        $q = "SELECT COUNT(`user_id`) FROM `brit_user` WHERE `user_email` = :email";
        if ($sql = $dbc->prepare($q)) {
            $sql->bindParam(':email', $email);
            $sql->execute();
            if ($sql->rowCount() > 0) {
                $sql->setFetchMode(PDO::FETCH_NUM);
        		$data = $sql->fetch();
            }
        }
        return ($data[0] == 1)? true : false;
    } catch(PDOexception $e) {
        file_put_contents('PDOErrors.txt', $e->getMessage()."\n", FILE_APPEND);
        echo 'Adatbázis-kezelési hiba!<br />';
        echo $e->getMessage();
    }
}

function logged_in() {
    return (isset($_SESSION['admin_id'])) ? true : false;
}

?>