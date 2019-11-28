<?php
##########################################
# ImageUpload Class                      #
#   version : 0.1 Beta                   #
# Created by: icecub                     #
# Update by : https://github.com/amin007 #
##########################################

Class ImageUpload
{
###################################################################################################
#--------------------------------------------------------------------------------------------------
	private $host = DB_HOST;
	private $user = DB_USER;
	private $pass = DB_PASS;
	private $dbname = DB_NAME;
	private $table = DB_TABLE;

	private $dbh;
	private $error = array();
	private $info = array();
	private $ids = array();
	private $obj;

	private $stmt;

	private $mtype;

	private $folder = F_PATH;
	private $htaccess = H_FILE;
	private $f_size = F_SIZE;
#--------------------------------------------------------------------------------------------------
	# Set up a PDO instance
	public function __construct()
	{
		# Set DSN
		$dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
		# Set Set options
        $options = array(
			PDO::ATTR_PERSISTENT => true,
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );
		# Create a new PDO instance
        try{
			$this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        }
		# Catch any errors
        catch(PDOException $e)
		{
			array_push($this->error, $e->getMessage());
			$this->obj->error = $this->error;
			return $this->obj;
		}
		#
		$this->obj = new StdClass;
	}
#--------------------------------------------------------------------------------------------------
	# Custom bindParam function
	private function bind($param, $value, $type = null)
	{
		if (is_null($type))
		{
			switch (true)
			{
				case is_int($value):
					$type = PDO::PARAM_INT;
					break;
				case is_bool($value):
					$type = PDO::PARAM_BOOL;
					break;
				case is_null($value):
					$type = PDO::PARAM_NULL;
					break;
				default:
					$type = PDO::PARAM_STR;
            }
		}
		#
		$this->stmt->bindValue($param, $value, $type);
	}
#--------------------------------------------------------------------------------------------------
	# Checks if the table already exists. If not, creates one
	private function createTable()
	{
		# Check if table already exists
		$this->stmt = $this->dbh->prepare("SHOW TABLES LIKE '". DB_TABLE ."'");

		try{ $this->stmt->execute(); }
		catch(PDOException $e)
		{
			array_push($this->error, $e->getMessage());
			return false;
		}

		$cnt = $this->stmt->rowCount();

		if($cnt > 0) { return true; }
		else
		{
			# Create table
			$this->stmt = $this->dbh->prepare("
				CREATE TABLE `". DB_TABLE ."` (
					`id` INT(11) NOT NULL AUTO_INCREMENT,
					`name` VARCHAR(64) NOT NULL,
					`original_name` VARCHAR(64) NOT NULL,
					`mime_type` VARCHAR(20) NOT NULL,
					PRIMARY KEY (`id`)
				) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;");
			try{
				$this->stmt->execute();
				return true;
			}
			catch(PDOException $e){
				array_push($this->error, $e->getMessage());
				return false;
			}
		}
		#
	}
#--------------------------------------------------------------------------------------------------
	# Checks if the htaccess file exists. If not, creates one
	private function createHtaccess()
	{
		if (!file_exists($this->folder . '/.htaccess'))
		{
			try
			{
				$file = fopen($this->folder . '/.htaccess',"w");
				$txt  = "order deny,allow\n";
				$txt .= "deny from all\n";
				$txt .= "allow from 127.0.0.1";
				fwrite($file, $txt);
				fclose($file);
				return true;
			}
			catch (Exception $e) { return false; }
		}
		else { return true; }
		#
	}
#--------------------------------------------------------------------------------------------------
	# Checks if required PHP extensions are loaded. Tries to load them if not
	private function check_phpExt()
	{
		if (!extension_loaded('fileinfo'))
		{
			# dl() is disabled in the PHP-FPM since php7 so we check if it's available first
			if(function_exists('dl'))
			{
				if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
				{
					if (!dl('fileinfo.dll')) { return false; }
					else { return true; }
				}
				else
				{
					if (!dl('fileinfo.so')) { return false; }
					else { return true; }
				}
			}
			else { return false; }
		}
		else { return true; }
		#
	}
#--------------------------------------------------------------------------------------------------
	# Creates a file with a random name
	private function tempnam_sfx($path, $suffix)
	{
		do {
			$file = $path . "/" . mt_rand() . $suffix;
			$fp = @fopen($file, 'x');
		}
		while(!$fp);

		fclose($fp);
		return $file;
	}
#--------------------------------------------------------------------------------------------------
	# Checks the true mime type of the given file
	private function check_img_mime($tmpname)
	{
		$finfo = finfo_open( FILEINFO_MIME_TYPE );
		$mtype = finfo_file( $finfo, $tmpname );
		$this->mtype = $mtype;
		if(strpos($mtype, 'image/') === 0){ return true; }
		else { return false; }
		finfo_close( $finfo );
	}
#--------------------------------------------------------------------------------------------------
	# Checks if the image isn't to large
	private function check_img_size($tmpname)
	{
		$size_conf = substr(F_SIZE, -1);
		$max_size = (int)substr(F_SIZE, 0, -1);

		switch($size_conf)
		{
			case 'k':
			case 'K':
				$max_size *= 1024;
				break;
			case 'm':
			case 'M':
				$max_size *= 1024;
				$max_size *= 1024;
				break;
			default:
				$max_size = 1024000;
		}

		if(filesize($tmpname) > $max_size){ return false; }
		else { return true; }
		#
	}
#--------------------------------------------------------------------------------------------------
	# Re-arranges the $_FILES array
	private function reArrayFiles($files)
	{
		$file_ary = array();
		$file_count = count($files['name']);
		$file_keys = array_keys($files);

		for ($i=0; $i<$file_count; $i++)
		{
			foreach ($file_keys as $key)
			{
				$file_ary[$i][$key] = $files[$key][$i];
			}
		}

		return $file_ary;
	}
#--------------------------------------------------------------------------------------------------
	# Handles the uploading of images
	public function uploadImages($files)
	{
		# Checks if the required PHP extension(s) are loaded
		if($this->check_phpExt()){
			# Checks if db table exists. Creates it if nessesary
			if($this->createTable()){
				# Checks if a htaccess file should be created and creates one if needed
				if($this->htaccess){
					if(!$this->createHtaccess()){
						array_push($this->error, "Unable to create htaccess file.");
						$this->obj->error = $this->error;
						return $this->obj;
					}
				}
				
				# Re-arranges the $_FILES array
				$files = $this->reArrayFiles($files);
				foreach($files as $file){
					# Checks if $file['tmp_name'] is empty. This occurs when a file is bigger than
					# allowed by the 'post_max_size' and/or 'upload_max_filesize' settings in php.ini
					if(!empty($file['tmp_name'])){
						// Checks the true MIME type of the file
						if($this->check_img_mime($file['tmp_name'])){
							// Checks the size of the the image
							if($this->check_img_size($file['tmp_name'])){
								// Creates a file in the upload directory with a random name
								$uploadfile = $this->tempnam_sfx($this->folder, ".tmp");
								
								// Moves the image to the created file
								if (move_uploaded_file($file['tmp_name'], $uploadfile)) {
									// Inserts the file data into the db
									$this->stmt = $this->dbh->prepare("INSERT INTO ". DB_TABLE ." (name, original_name, mime_type) VALUES (:name, :oriname, :mime)");

									$this->bind(':name', basename($uploadfile));
									$this->bind(':oriname', basename($file['name']));
									$this->bind(':mime', $this->mtype);

									try{
										$this->stmt->execute();
									}
									catch(PDOException $e){
										array_push($this->error, $e->getMessage());
										$this->obj->error = $this->error;
										return $this->obj;
									}
									
									array_push($this->ids, $this->dbh->lastInsertId());
									array_push($this->info, "File: ". $file['name'] ." was succesfully uploaded!");

									continue;
								} else {
									unlink($file['tmp_name']);
									array_push($this->info, "Unable to move file: ". $file['name'] ." to target folder. The file is removed!");
								}
							} else {
								array_push($this->info, "File: ". $file['name'] ." exceeds the maximum file size of: ". F_SIZE ."B. The file is removed!");
							}
						} else {
							unlink($file['tmp_name']);
							array_push($this->info, "File: ". $file['name'] ." is not an image. The file is removed!");
						}
					} else {
						array_push($this->info, "File: ". $file['name'] ." exceeds the maximum file size that this server allowes to be uploaded!");
					}
				}
				// Checks if the error array is empty
				foreach ($this->error as $key => $value) {
					if (empty($value)) {
					   unset($this->error[$key]);
					}
				}
				if (empty($this->error)) {

					$this->obj->info = $this->info;
					$this->obj->ids = $this->ids;
					
					return $this->obj;
				} else {
					$this->error = array_unique($this->error);
					$this->obj->error = $this->error;
					return $this->obj;
				}
			} else {
				if($this->error !== NULL){
					$this->obj->error = $this->error;
					return $this->obj;
				} else {
					// This should never happen, but it's here just in case
					array_push($this->error, "Unknown error! Failed to load ImageUpload class!");
					$this->obj->error = $this->error;
					return $this->obj;
				}
			}
		} else {
			array_push($this->error, "The PHP fileinfo extension isn't loaded and "
			. "ImageUpload was unable to load it for you.");
			$this->obj->error = $this->error;
			return $this->obj;
		}
	}
#--------------------------------------------------------------------------------------------------
	# Show the image in the browser
	public function showImage($id)
	{
		$this->stmt = $this->dbh->prepare("SELECT name, original_name, mime_type "
		. "FROM ". DB_TABLE ." WHERE id=:id");
		$this->bind(':id', $id);

		try{
			$this->stmt->execute();
			$result = $this->stmt->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e){
			array_push($this->error, $e->getMessage());
			$this->obj->error = $this->error;
			return $this->obj;
		}

		$newfile = $result['original_name'];

		# Send headers and file to visitor for display
		header("Content-Type: " . $result['mime_type']);
		readfile(F_PATH . '/' . $result['name']);
	}
#--------------------------------------------------------------------------------------------------
	# Force a download of the image
	public function downloadImage($id)
	{
		$this->stmt = $this->dbh->prepare("SELECT name, original_name, mime_type FROM "
		. DB_TABLE . " WHERE id=:id");

		$this->bind(':id', $id);

		try{
			$this->stmt->execute();
			$result = $this->stmt->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e){
			array_push($this->error, $e->getMessage());
			$this->obj->error = $this->error;
			return $this->obj;
		}

		$newfile = $result['original_name'];

		# Send headers and file to visitor for download
		header('Content-Description: File Transfer');
		header('Content-Disposition: attachment; filename='.basename($newfile));
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize(F_PATH.'/'.$result['name']));
		header("Content-Type: " . $result['mime_type']);
		readfile(F_PATH.'/'.$result['name']);
	}
#--------------------------------------------------------------------------------------------------
	# Delete an image
	public function deleteImage($id)
	{
		$this->stmt = $this->dbh->prepare("SELECT name, original_name, FROM "
		. DB_TABLE ." WHERE id=:id");

		$this->bind(':id', $id);

		try{
			$this->stmt->execute();
			$result = $this->stmt->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e){
			array_push($this->error, $e->getMessage());
			$this->obj->error = $this->error;
			return $this->obj;
		}

		unlink(F_PATH.'/'.$result['name']);

		$this->stmt = $this->dbh->prepare("DELETE FROM " . DB_TABLE . " WHERE id=:id");
		$this->bind(':id', $id);

		try{
			$this->stmt->execute();
		}
		catch(PDOException $e){
			array_push($this->error, $e->getMessage());
			$this->obj->error = $this->error;
			return $this->obj;
		}

		array_push($this->info, "File: ". $result['original_name'] ." succesfully deleted.");
		$this->obj->info = $this->info;
		return $this->obj;
	}
#--------------------------------------------------------------------------------------------------
###################################################################################################
}