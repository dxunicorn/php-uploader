<?php

class Limits
{
	public static function getMaxUploadFileSize(){
		$values = array(
			Environment::getBytes('upload_max_filesize'),
			Environment::getBytes('post_max_size'),
			Config::get('max_upload_size'),
		);
		
		$values = array_filter($values, function($v){
			return $v > 0;
		});
		
		return empty($values) ? 0 : min($values);
	}
}