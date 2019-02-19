<?php
require_once '../includes/model.php';

function addFriend($friend_user){ //добавляет пользователя в друзья
	//получаем массивы друзей у обоих пользователей
	$log_user_friend_list = unserialize($_SESSION['logged_user']['friends']);
	$friend_user_friend_list = unserialize($friend_user['friends']);

	//добавляем пользователей друг у другу в массивы
	$log_user_friend_list[] = [$friend_user['login'], $friend_user['id']];
	$friend_user_friend_list[] = [$_SESSION['logged_user']['login'], $_SESSION['logged_user']['id']];

	//сериализируем массивы друзей обратно
	$_SESSION['logged_user']['friends'] = serialize($log_user_friend_list);
	$friend_user['friends'] = serialize($friend_user_friend_list);

	//заносим изменения в БД
	ORM::Update('users', 'friends=?', 'id=?', [$_SESSION['logged_user']['friends'], $_SESSION['logged_user']['id']]);
	ORM::Update('users', 'friends=?', 'id=?', [$friend_user['friends'], $friend_user['id']]);

	return false;
}
if(isset($_POST["doGoOk"])){
	ORM::Delete('notice', 'id=?', [$_POST['id']]);
	App::refresh();
}
if(isset($_POST["doGoAdd"])){
	$friend_user = ORM::Read_one('users', 'login=?', [$_POST['sender']]);
	addFriend($friend_user);
	ORM::Delete('notice', 'id=?', [$_POST['id']]);
	App::refresh();
}
$notice = ORM::Read('notice', 
	'recipient=? ORDER BY pubdate DESC', 
	[$_SESSION['logged_user']['login']]
);