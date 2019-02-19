<?php
require_once 'components/index.php';
require_once 'includes/top_pattern.php';
require_once 'includes/top_panel.php';
?>

<?php if(!isset($_SESSION['logged_user'])){ ?>
	<div class="container">
		<div class="row">
			<div class="col-3"></div>
			<div class="col-6">
				<div class="auth_reg">
					<div><a href="views/login.php">Авторизация</a></div>
					<div><a href="views/signup.php">Регистрация</a></div>
				</div>
			</div>
			<div class="col-3"></div>
		</div>
	</div>
<?php }	?>

<?php require_once 'includes/bot_pattern.php';
