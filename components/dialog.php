<?php 
require_once '../includes/model.php';

function addMessage($text='text', $method='POST'){ //отправляет сообщение
    if($method === 'POST'){
        $text = $_POST["$text"];
    }else if($method === 'GET'){
        $text = $_GET["$text"];
    }

    $errors = array();

    if(trim($text) == ''){
        $errors[] = 'Пустая строка';
    }
    if(empty($errors)){
        ORM::Create('messages', [
            'message' => $text,
            'sender' => $_SESSION['logged_user']['login'],
            'room_id' => $_GET['room_id'],
            'room_type' => $_GET['room_type'],
            'readed' => 0
        ]);
        return true;
    }
} 
function getFriendsNotInGroup(){ //возвращает массив друзей, которые не учавствуют в беседе
    $friends = unserialize($_SESSION['logged_user']['friends']);
    $group = unserialize(ORM::Read_one('groups', 'id=?', [$_GET['room_id']])['party']);
    $friends_not_in_group = array();
    foreach ($friends as $fr){
        $not_in_group = true;
        foreach ($group as $gr){
            if($fr[0] === $gr){
                $not_in_group = false;
                break;
            }
        }
        if($not_in_group){
        $friends_not_in_group[] = $fr[0];
        }
    }
    return $friends_not_in_group;
}
function addMember($member='member', $method='POST'){ //добавляет выбранного друга в беседу
    if($method === 'POST'){
        $member = $_POST["$member"];
    }else if($method === 'GET'){
        $member = $_GET["$member"];
    }
    if(!is_null($member)){
        $group = ORM::Read_one('groups', 'id=?', [$_GET['room_id']]);
        $group_party_arr = unserialize($group['party']);
        $group_party_arr[] = $member;
        sort($group_party_arr);
        $group_party_str = serialize($group_party_arr);
        ORM::Update('groups', 'party=?, number_of_participian=?', 'id=?', [$group_party_str, count($group_party_arr), $_GET['room_id']]);

        //изменяет информацию о беседе в таблице users у добавленного пользователя
        $party_user = ORM::Read_one('users', 'login=?', [$member]);
        $user_groups_id_list = unserialize($party_user['groups_id']);
        $user_groups_id_list[] = [$_GET['room_id'], date('Y-m-d H:i:s')];
        $user_groups_id_str = serialize($user_groups_id_list);
        ORM::Update('users', 'groups_id=?', 'login=?', [$user_groups_id_str, $member]);

        //отправляет другу оповещение о добавлении
        ORM::Create('notice', [
            'text' => "Пользователь {$_SESSION['logged_user']['login']} добавил вас в беседу {$group['name']}.",
            'sender' => $_SESSION['logged_user']['login'],
            'recipient' => $member,
            'type' => 'OfferGroup'
        ]);
    }
}

if(isset($_POST['doGoAddMessage'])){
    addMessage();
    //обновляем информацию о последнем сообщении, отправленном в беседе или диалоге если существует 
    if(ORM::Exists('messages', 'room_id=? AND room_type=? ORDER BY pubdate DESC', [$_GET['room_id'], $_GET['room_type']])){
        $last_message = ORM::Read_one('messages', 'room_id=? AND room_type=? ORDER BY pubdate DESC', [$_GET['room_id'], $_GET['room_type']]);
        if($_GET['room_type'] === 'message_room'){
            ORM::Update('message_room', 'last_message_date=?', 'id=?', [$last_message['pubdate'], $_GET['room_id']]);
        }else if($_GET['room_type'] === 'group'){
            ORM::Update('groups', 'last_message_date=?', 'id=?', [$last_message['pubdate'], $_GET['room_id']]);
        }
    }
    App::refresh();
}

if(isset($_POST['doGoDeleteMessage'])){
    ORM::Delete('messages', 'id=? AND room_type=?', [$_POST['id'], $_POST['room_type']]);
    App::refresh();
}
if(isset($_POST['doGoAddMember'])) { 
    addMember();
    App::refresh();
} 
