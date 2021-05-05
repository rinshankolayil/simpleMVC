<?php

function base_path($css_root){
	if($css_root != ''){
		$path = base_path_project();// defined in config/settings.php
		$css_path = $path . 'static/' . $css_root;
		return $css_path;
	}
}

function media_path($root){
	if($root != ''){
		$path = base_path_project();// defined in config/settings.php
		$media_path = $path . 'media/' . $root;
		return $media_path;
	}
	
}

function image_path($root){
	if($root != ''){
		$path = base_path_project();// defined in config/settings.php
		$media_path = $path . $path . 'media/' . $root;
		return $media_path;
	}
	
}

function url($controller_method,$vars=array()){
	$path = base_path_project();// defined in config/settings.php
	$path_ex = explode(".", $controller_method);
	$controller = $path_ex[0];
	$method = $path_ex[1];
	$vars_string = '';
	if(count($vars) > 0){
		$vars_string = '/'.implode("/", $vars);
	}
	return $path . $controller . '/' . $method .$vars_string;

}

function url_parent($controller_method,$vars=array()){
	$path = root_project_dir();// defined in config/settings.php
	$controller = $controller_method;
	$vars_string = '';
	if(count($vars) > 0){
		$vars_string = '/'.implode("/", $vars);
	}
	$controller_split = explode(".", $controller);
	if(count($controller_split) > 1){

	}
	else{
		$controller = $controller . '.php';
	}
	return $path . $controller .$vars_string;

}

function back_url($root=''){
	$path = root_path_project();// defined in config/settings.php
	if(isset($root) && strlen(trim($root)) > 0){
		return $path . $root . '.php';
	}
	else{
		return $path;
	}
}
function hasFlash(){
	if(isset($_SESSION['flash_message'])){
		return true;
	}
	else{
		return false;
	}
}
function Flash(){
	if(isset($_SESSION['flash_message'])){
		
		if(is_array($_SESSION['flash_message'][key($_SESSION['flash_message'])])){
			echo '<div class="alert alert_hider alert-"'.key($_SESSION['flash_message']).'>';
			$status = strtoupper(key($_SESSION['flash_message']));
			$message = $_SESSION['flash_message'][key($_SESSION['flash_message'])];
			$message = str_replace("<strong>", "", $message);
			$message = str_replace("</strong>", "", $message);
			$message = str_replace($status, "", $message);
			$new_message = '<strong>' . $status . '! </strong>' . $message;
			print_r($new_message);
			echo '</div>';
		}
		else{
			echo '<div class="alert alert_hider alert-'.key($_SESSION['flash_message']).'">';
			$status = strtoupper(key($_SESSION['flash_message']));
			$message = $_SESSION['flash_message'][key($_SESSION['flash_message'])];
			$message = str_replace("<strong>", "", $message);
			$message = str_replace("</strong>", "", $message);
			$message = str_replace($status, "", $message);
			$new_message = '<strong>' . $status . '! </strong>' . $message;
			echo $new_message; 
			echo '</div>';
		}
	}
	if(isset($_SESSION['flash_message'])){
		unset($_SESSION['flash_message']);
	}
}