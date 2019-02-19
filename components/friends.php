<?php 
require_once '../includes/model.php';

$friends = array(); 
if(!is_null($_SESSION['logged_user']['friends'])){
	$friends = unserialize($_SESSION['logged_user']['friends']);
}