<?php
foreach ($config as $name => $config_param) {
	$GLOBALS[$name] = $config_param;
}
$GLOBALS['urlpatterns'] = $urlpatterns;
$GLOBALS['urlpaths'] = $urlpaths;

function base_path_project(){
	$project_dir = get_project_dir();
	$base = 'http://';
	if( isset($_SERVER['HTTPS'] ) ) {
		$base = 'https://';
	}
	$path = $base . $_SERVER['HTTP_HOST'] . $project_dir;
	return $path;
}

function export_path(){
	return $GLOBALS['EXPORT_PATH'];
}

function media_path_(){
	return $GLOBALS['MEDIA_PATH'];
}

function config_path(){
	return $GLOBALS['CONFIG_PATH'];
}

function lib_path(){
	return $GLOBALS['LIB_PATH'];
}

function urlpatterns(){
	$urlpatterns = $GLOBALS['urlpatterns'];
	return $urlpatterns;
}

function urlpaths(){
	$urlpaths = $GLOBALS['urlpaths'];
	return $urlpaths;
}

function get_controller_name(){
	if(!isset($_GET['url'])){
		$_GET['url'] = 'property/index';
	}
	if(isset($_GET['url'])){
		$url_params = $_GET['url'];
	}
	$url_params_explode = explode("/", $url_params);
	$controller_name = $url_params_explode[0];
	return $controller_name;
}

function get_project_dir(){
	$project_dir_config = rtrim($GLOBALS['project_dir'],"/");
	$project_dir_config = ltrim($project_dir_config,"/");
	$project_dir_config = '/' . $project_dir_config . '/';
	$project_dir = $project_dir_config;
	return $project_dir;
}

function root_project_dir(){
	$base = 'http://';
	if( isset($_SERVER['HTTPS'] ) ) {
		$base = 'https://';
	}
	$parent_path = parent_path();
	return $base . $_SERVER['HTTP_HOST'] . $parent_path;
}

function parent_path(){
	$project_dir = rtrim($GLOBALS['parent_directory'],"/");
	$project_dir = ltrim($project_dir,"/");
	$project_dir =  '/'.$project_dir . '/';
	return $project_dir;
}

function root_path_project(){
	$project_dir = root_project_dir();
	$base = 'http://';
	if( isset($_SERVER['HTTPS'] ) ) {
		$base = 'https://';
	}
	$path = $base . $_SERVER['HTTP_HOST'] . $project_dir;
	return $path;
}