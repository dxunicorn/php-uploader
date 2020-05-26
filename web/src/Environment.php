<?php

class Environment
{
	public static function getString( $name ){
		return ini_get($name);
	}
	
	public static function getBytes( $name ){
		return self::parseBytes(self::getString($name));
	}
	
	public static function getBoolean( $name ){
		return self::parseBoolean(self::getString($name), $name);
	}
	
	protected static function parseBytes( $text ){
		$value = filter_var($text, FILTER_SANITIZE_NUMBER_INT);
		$modifier = substr(trim($text), -1);
		switch( strtolower($modifier) ){
			case 'g':
				$value *= 1024;
			case 'm':
				$value *= 1024;
			case 'k':
				$value *= 1024;
		}
		return $value;
	}
	
	protected static function parseBoolean( $text, $name ){
		$value = strtolower($text);
		switch( $value ){
			case 'on':
			case 'yes':
			case 'true':
				return $name != 'assert.active';
		
			case 'stdout':
			case 'stderr':
				return $name != 'display_errors';
		
			default:
				return (bool)(int)$value;
		}
	}
}
?>