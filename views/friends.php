<?php 
require_once '../components/friends.php';
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
			<div class="friend_list main_cont">
				<p>List of your friends:</p>
				<ul>
					<?php
					foreach ($friends as $v) { ?>
						<li><a href="main_page.php?id=<?=$v[1]?>"><?=$v[0]?> <em>#<?=$v[1]?></em></a></li>
					<?php } ?>
				</ul>
			</div>
		</div>
	</div>
</div>
<?php require_once '../includes/bot_pattern.php';