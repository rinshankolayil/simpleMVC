<?php
session_start();
ini_set('display_errors', '1'); ## DEBUG = True in Django framework
include('app.php');
$class = new App();
//if you want to learn how to creat MVC structure in PHP Please refer youtube
