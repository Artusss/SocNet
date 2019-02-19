<?php 
require_once '../components/dialogs.php';
require_once '../components/left_panel.php';
require_once '../includes/top_pattern.php';
require_once '../includes/top_panel.php';
?>

<div class="container">
	<div class="row">
		<div class="col-3">
			<?php require_once '../includes/left_panel.php'; ?>
		</div>
		<div class="col-9">
			<div class="dialogs">
				<ul>
					<?php
					foreach($dialogs as $one_dialog){
						$last_message = ORM::Read_one('messages', 'room_id=? AND room_type=? ORDER BY pubdate DESC', [$one_dialog['id'], $one_dialog['type']]);
						if(is_null($one_dialog['name'])){
							$party_arr = unserialize($one_dialog['party']);
							foreach ($party_arr as $v) {
								if($v !== $_SESSION['logged_user']['login']){
									$one_dialog['name'] = $v;
									break;
								}
							}
						} ?>
						<a href="dialog.php?room_id=<?=$one_dialog['id']?>&room_type=<?=$one_dialog['type']?>&room_name=<?=$one_dialog['name']?>">
							<div class="dialog main_cont">
								<li>
									<em>Диалог с </em><?=$one_dialog['name']?>   
									<span class="date"><em><?=$one_dialog['last_message_date']?></em></span>
									<div>
										<span class="last_message">
											<?php 
											if($last_message['sender'] == $_SESSION['logged_user']['login']) echo "Вы: "; 
											?> 
											<?=mb_strimwidth($last_message['message'], 0, 20, "...")?>
										</span>
										<?php
										if(($last_message['readed'] == 0) && ($last_message['sender'] == $_SESSION['logged_user']['login'])){ ?> <i class="fas fa-genderless"></i> <?php }

											if($one_dialog['type'] == 'message_room'){
												if(($last_message['readed'] == 0) && ($last_message['sender'] != $_SESSION['logged_user']['login'])){
													$not_readed_count = ORM::Read_count('messages', 'sender!=? AND room_id=? AND room_type=? AND readed=?', [$_SESSION['logged_user']['login'], $one_dialog['id'], $one_dialog['type'], 0]);
													if($not_readed_count > 0){ ?>  
														<span class="counter"><?=$not_readed_count?></span>
													<?php }
												}
											}else if($one_dialog['type'] == 'group'){
												$groups_id = unserialize($_SESSION['logged_user']['groups_id']);
												$not_readed_count = 0;
												foreach ($groups_id as $v) {
													if(($v[0] == $one_dialog['id']) && ($last_message['sender'] != $_SESSION['logged_user']['login'])){
														$not_readed_count = ORM::Read_count('messages', 'sender!=? AND room_id=? AND room_type=? AND pubdate>?', [$_SESSION['logged_user']['login'], $one_dialog['id'], $one_dialog['type'], $v[1]]);
													}
												}
												if($not_readed_count > 0){ ?> 
													<span class="counter"><?=$not_readed_count?></span>
												<?php }
											}	?>
										</div>
									</li>
								</div>
							</a>
							<?php
							$one_dialog['name'] = null;
						} ?>
					</ul>
					<?php if(!is_null($_SESSION['logged_user']['friends'])){ ?>
						<div class="create_group"><a href="create_room.php"><i class="fas fa-plus"></i> Create group</a></div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
	<?php require_once '../includes/bot_pattern.php';