<?php
require_once '../includes/model.php';

function login($login, $password){ //вход в учетную запись
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
	return false;
}
$err_message = '';
if(isset($_POST['doGoLogin'])){
	if(login($_POST['login'], $_POST['password']) === true){
		login($_POST['login'], $_POST['password']);
		header("Location: ../views/main_page.php?id={$_SESSION['logged_user']['id']}");
		exit();
	}else{
		$err_message = login($_POST['login'], $_POST['password'])."<hr>";
	}
}
