<?php
require_once '../components/notices.php';
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
			<div class="notices">
				<ul>
					<?php 
					foreach($notice as $v){ ?>
						<li>
							<div class="notice main_cont">
								<div class="author"><?=$v['sender']?></div>
								<div class="text"><?=$v['text']?></div>
								<form action="<?=$_SERVER['SCRIPT_NAME']?>" method="POST">
									<input type="hidden" name="sender" value="<?=$v['sender']?>">
									<input type="hidden" name="id" value="<?=$v['id']?>">
									<?php 
									switch ($v['type']) {
										case 'OfferGroup': 
											?><button type="submit" name="doGoOk">Хорошо</button><?php
											break;
										case 'OfferFriend':
											?><button type="submit" name="doGoAdd">Принять заявку</button><?php
											break;
									} ?>
								</form>
							</div>
						</li>
					<?php } ?>
				</ul>
			</div>
		</div>
	</div>
</div>
<?php require_once '../includes/bot_pattern.php';