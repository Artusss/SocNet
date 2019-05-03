<?php
require_once '../includes/model.php';

function offerFriend($main_user_login){ //отправляет оповещение пользователю на добавление в друзья
    $offer_text = "Дорогой ".$main_user_login.", прошу тебя принять меня в друзья.";

    ORM::Create('notice', [
        'text'      => $offer_text,
        'sender'    => $_SESSION['logged_user']['login'],
        'recipient' => $main_user_login,
        'type'      => 'OfferFriend'
    ]);

    return true;
}
function deleteFriend($main_user){ //удаляет пользователя из друзей
    //получаем массивы друзей у обоих пользователей
    $log_user_friend_list  = unserialize($_SESSION['logged_user']['friends']);
    $main_user_friend_list = unserialize($main_user['friends']);

    //удаляем пользователей друг у друга из массивов
    unset($log_user_friend_list[array_search([$main_user['login'], $main_user['id']], $log_user_friend_list)]);
    unset($main_user_friend_list[array_search([$_SESSION['logged_user']['login'], $_SESSION['logged_user']['id']], $main_user_friend_list)]);

    //сериализируем массивы друзей обратно
    $_SESSION['logged_user']['friends'] = count($log_user_friend_list) === 0 ? null : serialize($log_user_friend_list);
    $main_user['friends']               = count($main_user_friend_list) === 0 ? null : serialize($main_user_friend_list);

    //заносим изменения в БД
    ORM::Update('users', 'friends=?', 'id=?', [$_SESSION['logged_user']['friends'], $_SESSION['logged_user']['id']]);
    ORM::Update('users', 'friends=?', 'id=?', [$main_user['friends'], $main_user['id']]);

    //получаем id диалога с данным пользователем
    $party = array($_SESSION['logged_user']['login'], $main_user['login']);
    sort($party);
    $party_str = serialize($party);
    $room_id   = (ORM::Read_one('message_room', 'party=?', [$party_str]))['id'];

    //удаляем диалог с пользователем и все сообщения из этого диалога
    ORM::Delete('message_room', 'party=?', [$party_str]);
    ORM::Delete('messages', 'room_id=?', [$room_id]);

    return true;
}

function checkFriend($main_user_login){ //возвращает true если друг найден
    $friend_list = $_SESSION['logged_user']['friends'];
    $check       = strpos($friend_list, $main_user_login);
    return $check? true : false;
}
function checkOffer($type, $main_user){ //возвращает true если отправлено оповещение
    $offer = ORM::Exists('notice',
        'sender=? AND recipient=? AND type=?',
        [$_SESSION['logged_user']['login'], $main_user['login'], $type]
    );

    return $offer;
}


//Все что связано с записями на стене (начало)

function createRecord(){
    $text   = $_POST['text'];
    $errors = array();

    if(trim($text) == ''){
        $errors[] = 'Пустая строка';
    }
    if(empty($errors)) {
        ORM::Create('record', [
            'author_id' => $_SESSION['logged_user']['id'],
            'author_name' => $_SESSION['logged_user']['login'],
            'text' => $text
        ]);
        return true;
    }
}
function getRecord(){
    return ORM::Read('record', 'author_id=?', [$_GET['id']]);
}
function deleteRecord(){
    ORM::Delete('record', 'id=?', [$_POST['record_id']]);
    ORM::Delete('record_like', 'record_id=?', [$_POST['record_id']]);
    ORM::Delete('record_comment', 'record_id=?', [$_POST['record_id']]);
}

function getLike_count($record_id){
    return ORM::Read_count('record_like', 'record_id=?', [$record_id]);
}
function setLike(){
    if(ORM::Exists('record_like', 'user_id=? AND record_id=?', [$_SESSION['logged_user']['id'], $_POST['record_id']])){
        ORM::Delete('record_like', 'user_id=? AND record_id=?', [$_SESSION['logged_user']['id'], $_POST['record_id']]);
    }else{
        ORM::Create('record_like', [
            'user_id'   => $_SESSION['logged_user']['id'],
            'record_id' => $_POST['record_id'],
        ]);
    }
}

function getComment($record_id){
    return ORM::Read('record_comment', 'record_id=? ORDER BY pubdate DESC', [$record_id]);
}
function addComment(){
    $text   = $_POST['text'];
    $errors = array();

    if(trim($text) == ''){
        $errors[] = 'Пустая строка';
    }
    if(empty($errors)){
        ORM::Create('record_comment', [
            'author'    => $_SESSION['logged_user']['login'],
            'record_id' => $_POST['record_id'],
            'text'      => $_POST['text']
        ]);
        return true;
    }
}
function deleteComment(){
    ORM::Delete('record_comment', 'id=?', [$_POST['comment_id']]);
}
//Все что связано с записями на стене (конец)


$main_user = ORM::Read_one('users', 'id=?', [$_GET['id']]);
//Обработчики форм
if(isset($_SESSION['logged_user'])){
    if(isset($_POST['doGoDeleteFriend'])){
        deleteFriend($main_user);
        App::refresh();
    }
    if(isset($_POST['doGoAddNotice'])){
        offerFriend($main_user['login']);
        App::refresh();
    }
    if(isset($_POST['doGoCreateRecord'])){
        createRecord();
        App::refresh();
    }
    if(isset($_POST['doGoDeleteRecord'])){
        deleteRecord();
        App::refresh();
    }
    if(isset($_POST['doSetLike'])){
        setLike();
        App::refresh();
    }
    if(isset($_POST['doGoAddComment'])){
        addComment();
        App::refresh();
    }
    if(isset($_POST['doGoDeleteComment'])){
        deleteComment();
        App::refresh();
    }
    if(isset($_POST['doGoLogout'])){
        header('Location: ../index.php');
        unset($_SESSION['logged_user']);
        exit();
    }
}