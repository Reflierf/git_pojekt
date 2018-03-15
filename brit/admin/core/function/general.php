<?php

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
	$title = str_replace('ő','o',$title);
	$title = str_replace('ú','u',$title);
	$title = str_replace('ü','u',$title);
	$title = str_replace('ű','u',$title);
	$title = str_replace('Á','A',$title);
	$title = str_replace('É','E',$title);
	$title = str_replace('Í','I',$title);
	$title = str_replace('Ó','O',$title);
	$title = str_replace('Ö','O',$title);
	$title = str_replace('Ő','O',$title);
	$title = str_replace('Ú','U',$title);
	$title = str_replace('Ü','U',$title);
	$title = str_replace('Ű','U',$title);
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

function save_newspic($imgtmp, $imgdest, $imgname) {
    //$imgname = iconv('utf-8', 'windows-1250', $imgname);
    //echo $current_web = $_SERVER['SCRIPT_NAME']; die();
    //echo $imgdest.$imgname; die();
    $done = move_uploaded_file($imgtmp, '../'.$imgdest.$imgname);
    return $done;
}

/*function array_sanitize(&$item) {
    return $item = htmlspecialchars(strip_tags(trim($item)));
}*/

function array_sanitaze(&$item) {
    return $item = htmlentities(htmlspecialchars(strip_tags(trim($item))));
}


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

?>