<?php
require_once '../components/main_page.php';
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
			<div class="container">
				<div class="row">
					<div class="col-4">
						<div class="user_photo main_cont">
							<img src="../includes/img/default-user.png" alt="user_photo">
						</div>
					</div>
					<div class="col-8">
						<div class="user_info main_cont">
							<ul>
								<li>User : <?=$main_user['login']?> <em>#<?=$main_user['id']?></em></li>
								<li><em><?=$main_user['email']?></em></li>
							</ul>
						</div>
						<div class="user_about main_cont">
							<?php
							if($main_user['login'] !== $_SESSION['logged_user']['login']){ 
								if(checkFriend($main_user['login'])){ ?>
									<p>Пользователь <?=$main_user['login']?> у вас в друзьях</p>
									<form action="<?=$_SERVER['SCRIPT_NAME']?>?id=<?=$_GET['id']?>" method="POST">
										<button type="submit" name="doGoDeleteFriend">Удалить из друзей</button>
									</form>
									<?php 
								}else if(checkOffer('OfferFriend', $main_user)){ ?>
									<p>Ожидается ответ пользователя <?=$main_user['login']?> на запрос в друзья</p>
									<?php
								}else{ ?>
									<form action="<?=$_SERVER['SCRIPT_NAME']?>?id=<?=$_GET['id']?>" method="POST">
										<button type="submit" name="doGoAddNotice">Заявка в друзья</button>
									</form>
								<?php }
							}else{ ?>
								<form action="<?=$_SERVER['SCRIPT_NAME']?>?id=<?=$_GET['id']?>" method="POST">
									<button type="submit" name="doGoLogout">Выйти</button>
								</form>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php require_once '../includes/bot_pattern.php';
