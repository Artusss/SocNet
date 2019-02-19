<?php
require_once '../includes/model.php';
//создает группу
function createRoom($party, $name='name', $submit='doGoCreateRoom', $method='POST'){
	if($method === 'POST'){
		$name  = $_POST["$name"];
	}else if($method === 'GET'){
		$name  = $_GET["$name"];
	}
	$errors = array();
	if(!isset($party)){
		$errors[] = 'Выберите участников беседы';
	}
	if(trim($name) == ''){
		$errors[] = 'Введите название беседы'; 
	}
	if(isset($party)){
		if((count($party) < 2)){
			$errors[] = 'В беседе должно состоять не менее 3 человек'; 
		}
	}

	if(empty($errors)){ //при отсутствии ошибок добавляет указанных участников в беседу 
		$party[] = $_SESSION['logged_user']['login'];
		sort($party);
		$party_str = serialize($party);
		ORM::Create('groups', [
			'name' => $name,
			'party' => $party_str,
			'number_of_participian' => count($party)
		]);

		$group = ORM::Read_one('groups', 'name=? AND party=? ORDER BY pubdate DESC', [$name, $party_str]);
		//добавляет в таблицу users информацию по беседе(id и дату создания)
		foreach($party as $v){
			$party_user = ORM::Read_one('users', 'login=?', [$v]);
			$user_groups_id_list = unserialize($party_user['groups_id']);

			$user_groups_id_list[] = [$group['id'], date('Y-m-d H:i:s')];
			$user_groups_id_str = serialize($user_groups_id_list);
			
			ORM::Update('users', 'groups_id=?', 'login=?', [$user_groups_id_str, $v]);
			
			//отправляет оповещение о добавлении в беседу не создателю 
			//и обновляет информацию о беседах в текущей сессии
			if($party_user['login'] === $_SESSION['logged_user']['login']){
				$_SESSION['logged_user']['groups_id'] = $user_groups_id_str;
			}else{
				ORM::Create('notice', [
				'text' => "Пользователь {$_SESSION['logged_user']['login']} создал с вами беседу {$name}.",
				'sender' => $_SESSION['logged_user']['login'],
				'recipient' => $v,
				'type' => 'OfferGroup'
			]);
			}
		}
		return true;
	}else{
		return array_shift($errors);
	}
}

$friends = unserialize($_SESSION['logged_user']['friends']); //получаем массив друзей

if(isset($_POST['doGoCreateRoom'])){
	if(createRoom($_POST['party']) === true){
		header("Location: dialogs.php");
		exit();
	}else{
		$err_message = createRoom($_POST['party'])."<hr>";
	}
} 

