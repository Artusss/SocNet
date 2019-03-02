<?php
require_once '../includes/model.php';

//регистрирует пользователя
function signUp($login, $email, $password, $password_2){
	$errors = array();

	if(trim($login) == ''){
		$errors[] = 'Введите логин'; 
	}
	if(trim($email) == ''){
		$errors[] = 'Введите e-mail'; 
	}
	if(!filter_var(trim($email), FILTER_VALIDATE_EMAIL)){
		$errors[] = 'E-mail указан неверно';
	}
	if($password == ''){
		$errors[] = 'Введите пароль'; 
	}
	if($password != $password_2){
		$errors[] = 'Повторный пароль введен неверно'; 
	}

	if(ORM::Exists('users', 'login=?', [$login])){
		$errors[] = 'Такой login уже существует';
	}
	if(ORM::Exists('users', 'email=?', [$email])){
		$errors[] = 'Такой e-mail уже существует';
	}

	if(empty($errors)){
		//хешируем пароль перед записью в БД
		$password = password_hash($password, PASSWORD_DEFAULT);
		ORM::Create('users', [
			'login' => $login, 
			'email' => $email,
			'password' => $password
		]
	);
		return true;
	}else{
		return array_shift($errors);
	}
}
$err_message = '';
if(isset($_POST['doGoSignUp'])){
	if(signUp($_POST['login'], $_POST['email'], $_POST['password'], $_POST['password_2']) === true){
		signUp($_POST['login'], $_POST['email'], $_POST['password'], $_POST['password_2']);
		header("Location: ../index.php");
		exit();
	}else{
		$err_message = signUp($_POST['login'], $_POST['email'], $_POST['password'], $_POST['password_2'])."<hr>";
	}
}
