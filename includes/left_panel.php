<?php
function not_readed_dialogs_count(){ //подсчитывает и возвращает колличество непрочитанных диалогов и групп
    $not_readed_dialogs = 0;
    if(!is_null($_SESSION['logged_user']['friends'])){
        $friends = unserialize($_SESSION['logged_user']['friends']);
        foreach ($friends as $v) {
            $party = array($_SESSION['logged_user']['login'], $v[0]);
            sort($party);
            $party_str = serialize($party);
            $room_id = (ORM::Read_one('message_room', 'party=?', [$party_str]))['id'];
            if(ORM::Exists('messages', 'sender!=? AND room_id=? AND room_type=? AND readed=?', [$_SESSION['logged_user']['login'], $room_id,'message_room', 0])){
                $not_readed_dialogs++;
            }
        }
    }
    if(!is_null($_SESSION['logged_user']['groups_id'])){
        $groups_id = unserialize($_SESSION['logged_user']['groups_id']);
        foreach ($groups_id as $v) {
            if(ORM::Exists('messages', 'sender!=? AND room_id=? AND room_type=? AND pubdate>?', [$_SESSION['logged_user']['login'], $v[0], 'group', $v[1]])){
                $not_readed_dialogs++;
            }
        }
    }
    return $not_readed_dialogs;
}

$not_readed_dialogs = not_readed_dialogs_count();
$count = [
	'friend' => is_null($_SESSION['logged_user']['friends']) ? 0 : count(unserialize($_SESSION['logged_user']['friends'])),
	'notice' => ORM::Read_count('notice', 'recipient=?', [$_SESSION['logged_user']['login']]) === 0 ? null : ORM::Read_count('notice', 'recipient=?', [$_SESSION['logged_user']['login']]),
	'dialogs' => $not_readed_dialogs === 0 ? null : $not_readed_dialogs
];
?>
<div class="left_panel">
	<ul>
        <li><a href="main_page.php?id=<?=$_SESSION['logged_user']['id']?>"><i class="fas fa-home"></i> My Page</a></li>
        <li><a href="friends.php"><i class="fas fa-users"></i> Friends <span class="counter"><?=$count['friend']?></span></a></li>
        <li><a href="dialogs.php"><i class="fas fa-comments"></i> Dialogs <span class="counter"><?=$count['dialogs']?></span></a></li>
        <li><a href="notices.php"><i class="fas fa-envelope"></i> Notices <span class="counter"><?=$count['notice']?></span></a></li>
        <li><a href="search_friend.php"><i class="fas fa-search"></i> Search Friends</a></li>
        <li><form action="<?=$_SERVER['SCRIPT_NAME']?>?id=<?=$_SESSION['logged_user']['id']?>" method="POST">
            <button type="submit" name="doGoLogout"><i class="fas fa-power-off"></i> Logout</button>
        </form></li> 

    </ul>
</div>