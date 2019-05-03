<?php
require_once '../includes/model.php';
//создает группу
function createRoom($count){
	$name   = $_POST['name'];
	$party  = $_POST['party'];
	$errors = array();

	if(!isset($party)){
		$errors[] = 'Выберите участников беседы';
	}
	if(trim($name) == ''){
		$errors[] = 'Введите название беседы'; 
	}
	if(isset($party)){
		if((count($party) < $count)){
			$errors[] = "В беседе должно состоять не менее {$count} человек";
		}
	}

	if(empty($errors)){ //при отсутствии ошибок добавляет указанных участников в беседу 

	    $party_str = addInParty_str($party, $_SESSION['logged_user']['login']);

		ORM::Create('groups', [
			'name'                  => $name,
			'party'                 => $party_str,
			'number_of_participian' => count($party)
		]);

		$group = ORM::Read_one('groups', 'name=? AND party=? ORDER BY pubdate DESC', [$name, $party_str]);

		foreach($party as $one_of_party){
			getInfoInUserDB($group, $one_of_party);
		}
		return true;
	}else{
		return array_shift($errors);
	}
}

function addInParty_str($party, $member){
    $party[] = $member;
    sort($party);
    return serialize($party);
}

function getInfoInUserDB($group, $one_of_party){ //добавляет в таблицу users информацию по беседе(id и дату создания)
    $party_user            = ORM::Read_one('users', 'login=?', [$one_of_party]);
    $user_groups_id_list   = unserialize($party_user['groups_id']);

    $user_groups_id_list[] = [$group['id'], date('Y-m-d H:i:s')];
    $user_groups_id_str    = serialize($user_groups_id_list);

    ORM::Update('users', 'groups_id=?', 'login=?', [$user_groups_id_str, $one_of_party]);

    //отправляет оповещение о добавлении в беседу не создателю
    //и обновляет информацию о беседах в текущей сессии
    if($party_user['login'] === $_SESSION['logged_user']['login']){
        $_SESSION['logged_user']['groups_id'] = $user_groups_id_str;
    }else{
        ORM::Create('notice', [
            'text'      => "Пользователь {$_SESSION['logged_user']['login']} создал с вами беседу {$name}.",
            'sender'    => $_SESSION['logged_user']['login'],
            'recipient' => $one_of_party,
            'type'      => 'OfferGroup'
        ]);
    }
}

$friends = unserialize($_SESSION['logged_user']['friends']); //получаем массив друзей
if(isset($_SESSION['logged_user'])){
    if(isset($_POST['doGoCreateRoom'])){
        if(createRoom(2) === true){
            header("Location: dialogs.php");
            exit();
        }else{
            $err_message = createRoom(2)."<hr>";
        }
    }
}


