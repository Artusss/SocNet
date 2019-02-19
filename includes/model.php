<?php
	require_once 'config.php'; //подключаем конфиг файл
	require_once 'libs/ORM.php'; //подключаем библиотеки 
	require_once 'libs/App.php';
	
	//подготавливаем БД для дальнейшего использования
	ORM::Prepare(
		$config['db']['name'],
		$config['db']['server'],
		$config['db']['username'],
		$config['db']['password']
	);
	
	session_start(); //начинаем сессию