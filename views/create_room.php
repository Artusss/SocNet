<?php
require_once '../components/create_room.php';
require_once '../components/left_panel.php';
require_once '../includes/top_pattern.php';
require_once '../includes/top_panel.php';
?>
<div class="container">
	<div class="row">
		<div class="col-3">
			<?php require_once '../includes/left_panel.php'; ?>
		</div>
		<div class="col-6">
			<div class="forms main_cont">
				<div class="err_message">
					<?=$err_message?>
				</div>
				<form action="<?=$_SERVER['SCRIPT_NAME']?>" method="POST">
					<label for="name">Название беседы:</label>
					<input type="text" placeholder="Введите название" name="name" value="<?=@$_POST['name']?>"><br>
					Выберите участников:<br/>
					<ul>
						<?php 
						foreach($friends as $fr){ ?>
							<li>
								<label for="party[]"><?=$fr[0]?></label>
								<input type="checkbox" name="party[]" value="<?=$fr[0]?>">
							</li>
						<?php }	?>
					</ul>	
					<button type="submit" name="doGoCreateRoom">Создать беседу</button>
				</form>
			</div>
		</div>
		<div class="col-3"></div>
	</div>
</div>
<?php require_once '../includes/bot_pattern.php';
