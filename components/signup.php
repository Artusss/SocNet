<?php
require_once '../includes/model.php';

//регистрирует пользователя
function signUp($login='login', $email='email', $password='password', $password_2='password_2', $submit='doGoSignUp', $method='POST'){
	if($method === 'POST'){
		$login      = $_POST["$login"];
		$email      = $_POST["$email"];
		$password   = $_POST["$password"];
		$password_2 = $_POST["$password_2"];
	}else if($method === 'GET'){
		$login      = $_GET["$login"];
		$email      = $_GET["$email"];
		$password   = $_GET["$password"];
		$password_2 = $_GET["$password_2"];
	}

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
		//хешируем пароль пере записью в БД
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
	if(signUp() === true){
		signUp();
		header("Location: ../index.php");
		exit();
	}else{
		$err_message = signUp()."<hr>";
	}
}
