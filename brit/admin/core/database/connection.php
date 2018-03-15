<?php

/**
 * @author Freiler Béla
 * @copyright 2017
 */

try {
    $db['host'] = '127.0.0.1';
    $db['name'] = 'brit';
    /*$db['user'] = 'relierf1968';
    $db['password'] = 'baraddur';*/
	$db['user'] = 'jalka74-17';
    $db['password'] = 'EdjK8NzLJw2WzrbF';
    $dbc = new PDO("mysql:host=$db[host];dbname=$db[name]", "$db[user]", "$db[password]");
	$dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //Segít elkapni a hibaüzenetet
    $dbc->query("SET NAMES utf8");
    
} catch(PDOexception $e) {
    echo 'nem ok';
	echo $e->getMessage();
	file_put_contents('PDOErrors.txt', $e->getMessage()."\n", FILE_APPEND);
	die('Adatbázis csatlakozási hiba.');
}

?>