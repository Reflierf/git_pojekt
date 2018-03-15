<?php



function save_picture($tmpname, $picture){
    return (move_uploaded_file($tmpname, $picture))? true : false;
}

/*function save_pictures($image_tmp, $image_destination_path, $image_name) {
    //Nagyon fontos! Ha feltöltöm a filet a szerverre, nem használni az iconv függvényt, mert akkor rossz lesz a file neve! 
    //Csak a 'localhoston' szabad használni!
	$image_name = iconv('utf-8', 'windows-1250', $image_name);
    return move_uploaded_file($image_tmp, $image_destination_path.$image_name);
}*/

function email($to, $subject, $body) {
    $to = sanitize($to);
    //$subject = sanitize($subject); //sanitize függvény: Levél küldésnél nem szabad használni, mert az ékezetes betûket átalakítja, és akkor nem lesz jó!
    //$body = sanitize($body);
    set_time_limit(300);
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
	$headers .= 'From: Britanny Bod fan site <terbp8@gmail.com>' . "\r\n";
    //mail($to, $subject, $body, $headers); //<- Nem frankó, mert az ékezetes karakterek rsszúl jelennek meg.
    //levél küldése
    //azért kétszer, mert kétszer hívod meg a mail() függvényt, elsõként egy változóba mented el, de utána ugyanúgy lefuttatod. 
    mail($to, '=?utf-8?B?'.base64_encode($subject).'?=', $body, $headers);
    set_time_limit(30);
}

function logged_in_redirect() {
    if (logged_in() === true) {
        header('Location: index.php');
        exit();
    }
}

function protect_page() {
    if (logged_in() === false) {
        header('Location: index.php?page=protected');
        exit();
    }
}

function admin_protect($dbc) {
    global $user_data;
    if (has_access($dbc, $user_data['user_id'],1) == false) {
        header('Location: index.php?page=main');
        exit();
    }
}

function moderator_protect($dbc) {
	global $user_data;
    if (has_access($dbc, $user_data['user_id'],2) === false) {
        header('Location: index.php?page=main');
        exit();
    }
}

function array_sanitize(&$item) {
    return $item = htmlspecialchars(strip_tags(trim($item)));
}

/*function array_sanitize(&$item, $key, $dbc) {
    return $item = htmlentities(htmlspecialchars($dbc->real_escape_string(trim($item))));
}*/


function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function debug_r($tomb) {
    echo '<pre class="debug_r">';
    print_r($tomb);
    echo '</pre>';
}

function output_errors($errors) {
    return '<ul><li class="errors pt-center">'.implode('</li><li class="errors pt-center">', $errors).'</li></ul>';
}

function output_success($success) {
    return '<ul><li class="success pt-center">'.implode('</li><li class="success pt-center">', $success).'</li></ul>';
}

function conv_ekezet($title) {
	//Lecseréli a nem biztonságos karaktereket a file nevében. Meghívás: $file_name = secure_filename($file_name)
	/*echo $ponthol = strrpos($name, '.');
	echo '3<br />'.$hossz = strlen($name);
	echo '<br />4'.$file = substr($name, 0, $ponthol);
	echo '<br />'.$kiterj = substr($name, $ponthol, $hossz-$ponthol);
	echo '<br />$title: '.$title = trim($file);*/
	$title = str_replace('á','a',$title);
	$title = str_replace('é','e',$title);
	$title = str_replace('í','i',$title);
	$title = str_replace('ó','o',$title);
	$title = str_replace('ö','o',$title);
	$title = str_replace('õ','o',$title);
	$title = str_replace('ú','u',$title);
	$title = str_replace('ü','u',$title);
	$title = str_replace('û','u',$title);
	$title = str_replace('Á','A',$title);
	$title = str_replace('É','E',$title);
	$title = str_replace('Í','I',$title);
	$title = str_replace('Ó','O',$title);
	$title = str_replace('Ö','O',$title);
	$title = str_replace('Õ','O',$title);
	$title = str_replace('Ú','U',$title);
	$title = str_replace('Ü','U',$title);
	$title = str_replace('Û','U',$title);
	$title = str_replace(',','-',$title);
	$title = str_replace('.','-',$title);
	$title = str_replace(';','',$title);
	$title = str_replace(':','-',$title);
	$title = str_replace('?','',$title);
	$title = str_replace('!','',$title);
	$title = str_replace('*','',$title);
	$title = str_replace('=','',$title);
    $title = str_replace('+','',$title);
	$title = str_replace('%','',$title);
	$title = str_replace('@','',$title);
	$title = str_replace('&','',$title);
    $title = str_replace('#','',$title);
	$title = str_replace('"','',$title);
	$title = str_replace('<','',$title);
	$title = str_replace('>','',$title);
	$title = str_replace('[','',$title);
	$title = str_replace(']','',$title);
	$title = str_replace('{','',$title);
	$title = str_replace('}','',$title);
	$title = str_replace('|','',$title);
	$title = str_replace('`','',$title);
	$title = str_replace('$','',$title);
	$title = str_replace(' ','_',$title);
	//echo '<br />'.$title.'<br />';
	return $title;
}

?>