<?php
include_once 'includes/db.class.php';
session_start();
//
// if (strlen($_POST['name'])<1) {
//   echo 'error';
//   return ;
// }isset,is_null

// if (isset($_POST['name']) || is_null($_POST['name'])
// || isset($_POST['content']) || is_null($_POST['content'])) {
//    echo 'value not be null';
//    return;
// }

$db = new Db();
//
 $UserId= Auth::user()->id;
 $name = $_POST['name'];
 $bg_color = $_POST['bg_color'];
// $content =htmlentities($_POST['content']);
//
$contentArr=$_POST['contentArr'];
$result = $db -> insertTemplate( $name,$UserId,$bg_color);
$tempId=$db->getLastTempId();
$result=false;
for ($i=0; $i < sizeof($contentArr); $i++) {
  if (isset($contentArr[$i]['id'])) {
    $result = $db -> insertTemplateBlocks( $tempId, $contentArr[$i]['id'],$contentArr[$i]['content']);
  }
}

if ($result) {
  echo 'ok';
}else {
   echo 'error';
}

?>
