<?php

function update_menu($dbc, $menu_data) {
    //print_r($menu_data);die();
    $menu_data['title'] = sanitize($menu_data['title']); //debug_r($menu_data['title']); die();
    $menu_data['anchor'] = sanitize($menu_data['anchor']);
    $menu_data['parent'] = (int)$menu_data['parent'];
    $menu_data['sort'] = (int)$menu_data['sort'];
    try {
        $q = "UPDATE `brit_menu` SET `menu_title`= :title, `menu_anchor`= :anchor, `menu_parent_id`= :parent,`menu_sort`= :sort WHERE `menu_id` = $menu_data[id]"; //debug_r($q); die();
        if ($sql = $dbc->prepare($q)) {
            $sql->bindParam(':title', $menu_data['title']);
            $sql->bindParam(':anchor', $menu_data['anchor']);
            $sql->bindParam(':parent', $menu_data['parent']);
            $sql->bindParam(':sort', $menu_data['sort']);
            return $sql->execute();
        }
    } catch(PDOexception $e) {
        file_put_contents('PDOErrors.txt', $e->getMessage()."\n", FILE_APPEND);
        echo 'Adatbázis-kezelési hiba!<br />';
        echo $e->getMessage();
    }
    
    $dbc = null;
    
}

function dell_menu($dbc, $menu_id) {
    $menu_id = (int)$menu_id;
    $q = "DELETE FROM `brit_menu` WHERE `menu_id` = $menu_id";
    $sql = $dbc->prepare($q);
    return $sql->execute();
}

function add_menu($dbc, $menu_data) {
    //print_r($menu_data);
    $menu_data['new_menu_name'] = sanitize($menu_data['new_menu_name']);
    $menu_data['new_menu_anchor'] = sanitize($menu_data['new_menu_anchor']);
    $menu_data['menu_parent'] = (int)$menu_data['menu_parent'];
    $q = "SELECT MAX(`menu_sort`) FROM `brit_menu` WHERE `menu_parent_id` = $menu_data[menu_parent]";
    if ($sql = $dbc->query($q)) {
        if ($sql->rowCount()) {
            $sql->setFetchMode(PDO::FETCH_NUM);
            $result = $sql->fetch();
        }
    }
    
    $result[0] = $result[0]+1;
    $q = "INSERT INTO `brit_menu`(`menu_title`, `menu_anchor`, `menu_parent_id`, `menu_sort`) 
        VALUES ('$menu_data[new_menu_name]','$menu_data[new_menu_anchor]',$menu_data[menu_parent], $result[0])";
    $sql = $dbc->prepare($q); 
   	return $sql->execute();
}


function has_children($rows,$id) {
  foreach ($rows as $row) {
    if ($row['parent_id'] == $id)
      return true;
  }
  return false;
}

function build_menu($rows,$parent=0)
{  
  $result = "<ul>";
  foreach ($rows as $row)
  {
    if ($row['parent_id'] == $parent){
      //$result.= "<li>{'$row[title]'}";          //<li><a href="#">Menu 2</a></li>
      $result.= '<li><a href="'.$row['anchor'].'">'.$row['title'].'</a>';
      if (has_children($rows,$row['id']))
        $result.= build_menu($rows,$row['id']);
      $result.= "</li>";
    }
  }
  $result.= "</ul>";

  return $result;
}

?>