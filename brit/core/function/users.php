<?php

function get_ip() {
	//Just get the headers if we can or else use the SERVER global
	if ( function_exists( 'apache_request_headers' ) ) {
		$headers = apache_request_headers();
	} else {
		$headers = $_SERVER;
	}
	//Get the forwarded IP if it exists
	if ( array_key_exists( 'X-Forwarded-For', $headers ) && filter_var( $headers['X-Forwarded-For'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
		$the_ip = $headers['X-Forwarded-For'];
	} elseif ( array_key_exists( 'HTTP_X_FORWARDED_FOR', $headers ) && filter_var( $headers['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 )
	) {
		$the_ip = $headers['HTTP_X_FORWARDED_FOR'];
	} else {
		
		$the_ip = filter_var( $_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 );
	}
	return $the_ip;
}


function password_correct($dbc, $password){
    //echo $password; die();
    $password = sha1($password);
    try{
        $q = "SELECT COUNT(`user_id`) FROM `brit_user` WHERE `user_password` = :password";
        if ($sql = $dbc->prepare($q)){
            $sql->bindParam(':password', $password);
            $sql->execute();
            if ($sql->rowCount() > 0) {
        		$sql->setFetchMode(PDO::FETCH_NUM);
        		$result = $sql->fetch();
        	} else {
        	   $result[0] = false;
        	}
            //debug_r($result); die();
            return $result[0];
        }
    }catch(PDOException $e){
        file_put_contents('PDOErrors.txt', $e->getMessage()."\n", FILE_APPEND);
        echo 'Adatbázis-kezelési hiba!<br />';
        echo $e->getMessage();
        return $e->getMessage();
    }
}

function recover($dbc, $mode, $email) {
    $mode = sanitize($mode);
    $email = sanitize($email);
    //die($mode.', '.$email);
    $user_id = user_id_from_email($dbc, $email);
    $user_data = user_data($dbc, $user_id, 'user_name', 'user_firstname');
    
    if ($mode == 'username') {
        $body = "<p style=\"padding-top: 5px;\">Hello, ".$user_data['user_firstname']."!</p>";
        $body .= "<p style=\"padding-top: 5px;\">\n\nTe ezen a felhasználó neven regisztráltál a <b><i>Britanny Bod rajongói weboldalára</i></b>: ".$user_data['user_name']."</p>";
        $body .= "<p style=\"padding-top: 5px;\">\n\nBritanny Bod fan site</p>";
        html_mail($email, 'ELFELEJTETT FELHASZNÁLÓ NÉV!', $body);
    } else if ($mode == 'password') {
        $generated_password = substr(sha1(rand(999, 999999)), 0, 24);
        change_password($dbc, $user_id, $generated_password);
        update_user($dbc, array('user_password_recover' => '1'), $user_id);
        $body = "<p style=\"padding-top: 5px;\">Hello, ".$user_data['user_firstname']."!</p>";
        $body .= "<p style=\"padding-top: 5px;\">\n\nAz új jelszavad: ".$generated_password.". Kérlek, cseréld le a következő bejelentkezésnél.</p>";
        $body .= "<p style=\"padding-top: 5px;\">\n\nBritanny Bod fan site</p>";
        html_mail($email, 'ÚJ JELSZVAD VAN!', $body);
    }
    //die();
    return;
}

function update_user2( $dbc, $update_data, $id) {
    //debug_r($update_data);           
    $id = (int)$id;
    $update = array();
    foreach ($update_data as $field => $data) {
        $update[] = "`".$field."` = '".$data."'";
    }
    try {
        $q = "UPDATE `brit_user` SET ".implode(', ', $update)." WHERE `user_id` = :id"; //if (isset($q)){debug_r($q);}
        if ($sql = $dbc->prepare($q)) {
            $sql->bindParam(':id', $id);
           	$sql->execute();
        }
        return;
    } catch(PDOexception $e) {
        file_put_contents('PDOErrors.txt', $e->getMessage()."\n", FILE_APPEND);
        echo 'Adatbázis-kezelési hiba!<br />';
        echo $e->getMessage();
    }
    
    $dbc = null;
}

function update_user($dbc, $profile_data, $user_id){
    //debug_r($profile_data); die();
    if ($profile_data['file_isset'] == 1){
        $q = "UPDATE `brit_user` SET `user_firstname` = :firstname,`user_lastname` = :lastname,`user_email` = :email, `user_allow_email` = :allow, `user_profile`= :profile WHERE `user_id` = :id";
        if (save_picture($profile_data['tmpname'], $profile_data['user_profile']) == true){
            $result['save_image'] = true;
            if ($profile_data['old_profile'] != ''){
                $result['delete_image'] = (unlink($profile_data['old_profile']) == true)? true : false;
            }
        } else {
            $result['save_image'] = false;
        }
    } else {
        $q = "UPDATE `brit_user` SET `user_firstname` = :firstname,`user_lastname` = :lastname,`user_email` = :email, `user_allow_email` = :allow WHERE `user_id` = :id";
        $result['save_image'] = true;
    }
    try{
        //$q = "UPDATE `ref2_user` SET `user_firstname` = :firstname,`user_lastname` = :lastname,`user_email` = :email, `user_profile`= :profile WHERE `user_id` = :id";
        if ($sql = $dbc->prepare($q)) {
            $sql->bindParam(':firstname', $profile_data['user_firstname']);
            $sql->bindParam(':lastname', $profile_data['user_lastname']);
            $sql->bindParam(':email', $profile_data['user_email']);
            $sql->bindParam(':allow', $profile_data['user_allow_email']);
            if ($profile_data['file_isset'] == 1){
                $sql->bindParam(':profile', $profile_data['user_profile']);
            }
            $sql->bindParam(':id', $user_id);
            if ($sql->execute()) {
                $result['sql'] = true;
            } else {
                $result['sql'] = false;
            }
            return $result;
        }
    }catch(PDOException $e){
        file_put_contents('PDOErrors.txt', $e->getMessage()."\n", FILE_APPEND);
        echo 'Adatbázis-kezelési hiba!<br />';
        echo $e->getMessage();
        return $e->getMessage();
    }
    
}

function activate($dbc, $email, $email_code) {
    $email = $dbc->quote($email);
    //$email_code = $dbc->quote($email_code);
    
    $q = "SELECT COUNT(`user_id`) FROM `brit_user` WHERE `user_email` = $email AND `user_email_code` = '$email_code' AND `user_active` = 0";
    //$q = "SELECT `user_email_code` FROM `brit_user` WHERE `user_id` = 1";
    //echo $q;
    if ($sql = $dbc->query($q)) {
    	if ($sql->rowCount()) {
    		$sql->setFetchMode(PDO::FETCH_NUM);
    		$data = $sql->fetch();
    	}
    }
    //echo '<br />'.$data[0];
    if ($data[0] == 1) {
        $q = "UPDATE `brit_user` SET `user_active`= 1 WHERE `user_email` = $email";
        $sql = $dbc->prepare($q);
    	$sql->execute();
        return true;
    } else {
        return false;
    }
}

function change_password($dbc, $user_id, $password) {
    $user_id = (int)$user_id;
    $password = sha1($password);
    $q = "UPDATE `brit_user` SET `user_password`= '$password', `user_password_recover` = 0 WHERE `user_id` = $user_id";
    if ($sql = $dbc->prepare($q)) {
        if ($sql->execute()) {
            $data = true;
        } else {
            $data = false;
        }
    }
    return $data;
}

function has_access($dbc, $user_id, $type) {
    $user_id = (int)$user_id;
    $type    = (int)$type;
    try {
        $q = "SELECT COUNT(`user_id`) FROM `brit_user` WHERE `user_id` = :user_id AND `user_type` = :type";
        if ($sql = $dbc->prepare($q)) {
            $sql->bindParam(':user_id', $user_id);
            $sql->bindParam(':type', $type);
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
    
    $dbc = null;
}

function register_user($dbc, $register_data) {
    array_walk($register_data, 'array_sanitize', $dbc);
    $register_data['user_password'] = sha1($register_data['user_password']);
    //debug_r($register_data);
    $fields = '`'.implode('`, `', array_keys($register_data)).'`';
    $data = "'".implode("', '", $register_data)."'";
    $q = "INSERT INTO `brit_user` ($fields) VALUES ($data)";
    if ($sql = $dbc->prepare($q)) {
        if ($sql->execute()) {
            $data = true;
        } else {
            $data = false;
        }
    }
    
    $body = "<p>Hello, ".$register_data['user_firstname']."!</p>";
    $body .= "<p>\n\nTe, vagy valaki a nevedben regisztrált a Britanny Bod rajongói weboldalára.";
    $body .= " (Ha nem te voltál, hagyd figyelmen kívűl ezt a levelet)";
    $body .= "\n\nA teljes eléréshez aktíválni kell a regisztrációdat, amit az alábbi linken érhetsz el:";
    $body .= " <a href=\"http://localhost/brit/index.php?page=activate&email=".$register_data['user_email']."&email_code=".$register_data['user_email_code']."\">Aktiválás.<a/></p>";
    $body .= "<p>\n\nBritanny Bod fan site</p>";
    //email($register_data['user_email'], 'Regisztráció aktiválása', $body);
    html_mail($register_data['user_email'], 'SIKERES REGISZTRÁCIÓ!', $body);
    return $data;
	
}

function user_count($dbc) {
    $q = "SELECT COUNT(`user_id`) FROM `brit_user` WHERE `user_active` = 1";
    if ($sql = $dbc->query($q)) {
    	if ($sql->rowCount()) {
    		$sql->setFetchMode(PDO::FETCH_NUM);
    		$data = $sql->fetch();
    		$result = $data[0];
    		//debug_r($result); die();
    	}
    }
    return $result;
}

function user_data($dbc, $user_id) {
    $data = array();
    $user_id =(int)$user_id;
    $func_num_args = func_num_args();
    $func_get_args = func_get_args();
    
    if ($func_num_args > 2) {
        unset($func_get_args[0], $func_get_args[1]);
    }
    $fields = '`'.implode('`, `', $func_get_args).'`'; //if (isset($fields)){debug_r($fields);}
    
    try {
        $q = "SELECT $fields FROM `brit_user` WHERE `user_id` = :user_id"; //if (isset($q)){debug_r($q);} die();
        if ($sql = $dbc->prepare($q)) {
            $sql->bindparam(':user_id', $user_id);
            $sql->execute();
        	if ($sql->rowCount()) {
        		$sql->setFetchMode(PDO::FETCH_ASSOC);
        		$data = $sql->fetch();
        	}
        }
        //debug_r($data);
        return $data;
    } catch(PDOexception $e){
        file_put_contents('PDOErrors.txt', $e->getMessage()."\n", FILE_APPEND);
        echo 'Adatbázis-kezelési hiba!<br />';
        echo $e->getMessage();
    }
}

function logged_in() {
    return (isset($_SESSION['user_id'])) ? true : false;
}

function user_id_from_username($dbc, $username) {
    $username = sanitize($username);
    
    try {
        $q = "SELECT `user_id` FROM `brit_user` WHERE `user_name` = :username";
        if ($sql = $dbc->prepare($q)) {
            $sql->bindParam(':username', $username);
            $sql->execute();
        	if ($sql->rowCount()) {
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

function user_id_from_email($dbc, $email) {
    $email = sanitize($email);
    try {
        $q = "SELECT `user_id` FROM `brit_user` WHERE `user_email` = :email";
        if ($sql = $dbc->prepare($q)) {
            $sql->bindParam(':email', $email);
            $sql->execute();
            if ($sql->rowCount() > 0) {
                $sql->setFetchMode(PDO::FETCH_NUM);
                $result = $sql->fetch();
            }
        }
        return $result[0];
    } catch (PDOexception $e) {
        file_put_contents('PDOErrors.txt', $e->getMessage()."\n", FILE_APPEND);
        echo 'Adatbázis-kezelési hiba!<br />';
        echo $e->getMessage();
    }
    
    $dbc = null;
    
}


function login($dbc, $username, $password) {
    $user_id = user_id_from_username($dbc, $username);
    $username = sanitize($username);
    $password = sha1($password);
    try {
        $q = "SELECT COUNT(`user_id`) FROM `brit_user` WHERE `user_name` = '$username' AND `user_password` = '$password'";
        if ($sql = $dbc->query($q)) {
        	if ($sql->rowCount()) {
        		$sql->setFetchMode(PDO::FETCH_NUM);
        		$data = $sql->fetch();
        	}
        }
        return ($data[0] == 1) ? $user_id : false;
    } catch(PDOexception $e) {
        file_put_contents('PDOErrors.txt', $e->getMessage()."\n", FILE_APPEND);
        echo 'Adatbázis-kezelési hiba!<br />';
        echo $e->getMessage();
    }
    
}

function user_active($dbc, $username)
{
    $username = sanitize($username);
    //return (mysql_result(mysql_query("SELECT COUNT(`user_id`) FROM `refcover_user` WHERE `user_name` = '$username'"), 0) == 1) ? true : false;
    $q = "SELECT COUNT(`user_id`) FROM `brit_user` WHERE `user_name` = '$username' AND `user_active` = 1";
    if ($sql = $dbc->query($q)) {
    	if ($sql->rowCount()) {
    		$sql->setFetchMode(PDO::FETCH_NUM);
    		$data = $sql->fetch();
    	}
    }
    //print_r($data[0]); die();
    return ($data[0] == 1) ? true : false;
}

function email_exists($dbc, $email)
{
    $email = sanitize($email);
    //return (mysql_result(mysql_query("SELECT COUNT(`user_id`) FROM `refcover_user` WHERE `user_email` = '$email'"), 0) == 1) ? true : false;
    $q = "SELECT COUNT(`user_id`) FROM `brit_user` WHERE `user_email` = '$email'";
    if ($sql = $dbc->query($q)) {
    	if ($sql->rowCount()) {
    		$sql->setFetchMode(PDO::FETCH_NUM);
    		$data = $sql->fetch();
    	}
    }
    //print_r($data[0]); die();
    return ($data[0] == 1) ? true : false;
}

function user_exists($dbc, $username)
{
    $username = sanitize($username);
    //return (mysql_result(mysql_query("SELECT COUNT(`user_id`) FROM `refcover_user` WHERE `user_name` = '$username'"), 0) == 1) ? true : false;
    $q = "SELECT COUNT(`user_id`) FROM `brit_user` WHERE `user_name` = '$username'";
    if ($sql = $dbc->query($q)) {
    	if ($sql->rowCount()) {
    		$sql->setFetchMode(PDO::FETCH_NUM);
    		$data = $sql->fetch();
    	}
    }
    //print_r($data[0]); die();
    return ($data[0] == 1) ? true : false;
}

?>