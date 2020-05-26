<?php

class App
{
	public function __construct(){
		spl_autoload_register(array($this, 'classLoader'));
	}
	
	public function getTitle(){
		return Config::get('name');
	}

	public function getJsParams(){
		return json_encode(array(
			'messages' => Config::getSection('messages'),
			'limits' => array(
				'fileSize' => Limits::getMaxUploadFileSize(),
			),
		), JSON_HEX_TAG);
	}

	public function generateIconsBundle( $path ){
		$output = '';
		$files = glob($path.'*.svg');
		foreach( $files as $file ){
			$output .= file_get_contents($file);
		}
		return '<div style="display:none">'.$output.'</div>';
	}
	
	public function recieveFile(){
		if( empty($_FILES['file']) ) throw new Exception('Empty file data');
		
		$uploadDir = Config::get('upload_path');
		if( !is_dir($uploadDir) ) mkdir($uploadDir, 0644);
		
		$source = $_FILES['file']['tmp_name'];
		$filename = basename($_FILES['file']['name']);
		$filename = $this->checkRestrictedExtension($filename);
		$filename = $this->checkFileExist( $filename, $uploadDir );
		$filename = ltrim( $filename, '.' );
		$target = $uploadDir.$filename;
		
		if( !move_uploaded_file($source, $target) ) throw new Exception('File save error');
		
		return $target;
	}
	
	private function checkRestrictedExtension( $filename ){
		$list = explode(',', Config::get('blacklist'));
		$extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
		if( in_array($extension, $list) ) $filename .= '.'.Config::get('safe_extension');
		return $filename;
	}
	
	private function checkFileExist( $filename, $dir ){
		$parts = pathinfo($filename);
		$basename = $parts['filename'];
		$extension = $parts['extension'];
		$newFilename = $filename;
		for( $i = 1; $i < PHP_INT_MAX; $i++ ){
			if( !file_exists($dir.$newFilename) ){
				return $newFilename;
			}
			$newFilename = $basename.'('.$i.').'.$extension;
		}
		throw new Exception('Duplicate file save error');
	}
	
	private function classLoader($class){
		require __DIR__.'\\'.$class.'.php';
	}
}