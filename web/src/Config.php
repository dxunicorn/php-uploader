<?php

class Config
{
	const INI_PATH = 'config.ini';
	
	protected static $data = null;
	
	public static function get( $name, $section = 'global' ){
		self::lazyload();
		$data = self::getSection( $section );
		return array_key_exists($name, $data) ? $data[$name] : '';
	}
	
	public static function getSection( $name ){
		self::lazyload();
		return array_key_exists($name, self::$data) ? self::$data[$name] : array();
	}
	
	protected static function lazyload(){
		if( !is_null(self::$data) ) return;
		self::$data = parse_ini_file(Config::INI_PATH, true);
		if( empty(self::$data) ) self::$data = array();
	}
}