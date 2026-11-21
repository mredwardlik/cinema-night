<?php
/**
 * Seat bookings (RedBean table "seat").
 *
 * Lifecycle: PENDING -> CONFIRMED (operator called the user) -> USED (ticket scanned at the entrance)
 *            PENDING -> REJECTED (seats become free again)
 */
class Booking
{
	const PENDING   = 0;
	const CONFIRMED = 1;
	const USED      = 2;
	const REJECTED  = 3;

	public static $statusNames = [
		self::PENDING   => 'Ожидает подтверждения',
		self::CONFIRMED => 'Подтверждён',
		self::USED      => 'Использован',
		self::REJECTED  => 'Отклонён'
	];

	/** Seats of a booking as ["1,5", "1,6"] */
	public static function seats($booking)
	{
		$seats = json_decode((string) $booking->seat, true);
		return is_array($seats) ? $seats : [];
	}

	public static function currentPrice()
	{
		$price = R::findOne('prices', 'status = 1');
		return $price ? (int) $price->price : 0;
	}

	/**
	 * Creates a pending booking. Returns [true, bean] or [false, "error message"].
	 */
	public static function create($login, $seats)
	{
		if (!is_array($seats) || empty($seats))
		{
			return [false, 'Выберите место!'];
		}

		$seats = array_values(array_unique($seats));
		$max = (int) Config::getapp('max_seats_per_booking');

		if (count($seats) > $max)
		{
			return [false, "За один раз можно забронировать не больше $max мест."];
		}

		foreach ($seats as $seat)
		{
			if (!Hall::isValidSeat($seat))
			{
				return [false, 'Такого места нет в зале.'];
			}
		}

		$price = self::currentPrice();

		if ($price <= 0)
		{
			return [false, 'Продажа билетов ещё не открыта.'];
		}

		R::begin();

		try
		{
			$taken = array_intersect($seats, Hall::occupiedSeats());

			if ($taken)
			{
				R::rollback();
				$list = implode(', ', array_map(function ($seat) {
					$s = Hall::parse($seat);
					return "ряд {$s['row']}, место {$s['seat']}";
				}, $taken));
				return [false, "Эти места уже заняты: $list. Выберите другие."];
			}

			$booking = R::dispense('seat');
			$booking->login  = $login;
			$booking->seat   = json_encode($seats);
			$booking->iden   = self::uniqueIden();
			$booking->code   = Str::random(2, 2, 6);
			$booking->date   = date('Y-m-d H:i:s');
			$booking->cost   = $price;
			$booking->kof    = count($seats);
			$booking->price  = count($seats) * $price;
			$booking->status = self::PENDING;
			R::store($booking);

			R::commit();
			return [true, $booking];
		}
		catch (Exception $e)
		{
			R::rollback();
			throw $e;
		}
	}

	public static function setStatus($id, $from, $to)
	{
		$booking = R::findOne('seat', 'id = ? AND status = ?', [(int) $id, $from]);

		if (!$booking)
		{
			return false;
		}

		$booking->status = $to;
		$booking->updated = date('Y-m-d H:i:s');
		R::store($booking);

		return $booking;
	}

	public static function findByIden($iden)
	{
		return R::findOne('seat', 'iden = ?', [(string) $iden]);
	}

	public static function forUser($login)
	{
		return R::find('seat', 'login = ? AND status IN (?, ?, ?) ORDER BY id DESC', [$login, self::PENDING, self::CONFIRMED, self::USED]);
	}

	public static function withStatus($status)
	{
		return R::find('seat', 'status = ? ORDER BY id DESC', [$status]);
	}

	/** 9-digit ticket number that is not used yet */
	private static function uniqueIden()
	{
		do
		{
			$iden = (string) random_int(100000000, 999999999);
		}
		while (R::count('seat', 'iden = ?', [$iden]) > 0);

		return $iden;
	}
}
