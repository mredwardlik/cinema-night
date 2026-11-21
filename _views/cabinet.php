<?php
$profile  = G::var('profile');
$films    = G::var('films') ?: [];
$price    = (int) G::var('price');
$bookings = G::var('bookings') ?: [];
$pending  = G::var('pending') ?: [];
$tickets  = G::var('tickets') ?: [];
$used     = G::var('used') ?: [];

$field = function ($name) use ($profile) {
	return $profile ? e($profile->$name) : '';
};

$seatList = function ($booking) {
	$items = [];
	foreach (Booking::seats($booking) as $seat)
	{
		$s = Hall::parse($seat);
		$items[] = '<span class="seat-chip">Ряд ' . $s['row'] . ' · Место ' . $s['seat'] . '</span>';
	}
	return implode('', $items);
};

$profiles = [];
$fullName = function ($login) use (&$profiles) {
	if (!isset($profiles[$login]))
	{
		$profiles[$login] = R::findOne('profile', 'login = ?', [$login]);
	}
	$p = $profiles[$login];
	return $p ? trim($p->surname . ' ' . $p->name . ' ' . $p->patronymic) : '';
};
$phoneOf = function ($login) use (&$profiles, $fullName) {
	$fullName($login);
	return $profiles[$login] ? $profiles[$login]->phone : '';
};

$profileIncomplete = !$profile || trim($profile->name) === '' || trim($profile->phone) === '';
?>
<!DOCTYPE html>
<html lang="ru">

<?php view('parts/head.php') ?>

<body>
	<div class="wrapper">
		<div class="wrapper__navigation" id="wrapper__navigation"></div>

		<?php view('parts/nav.php') ?>

		<header class="header"></header>

		<div class="container">

			<div class="box__photo">
				<img class="user-photo" src="/template/image/logoCN1.png" alt="">
			</div>

			<section class="box-of-data">

				<p class="greeting">
					<?php echo is_admin() ? 'Панель оператора' : 'Личный кабинет' ?> ·
					<b><?php echo e(auth_user('login')) ?></b>
				</p>

				<?php if (!is_admin() && $profileIncomplete) { ?>
				<p class="notice"><?php echo icon('phone') ?> Заполните имя и номер телефона: по ним оператор подтвердит бронь.</p>
				<?php } ?>

				<div class="boxer-wrapper">
					<form class="boxer" id="profileForm" novalidate>
						<div class="p-login">
							<h2>Логин</h2><input type="text" name="login" value="<?php echo e(auth_user('login')) ?>" disabled>
						</div>
						<div class="p-login">
							<h2>Email</h2><input type="email" name="email" value="<?php echo e(auth_user('email')) ?>" disabled>
						</div>
						<div class="p-login">
							<h2>Имя</h2><input type="text" name="name" value="<?php echo $field('name') ?>" maxlength="50" disabled>
						</div>
						<div class="p-login">
							<h2>Фамилия</h2><input type="text" name="surname" value="<?php echo $field('surname') ?>" maxlength="50" disabled>
						</div>
						<div class="p-login">
							<h2>Отчество</h2><input type="text" name="patronymic" value="<?php echo $field('patronymic') ?>" maxlength="50" disabled>
						</div>
						<div class="p-login">
							<h2>Номер телефона</h2><input type="tel" name="phone" value="<?php echo $field('phone') ?>" placeholder="+7 701 123 45 67" disabled>
						</div>
						<div class="p-login responce" id="responce"></div>
						<div class="p-login butts">
							<button class="button-save button" id="button-save" type="submit" style="display: none;">Сохранить</button>
							<button class="button-change button" id="button-change" type="button"><?php echo icon('edit') ?> Изменить</button>
						</div>
					</form>
				</div>

				<div class="logout-box">
					<form action="/cabinet/logout" method="POST">
						<?php csrf_field() ?>
						<input type="hidden" name="redirect" value="/login">
						<button class="button-logout button" id="button-logout" type="submit"><?php echo icon('logout') ?> Выйти</button>
					</form>
				</div>

			</section>

			<section class="content">
				<div class="choose-block">
					<h2 class="choose-text">Сеанс</h2>
				</div>

				<div class="session-block fixed-film">

					<?php foreach ($films as $film) { ?>
					<div class="film-block f_film">
						<h2><?php echo e($film->num) ?> фильм</h2>
						<div class="film-info-block">
							<table>
								<tbody>
									<tr><td>Название:</td><td><?php echo e($film->name) ?></td></tr>
									<tr><td>Жанр:</td><td><?php echo e($film->genre) ?></td></tr>
									<tr><td>Режиссер:</td><td><?php echo e($film->producer) ?></td></tr>
									<tr><td>Год выпуска:</td><td><?php echo e($film->year) ?></td></tr>
									<tr><td>Продолжительность:</td><td><?php echo e($film->time) ?></td></tr>
									<tr><td>Язык:</td><td><?php echo e($film->lang) ?></td></tr>
									<tr><td>Краткое описание:</td><td><?php echo e($film->about) ?></td></tr>
								</tbody>
							</table>
						</div>
					</div>
					<?php } ?>

					<?php if (!$films) { ?>
					<p class="empty">Программа сеанса пока не опубликована.</p>
					<?php } ?>

				</div>
				<div class="price-block">
					<p class="price-string">Цена сеанса: <?php echo $price ? e(money($price)) : 'уточняется' ?></p>
				</div>
			</section>

<?php if (!is_admin()) { ?>

			<section class="content">
				<div class="choose-block">
					<h2 class="choose-text">Выберите место</h2>
				</div>
				<div class="hole-block">
					<div class="screen">Экран</div>
					<div class="cinema room" id="room" data-hall="<?php echo e(json_encode(Hall::layout())) ?>" data-price="<?php echo $price ?>"></div>
					<div class="legend">
						<span><i class="seat seat--demo"></i> Свободно</span>
						<span><i class="seat seat--demo bay"></i> Выбрано</span>
						<span><i class="seat seat--demo bought"></i> Занято</span>
					</div>
				</div>
				<div class="block-take">
					<p class="selection" id="selection">Нажмите на свободные места на схеме</p>
					<div class="responce" id="responce-taken"></div>
					<button class="button-take button" id="button-take" type="button" disabled><?php echo icon('ticket') ?> Забронировать</button>
				</div>
			</section>

			<?php if ($bookings) { ?>
			<section class="content">
				<div class="choose-block">
					<h2 class="choose-text">Мои билеты</h2>
				</div>

				<div class="tickets-block">
					<?php $num = 0; foreach ($bookings as $seat) { $num++; $status = (int) $seat->status; ?>
					<div class="ticket ticket--<?php echo $status ?>">
						<h2 class="num-box"><?php echo $num ?></h2>
						<span class="status status--<?php echo $status ?>"><?php echo e(Booking::$statusNames[$status]) ?></span>

						<?php if ($status === Booking::PENDING) { ?>
						<div class="barcode barcode--wait"><?php echo icon('clock') ?></div>
						<p class="ticket-note">Оператор позвонит вам, чтобы подтвердить бронь</p>
						<?php } else { ?>
						<a href="/ticket<?php echo e($seat->iden) ?>" target="_blank" class="barcode" title="Открыть билет"><?php echo icon('qrcode') ?></a>
						<div class="ticket-card">
							<a href="/ticket<?php echo e($seat->iden) ?>" target="_blank">Билет № <?php echo e($seat->iden) ?></a>
						</div>
						<?php } ?>

						<div class="places"><?php echo $seatList($seat) ?></div>

						<div class="ticket-price-block">
							<p class="price-string-for-ticket"><?php echo e(money($seat->price)) ?></p>
						</div>
					</div>
					<?php } ?>
				</div>
			</section>
			<?php } ?>

<?php } else { ?>

			<section class="content">
				<div class="choose-block">
					<h2 class="choose-text">Заявки на бронь <span class="counter"><?php echo count($pending) ?></span></h2>
				</div>

				<div class="admin-block">
					<?php if (!$pending) { ?>
					<p class="empty">Новых заявок нет.</p>
					<?php } ?>

					<?php foreach ($pending as $seat) { $phone = $phoneOf($seat->login); ?>
					<div class="request" id="request-<?php echo (int) $seat->id ?>">
						<div class="request__who">
							<b><?php echo e($fullName($seat->login) ?: $seat->login) ?></b>
							<span>@<?php echo e($seat->login) ?> · <?php echo e(date('d.m.Y H:i', strtotime($seat->date))) ?></span>
							<?php if ($phone) { ?>
							<a class="request__phone" href="tel:<?php echo e(preg_replace('/[^0-9+]/', '', $phone)) ?>"><?php echo icon('phone') ?> <?php echo e($phone) ?></a>
							<?php } ?>
						</div>
						<div class="places"><?php echo $seatList($seat) ?></div>
						<div class="request__sum"><?php echo e(money($seat->price)) ?></div>
						<div class="request__actions">
							<button class="btn btn--green btn--small js-approve" data-id="<?php echo (int) $seat->id ?>" type="button"><?php echo icon('check') ?> Подтвердить</button>
							<button class="btn btn--red btn--small js-reject" data-id="<?php echo (int) $seat->id ?>" type="button"><?php echo icon('x') ?> Отклонить</button>
						</div>
					</div>
					<?php } ?>
					<div class="responce" id="responce-admin"></div>
				</div>
			</section>

			<section class="content">
				<div class="choose-block">
					<h2 class="choose-text">Выданные билеты <span class="counter"><?php echo count($tickets) ?></span></h2>
				</div>

				<div class="admin-block">
					<?php if (!$tickets) { ?>
					<p class="empty">Подтверждённых билетов пока нет.</p>
					<?php } ?>

					<?php foreach ($tickets as $seat) { ?>
					<div class="request">
						<div class="request__who">
							<b><?php echo e($fullName($seat->login) ?: $seat->login) ?></b>
							<span>Билет № <?php echo e($seat->iden) ?> · код <?php echo e($seat->code) ?></span>
						</div>
						<div class="places"><?php echo $seatList($seat) ?></div>
						<div class="request__sum"><?php echo e(money($seat->price)) ?></div>
						<div class="request__actions">
							<a class="btn btn--amber btn--small" href="/ticket<?php echo e($seat->iden) ?>" target="_blank"><?php echo icon('ticket') ?> PDF</a>
							<a class="btn btn--small btn--dark" href="/wipe<?php echo e($seat->iden) ?>"><?php echo icon('qrcode') ?> Проверка</a>
						</div>
					</div>
					<?php } ?>

					<?php if ($used) { ?>
					<p class="empty">Погашено на входе: <?php echo count($used) ?></p>
					<?php } ?>
				</div>
			</section>

<?php } ?>

		</div>
	</div>

	<?php view('parts/scripts.php') ?>
	<script src="/template/js/cabinet.js"></script>

</body>
</html>
