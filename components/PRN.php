<?php


class PRN {

	public static function PR($array = array()){
		echo '<pre>';
		print_r($array);
		echo '</pre>';
	}

	public static function PRE($array = array()){
		if(isset($array)){
			echo '<pre>';
			print_r($array);
			echo '</pre>';
			
		}
		exit;
		
	}

	public static function EE($data){
		if(isset($data)){
			echo $data;
		echo '<br>';
		
		}
		exit;
	}
	public static function E($data){
		echo $data;
		echo '<br>';
	}

	public static function JA($json=''){

		$json_decode = json_decode($json,true);
		echo '<pre>';
		print_r($json_decode);
		echo '</pre>';
	}

	public static function JAE($json=''){
		$json_decode = json_decode($json,true);
		echo '<pre>';
		print_r($json_decode);
		echo '</pre>';
		exit;
	}

	public static function J($json = ''){
		echo $json;
	}

	public static function JEX($json = ''){
		echo $json;
		exit;
	}

	public static function JE($arr = array()){
		$json_encode = json_encode($arr,true);
		echo $json_encode;
	}

	public static function JEE($arr = array()){
		$json_encode = json_encode($arr,true);
		echo $json_encode;
		exit;
	}
}
