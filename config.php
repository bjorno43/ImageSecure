<?php
# Always provide a slash behind (/) at the end of the road
define('URL', dirname('http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF']) . '/');

# set jquery, bootstrap and awesome font whether local or cdn
## cdn
      //$jquery_cdn = 'https://code.jquery.com/jquery-2.2.3.min.js';
      $jquery_cdn = 'https://code.jquery.com/jquery-3.3.1.slim.min.js';
      $popper_cdn = 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js';
# 337
 $bootstrapJS_cdn = 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js';
$bootstrapCSS_cdn = 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css';
 $ceruleanCSS_cdn = 'https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/cerulean/bootstrap.min.css';
 $fontawesome_cdn = 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css';
## 431
 $bootstrapJS_431 = 'https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js';
$bootstrapCSS_431 = 'https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css';
 $ceruleanCSS_431 = 'https://maxcdn.bootstrapcdn.com/bootswatch/4.3.1/cerulean/bootstrap.min.css';
 $fontawesome_510 = 'https://use.fontawesome.com/releases/v5.1.0/css/all.css';
## local
            $sumber = 'sumber/utama/';
      $jquery_local = $sumber . 'jquery/jquery-2.2.3.min.js';
	  $popper_local = null;
 $bootstrapJS_local = $sumber . 'bootstrap/3.3.7/js/bootstrap.min.js';
$bootstrapCSS_local = $sumber . 'bootstrap/3.3.7/css/bootstrap.min.css';
 $fontawesome_local = $sumber . 'font-awesome/4.7.0/css/font-awesome.min.css';

##################################################
#             Database configuration             #
##################################################
# DB_HOST:  The MySQL server to connect to       #
# DB_USER:  The MySQL server username            #
# DB_PASS:  The MySQL server password            #
# DB_NAME:  The MySQL server database            #
# DB_TABLE: The MySQL server table to create/use #
############################################################################################

$ip = $_SERVER['REMOTE_ADDR'];
$hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
$server = $_SERVER['SERVER_NAME'];

/*
echo "<br>Alamat IP : <font color='red'>" . $ip . "</font> |
\r<br>Nama PC : <font color='red'>" . $hostname . "</font> |
\r<br>Server : <font color='red'>" . $server . "</font>\r";
//*/

if ($server == 'your.web.site')
{	# isytihar tatarajah mysql
	define('DB_TYPE',  'mysql');
	define('DB_HOST',  'localhost');
	define('DB_NAME',  '***');
	define('DB_TABLE', '***');
	define('DB_USER',  '***');
	define('DB_PASS',  '***');
	# isytihar lokasi folder js
	define('SUMBER', 'http://' . $_SERVER['SERVER_NAME'] . '/sumberonline/');
	define('CSS_ARRAY_CDN', serialize(
		array ($bootstrapCSS_431,$fontawesome_510)
	));
	define('JS_ARRAY_CDN', serialize(
		array ($jquery_cdn,$popper_cdn,$bootstrapJS_431)
	));
}
else
{	# isytihar tatarajah mysql
	define('DB_TYPE',  'mysql');
	define('DB_HOST',  'localhost');
	define('DB_NAME',  '***');
	define('DB_TABLE', '***');
	define('DB_USER',  '**');
	define('DB_PASS',  '***');
	# isytihar lokasi folder js
	define('SUMBER', 'http://' . $_SERVER['SERVER_NAME'] . '/sumberoffline/');
	define('CSS_ARRAY', serialize(
		array ($bootstrapCSS_local,$fontawesome_local)
	));
	define('JS_ARRAY', serialize(
		array ($jquery_local,$popper_local,$bootstrapJS_local)
	));
	define('CSS_ARRAY_CDN', serialize(
		array ($bootstrapCSS_431,$fontawesome_510)
	));
	define('JS_ARRAY_CDN', serialize(
		array ($jquery_cdn,$popper_cdn,$bootstrapJS_431)
	));
}
//echo DB_HOST . "," . DB_USER . "," . DB_PASS . ",," . DB_NAME . "<br>";
############################################################################################

##################################################
#              Folder configuration              #
##################################################
# R_PATH: The absolute path. Don't change this   #
#   unless you know what you're doing!           #
# F_PATH: The folder to store images             #
#                                                #
#   INFO: This folder is relative to the         #
#   location of your form upload                 #
#   handler. (eg: upload.php)                    #
# H_FILE: Change this to 'true' if you want      #
#   to create/use a .htaccess file to            #
#   protect your images folder.                  #
#                                                #
#   WARNING: htaccess files are not 100%         #
#   reliable! It's STRONGLY advised to           #
#   use a folder outside of your                 #
#   document root instead! This option           #
#   is only there for those who are              #
#   unable to do so and therefor have no         #
#   other choice but to rely on                  #
#   htaccess!                                    #
##################################################

define('R_PATH', __DIR__);
define('F_PATH', R_PATH . '/images');
define('H_FILE', false);

##################################################
#              File configuration                #
##################################################
# F_SIZE: The maximum file size in KB or MB      #
#   Example: 512K / 2M                           #
#                                                #
#   WARNING: Make sure to check the              #
#   values of 'post_max_size' and                #
#   'upload_max_filesize' in your                #
#   php.ini file! This setting should            #
#   not be larger than either of those!          #
##################################################

define('F_SIZE', '4M');