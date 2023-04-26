<?php
include_once 'includes/db.class.php';
session_start();


$db = new Db();


$name = $_POST['name']; 
$Id=$_POST["id"];
$contentArr=$_POST['contentArr'];

$result = $db->updateTemplate( $name  , $Id ); 
$db->deleteTemplateBlocks($Id);
for ($i=0; $i < sizeof($contentArr); $i++) {
  $blockId = isset($contentArr[$i]['id']) ? $contentArr[$i]['id'] : 1;
  $result = $db->insertTemplateBlocks( $Id, $blockId,$contentArr[$i]['content']);
}
if ($result) {
  echo 'ok';
}else {
   echo 'error';
}


?>
