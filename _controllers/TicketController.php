<?php
/**
 * /ticket{iden} - PDF ticket with QR code (owner or admin)
 * /wipe{iden}   - ticket check at the entrance (admin scans the QR code)
 */
class TicketController
{

//	**************************************************
//	**************************************************


	public function actionIndex($params)
	{
		if (!session_is('user'))
		{
			redirect('/login');
		}

		$bean = Booking::findByIden($params[0] ?? '');

		$allowed = $bean
			&& in_array((int) $bean->status, [Booking::CONFIRMED, Booking::USED], true)
			&& (is_admin() || auth_user('login') === $bean->login);

		if (!$allowed)
		{
			http_response_code(404);
			include ROOT . '/error.php';
			return;
		}

		G::setvar(false, [
			'bean'    => $bean,
			'iden'    => $bean->iden,
			'login'   => $bean->login,
			'profile' => R::findOne('profile', 'login = ?', [$bean->login])
		]);

		view('ticketing.php');
	}


//	**************************************************
//	**************************************************


	public function actionWipe($params)
	{
		$iden = $params[0] ?? '';

		if (!is_admin())
		{
			redirect('/login');
		}

		$bean = Booking::findByIden($iden);
		$message = null;

		if ($bean && $_SERVER['REQUEST_METHOD'] === 'POST')
		{
			if (Booking::setStatus($bean->id, Booking::CONFIRMED, Booking::USED))
			{
				$message = 'Билет погашен. Можно проходить!';
				$bean = Booking::findByIden($iden);
			}
		}

		G::setvar(true, [
			'menu__active' => 'cabinet',
			'css' => [
				'parts/wrapper.css',
				'parts/nav.css',
				'cabinet.css'
			]
		]);

		G::addvar([
			'bean'    => $bean,
			'iden'    => $iden,
			'message' => $message,
			'profile' => $bean ? R::findOne('profile', 'login = ?', [$bean->login]) : null
		]);

		view('ticket-check.php');
	}


}
