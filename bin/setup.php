<?php
/**
 * First-time setup: creates the session program (3 films), the ticket price
 * and, optionally, an admin (operator) account.
 *
 *     php bin/setup.php                      # films + price only
 *     php bin/setup.php admin secret123      # + admin account "admin"
 *     php bin/setup.php --price=1000         # change the ticket price
 */

if (PHP_SAPI !== 'cli')
{
	exit('Run from the command line: php bin/setup.php');
}

require dirname(__DIR__) . '/web/framework.php';

$args = array_values(array_filter(array_slice($argv, 1), function ($a) { return strpos($a, '--') !== 0; }));
$options = getopt('', ['price::']);

//	**************************************************
//	                     FILMS
//	**************************************************

if (R::count('films') === 0)
{
	$films = [
		[
			'num' => 'Первый', 'name' => 'Эверест', 'genre' => 'Приключения',
			'producer' => 'Балтазар Кормакур', 'time' => '121 минута', 'year' => '2015', 'lang' => 'Русский',
			'about' => 'Приключенческий фильм исландского режиссёра Балтазара Кормакура, главные роли в котором исполнили Джейсон Кларк, Джош Бролин, Джейк Джилленхол, Сэм Уортингтон и Джон Хоукс.'
		],
		[
			'num' => 'Второй', 'name' => 'Титаник', 'genre' => 'Катастрофа',
			'producer' => 'Джеймс Кэмерон', 'time' => '194 минуты', 'year' => '1997', 'lang' => 'Русский',
			'about' => 'Американский фильм-катастрофа 1997 года, снятый режиссёром Джеймсом Кэмероном, в котором показана гибель легендарного лайнера «Титаник».'
		],
		[
			'num' => 'Третий', 'name' => 'Побег из Шоушенка', 'genre' => 'Драма',
			'producer' => 'Фрэнк Дарабонт', 'time' => '142 минуты', 'year' => '1994', 'lang' => 'Русский',
			'about' => 'Культовый американский фильм-драма 1994 года, снятый Фрэнком Дарабонтом по повести Стивена Кинга «Рита Хейуорт и спасение из Шоушенка».'
		]
	];

	foreach ($films as $data)
	{
		$film = R::dispense('films');
		$film->import($data);
		$film->status = 1;
		R::store($film);
	}

	echo "✔ Added the session program: 3 films\n";
}
else
{
	echo "• Films already exist, skipped\n";
}

//	**************************************************
//	                     PRICE
//	**************************************************

$newPrice = isset($options['price']) ? (int) $options['price'] : null;
$price = R::findOne('prices', 'status = 1');

if (!$price || $newPrice)
{
	if ($price)
	{
		$price->status = 0;
		R::store($price);
	}

	$price = R::dispense('prices');
	$price->price = $newPrice ?: 800;
	$price->status = 1;
	R::store($price);

	echo "✔ Ticket price: {$price->price} ₸\n";
}
else
{
	echo "• Ticket price already set: {$price->price} ₸\n";
}

//	**************************************************
//	                     ADMIN
//	**************************************************

if (count($args) >= 2)
{
	[$login, $password] = $args;

	if (!preg_match('/^[A-Za-z0-9_.\-]{3,32}$/', $login) || strlen($password) < 6)
	{
		fwrite(STDERR, "✘ Login: 3-32 latin letters/digits, password: at least 6 characters\n");
		exit(1);
	}

	$user = R::findOne('users', 'login = ?', [$login]) ?: R::dispense('users');
	$isNew = !$user->id;

	$user->login    = $login;
	$user->email    = $user->email ?: $login . '@cinema-night.local';
	$user->password = password_hash($password, PASSWORD_DEFAULT);
	$user->role     = 'admin';
	$user->date     = $user->date ?: date('Y-m-d H:i:s');
	R::store($user);

	if (!R::findOne('profile', 'login = ?', [$login]))
	{
		$profile = R::dispense('profile');
		$profile->import(['login' => $login, 'name' => 'Оператор', 'surname' => '', 'patronymic' => '', 'phone' => '']);
		R::store($profile);
	}

	echo $isNew ? "✔ Created admin \"$login\"\n" : "✔ User \"$login\" is now an admin, password updated\n";
}
else
{
	echo "• No admin account given. Create one with: php bin/setup.php <login> <password>\n";
}
