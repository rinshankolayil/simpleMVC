<?php

class RestApiController extends MainController
{
	public $restmodal;
	protected $http_origin;
	protected $allowed_orgins;
	function __construct()
	{
		parent::__construct();
		$this->http_origin = $_SERVER['REMOTE_ADDR'];
		$this->allowed_orgins = array(

		);
		if (!in_array($this->http_origin, $this->allowed_orgins)) {
			if (isset($_SERVER['HTTP_ORIGIN'])) {
				$this->http_origin = rtrim($_SERVER['HTTP_ORIGIN'], "/");
			} else if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
				$this->http_origin = $_SERVER['HTTP_CLIENT_IP'];
			} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$this->http_origin = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$this->http_origin = $_SERVER['REMOTE_ADDR'];
			}
		}
		header("Access-Control-Allow-Origin: *");
		header("Content-Type: application/json; charset=UTF-8");
		if (!in_array($this->http_origin, $this->allowed_orgins)) {
			$this->create_response(array(), 5);
		}
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			$this->create_response(array(), 6);
		}
		$this->restmodal = new RestModal();
	}
	// START **** CHECK OUT THESE FUNCTIONS ****  //
	// $this->security_check();
	// $this->required_vars_check();
	// $this->verify_isset_middlewaretoken();
	// $token = $this->verify_middle_ware_token();
	// $this->create_response();
	// END **** CHECK OUT THESE FUNCTIONS **** //
	public function index()
	{
		$message['status'] = "UNAUTHORIZED";
		$message['message'] = "YOU ARE NOT ALLOWED TO ACCESS THIS PAGE";
		$message['result'] = (object) array();
		echo json_encode($message);
	}

	public function security_check($post_data)
	{
		$this->required_vars_check(
			array(
				'credentials',
			),
			$post_data
		);

		$json_decode = json_decode($post_data['credentials'], true);
		$this->required_vars_check(
			array(
				'middlewaretoken__json',
				'auth_id__json',
			),
			$json_decode
		);
		$middlewaretoken = $json_decode['middlewaretoken'];
		$auth_id = $json_decode['auth_id'];
		$this->verify_middle_ware_token($auth_id, $middlewaretoken);
	}

	public function verify_middle_ware_token($user_id, $middlewaretoken)
	{
		$check_middle_ware = $this->restmodal->check_middle_ware_token($user_id, $middlewaretoken);
		if (!isset($check_middle_ware['middlewaretoken'])) {
			$this->create_response($check_middle_ware, 0);
			exit;
		} else {
			$verify_token = $this->lib->verify_middleware_openssl($check_middle_ware['middlewaretoken'], $user_id);
			if ($verify_token == 'unverified') {
				$return_array = $this->getStatus($this->status_unauthorized, 'Token verification failed, Please verify your token or contact IT support');
				$this->create_response($return_array, 0);
				exit;
			}
		}
		return $check_middle_ware;
	}
}
