<?php

##################################################
#             Database configuration             #
##################################################
# DB_HOST:	The MySQL server to connect to		 #
# DB_USER:	The MySQL server username			 #
# DB_PASS:	The MySQL server password			 #
# DB_NAME:	The MySQL server database			 #
# DB_TABLE:	The MySQL server table to create/use #
##################################################

define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASS", "");
define("DB_NAME", "imagesecure");
define("DB_TABLE", "images");

##################################################
#              Folder configuration              #
##################################################
# R_PATH:	The absolute path. Don't change this #
#			unless you know what you're doing!	 #
# F_PATH:	The folder to store images			 #
#												 #
#			INFO: This folder is relative to the #
#			location of your form upload		 #
#			handler. (eg: upload.php)			 #
# H_FILE:	Change this to 'true' if you want    #
#			to create/use a .htaccess file to    #
#			protect your images folder.			 #
#												 #
#			WARNING: htaccess files are not 100% #
#			reliable! It's STRONGLY advised to   #
#			use a folder outside of your		 #
#			document root instead! This option   #
#			is only there for those who are		 #
#			unable to do so and therefor have no #
#			other choice but to rely on			 #
#			htaccess!							 #
##################################################

define("R_PATH", __DIR__);
define("F_PATH", R_PATH.'/images');
define("H_FILE", false);

##################################################
#              File configuration                #
##################################################
# F_SIZE:	The maximum file size in KB or MB	 #
#			Example: 512K / 2M					 #
#												 #
#			WARNING: Make sure to check the		 #
#			values of 'post_max_size' and		 #
#			'upload_max_filesize' in your		 #
#			php.ini file! This setting should	 #
#			not be larger than either of those!	 #
##################################################

define("F_SIZE", "4M");

?>
