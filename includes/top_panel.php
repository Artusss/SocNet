<?php
require_once 'model.php';
?>

<div class="top_panel">
	<div class="container">
		<div class="row">
			<div class="col-3">
				<div class="logotype"><i class="far fa-eye"></i></div>
			</div>
			<?php if(isset($_SESSION['logged_user'])){ ?>
				<div class="col-9">
					<div class="user">
						<a href="../views/main_page.php?id=<?=$_SESSION['logged_user']['id']?>">
							<i class="fas fa-user"></i> <?=$_SESSION['logged_user']['login']?>
						</a>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
</div>