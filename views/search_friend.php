<?php
require_once '../components/search_friend.php';
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
			<div class="founded_users">
				<form action="<?=$_SERVER['SCRIPT_NAME']?>" method="POST">
					<div class="container">
						<div class="row">
							<div class="col-9">
								<input type="text" name="friend_login" placeholder="  Введите имя для поиска.." value="<?=@$_POST['friend_login']?>">
							</div>
							<div class="col-3">
								<button type="submit" name="doGoSearchFriend">Найти</button>
							</div>
						</div>
					</div>
				</form>
				<ul>
					<?php
					if(isset($founded_user)){
						if(!empty($founded_user)){
							foreach($founded_user as $v){ ?>
								<li>
									<a href="main_page.php?id=<?=$v['id']?>">
										<div class="founded_user main_cont">
											<?=$v['login']?> <em>#<?=$v['id']?></em>
										</div>
									</a>
								</li>
							<?php }
						}else{ ?>
							<li><div class="founded_user main_cont">Совпадений не найдено</div></li>
						<?php }
					} ?>
				</ul>
			</div>
		</div>
	</div>
</div>
<?php require_once '../includes/bot_pattern.php';