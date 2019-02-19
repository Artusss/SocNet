<?php
require_once '../includes/model.php';

function offerFriend($main_user_login){ //отправляет оповещение пользователю на добавление в друзья
    $offer_text = "Дорогой ".$main_user_login.", прошу тебя принять меня в друзья.";

    ORM::Create('notice', [
        'text' => $offer_text,
        'sender' => $_SESSION['logged_user']['login'], 
        'recipient' => $main_user_login,
        'type' => 'OfferFriend'
    ]);

    return true;
}
function deleteFriend($main_user){ //удаляет пользователя из друзей
    //получаем массивы друзей у обоих пользователей
    $log_user_friend_list = unserialize($_SESSION['logged_user']['friends']);
    $main_user_friend_list = unserialize($main_user['friends']);

    //удаляем пользователей друг у друга из массивов
    unset($log_user_friend_list[array_search([$main_user['login'], $main_user['id']], $log_user_friend_list)]);
    unset($main_user_friend_list[array_search([$_SESSION['logged_user']['login'], $_SESSION['logged_user']['id']], $main_user_friend_list)]);

    //сериализируем массивы друзей обратно
    $_SESSION['logged_user']['friends'] = count($log_user_friend_list) === 0 ? null : serialize($log_user_friend_list);
    $main_user['friends'] = count($main_user_friend_list) === 0 ? null : serialize($main_user_friend_list);

    //заносим изменения в БД
    ORM::Update('users', 'friends=?', 'id=?', [$_SESSION['logged_user']['friends'], $_SESSION['logged_user']['id']]);
    ORM::Update('users', 'friends=?', 'id=?', [$main_user['friends'], $main_user['id']]);

    //получаем id диалога с данным пользователем
    $party = array($_SESSION['logged_user']['login'], $main_user['login']);
    sort($party);
    $party_str = serialize($party);
    $room_id = (ORM::Read_one('message_room', 'party=?', [$party_str]))['id'];

    //удаляем диалог с пользователем и все сообщения из этого диалога
    ORM::Delete('message_room', 'party=?', [$party_str]);
    ORM::Delete('messages', 'room_id=?', [$room_id]);

    return true;
}

function checkFriend($main_user_login){ //возвращает true если друг найден
    $friend_list = $_SESSION['logged_user']['friends'];
    $check = strpos($friend_list, $main_user_login);
    return $check? true : false;
}
function checkOffer($type, $main_user){ //возвращает true если отправлено оповещение
    $offer = ORM::Exists('notice',
        'sender=? AND recipient=? AND type=?',
        [$_SESSION['logged_user']['login'], $main_user['login'], $type]
    );

    return $offer;
}

$main_user = ORM::Read_one('users', 'id=?', [$_GET['id']]);
if(isset($_POST['doGoDeleteFriend'])){
	deleteFriend($main_user);
	App::refresh();
}
if(isset($_POST['doGoAddNotice'])){
	offerFriend($main_user['login']);
	App::refresh();
}
if(isset($_POST['doGoLogout'])){
    header('Location: ../index.php');
    unset($_SESSION['logged_user']);
    exit();
}