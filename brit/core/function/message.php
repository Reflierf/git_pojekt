<?php 

function count_unread_messages($dbc, $id) {
	$q = "SELECT COUNT(`brit_conversations_messages`.`message_text`) 
	FROM `brit_conversations_messages`, `brit_conversations_members` 
	WHERE `brit_conversations_messages`.`conversation_id` = `brit_conversations_members`.`conversation_id` 
	AND `brit_conversations_members`.`user_id` = $id 
	AND `brit_conversations_messages`.`message_date` > `brit_conversations_members`.`conversation_last_view` AND `brit_conversations_members`.`conversation_deleted` = 0"; 
	
	if ($sql = $dbc->query($q)) {
		if ($sql->rowCount() > 0) {
			$sql->setFetchMode(PDO::FETCH_NUM);
			$result = $sql->fetch();
		}
	}
	//debug_r($result); die();
	return $result[0];
}

function delete_conversation($dbc, $conversation_id, $session_user_id) {
	$conversation_id = (int)$conversation_id;
	//echo $conversation_id.', '. $session_user_id;
	$q="SELECT DISTINCT `conversation_deleted` FROM `brit_conversations_members` WHERE `user_id` != $session_user_id AND `conversation_id` = $conversation_id";
	if ($result = $dbc->query($q)) {
		if ($result->rowCount() > 0) {
			$result->setFetchMode(PDO::FETCH_NUM);
			$myhem = $result->fetch();
		}
	}
	//debug_r($result->rowCount());
	//debug_r($myhem[0]); die();
	
	if ($myhem[0] === 1) {
		$sql = $dbc->prepare("DELETE FROM `brit_conversations` WHERE `conversation_id` = $conversation_id");
		$sql->execute();
		$sql = $dbc->prepare("DELETE FROM `brit_conversations_members` WHERE `conversation_id` = $conversation_id");
		$sql->execute();
		$sql = $dbc->prepare("DELETE FROM `brit_conversations_messages` WHERE `conversation_id` = $conversation_id");
		$sql->execute();
	} else {
		$q = "UPDATE `brit_conversations_members` SET `conversation_deleted`= 1 WHERE `conversation_id` = '$conversation_id' AND `user_id` = $session_user_id";
		$sql = $dbc->prepare($q);
		$sql->execute();
	}
}

function update_conversation_last_view($dbc, $conversation_id) {
	$conversation_id = (int)$conversation_id;
	$q="UPDATE `brit_conversations_members` SET `conversation_last_view` = UNIX_TIMESTAMP() WHERE `conversation_id` = $conversation_id AND `user_id` = '$_SESSION[user_id]'";
	$sql = $dbc->prepare($q);
	$sql->execute();
}

function fetch_conversation_messages($dbc, $conversation_id) {
	$conversation_id = (int)$conversation_id;
	//$q="SELECT `brit_conversations_messages`.`message_date`, `brit_conversations_messages`.`message_text`, `brit_users`.`user_name` FROM `brit_conversations_messages` INNER JOIN `brit_users` On `brit_conversations_messages`.`user_id` = `brit_users`.`user_id` WHERE `brit_conversations_messages`.`conversation_id` = $conversation_id ORDER BY `brit_conversations_messages`.`message_date` DESC";
	$q = "SELECT `brit_conversations_messages`.`message_date`, 
	`brit_conversations_messages`.`message_date` > `brit_conversations_members`.`conversation_last_view` AS `message_unread`, 
	`brit_conversations_messages`.`message_text`, `brit_user`.`user_name` FROM `brit_conversations_messages` 
	INNER JOIN `brit_user` ON `brit_conversations_messages`.`user_id` = `brit_user`.`user_id` 
	INNER JOIN `brit_conversations_members` ON `brit_conversations_messages`.`conversation_id` = `brit_conversations_members`.`conversation_id` 
	WHERE `brit_conversations_messages`.`conversation_id` = '$conversation_id' 
	AND `brit_conversations_members`.`user_id` = '$_SESSION[user_id]' 
	ORDER BY `brit_conversations_messages`.`message_date` DESC";
	if ($sql = $dbc->query($q)) {
		if ($sql->rowCount() > 0) {
			$sql->setFetchMode(PDO::FETCH_ASSOC);
			$messages = array();
			while ($result = $sql->fetch()) {
				$messages[] = array(
					'date' 		=> $result['message_date'],
					'unread' 	=> $result['message_unread'],
					'text' 		=> $result['message_text'],
					'user_name' => $result['user_name']
				);
			}
		}
	}
	
	return $messages;
}


function add_conversation_message($dbc, $conversation_id, $text) {
	$conversation_id = (int)$conversation_id;
	//$text = mysql_real_escape_string(($text));
	
	//echo "INSERT INTO `brit_conversations_messages`(`conversation_id`, `user_id`, `message_date`, `message_text`) VALUES ($conversation_id, '$_SESSION[user_id]', UNIX_TIMESTAMP(), $text)";
	$q = "INSERT INTO `brit_conversations_messages`(`conversation_id`, `user_id`, `message_date`, `message_text`) VALUES ($conversation_id, '$_SESSION[user_id]', UNIX_TIMESTAMP(), '$text')";
	$sql = $dbc->prepare($q);
	$sql->execute();
}

function validate_conversation_id($dbc, $conversation_id) {
    //debug_r($conversation_id); die();
	//$conversation_id = (int)$conversation_id;
	$q="SELECT COUNT(`conversation_id`) FROM `brit_conversations_members` WHERE `conversation_id` = $conversation_id AND `user_id` = '$_SESSION[user_id]' AND `conversation_deleted` = 0";
	//$q="SELECT COUNT(1) FROM `brit_conversations_members` WHERE `conversation_id` = $conversation_id AND `user_id` = '$_SESSION[user_id]' AND `conversation_deleted` = 0";
	if ($sql = $dbc->query($q)) {
		if ($sql->rowCount() > 0) {
			$sql->setFetchMode(PDO::FETCH_NUM);
			$result = $sql->fetch();
			//debug_r($result); die();
		}
	}
	return ($result[0] == 0)? false : true;
}

function fetch_conversation_users($dbc, $cid) {
    try{
        $q = "SELECT `brit_user`.`user_id`, `brit_user`.`user_name`, `brit_user`.`user_profile`, `brit_conversations_members`.`conversation_id`, 
            `brit_conversations_members`.`user_id` FROM `brit_user`, `brit_conversations_members` WHERE `brit_conversations_members`.`conversation_id` 
            = '$cid' AND `brit_conversations_members`.`user_id` = `brit_user`.`user_id`";
        if ($sql = $dbc->query($q)) {
            if ($sql->rowCount() > 0) {
                $sql->setFetchMode(PDO::FETCH_ASSOC);
                $data = $sql;
            }
        }
    }catch(PDOException $e) {
        file_put_contents('PDOErrors.txt', $e->getMessage()."\n", FILE_APPEND);
        echo 'Adatbázis-kezelési hiba!<br />';
        echo $e->getMessage();
    }
    return $data;
}

function fetch_conversation_summery($dbc) {
		
	//$q = "SELECT `brit_conversations`.`conversation_id`, `brit_conversations`.`conversation_subject`, MAX(`brit_conversations_messages`.`message_date`) AS `conversations_last_replay`, MAX(`brit_conversations_messages`.`message_date`) > `brit_conversations_members`.`conversation_last_view` AS `conversations_unread` FROM `brit_conversations` LEFT JOIN `brit_conversations_messages` ON `brit_conversations`.`conversation_id` = `brit_conversations_messages`.`conversation_id` INNER JOIN `brit_conversations_members` ON `brit_conversations`.`conversation_id` = `brit_conversations_members`.`conversation_id` WHERE `brit_conversations_members`.`user_id` = '$_SESSION[user_id]' AND `brit_conversations_members`.`conversation_deleted` = 0 GROUP BY `brit_conversations`.`conversation_id` ORDER BY `conversations_last_replay` DESC";
	
	$q = "SELECT `brit_conversations`.`conversation_id`, `brit_conversations`.`conversation_subject`, 
		MAX(`brit_conversations_messages`.`message_date`) AS `conversations_last_replay`,
MAX(`brit_conversations_messages`.`message_date`) > `brit_conversations_members`.`conversation_last_view` AS `conversations_unread`
FROM `brit_conversations` 
		LEFT JOIN `brit_conversations_messages` ON `brit_conversations`.`conversation_id` = `brit_conversations_messages`.`conversation_id` 
		INNER JOIN `brit_conversations_members` ON `brit_conversations`.`conversation_id` = `brit_conversations_members`.`conversation_id` 
		WHERE `brit_conversations_members`.`user_id` = '$_SESSION[user_id]' AND `brit_conversations_members`.`conversation_deleted` = 0 
		GROUP BY `brit_conversations`.`conversation_id` ORDER BY `conversations_last_replay` DESC";
	if ($sql = $dbc->query($q)) {
		if ($sql->rowCount() > 0) {
			$sql->setFetchMode(PDO::FETCH_ASSOC);
			$conversations = array();
			while ($result = $sql->fetch()) {
				$conversations[] = array(
					'id' => $result['conversation_id'],
					'subject' => $result['conversation_subject'],
					'last_replay' => $result['conversations_last_replay'],
					'unread_messages' => ($result['conversations_unread'] == 1)
				);
			}
		} else {
			$conversations = false;
		}
	}
	return $conversations;
}

function create_conversation($dbc, $user_ids, $subject, $body) {
	//$subject = mysql_real_escape_string(htmlentities($subject));
	//$body = mysql_real_escape_string($body);
	//echo '1<br />';
	$q = "INSERT INTO `brit_conversations`(`conversation_subject`) VALUES ('$subject')";
	$sql = $dbc->prepare($q);
	$sql->execute();
	
	$conversation_id = $dbc->lastInsertId();
	//echo '2<br />';
	$q = "INSERT INTO `brit_conversations_messages`(`conversation_id`, `user_id`, `message_date`, `message_text`) VALUES ($conversation_id, '$_SESSION[user_id]', UNIX_TIMESTAMP(), '$body')";
	$sql = $dbc->prepare($q);
	$sql->execute();
	
	$values = array("($conversation_id, '$_SESSION[user_id]', UNIX_TIMESTAMP(), 0)");
	//$user_ids[] = $_SESSION['user_id'];
	
	foreach ($user_ids as $user_id) {
		$user_id = (int)$user_id;
		$values[] = "($conversation_id, $user_id, 0, 0)";
	}
	//echo '3<br />';
	$q = "INSERT INTO `brit_conversations_members`(`conversation_id`, `user_id`, `conversation_last_view`, `conversation_deleted`) VALUES ".implode(", ", $values);
	$sql = $dbc->prepare($q);
	$sql->execute();
}

function fetch_user_ids($dbc, $user_names) {
	foreach ($user_names as &$name) {
		//$name = sanitize($name);
	}
	$q = "SELECT `user_id`, `user_name` FROM `brit_user` WHERE `user_name` IN ('".implode("', '", $user_names)."')";
	$names = array();
	if ($sql = $dbc->query($q)) {
		if ($sql->rowCount() > 0) {
			$sql->setFetchMode(PDO::FETCH_ASSOC);
			while ($row = $sql->fetch()) {
				$names[$row['user_name']] = $row['user_id'];
				//print_r($names); die();
			}
		}
	}
	return $names;
}
?>