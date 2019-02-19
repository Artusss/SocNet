<?php
require_once '../includes/model.php';

function login($login='login', $password='password', $method='POST'){ //вход в учетную запись
	if($method === 'POST'){
		$login    = $_POST["$login"];
		$password = $_POST["$password"];
	}else if($method === 'GET'){
		$login    = $_GET["$login"];
		$password = $_GET["$password"];
	}
	
	$errors = array();
	if(trim($login) == ''){
		$errors[] = 'Введите логин'; 
	}
	if($password == ''){
		$errors[] = 'Введите пароль';
	}
	
	$this_user = ORM::Read_one('users', 'login=?', [$login]);
	
	if($this_user){
		if(password_verify($password, $this_user['password'])){ //проверка пароля на совместимость с хешированным паролем в БД
			$_SESSION['logged_user'] = $this_user;
			return true;
		}else{
			$errors[] = 'Неправильный пароль';
		}
	}else{
		$errors[] = 'Пользователь не найден';
	}
	if(!empty($errors)){
		return array_shift($errors);
	}
}
$err_message = '';
if(isset($_POST['doGoLogin'])){
	if(login() === true){
		login();
		header("Location: ../views/main_page.php?id={$_SESSION['logged_user']['id']}");
		exit();
	}else{
		$err_message = login()."<hr>";
	}
}
