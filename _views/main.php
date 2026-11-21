<?php
$films = G::var('films') ?: [];
$price = G::var('price');
$cta   = session_is('user') ? '/cabinet' : '/login';
?>
<!DOCTYPE html>
<html lang="ru">

<?php view('parts/head.php') ?>

<body>
	<div class="wrapper">
		<div class="wrapper__navigation" id="wrapper__navigation"></div>
		<header class="header">
			<div class="header__overlay"></div>

			<?php view('parts/nav.php') ?>

			<section class="header__text">
				<div class="header__text__block">
					<p class="header__text__p1">Онлайн-покупка билетов на киноночь!</p>
					<p class="header__text__p2">Три фильма подряд под открытым небом Алматы. Выберите места на схеме зала, а оператор подтвердит бронь и выдаст электронный билет с QR-кодом.</p>
					<div class="header__actions">
						<a class="btn btn--amber" href="<?php echo $cta ?>"><?php echo icon('seat') ?> Выбрать места</a>
						<a class="btn btn--ghost" href="#info">Как это работает</a>
					</div>
				</div>
			</section>
		</header>

		<section class="info" id="info">
			<div class="info__text">
				<h2>Забронировать место!</h2>
				<p>Четыре простых шага от регистрации до входа в зал</p>
			</div>
			<div class="info__blocks">
				<div class="info__block">
					<div class="info__card info__one">
						<span class="info__num">1</span>
						<?php echo icon('user', 'info__icon') ?>
						<h3>Аккаунт</h3>
						<p>Зарегистрируйтесь и укажите в профиле имя и телефон</p>
					</div>
				</div>
				<div class="info__block">
					<div class="info__card info__two">
						<span class="info__num">2</span>
						<?php echo icon('seat', 'info__icon') ?>
						<h3>Места</h3>
						<p>Отметьте свободные места на схеме зала и отправьте запрос</p>
					</div>
				</div>
				<div class="info__block">
					<div class="info__card info__three">
						<span class="info__num">3</span>
						<?php echo icon('phone', 'info__icon') ?>
						<h3>Звонок</h3>
						<p>Оператор перезвонит и подтвердит бронь</p>
					</div>
				</div>
				<div class="info__block">
					<div class="info__card info__four">
						<span class="info__num">4</span>
						<?php echo icon('qrcode', 'info__icon') ?>
						<h3>Билет</h3>
						<p>Откройте PDF-билет в кабинете и покажите QR-код на входе</p>
					</div>
				</div>
			</div>
		</section>

		<section class="form" id="form">
			<div class="program">
				<div class="program__head">
					<h2>Программа вечера</h2>
					<?php if ($price) { ?>
					<span class="program__price">Билет на весь сеанс — <?php echo e(money($price)) ?></span>
					<?php } ?>
				</div>

				<?php if ($films) { ?>
				<div class="program__films">
					<?php $i = 0; foreach ($films as $film) { $i++; ?>
					<article class="program__film">
						<span class="program__index"><?php echo $i ?></span>
						<h3><?php echo e($film->name) ?></h3>
						<p class="program__meta"><?php echo e($film->genre) ?> · <?php echo e($film->year) ?> · <?php echo e($film->time) ?></p>
						<p class="program__about"><?php echo e($film->about) ?></p>
						<p class="program__producer">Режиссёр: <?php echo e($film->producer) ?></p>
					</article>
					<?php } ?>
				</div>
				<?php } else { ?>
				<p class="program__empty">Программа скоро появится.</p>
				<?php } ?>

				<a class="btn btn--amber program__cta" href="<?php echo $cta ?>"><?php echo icon('ticket') ?> Забронировать место</a>
			</div>
		</section>

		<?php view('parts/footer.php') ?>

	</div>

<?php view('parts/scripts.php') ?>

</body>

</html>
