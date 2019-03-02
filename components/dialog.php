<?php 
require_once '../includes/model.php';

function addMessage($text){ //отправляет сообщение
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
    return false;
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
function addMember($member){ //добавляет выбранного друга в беседу
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
    addMessage($_POST['text']);
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
    addMember($_POST['member']);
    App::refresh();
}

$dialog = ORM::Read('messages',
    'room_id=? AND room_type=? ORDER BY pubdate DESC LIMIT 50',
    [$_GET['room_id'], $_GET['room_type']]
);

if($_GET['room_type'] === 'group'){
    $groups_id = unserialize($_SESSION['logged_user']['groups_id']);
    foreach ($groups_id as &$v) {
        if($v[0] == $_GET['room_id']){
            $v[1] = date('Y-m-d H:i:s');
            break;
        }
    }
    $_SESSION['logged_user']['groups_id'] = serialize($groups_id);
    ORM::Update('users', 'groups_id=?', 'id=?', [$_SESSION['logged_user']['groups_id'], $_SESSION['logged_user']['id']]);
}
