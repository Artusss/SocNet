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
                    <?php foreach ($friends as $one_friend) { ?>
						<li>
                            <a href="main_page.php?id=<?=$one_friend[1]?>">
                                <?=$one_friend[0]?> <em>#<?=$one_friend[1]?></em>
                            </a>
                        </li>
					<?php } ?>
				</ul>
			</div>
		</div>
	</div>
</div>
<?php require_once '../includes/bot_pattern.php';