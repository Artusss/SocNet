<?php
require_once '../includes/model.php';
//Все что связано с записями на стене (начало)

function getRecord(){
    $friends_arr = unserialize($_SESSION['logged_user']['friends']);
    $friends_records = array();
    foreach($friends_arr as $friend){
        $friend_records = ORM::Read('record', 'author_id=?', [$friend[1]]);
        foreach($friend_records as $one_friend_record){
            $friends_records[] = $one_friend_record;
        }
    }
    sortDB_date($friends_records, 'pubdate');
    return $friends_records;
}
function sortDB_date(&$arr, $date){ //сортирует таблицу по дате
    $sort_var = array_column($arr, $date);
    foreach ($sort_var as &$v){
        $v = strtotime($v);
    }
    array_multisort($sort_var, SORT_DESC, $arr);
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

//Обработчики форм
if(isset($_SESSION['logged_user'])){
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
}
