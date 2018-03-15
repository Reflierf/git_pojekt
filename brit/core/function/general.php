<?php



function save_picture($tmpname, $picture){
    return (move_uploaded_file($tmpname, $picture))? true : false;
}

/*function save_pictures($image_tmp, $image_destination_path, $image_name) {
    //Nagyon fontos! Ha felt�lt�m a filet a szerverre, nem haszn�lni az iconv f�ggv�nyt, mert akkor rossz lesz a file neve! 
    //Csak a 'localhoston' szabad haszn�lni!
	$image_name = iconv('utf-8', 'windows-1250', $image_name);
    return move_uploaded_file($image_tmp, $image_destination_path.$image_name);
}*/

function email($to, $subject, $body) {
    $to = sanitize($to);
    //$subject = sanitize($subject); //sanitize f�ggv�ny: Lev�l k�ld�sn�l nem szabad haszn�lni, mert az �kezetes bet�ket �talak�tja, �s akkor nem lesz j�!
    //$body = sanitize($body);
    set_time_limit(300);
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
	$headers .= 'From: Britanny Bod fan site <terbp8@gmail.com>' . "\r\n";
    //mail($to, $subject, $body, $headers); //<- Nem frank�, mert az �kezetes karakterek rssz�l jelennek meg.
    //lev�l k�ld�se
    //az�rt k�tszer, mert k�tszer h�vod meg a mail() f�ggv�nyt, els�k�nt egy v�ltoz�ba mented el, de ut�na ugyan�gy lefuttatod. 
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
	//Lecser�li a nem biztons�gos karaktereket a file nev�ben. Megh�v�s: $file_name = secure_filename($file_name)
	/*echo $ponthol = strrpos($name, '.');
	echo '3<br />'.$hossz = strlen($name);
	echo '<br />4'.$file = substr($name, 0, $ponthol);
	echo '<br />'.$kiterj = substr($name, $ponthol, $hossz-$ponthol);
	echo '<br />$title: '.$title = trim($file);*/
	$title = str_replace('�','a',$title);
	$title = str_replace('�','e',$title);
	$title = str_replace('�','i',$title);
	$title = str_replace('�','o',$title);
	$title = str_replace('�','o',$title);
	$title = str_replace('�','o',$title);
	$title = str_replace('�','u',$title);
	$title = str_replace('�','u',$title);
	$title = str_replace('�','u',$title);
	$title = str_replace('�','A',$title);
	$title = str_replace('�','E',$title);
	$title = str_replace('�','I',$title);
	$title = str_replace('�','O',$title);
	$title = str_replace('�','O',$title);
	$title = str_replace('�','O',$title);
	$title = str_replace('�','U',$title);
	$title = str_replace('�','U',$title);
	$title = str_replace('�','U',$title);
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