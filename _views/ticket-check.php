<?php
$bean    = G::var('bean');
$iden    = G::var('iden');
$message = G::var('message');
$profile = G::var('profile');
$status  = $bean ? (int) $bean->status : null;

if ($status === Booking::CONFIRMED)
{
	[$mod, $badge, $title] = ['ok', 'check', 'Билет действителен'];
}
elseif ($status === Booking::USED)
{
	[$mod, $badge, $title] = ['used', 'check', $message ? 'Билет погашен' : 'Билет уже использован'];
}
else
{
	[$mod, $badge, $title] = ['bad', 'x', 'Билет не найден'];
}
?>
<!DOCTYPE html>
<html lang="ru">

<?php view('parts/head.php') ?>

<body>
	<div class="wrapper">
		<div class="wrapper__navigation" id="wrapper__navigation"></div>

		<?php view('parts/nav.php') ?>

		<div class="check check--<?php echo $mod ?>">
			<div class="check__badge"><?php echo icon($badge) ?></div>
			<h1><?php echo e($title) ?></h1>

			<?php if ($message) { ?>
			<p class="is-success"><?php echo e($message) ?></p>
			<?php } ?>

			<?php if ($bean && in_array($status, [Booking::CONFIRMED, Booking::USED], true)) { ?>
			<table>
				<tr><td>Билет №</td><td><?php echo e($bean->iden) ?></td></tr>
				<tr><td>Код</td><td><?php echo e($bean->code) ?></td></tr>
				<tr><td>Зритель</td><td><?php echo e($profile ? trim($profile->surname . ' ' . $profile->name) : '') ?: e($bean->login) ?></td></tr>
				<tr><td>Места</td><td>
					<?php foreach (Booking::seats($bean) as $seat) { $s = Hall::parse($seat); ?>
					<span class="seat-chip">Ряд <?php echo $s['row'] ?> · Место <?php echo $s['seat'] ?></span>
					<?php } ?>
				</td></tr>
				<tr><td>Сумма</td><td><?php echo e(money($bean->price)) ?></td></tr>
				<?php if ($status === Booking::USED && $bean->updated) { ?>
				<tr><td>Погашен</td><td><?php echo e(date('d.m.Y H:i', strtotime($bean->updated))) ?></td></tr>
				<?php } ?>
			</table>
			<?php } else { ?>
			<p>Номер <?php echo e($iden) ?> не найден или бронь ещё не подтверждена.</p>
			<?php } ?>

			<?php if ($status === Booking::CONFIRMED) { ?>
			<form method="POST">
				<?php csrf_field() ?>
				<button class="btn btn--green" type="submit"><?php echo icon('check') ?> Погасить и пропустить</button>
			</form>
			<?php } ?>

			<a class="btn btn--dark" href="/cabinet"><?php echo icon('arrow-left') ?> В панель оператора</a>
		</div>
	</div>

	<?php view('parts/scripts.php') ?>
</body>
</html>
