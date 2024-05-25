<?php
// import autoload
require '../vendor/autoload.php';

session_start(); //support $_SESSION
// router của phần giao diện người dung
$c = $_GET['c'] ?? 'home';
$a = $_GET['a'] ?? 'index';

$strController = ucfirst($c) . 'Controller';
// import file controller
require "controller/$strController.php";

// import config & database
require '../config.php';
require '../connectDB.php';

// import boostrap
require '../bootstrap.php';

// tạo đối tượng controller
$controller = new $strController();

// Gọi hàm tương ứng với action
// $controller->index();
$controller->$a();
