<?php
class App
{

	function __construct()
	{
		if (isset($_GET['url'])) {
			$url_params = $_GET['url'];
			$url_params_explode = array_filter(explode("/", $url_params));
			$controller = $url_params_explode[0];
			$count_url = count($url_params_explode);
			$params = array();
			if ($count_url > 1) {
				$method = $url_params_explode[1];
				if (count($url_params_explode) > 2) {
					$params = array_slice($url_params_explode, 2, (count($url_params_explode) - 1));
				}
			} else {
				$method = 'index';
			}

			$this->include_controllers($controller, $method, $params);
		} else {
			$this->include_controllers('', '', '');
		}
	}

	public function include_controllers($controller, $method, $params)
	{

		include_once('config/imports.php');
		foreach ($imports['default_imports'] as $key => $path) {
			$new_path = str_replace(".", "/", $path);
			$new_path = $new_path . '.php';
			require_once $new_path;
		}
		if ($controller == '' && $method == '') {
			$controller_include = strtolower($config['defaultcontroller']);
			$call_controller = ucfirst($config['defaultcontroller']);
		} else {
			$controller_include = strtolower($controller);
			$call_controller = ucfirst($controller);
		}
		require_once('controllers/' . $controller_include . 'controller.php');
		$classname = $call_controller . 'Controller';
		$classname_lower = strtolower($classname);
		$_SESSION['url_name_view'] = $method;
		if ($classname_lower != 'settingscontroller' && $classname_lower != 'default_viewscontroller') {
			if (isset($_SESSION['parent_controller_modules'])) {
				unset($_SESSION['parent_controller_modules']);
			}
			$_SESSION['parent_controller_modules'] = $controller;
		}
		
		// $csrf_middleware_token_key = bin2hex(openssl_random_pseudo_bytes(64));
		// $csrf_middleware_token_value = bin2hex(openssl_random_pseudo_bytes(64));
		$csrf_middleware_token_key = '';
		$csrf_middleware_token_value = '';
		$_SESSION['csrf_middleware_token'] = $csrf_middleware_token_key;
		$_SESSION['csrf_middleware_token_value'] = $csrf_middleware_token_value;
		$_SESSION['url_params'] = $params;

		$class_call = new $classname();
		if (method_exists($class_call, $method)) {
			$class_call->url_name_view = $method;
		} else {
			$method = 'controller_not_found';
		}
		$class_call->url_params = $params;
		$class_call->$method();
	}
}
