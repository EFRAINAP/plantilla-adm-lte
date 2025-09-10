<?php

class  Media {

	public $imageInfo;
	public $fileName;
	public $fileType;
	public $fileTempPath;
	//Set destination for upload
	public $userPath = SITE_ROOT.DS.'..'.DS.'Documentos/01_Imagenes';

	public $errors = array();
	public $upload_errors = array(
		0 => 'There is no error, the file uploaded with success',
		1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
		2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
		3 => 'The uploaded file was only partially uploaded',
		4 => 'Ningun archivo fue subido',
		6 => 'Missing a temporary folder',
		7 => 'Failed to write file to disk.',
		8 => 'A PHP extension stopped the file upload.'
		);
	public$upload_extensions = array(
		'gif',
		'jpg',
		'jpeg',
		'png',
		);
	public function file_ext($filename){
		$ext = strtolower(substr( $filename, strrpos( $filename, '.' ) + 1 ) );
		if(in_array($ext, $this->upload_extensions)){
			return true;
		}
	}
	public function upload($file){
		if(!$file || empty($file) || !is_array($file)):
			$this->errors[] = "Ningún archivo subido.";
			return false;
		elseif($file['error'] != 0):
			$this->errors[] = $this->upload_errors[$file['error']];
			return false;
		elseif(!$this->file_ext($file['name'])):
			$this->errors[] = 'Formato de archivo incorrecto ';
			return false;
		else:
			$this->imageInfo = getimagesize($file['tmp_name']);
			$this->fileName  = basename($file['name']);
			$this->fileType  = $this->imageInfo['mime'];
			$this->fileTempPath = $file['tmp_name'];
			return true;
		endif;
	}

	/*--------------------------------------------------------------*/
	/* Function for Process user image
	/*--------------------------------------------------------------*/
	public function process_user($username){

		if(!empty($this->errors)){
			return false;
		}
		if(empty($this->fileName) || empty($this->fileTempPath)){
			$this->errors[] = "La ubicación del archivo no estaba disponible.";
			return false;
		}
		if(!is_writable($this->userPath)){
			$this->errors[] = $this->userPath." Debe tener permisos de escritura";
			return false;
		}
		if(!$username){
			$this->errors[] = " Username de usuario ausente.";
			return false;
		}
		$ext = explode(".",$this->fileName);
		$new_name = randString(8).$username.'.' . end($ext);
		$this->fileName = $new_name;
		
		if($this->user_image_destroy($username)){
			if(move_uploaded_file($this->fileTempPath,$this->userPath.'/'.$this->fileName)){
				if($this->update_userImg($username)){
				unset($this->fileTempPath);
				return true;
				}
			} else {
			 $this->errors[] = "Error en la carga del archivo, posiblemente debido a permisos incorrectos en la carpeta de carga.";
			 return false;
			}
		}
	}
	/*--------------------------------------------------------------*/
	/* Function for Update user image
	/*--------------------------------------------------------------*/
	private function update_userImg($username){
	global $db;
		$sql = "UPDATE users SET";
		$sql .=" image='{$db->escape($this->fileName)}'";
		$sql .=" WHERE username='{$db->escape($username)}'";
		$result = $db->query($sql);
		return ($result && $db->affected_rows() === 1 ? true : false);
	}
	/*--------------------------------------------------------------*/
	/* Function for Delete old image
	/*--------------------------------------------------------------*/
	public function user_image_destroy($username){
		$image = find_table_field_array('users','username', $username);
		if($image['image'] === 'no_image.jpg')
		{
		return true;
		} else {
		unlink($this->userPath.'/'.$image['image']);
		return true;
		}
	}
	/*--------------------------------------------------------------*/
	/* Function for insert media image
	/*--------------------------------------------------------------*/
	private function insert_media(){
		global $db;
		$sql  = "INSERT INTO media ( file_name,file_type )";
		$sql .=" VALUES ";
		$sql .="(
			  '{$db->escape($this->fileName)}',
			  '{$db->escape($this->fileType)}'
			  )";
		return ($db->query($sql) ? true : false);
	}	
}

?>