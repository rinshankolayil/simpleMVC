<?php

class MainController extends  StatusCodes
{

	public $TESTMODE;
	public $restmodalsettings;
	public $controller_name;
	public $project_dir;
	public $base_path;
	public $layout;
	protected $lib;
	public $common_to_all;
	protected $page_security_check;
	protected $security_pages_array;
	protected $test_page_layout;
	public $user;
	public $url_params = array();
	public $module_name;
	public $export_path;
	public $user_id_name = 'UserID';
	public $user_login_authenticate;
	public $urlpatterns = array();
	public $urlpaths = array();
	public $menu_display_layout;
	public $url_security_check;
	public $check_csrf;
	public $csrfmiddlewaretoken;
	public $csrfmiddlewaretoken_input;
	public $lib_path;
	public $user_cateogory_admin;
	function __construct()
	{
		parent::__construct();
		date_default_timezone_set('Asia/Qatar');
		$this->url_security_check = true;
		$this->restmodalsettings = new RestModalSettings();
		$_SESSION['config_path'] = config_path();
		if (isset($_SESSION[$this->user_id_name])) {
			$this->user = $_SESSION['UserID'];
		}
		$this->urlpatterns = urlpatterns();
		$this->urlpaths = urlpaths();
		$_SESSION['lib_path'] = lib_path();
		$this->controller_name = get_controller_name(); // defined in config/settings.php
		$this->lib = new Library();
		$this->common_to_all = array(); // dont use equal to assignment operator - always use key value pair method
		// example $this->common_to_all['key'] = 'value'; 
		// For reference please go and visit $this->get_notfications(); function
		$this->page_security_check = true;
		$this->export_path = export_path();
		$this->base_path = base_path_project(); // defined in config/settings.php
		$this->security_pages_array = array();
		$this->test_page_layout = 'test_page_layout';
		$this->import_models();
		$this->module_name = $_SESSION['parent_controller_modules'];
		$this->url_name_view = $_SESSION['url_name_view'];
		$this->csrfmiddlewaretoken = $_SESSION['csrf_middleware_token'];
		$this->csrfmiddlewaretoken_input = $this->csrf_token($this->csrfmiddlewaretoken);
		$this->user_cateogory_admin = 'user';
		$this->check_csrf = false;
	}

	public function auto_logout()
	{
		if (time() - $_SESSION['timestamp_login'] > 3600) { //subtract new timestamp from the old one
			$this->redirect('login.logout');
		} else {
			$_SESSION['timestamp_login'] = time(); //set new timestamp
		}
	}

	public function csrf_token()
	{
		return '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_middleware_token'] . '">';
	}

	public function error_page_csrf()
	{
		$this->render('default_views.error_security_privileges');
		exit;
	}

	public function user_none()
	{
		$array_send['main_menu_hidden'] = "true";
		$this->render('default_views.user_none', $array_send);
		exit;
	}

	public function menu_display_func($main_menu, $main_menu_url, $submenu)
	{
		$url_name_view = $_SESSION['url_name_view'];
		if (isset($this->urlpaths[$url_name_view])) {
			$path = $this->urlpaths[$url_name_view]['path'];
			$category = $this->urlpaths[$url_name_view]['category'];
			$category_explode = explode(",", $category);
			$path_explode = explode(",", $path);
			$menu_key = '';
			$path_append = '
			<div class="container-full">
				<div class="row mb-3">
					<div class="col text-left">&nbsp;';
			foreach ($category_explode as $key => $categories) {
				if ($categories == 'menu') {
					$array_menu = $submenu['head'];
					$path_ = $path_explode[$key];
					$menu_key = array_search($path_, $array_menu);
					$path_append .= '<span class="c-b"> ' . $path_ . '</span>  ';
				} else if ($categories == 'submenu') {
					$array_submenu = $submenu['submenu'];
					$submenu_arr = $array_submenu[$menu_key];
					$path_sub_ = $path_explode[$key];
					$sub_menu_key = array_search($path_sub_, $submenu_arr);
					$array_submenu_urls = $submenu['submenu_url'];
					$sub_menu_url = $array_submenu_urls[$menu_key][$sub_menu_key];
					// $sub_menu_url = 
					$path_append = rtrim($path_append, "/");
					$path_append .= ' / <a class="nv-link" href="' . url($sub_menu_url) . '">' . $path_sub_ . '</a>';
				} else if ($categories == 'controller') {
					$path_controller_ = $path_explode[$key];
					$url_controller = $_SESSION['parent_controller_modules'] . '.' . $url_name_view;
					$path_append = rtrim($path_append, "/");
					$path_append .= ' / <a class="nv-link" href="' . url($url_controller, $_SESSION['url_params']) . '">' . $path_controller_ . '</a>';
				}
			}
			$path_append .= '
					</div>
				</div>
			</div>';
			return $path_append;
		} else {

			if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			} else {
				if ($_SESSION['user_cateogory_admin'] == 'admin') {
					$this->setFlashStatus(array(
						'status' => 'info',
						'message' => 'Please set up Path to this URL in <b>config/url_path.php',
					));
				}
			}
			return '';
		}
	}

	public function url_security_check()
	{
		if ($this->url_security_check == true) {
			$url_name_view = $this->url_name_view;
			$urlpatterns = $this->urlpatterns;
			$user_cateogory_admin = $_SESSION['user_cateogory_admin'];
			if (isset($_SESSION['page_permissions_user'])) {
				$page_permissions_user = $_SESSION['page_permissions_user'];
				$namespaces = array_keys($page_permissions_user);
				if (!isset($urlpatterns[$url_name_view])) {

					if ($user_cateogory_admin != 'admin') {
						$this->url_error($url_name_view, $namespaces, $page_permissions_user, $user_cateogory_admin);
					} else {
						$this->url_not_set($url_name_view, $namespaces, $page_permissions_user, $user_cateogory_admin);
					}
				} else {

					if (isset($urlpatterns[$url_name_view]['params'])) {
						$params = (int) $urlpatterns[$url_name_view]['params'];
						if (count($_SESSION['url_params']) < $params) {

							$this->url_error($url_name_view, $namespaces, $page_permissions_user, $user_cateogory_admin, $params);
						}
					}
					if (isset($urlpatterns[$url_name_view]['permitted'])) {

						$permitted = $urlpatterns[$url_name_view]['permitted'];
						if ($permitted != 'true' && $user_cateogory_admin != 'admin') {
							if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

								$flash = array(
									'status' => 'warning',
									'message' => 'Please configure URL permission in <b>config/urls.php</b>',
								);
								if ($user_cateogory_admin != 'admin') {
									$flash['message'] = '
							 		<div class="text-center">
							 			Please contact IT Support
							 		</div>';
								}
								echo json_encode($flash, true);
								exit;
							} else {
								$array_send['url_name_view'] = $url_name_view;
								$this->error_page();
								exit;
							}
						}
					} else {
						if (isset($urlpatterns[$url_name_view]['namespace'])) {
							$category = $urlpatterns[$url_name_view]['category'];
							$namespace = $urlpatterns[$url_name_view]['namespace'];
							$namespace_permissions = $page_permissions_user[$namespace];

							if (!in_array($category, $namespace_permissions)) {

								$this->url_error($url_name_view, $namespaces, $page_permissions_user, $user_cateogory_admin);
							}
						}
					}
				}
			}
		}
	}

	public function controller_not_found()
	{
		if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			$flash = array(
				'method' => $_SESSION['url_name_view'],
				'controller' => $_SESSION['parent_controller_modules'] . 'Controller',
				'message' => 'Please declare ' . strtoupper($_SESSION['url_name_view']) . ' under controllers/' .  $_SESSION['parent_controller_modules'] . 'controller.php',
			);
			if ($this->user_cateogory_admin == 'admin') {
				PRN::PRE($flash);
			} else {
				echo '<div class="text-center">Please contact IT Support</div>';
				exit;
			}
		} else {
			if ($this->user_cateogory_admin == 'admin') {
				$this->render("default_views.controller_not_found");
			} else {
				$this->error_page();
			}
		}
	}

	public function url_not_set($url_name_view, $namespaces, $page_permissions_user, $user_cateogory_admin, $params = false)
	{
		$array_send['url_name_view'] = $url_name_view;
		$array_send['namespaces'] = $namespaces;
		$array_send['category'] = $page_permissions_user;
		$array_send['params'] = $params;
		if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			$flash['message'] = 'Please configure URL permission in config/urls.php';
			$flash['url_name_view'] = $url_name_view;
			$flash['controller'] = $_SESSION['parent_controller_modules'];
			echo json_encode($flash, true);
		} else {
			$this->render('default_views.add_url_permission', $array_send);
		}
		exit;
	}

	public function url_error($url_name_view, $namespaces, $page_permissions_user, $user_cateogory_admin, $params = false)
	{
		$array_send['url_name_view'] = $url_name_view;
		$array_send['namespaces'] = $namespaces;
		$array_send['category'] = $page_permissions_user;
		$array_send['params'] = $params;
		if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			$flash = array(
				'status' => 'warning',
				'message' => '<div class="text-center">
		 						Please contact IT Support
		 					</div>',
			);
			if ($user_cateogory_admin == 'admin') {
				$flash['message'] = 'Please configure URL permission in config/urls.php';
				$flash['url_name_view'] = $url_name_view;
				$flash['controller'] = $_SESSION['parent_controller_modules'];
			}
			echo json_encode($flash, true);
			exit;
		} else {
			if ($user_cateogory_admin == 'admin') {
				$this->error_page();
				exit;
			} else {
				$this->error_page();
				exit;
			}
		}
	}

	public function get_formatted_url($url, $return_by = '.')
	{
		$explode = explode("//", $url);
		if (count($explode) > 1) {
			$url_  = $explode[1];
		} else {
			$url_ = $url;
		}

		$split_ = explode("/", $url_);

		$controller = $split_[2];
		$method = $split_[3];
		$params = '';
		$params_arr = array();
		if (isset($split_[4])) {
			$last_key = count($split_);
			$params_arr = array_slice($split_, 4, $last_key);
			$params = '/' . implode("/", $params_arr);
		}
		if ($return_by == '/') {
			return $controller . '/' . $method . $params;
		} else if ($return_by == 'array') {
			$return_array = array();
			$return_array['path'] = $controller . '.' . $method;
			$return_array['params'] = $params_arr;
			return $return_array;
		} else {
			return $controller . '.' . $method;
		}
	}

	public function formatted_media_path($file_path, $return_base_path = "")
	{
		$explode = explode("//", $file_path);
		if (count($explode) > 1) {
			$url = $explode[1];
		} else {
			$url = $file_path;
		}
		$split_url = explode("/", $url);
		$url_formatted = array_slice($split_url, 3, count($split_url));
		$url_im = implode("/", $url_formatted);
		if ($return_base_path != "") {
			return $url_im;
		}
		$media_path = $this->media_path($url_im);
		return $media_path;
	}

	public function exist_server_name($path)
	{
		$server = $_SERVER['HTTP_HOST'];
		$path = (string) $path;
		if (strpos($path, $server)) {
			return true;
		} else {
			return false;
		}
	}

	public function get_url_name_view($url, $return_by = '.')
	{
		$explode = explode("//", $url);
		if (count($explode) > 1) {
			$url_  = $explode[1];
		} else {
			$url_ = $url;
		}
		$split_ = explode("/", $url_);
		return $method = end($split_);
	}

	public function check_session()
	{
		if (!isset($_SESSION[$this->user_id_name])) {
			$location = base_path_project() . 'login/index';
			header("Location: " . $location);
		}
	}


	public function render($page_name, $data = '')
	{

		if (is_array($data)) {
			extract($data);
		} else {
			$$data = $data; // assign variable name as data name
		}
		if (count($this->common_to_all) > 0) {
			extract($this->common_to_all);
		}

		$base_path = $this->base_path;
		// require_once('.render_functions.php');
		$module_name = $this->controller_name;
		$url_name_view = $this->url_name_view;
		$menu_display_layout = $this->menu_display_layout;

		// if($this->page_security_check == true){
		// 	$this->check_security($page_name);
		// }

		if ($page_name == $this->test_page_layout) {
			$page_name_render = $page_name . '.php';
		} else {
			$page_name_explode = explode(".", $page_name);
			if (count($page_name_explode) > 1) {
				$page_name_render = str_replace(".", "/", $page_name) .  '.php';
			} else {
				$page_name_render = $this->controller_name . '/' . $page_name . '.php';
			}

			if (!file_exists('./views/' . $page_name_render)) {
				$page_name_render = 'default_views/page_not_found.php';
			}
		}

		if (isset($this->layout) && strlen(trim($this->layout)) > 0) {
			$layout_view = trim($this->layout) . '.php';
		} else {

			$layout_view = 'layout.php';
		}
		$this->create_view_folder();
		require_once('./views/' . $layout_view);
	}

	public function renderPartial($page_name, $data = '')
	{
		if (is_array($data)) {
			extract($data);
		} else {
			$$data = $data; // assign variable name as data name
		}
		$base_path = $this->base_path;
		// require_once('render_functions.php');
		$module_name = $this->controller_name;
		$url_name_view = $this->url_name_view;
		if ($this->page_security_check == true) {
			$this->check_security_partial($page_name);
		}
		$page_name_explode = explode(".", $page_name);
		if (count($page_name_explode) > 1) {
			$page_name_render = str_replace(".", "/", $page_name) .  '.php';
		} else {
			$page_name_render = $this->controller_name . '/' . $page_name . '.php';
		}
		if (!file_exists('./views/' . $page_name_render)) {
			$page_name_render = 'default_views/page_not_found.php';
		}
		require_once('./views/' . $page_name_render);
	}

	public function setFlash($message)
	{
		if (isset($_SESSION['flash_message'])) {
			unset($_SESSION['flash_message']);
		}
		$flash_message = $message;
		$_SESSION['flash_message'] = $message;
	}

	public function setFlashStatus($flash, $success = false)
	{
		$status = $flash['status'];
		$message = $flash['message'];
		if ($success == false) {
			$this->setFlash(array($status => $message));
		} else {
			if ($status == $success) {
				$this->setFlash(array($status => $message));
			}
		}
	}

	public function check_security($page_name)
	{
		$page_name_with_php = $page_name . '.php';;
		if (!array_key_exists($page_name_with_php, $_SESSION['PageSecurityArray'])) {
			$page_name_render = 'error_page.php';
			if (count($this->common_to_all) > 0) {
				extract($this->common_to_all);
			}
			require_once('./views/layout.php');
			exit;
		}
	}

	public function getTemplate($page_name, $post_array)
	{
		$page_name = str_replace(".", "/", $page_name);
		$post_data = http_build_query($post_array);
		$opts = array(
			'http' => array(
				'method' => 'POST',
				'header' => 'Content-Type: application/x-www-form-urlencoded',
				'content' => $post_data
			),
		);
		$path = base_path_project() . 'views/' . $page_name . '.php';
		$context = stream_context_create($opts);
		$result = file_get_contents($path, false, $context);
		if ($this->email_signature != '') {
			$result = str_replace("</html>", "", $result);
			$result = str_replace("</body>", "", $result);
			$result .= '<table>
			<tr>
				<td> ' . $this->email_signature . ' </td>
			</tr>
			</table>';
			$result .= '</body>';
			$result .= '</html>';
		}
		return $result;
	}

	public function check_security_partial($page_name)
	{
		$page_name_with_php = $page_name . '.php';;
		if (!array_key_exists($page_name_with_php, $this->security_pages_array)) {
			$page_name_render = 'default_views/error_page.php';
			require_once('./views/' . $page_name_render);
			exit;
		}
	}

	public function redirect_pre_page($return = false)
	{
		$pre_page = $_SERVER['HTTP_REFERER'];
		if ($return == false) {
			$url_arr = $this->get_formatted_url($pre_page, "array");
			$path = $url_arr['path'];
			$params = $url_arr['params'];
			$this->redirect($path, $params);
			exit;
		} else {
			$url = base_path_project() . $this->get_formatted_url($pre_page, "/");
			return $url;
		}
	}

	public function redirect($controller_method, $params = '')
	{
		$params_im = '';
		if (is_array($params)) {
			if (count($params) > 0) {
				$params_im = implode(",", $params);
			}
		}
		$split_controller = explode(".", $controller_method);
		if (count($split_controller) == 1) {
			$controller_method = $_SESSION['parent_controller_modules'] . '.' .  $split_controller[0];
		}
		$path = $this->url($controller_method);
		if ($params == '' && $params_im == '') {
			header("Location: " . $path);
		} else {
			if (is_array($params)) {
				$params =  array_filter($params);
				$params = implode("/", array_values($params));
			}
			$path = $path . '/' . $params;
			header("Location: " . $path);
		}
	}

	public function post($controller_method, $post_arr, $header = null)
	{
		$array_send['controller_method'] = $controller_method;
		$array_send['post_arr'] = $post_arr;
		$this->renderPartial('settings.post', $array_send);
	}

	public function exportPath($path_type = '')
	{
		if ($path_type == '' || $path_type == 'pdf') {
			require_once($this->export_path['pdf']);
		} else {
			require_once($this->export_path[$path_type]);
		}
	}

	public function add_pdf_path()
	{
		$this->exportPath('pdf');
	}

	public function add_excel_path()
	{
		$this->exportPath('excel');
	}

	public function error_page($array = array())
	{
		$array_send['user_cateogory_admin'] = $_SESSION['user_cateogory_admin'];
		$this->render('default_views.error_page', $array_send);

		exit;
	}

	public function notification_error_page($array = array())
	{
		$array_send['user_cateogory_admin'] = $_SESSION['user_cateogory_admin'];
		$this->render('default_views.notification_error_page', $array_send);

		exit;
	}



	public function url($controller_method)
	{
		$path = base_path_project(); // defined in config/settings.php
		$path_ex = explode(".", $controller_method);
		$controller = $path_ex[0];
		$method = $path_ex[1];
		if ($controller == 'default_views' || $controller == 'settings') {
			$controller = $_SESSION['parent_controller_modules'];
		}
		return $path . $controller . '/' . $method;
	}

	public function media_path($path)
	{
		$media_path = media_path_();
		return $media_path . $path;
	}

	public function unlink_file($path)
	{
		if (file_exists($this->media_path($path))) {
			$split = explode("/", $path);
			if (count($split) > 1) {
				$file_name = $split[1];
			} else {
				$file_name = $path;
			}
			if ($file_name != '') {
				unlink($this->media_path($path));
			}
		}
	}

	public function base64DecodeImage($imageData, $file_name, $path)
	{
		$file_name = strtolower($file_name);
		list($type, $imageData) = explode(';', $imageData);
		list(, $extension) = explode('/', $type);
		list(, $imageData) = explode(',', $imageData);
		$path = rtrim($path, "/");
		$path = ltrim($path, "/");
		$target_dir = media_path_() . $path;
		$target_dir_post = $path . '/';
		if (!file_exists($target_dir) && strlen(trim($path)) > 0) {
			mkdir($target_dir);
			chmod($target_dir, 0755);
		}
		if (file_exists($target_dir)) {
			$file_name_new = $file_name . '.' . $extension;
			$base_path = $target_dir_post . $file_name_new;
			$full_path = $target_dir . '/' .  $file_name_new;
			$imageData = base64_decode($imageData);
			file_put_contents($full_path, $imageData);
			$return_array = array();
			$return_array['status'] = 'success';
			$return_array['full_path'] = media_path($base_path);
			$return_array['base_path'] = $base_path;
		} else {
			$return_array['status'] = 'warning';
			$return_array['message'] = 'IMAGE UPLOADING FAILS';
		}
		return $return_array;
	}

	public function upload_file($file, $file_name, $path)
	{
		$file_name = strtolower($file_name);
		$target_dir = media_path_();
		$path = rtrim($path, "/");
		if (!file_exists($target_dir . $path) && strlen(trim($path)) > 0) {
			mkdir($target_dir . $path);
			chmod($target_dir . $path, 0755);
		}
		$target_dir = media_path_() . $path . '/';
		$target_dir_post = $path . '/';
		$base_file = basename($file["name"]);
		$extension = strtolower(pathinfo($base_file, PATHINFO_EXTENSION));
		$file_name_new = basename($file["name"]);
		if (strlen(trim($file_name)) > 0) {
			$file_name_new = $file_name . '.' . $extension;
		}
		$target_file = $target_dir . $file_name_new;
		$target_file_post = $target_dir_post . $file_name_new;

		if (!move_uploaded_file($file["tmp_name"], $target_file)) {
			$return_array['status'] = 'warning';
			$return_array['message'] = "WARNING! there was an error uploading your file. Please contact IT support";
			$return_array['image_path'] = '';
			return $return_array;
		} else {
			$return_array['status'] = 'success';
			$return_array['message'] = "SUCCESS! Uploaded successfully";
			if ($extension == 'jpeg' || $extension == 'png' || $extension == 'jpg') {
				$return_array['full_image_path'] = base_path_project() . $target_file;
				$return_array['image_path'] = $target_file_post;
				$return_array['full_path'] = base_path_project() . $target_file;
				$return_array['base_path'] = $target_file_post;
			} else {
				$return_array['full_path'] = base_path_project() . $target_file;
				$return_array['base_path'] = $target_file_post;
			}

			return $return_array;
		}
	}

	public function import_models()
	{
		include('imports.php');
		if (isset($imports[$this->controller_name])) {
			foreach ($imports[$this->controller_name] as $key => $path) {
				$new_path = str_replace(".", "/", $path);
				$new_path = './' . $new_path . '.php';
				require_once($new_path);
			}
		}
	}

	public function test()
	{
		$this->render('test_page_layout');
	}
	public function create_view_folder()
	{
		$controller_path = 'views/' . $this->controller_name . '/';
		if (!file_exists($controller_path)) {
			mkdir($controller_path);
		}
	}

	public function create_token($bytes = 32)
	{
		$token = bin2hex(openssl_random_pseudo_bytes($bytes));
		return $token;
	}

	public function required_vars_check($posted_keys, $posted_data, $cred = false)
	{
		$return_array = array();
		foreach ($posted_keys as $key => $posted_key) {
			$posted_key_check = str_replace("__json", "", $posted_key);
			if (!isset($posted_data[$posted_key_check])) {
				$return_array['message'] = $this->getStatusMessage($this->status_require_vars, "", $posted_key, $cred);
				$this->create_response($return_array, $this->status_fail_params_three);
				exit;
			} else if (strlen(trim($posted_data[$posted_key_check])) == 0) {
				$return_array['message'] = $this->getStatusMessage($this->status_post_value_none, "", $posted_key, $cred);
				$this->create_response($return_array, $this->status_fail_params_three);
				exit;
			}
		}
	}

	public function required_vars_defined($message, $posted)
	{
		foreach ($message as $message => $key) {
			if (!isset($posted[$key])) {
				$return_array['message'] = $this->status_message_internal_error;
				$this->create_response($return_array, 3);
				exit;
			}
		}
	}

	public function create_response($array, $status = 1, $print_test = false)
	{
		$return_array = array();
		$this->print_response($print_test, $array);
		$return_array['data'] = $array;
		if ($status == $this->status_fail_zero) {
			$result['status'] = 'FAILED';
			$result['message'] = $array['message'];
			if (isset($array['data'])) {
				$result['result'] = $array['data'];
			}
		} else if ($status == $this->status_fail_unauthorized_two) {
			$result['status'] = 'UNAUTHORIZED';
			$result['message'] = $this->status_message_unauthorized;
		} else if ($status == $this->status_fail_params_three) {

			if (!isset($array['status'])) {
				$result['status'] = 'FAILED';
			} else {
				$result['status'] = $array['status'];
			}
			$result['message'] = $array['message'];
		} else if ($status == $this->status_fail_forbidden_four) {
			$result['status'] = 'FAILED';
			if (isset($array['message'])) {
				$message = $array['message'];
			} else {
				$message = $this->getStatusMessage($this->status_forbidden);
			}
			$result['message'] = 'FORBIDDEN!' . $message;
		} else if ($status == $this->status_fail_orgin_access_five) {
			$result['status'] = 'FAILED';
			$result['message'] = $this->status_message_orgin_access;
		} else if ($status == $this->status_fail_method_six) {
			$result['status'] = 'FAILED';
			$result['message'] = $this->status_message_method;
		} else {
			if (count($array) > 0) {
				if (!isset($array['message']) || (isset($array['message']) && $array['message'] == '')) {
					$message = '';
				} else {
					$message = $array['message'];
				}
				$result = $this->getStatus($this->status_success, $message);
				if (isset($array['data'])) {
					if (is_string($array['data']) && strlen(trim($array['data'])) != 0) {
						$result['result'] = $array['data'];
					} else {
						$result['result'] = $array['data'];
					}
				} else {
					$result['result'] = $array;
				}
			} else {
				$result = $this->getStatus($this->status_data_none);
			}
		}
		if (!isset($result['result']) || isset($result['result']) && count($result['result']) == 0) {
			$result['result'] = (object) array();
		}
		echo json_encode($result, true);
		exit;
	}

	public function verify_isset_middlewaretoken($post_data, $len = 64)
	{
		if (!isset($post_data['middlewaretoken']) || strlen($post_data['middlewaretoken']) != $len) {
			$this->create_response(array(), $this->status_fail_unauthorized_two);
			exit;
		}
		if (isset($post_data['static_token_validate']) && isset($post_data['middlewaretoken']) && ($post_data['middlewaretoken'] != $post_data['static_token_validate'])) {
			$this->create_response(array(), $this->status_fail_unauthorized_two);
			exit;
		}
	}

	public function print_response($print_test, $array)
	{
		if ($print_test == true) {
			PRN::PR($print_test);
		}
	}
}
