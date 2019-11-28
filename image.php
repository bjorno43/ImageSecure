<?php
//require_once 'config.php';
require_once 'i-tatarajah.php';
require_once "imgupload.class.php";

$img = new ImageUpload;
$img->showImage($_GET['id']);

?>
