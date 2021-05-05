<?php

class SiteController extends MainController
{
	public $restmodal;
	protected $http_origin;
	protected $allowed_orgins;
	function __construct()
	{
		parent::__construct();
	}
	public function index()
	{
		echo "Hi";
	}
}
