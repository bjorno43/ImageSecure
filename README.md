# ImageSecure
PHP Library to securely handle images uploaded by users

Unfortunetely most enthousiast web developers don't realise the potential risks that come with this. If you don't process the files correctly, someone could (intentially or not) bring serious harm to your server (and any visitors of your website(s)). However, processing the files correctly is not so easy if you're just an enthousiast.

The ImageUpload Class was developed with that in mind. If you follow the guide below, the class will do most of the difficult work for you while keeping it as simple as possible for you to use.

# USAGE

- Edit config.php.
- Upload the files to your server.

1) Setup your upload form

```html
<form name="upload" action="upload.php" method="POST" enctype="multipart/form-data">
	Select image to upload: <input type="file" name="image[]" multiple>
	<input type="submit" name="upload" value="upload">
</form>
```
ImageUpload is designed to handle single or multiple files at once. It is however important that you always use the same name in the name attribute of your file input elements!

So for multiple file selections:
```html
<input type="file" name="image[]" multiple>
```

And for single file selections:
```html
<input type="file" name="image[]">
<input type="file" name="image[]">
<input type="file" name="image[]">
```

2) Prepare your PHP form handler to use the ImageUpload class
```php
<?php
require_once "config.php";
require_once "imgupload.class.php";

$img = new ImageUpload;

$result = $img->uploadImages($_FILES[\'image\']);
```
The result is een object that contains any errors, messages and database table id's regarding the uploads.

To display errors:
```php
if(!empty($result->error)){
	foreach($result->error as $errMsg){
		echo $errMsg;
	}
}
```

To display information, for example:
> File: img1.png was succesfully uploaded!<br/>
> File: img2.png was succesfully uploaded!<br/>
> File: test.exe is not an image. The file is removed!<br/>
> File: img3.jpg exceeds the maximum file size of: 2MB. The file is removed!<br/>
> File: img4.gif was succesfully uploaded!
```php
if(!empty($result->info)){
	foreach($result->info as $infoMsg){
		echo $infoMsg;
	}
}
```

If you need the database row ids for each succesful upload:
```php
if(!empty($result->ids)){
	foreach($result->ids as $id){
		// Do something with $id
	}
}
```

3) Display images to your visitors

Using a direct link: `http(s)://www.yourwebsite.com/image.php?id=##`
Embed in your website:
```html
<img src="image.php?id=##">
```

4) Allow visitors to download the images

Using a direct link: `http(s)://www.yourwebsite.com/download.php?id=##`

5) Deleting images
```php
<?php
require_once "config.php";
require_once "imgupload.class.php";

$img = new ImageUpload;
$id = 1;

$result = $img->deleteImage($id);

echo $result->info[0];
```

# FAQ
**I'm getting the error "The image cannot be displayed because it contains errors".**<br/>
The ImageUpload class renames all uploaded images to a random name and changes the extension to '.tmp'. In order to correctly
display the image to the visitor, it needs to send headers towards the browser to tell it what kind of file it is and how it should handle it. Headers however cannot be sent if there's already output sent towards the browser. And this is VERY strict! It can be as simple as some text or PHP echo, or as difficult a <a href="https://en.wikipedia.org/wiki/Byte_order_mark">Byte Order Marker</a> because you've edited some of the class's files with an editor that supports this. See if your editor is able to save the file with ANSI or UTF-8 encoding without a BOM.

This is also why you should use the image.php file to retrieve the image. It was coded and saved in such a way that no output is sent towards the browser before displaying the image.

**I'm getting the error "Unable to create htaccess file.".**<br/>
Make sure that the ImageUpload class has sufficient read/write permissions to your upload directory. This directory should normally be owned by your webserver user (apache for example) so it has all the rights nessesary. Do NOT chmod 777 the directory! This would make you vulnerable!

**I'm getting the error "The PHP fileinfo extension isn't loaded and ImageUpload was unable to load it for you.".**<br/>
The ImageUpload class relies on the PHP fileinfo extension to validate the image file. If this extension isn't installed or disabled, the class can't function. Please contact your server provider about this issue and ask them to install / enable it.

**I'm getting the error " 'file' exceeds the maximum file size that this server allowes to be uploaded!".**<br/>
This means that the visitor is trying to upload a file that is larger than what is allowed by the 'post_max_size' and/or 'upload_max_filesize' settings in your php.ini file. Either adjust the maximum file size allowed in your config.php to match the php.ini settings or edit your php.ini file to allow larger files to be uploaded.

**I have other questions and/or suggestions about the ImageUpload class.**<br/>
I'm always happy to hear from my users! Just make a new issue with your questions / suggestions or problems and I'll get back to you as soon as possible.
