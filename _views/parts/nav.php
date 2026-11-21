<nav class="navbar" id="navbar">
	<div class="navbar__container" id="navbar__container">
		<div class="navbar__adapted">
			<a class="navbar__adapted__bar" id="navbar__adapted__bar" href="#" aria-label="Меню"><?php echo icon('menu') ?></a>
		</div>
		<div class="navbar__logo" id="navbar__logo">
			<a class="navbar__logo__cinema" href="/">CINEMA</a>
			<a class="navbar__logo__picture" href="/"><img src="/template/image/logoCN1.png" alt="Cinema Night" width="60" height="60"></a>
			<a class="navbar__logo__night" href="/">NIGHT</a>
		</div>
		<ul class="navbar__list" id="navbar__list">
			<li class="navbar__list__li">
				<a class="navbar__list__a <?php if (G::var('menu__active') == 'main') echo 'menu__active'; ?>" href="/">
					<?php echo icon('home', 'ico__main') ?>
					<span class="navbar__list__span">Главная</span>
				</a>
			</li>
			<li class="navbar__list__li">
				<a class="navbar__list__a <?php if (G::var('menu__active') == 'doc') echo 'menu__active'; ?>" href="/doc">
					<?php echo icon('book', 'ico__documentation') ?>
					<span class="navbar__list__span">Инструкция</span>
				</a>
			</li>
		</ul>
		<div class="navbar__form" id="navbar__form">

			<?php if (!session_is('user')) { ?>
			<a class="navbar__form__login" href="/login">
				<?php echo icon('login') ?>
				<span class="navbar__form__span">Войти</span>
			</a>
			<?php } else { ?>
			<a class="navbar__form__cabinet <?php if (G::var('menu__active') == 'cabinet') echo 'menu__active'; ?>" href="/cabinet">
				<?php echo icon(is_admin() ? 'shield' : 'user') ?>
				<span class="navbar__form__span"><?php echo is_admin() ? 'Оператор' : 'Кабинет' ?></span>
			</a>
			<?php } ?>

		</div>
	</div>
</nav>
