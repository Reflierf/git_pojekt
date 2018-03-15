<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */

function ecpec($dbc, $id) {
    debug_r("KACS-MACSA!!!!!!4") ;
    debug_r($id); //die();
}

function delete_email($dbc, $id) {
    try {
        $q = "UPDATE `brit_email` SET `send_email_delete` = '1' WHERE `brit_email`.`send_email_id` = $id";
    	if ($sql = $dbc->query($q)) {
    		if ($sql->rowCount() > 0) {
    			$sql->setFetchMode(PDO::FETCH_NUM);
    			$result = $sql->fetch();
    		}
    	}
    	//debug_r($result[0]); die();
    	return $result[0];
    }catch(PDOexception $e) {
        file_put_contents('PDOErrors.txt', $e->getMessage()."\n", FILE_APPEND);
        echo 'Adatbázis-kezelési hiba!<br />';
        echo $e->getMessage();
    }
}

function get_email($dbc, $id) {
    try {
        $q = "SELECT `brit_email`.`send_email_id`, `brit_email`.`send_email_from`, `brit_email`.`send_email_to`, `brit_email`.`send_email_date`,
            `brit_email`.`send_email_subject`, `brit_email`.`send_email_message`, `brit_user`.`user_id`, `brit_user`.`user_name`, `brit_user`.`user_email` 
            FROM `brit_email`, `brit_user` WHERE `brit_email`.`send_email_id` = $id AND `brit_user`.`user_id` = `brit_email`.`send_email_from`";
    	if ($sql = $dbc->query($q)) {
    		if ($sql->rowCount() > 0) {
    			$sql->setFetchMode(PDO::FETCH_ASSOC);
    			$result = $sql->fetch();
    		}
    	}
    	//debug_r($result[0]); die();
    	return $result;
    }catch(PDOexception $e) {
        file_put_contents('PDOErrors.txt', $e->getMessage()."\n", FILE_APPEND);
        echo 'Adatbázis-kezelési hiba!<br />';
        echo $e->getMessage();
    }
}

function count_unread_letters($dbc, $id) {
    try {
        $q = "SELECT COUNT(`send_email_id`) FROM `brit_email` WHERE `send_email_lastview` = 0 AND `send_email_to` = $id AND `send_email_delete` = 0";
    	if ($sql = $dbc->query($q)) {
    		if ($sql->rowCount() > 0) {
    			$sql->setFetchMode(PDO::FETCH_NUM);
    			$result = $sql->fetch();
    		}
    	}
    	//debug_r($result[0]); die();
    	return $result[0];
    }catch(PDOexception $e) {
        file_put_contents('PDOErrors.txt', $e->getMessage()."\n", FILE_APPEND);
        echo 'Adatbázis-kezelési hiba!<br />';
        echo $e->getMessage();
    }
}

function get_sentmail_email($dbc, $id) {
    try {
        $q = "SELECT `brit_email`.`send_email_id`, `brit_email`.`send_email_from`, `brit_email`.`send_email_to`, `brit_email`.`send_email_date`, 
            `brit_email`.`send_email_subject`, `brit_email`.`send_email_message`, `brit_user`.`user_id`, `brit_user`.`user_name` 
            FROM `brit_email`, `brit_user` 
            WHERE `brit_email`.`send_email_from` = $id 
            AND `brit_user`.`user_id` = `brit_email`.`send_email_to` AND `brit_email`.`send_email_delete` = 0
            ORDER BY `brit_email`.`send_email_date` DESC";
            
        if ($sql = $dbc->query($q)) {
            if ($sql->rowCount() > 0) {
                $sql->setFetchMode(PDO::FETCH_ASSOC);
                $result = $sql;
            } else {
                $result = false;
            }
            //debug_r($result); die();
            return $result;
        }
    }catch(PDOexception $e) {
        file_put_contents('PDOErrors.txt', $e->getMessage()."\n", FILE_APPEND);
        echo 'Adatbázis-kezelési hiba!<br />';
        echo $e->getMessage();
    }
}

function get_received_email($dbc, $id) {
    try {
        /*$q = "SELECT `brit_email`.`send_email_id`, `brit_email`.`send_email_from`, `brit_email`.`send_email_to`, `brit_email`.`send_email_date`, 
            `brit_email`.`send_email_subject`, `brit_email`.`send_email_message`, `brit_user`.`user_id`, `brit_user`.`user_name` 
            FROM `brit_email`, `brit_user` 
            WHERE `brit_email`.`send_email_to` = $id 
            AND `brit_user`.`user_id` = `brit_email`.`send_email_from`";*/
        $q = "SELECT `brit_email`.`send_email_id`, `brit_email`.`send_email_from`, `brit_email`.`send_email_to`, `brit_email`.`send_email_date`, 
            (`brit_email`.`send_email_date` > `brit_email`.`send_email_lastview`) AS `email_unread`, `brit_email`.`send_email_subject`, 
            `brit_email`.`send_email_message`, `brit_user`.`user_id`, `brit_user`.`user_name` FROM `brit_email`, `brit_user` 
            WHERE `brit_email`.`send_email_to` = $id AND `brit_user`.`user_id` = `brit_email`.`send_email_from` AND `brit_email`.`send_email_delete` = 0 
            ORDER BY `brit_email`.`send_email_date` DESC";
        if ($sql = $dbc->query($q)) {
            if ($sql->rowCount() > 0) {
                $sql->setFetchMode(PDO::FETCH_ASSOC);
                $result = $sql;
            } else {
                $result = false;
            }
            //debug_r($result); die();
            return $result;
        }
    }catch(PDOexception $e) {
        file_put_contents('PDOErrors.txt', $e->getMessage()."\n", FILE_APPEND);
        echo 'Adatbázis-kezelési hiba!<br />';
        echo $e->getMessage();
    }
}

function get_last_email($dbc, $hash, $id) {
    try{
        /*$q = "SELECT `send_email_id`, `send_email_hash`, `send_email_from`, `send_email_to`, `send_email_date`, `send_email_subject`, 
            `send_email_message` FROM `brit_email` WHERE `send_email_hash` = $hash AND `send_email_to` = $id";*/
        $q = "SELECT `brit_email`.`send_email_id`, `brit_email`.`send_email_hash`, `brit_email`.`send_email_to`, `brit_email`.`send_email_date`, 
            `brit_email`.`send_email_subject`, `brit_email`.`send_email_message`, `brit_user`.`user_id`, `brit_user`.`user_name`  
            FROM `brit_email`, `brit_user` 
            WHERE `brit_email`.`send_email_hash` = $hash
            AND `brit_email`.`send_email_to` = $id 
            AND `brit_user`.`user_id` = `brit_email`.`send_email_to`";
        if ($sql = $dbc->query($q)) {
            if ($sql->rowCount() > 0) {
                $sql->setFetchMode(PDO::FETCH_ASSOC);
                $result = $sql->fetch();
            } else {
                $result = false;
            }
            //debug_r($result); die();
            return $result;
        }
    } catch(PDOexception $e) {
        file_put_contents('PDOErrors.txt', $e->getMessage()."\n", FILE_APPEND);
        echo 'Adatbázis-kezelési hiba!<br />';
        echo $e->getMessage();
    }
}

function email_for_user($dbc, $email_data) {
    //debug_r($email_data); die();
    try{
        $q = "INSERT INTO `brit_email`(`send_email_hash`, `send_email_from`, `send_email_to`, `send_email_date`, `send_email_subject`, 
            `send_email_message`) VALUES ('$email_data[send_email_hash]', '$email_data[send_email_from]', '$email_data[send_email_to]', 
            '$email_data[send_email_date]', :subject, :message)";
        if ($sql = $dbc->prepare($q)) {
            $sql->bindParam(':subject', $email_data['send_email_subject']);
            $sql->bindParam(':message', $email_data['send_email_message']);
           	if ($sql->execute()) {
                $result = true;
            } else {
                $result = false;
            }
        }
        return $result;
    } catch(PDOexception $e) {
        file_put_contents('PDOErrors.txt', $e->getMessage()."\n", FILE_APPEND);
        echo 'Adatbázis-kezelési hiba!<br />';
        echo $e->getMessage();
    }
}

function email_for_all_users($dbc, $subject, $body, $user_id) {
    $subject = sanitize($subject);
    $body = sanitize($body);
    $user_id = (int)$user_id;
    try {
        $q = "SELECT `user_email`, `user_firstname` FROM `brit_user` WHERE `user_allow_email` = 1";
        if ($sql = $dbc->query($q)) {
        	if ($sql->rowCount() > 0) {
        		$sql->setFetchMode(PDO::FETCH_ASSOC);
        		//$data = $sql->fetch();
                while (($row = $sql->fetch()) != false) {
                    //$bodytext = "Hello ".$row['user_firstname']."!\n\n".$body;
                    $bodytext = "<p style=\"padding-top: 5px;\">Hello, ".$row['user_firstname']."!</p>";
                    $bodytext .= "<p style=\"padding-top: 5px;\">\n\n".$body."</p>";
                    $bodytext .= "<p style=\"padding-top: 5px;\">\n\nBritanny Bod fan site</p>";
				    html_mail($row['user_email'], $subject, $bodytext);
                }
        	}
        }
        return;
    } catch(PDOexception $e) {
        file_put_contents('PDOErrors.txt', $e->getMessage()."\n", FILE_APPEND);
        echo 'Adatbázis-kezelési hiba!<br />';
        echo $e->getMessage();
    }
    
    //$dbc = null;
}



function html_mail($to, $subject, $body) {
    $date_now = date("Y-m-d");
    $text = $body;
    $message = '
    <!DOCTYPE HTML>
    <html>
    <head>
    	<meta http-equiv="content-type" content="text/html" />
        <meta charset="utf-8"/>
    	<meta name="author" content="Freiler Béla" />
        
        <style>
            * {
                margin: 0;
                border: 0;
                padding: 0;
            }
            
            a, a:link {
                color: #fbeebb;
                text-decoration: none;
            }
            
            table {
                width: 700px;
                margin: 0 auto;
                text-align: center;
                background: #000;
            }
            
            .email_top_margo {
                height: 20px;
            }
            
            .midle_margin {
                width: 20px;
            }
            
            .center_cell {
                width: 660px;
                height: 50px;
            }
            
            .body {
                font-size: 14pt;
            }
            
            .message_table, .footer_table {
                width: 660px;
            }
            
            .line1, .line2 {
                    background: #7a0026;
                    height: 4px;
            }
            
            /*.line1 {
                
                top: -3px;
            }
            
            .line2 {
                top: 3px;
            }*/
            
            .footer_table {
                margin-top: 5px;
            }
            
            .footer_table .x18{
                width: 82px;
            }
            
            .footer_table .warning_text {
                font-size: 10px;
            }
            
        </style>
        
    </head>
    
    <body>
    
    <table cellpadding="0" cellspacing="0" border="0" align="center">
        <tr><!-- felső margó -->
            <td class="email_top_margo" colspan="3" cellpadding="0" cellspacing="0" border="0"></td>
        </tr>
        <tr><!-- Üzenet terület -->
            <td class="midle_margin"></td>
            <td class="center_cell">
                <table class="message_table"> <!--  style="border: 1px solid #fff;" -->
                    <tr>
                        <td align="center">
                            <a href="http://localhost/brit/index.php">
                                <img src="http://localhost/brit/img/logo_racy_2.png" width="660" border="0" style="display:block" alt="Logo"/>
                            </a>
                        </td>
                    </tr>
                     <tr>
                        <td class="line1" width="660" style="position: relative; top: 3px;"></td>
                    </tr>
                    <tr>
                        <td>
                            <p style="color: #fff; margin: 5px auto 10px; font-size: 20pt; font-weight: bold; font-style: italic;">'.$subject.'</p>
                        </td>
                    </tr>
                    <tr>
                        
                        <td class="body" style="color: #fffdea; margin: 10px 0;">'.$text.'</td>
                        
                    </tr>
                    <tr>
                        <td class="line2" width="660" style="position: relative; top: 3px;"></td>
                    </tr>
                    <tr>
                        <td>
                            <table class="footer_table" style="color: #fff;">
                                <tr>
                                    <td class="x18"><img src="http://localhost/brit/img/18.jpg" /></td>
                                    <td class="warning_text"colspan="2">
                                    <p>
                                        Figyelem!
                                    </p>
                                    <p>Felnőtt tartalom, megtekintés csak tizennyolc éven felülieknek.</p>
                                    <p>&copf; 2017 Ref, Minden jog fentartva! A hivatkozott valamennyi védjegy a megfelelő tulajdonosok tulajdonát képezi.</p>
                                    <p>Ha már nem szeretne <i>Britannys Bod Fan Site</i> hírleveleket vagy a reklámozott termékekkel kapcsolatos információkat kérni, 
                                    kérjük, válaszoljon erre az e-mailre az e-mail szövegének "Leiratkozás" feliratával. 
                                    Alternatív megoldásként <a href="#">kattintson ide</a> a beállítások kezeléséhez.</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
            <td class="midle_margin"></td>
        </tr>
        <tr><!-- alsó margó -->
            <td class="email_top_margo" colspan="3" cellpadding="0" cellspacing="0" border="0" align="center"></td>
        </tr>
    </table>
    
    </body>
    </html>
    ';
    // To send HTML mail, the Content-type header must be set
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
    
    // Additional headers
    //$headers .= 'To: Mary <ref@freemail.hu>, Kelly <ref68@upcmail.hu>, Bela Pornerastic <terb98@gmail.com>' . "\r\n";
    $headers .= 'From: Britannys Bod Fan Site <terb98@gmail.com>' . "\r\n";
    
    // Mail it
    mail($to, '=?utf-8?B?'.base64_encode($subject).'?=', $message, $headers);
    //echo 'elküldve.';
}

?>