<?php
/**
 * Cinema hall layout and seat helpers.
 *
 * A seat is stored as the string "row,seat", e.g. "3,12".
 */
class Hall
{
	/** Number of seats in every row: [16, 16, ...] */
	public static function layout()
	{
		return Config::getapp('hall');
	}

	public static function isValidSeat($value)
	{
		if (!is_string($value) || !preg_match('/^(\d{1,2}),(\d{1,2})$/', $value, $m))
		{
			return false;
		}

		$layout = self::layout();
		$row = (int) $m[1];
		$seat = (int) $m[2];

		return $row >= 1 && $row <= count($layout) && $seat >= 1 && $seat <= $layout[$row - 1];
	}

	/** "3,12" -> ['row' => 3, 'seat' => 12] */
	public static function parse($value)
	{
		[$row, $seat] = array_map('intval', explode(',', $value));
		return ['row' => $row, 'seat' => $seat];
	}

	/** Seats that can't be booked any more (pending, confirmed or used) */
	public static function occupiedSeats()
	{
		$occupied = [];

		foreach (R::find('seat', 'status IN (?, ?, ?)', [Booking::PENDING, Booking::CONFIRMED, Booking::USED]) as $booking)
		{
			foreach (Booking::seats($booking) as $seat)
			{
				$occupied[$seat] = true;
			}
		}

		return array_keys($occupied);
	}
}
