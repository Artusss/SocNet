<?php 
require_once '../includes/model.php';

function checkRoom($party){ //возвращает true если диалог существует 
	$check_room = ORM::Exists('message_room', 'party=? ', [$party]);
	return $check_room ? true : false;
}
function sortDB_date(&$arr, $date){ //сортирует таблицу по дате
	$sort_var = array_column($arr, $date);
	foreach ($sort_var as &$v){
		$v = strtotime($v);
	}
	array_multisort($sort_var, SORT_DESC, $arr);
}

function getRooms(){ //возвращает массив со всеми диалогами и группами пользователя
	$dialogs = array();
	if(!is_null($_SESSION['logged_user']['friends'])){
		$friends = unserialize($_SESSION['logged_user']['friends']);
		foreach($friends as $f){
			$dialog = array($_SESSION['logged_user']['login'], $f[0]);
			sort($dialog);
			$our_dialog = serialize($dialog);
			if(!checkRoom($our_dialog)){
				ORM::Create('message_room', [
					'party' => $our_dialog,
					'number_of_participian' => 2
				]);	
			}
			$msg_r = array_merge(ORM::Read_one('message_room', 'party=?', [$our_dialog]), ['type' => 'message_room']);
			$dialogs[] = $msg_r;
		}
		if(!is_null($_SESSION['logged_user']['groups_id'])){
			$groups_id = unserialize($_SESSION['logged_user']['groups_id']);
			foreach ($groups_id as $id_date) {
				$grp = array_merge(ORM::Read_one('groups', 'id=?', [$id_date[0]]), ['type' => 'group']);
				$dialogs[] = $grp;
			}
		}
		sortDB_date($dialogs, 'last_message_date');
	}
	return $dialogs;
}
$dialogs = getRooms();