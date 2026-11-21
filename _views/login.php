<!DOCTYPE html>
<html lang="ru">

<?php view('parts/head.php') ?>

<body>
	<div class="container">
		<div class="big-image"></div>
		<div class="wrapper">
			<a class="arrow-back" href="/" aria-label="На главную"><?php echo icon('arrow-left') ?></a>
			<div class="form__wrapper">

				<form class="form" id="loginForm" style="display: block;" novalidate>

					<div class="logo__circle"><a class="logo__image" href="/"><img src="/template/image/logoCN1.png" alt="Cinema Night" width="70" height="70"></a></div>

					<span class="form__welcome">Добро пожаловать!</span>

					<div class="login out-input">
						<input class="input-style" type="text" name="login" placeholder="Логин" autocomplete="username" required>
					</div>

					<div class="password out-input">
						<input class="input-style" id="password-1" type="password" name="password" placeholder="Пароль" autocomplete="current-password" required>
						<span class="eye eye-active" title="Показать пароль"><?php echo icon('eye') ?></span>
						<span class="eye" title="Скрыть пароль"><?php echo icon('eye-off') ?></span>
					</div>

					<div class="result_form" id="result_form_log"></div>

					<div class="sign">
						<button class="button green" id="do-sign-in" type="submit">Войти</button>
					</div>

					<div class="pop">
						<span>У вас нет аккаунта? </span><span class="clip-sign-up" id="sign-up">Зарегистрироваться</span>
					</div>

				</form>

				<form class="form" id="regForm" style="display: none;" novalidate>

					<div class="logo__circle"><a class="logo__image" href="/"><img src="/template/image/logoCN1.png" alt="Cinema Night" width="70" height="70"></a></div>

					<span class="form__welcome">Регистрация</span>

					<div class="email out-input">
						<input class="input-style" type="text" name="login" placeholder="Логин (латиницей)" autocomplete="username" required>
					</div>

					<div class="login out-input">
						<input class="input-style" type="email" name="email" placeholder="Email" autocomplete="email" required>
					</div>

					<div class="password out-input">
						<input class="input-style" id="password-2" type="password" name="password" placeholder="Пароль (от 6 символов)" autocomplete="new-password" required>
						<span class="eye eye-active" title="Показать пароль"><?php echo icon('eye') ?></span>
						<span class="eye" title="Скрыть пароль"><?php echo icon('eye-off') ?></span>
					</div>

					<div class="password out-input">
						<input class="input-style" id="password-3" type="password" name="password_r" placeholder="Повторите пароль" autocomplete="new-password" required>
					</div>

					<div class="result_form" id="result_form_reg"></div>

					<div class="sign">
						<button class="button red" id="do-sign-up" type="submit">Создать</button>
					</div>

					<div class="pop">
						<span>У вас уже есть аккаунт? </span><span class="clip-sign-in" id="sign-in">Войти</span>
					</div>

				</form>

			</div>
		</div>
	</div>
	<script src="/template/js/jquery-3.3.1.min.js"></script>
	<script src="/template/js/app.js"></script>
	<script src="/template/js/login.js"></script>
</body>
</html>
