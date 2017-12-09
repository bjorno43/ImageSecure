<?php
require_once "config.php";
require_once "imgupload.class.php";

$img = new ImageUpload;
$img->downloadImage($_GET['id']);

?>
