<?php
require_once '../components/signup.php';
require_once '../includes/top_pattern.php';
require_once '../includes/top_panel.php';
?>

<div class="container">
	<div class="row">
		<div class="col-3"></div>
		<div class="col-6">
			<div class="forms main_cont">
				<div class="err_message">
					<?=$err_message?>
				</div>
				<form action="<?=$_SERVER['SCRIPT_NAME']?>" method="POST">
					<p>
						<label for="login">Ваш логин:</label><br>
						<input type="text" placeholder="Введите логин" name="login" value="<?=@$_POST['login']?>">
					</p>
					<p>
						<label for="email">Ваш e-mail:</label><br>
						<input type="text" placeholder="Введите e-mail" name="email" value="<?=@$_POST['email']?>">
					</p>
					<p>
						<label for="password">Ваш пароль:</label><br>
						<input type="password" placeholder="Введите пароль" name="password" value="<?=@$_POST['password']?>">
					</p>
					<p>
						<label for="password_2">Повторите пароль:</label><br>
						<input type="password" placeholder="Повторите пароль" name="password_2" value="<?=@$_POST['password_2']?>">
					</p>
					<button type="submit" name="doGoSignUp">Зарегистрироваться</button>
				</form>
			</div>
		</div>
		<div class="col-3"></div>
	</div>
</div>
<?php require_once '../includes/bot_pattern.php';