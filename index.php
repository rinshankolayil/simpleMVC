<?php
session_start();
$test_user = array(
	'rinshan',
);
if(isset($_SESSION['UserID'])){
	if(!in_array($_SESSION['UserID'],$test_user)){
		ini_set('display_errors', '1');
	}
	else{
		ini_set('display_errors', '0');
	}	
}
ini_set('display_errors', '1');
include('app.php');
$class = new App();
//if you want to learn how to creat MVC structure in PHP Please refer youtube