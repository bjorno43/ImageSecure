<?php
set_time_limit(20);
require_once "config.php";
require_once "imgupload.class.php";
$img = new ImageUpload;

$result = $img->uploadImages($_FILES['image']);

if(!empty($result->info)){
    foreach($result->info as $infoMsg){
        echo $infoMsg .'<br />';
    }
}

echo "Your images can be viewed here:<br/><br/>";

if(!empty($result->ids)){
    foreach($result->ids as $id){
        echo "http://home.icecub.nl/image.php?". $id;
    }
}


?>
