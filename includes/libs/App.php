<?php
class App
{
	static public function refresh(){ //редиректит страницу 
		$id = time();
		header("Location: http://{$_SERVER['SERVER_NAME']}{$_SERVER['SCRIPT_NAME']}?{$_SERVER['QUERY_STRING']}&{$id}");
		exit;
	}
	static public function debug($var){ //выводит входные данные в упорядоченном виде
		echo '<pre>';
		print_r($var);
		echo '</pre>';
	}
}