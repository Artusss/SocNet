<?php
require_once '../includes/model.php';

//возвращает массив пользователей по совпадению с введенными данными
function searchFriend($friend_login='friend_login', $method='POST'){ 
	if($method === 'POST'){
		$friend_login = $_POST["$friend_login"];
	}else if($method === 'GET'){
		$friend_login = $_GET["$friend_login"];
	}else{
		$friend_login = $_REQUEST["$friend_login"];
	}

	$friend_login = trim($friend_login);

	$founded_user = array();
	if($friend_login !== ''){	
		$users = ORM::Read('users', 'login!=?', [$_SESSION['logged_user']['login']]);
		foreach ($users as $v) {
			if((strpos($v['login'], $friend_login) !== false)){
				$founded_user[] = $v;
			}
		}
	}
	return $founded_user;
}

if(isset($_POST['doGoSearchFriend'])){
	$founded_user = searchFriend();
}



