<?php
/**
 * Personal cabinet: profile, seat booking, own tickets.
 * For admins: booking requests and ticket management.
 */
class CabinetController
{


//	**************************************************
//	**************************************************

	private function requireUser()
	{
		if (!session_is('user'))
		{
			if (Ajax::is())
			{
				json_response(['error' => 'Войдите в аккаунт'], 401);
			}
			redirect('/login');
		}
	}

	private function requireAdmin()
	{
		$this->requireUser();

		if (!is_admin())
		{
			json_response(['error' => 'Недостаточно прав'], 403);
		}
	}

	private function profile()
	{
		return R::findOne('profile', 'login = ?', [auth_user('login')]);
	}


//	**************************************************
//	**************************************************


	public function actionIndex()
	{
		$this->requireUser();

		$login = auth_user('login');

		G::setvar(true, [
			'menu__active' => 'cabinet',
			'css' => [
				'parts/wrapper.css',
				'parts/nav.css',
				'cabinet.css'
			]
		]);

		G::addvar([
			'profile'  => $this->profile(),
			'films'    => R::find('films', 'status = 1 ORDER BY id LIMIT 3'),
			'price'    => Booking::currentPrice(),
			'bookings' => Booking::forUser($login),
			'pending'  => is_admin() ? Booking::withStatus(Booking::PENDING) : [],
			'tickets'  => is_admin() ? Booking::withStatus(Booking::CONFIRMED) : [],
			'used'     => is_admin() ? Booking::withStatus(Booking::USED) : []
		]);

		view('cabinet.php');
	}


//	**************************************************
//	**************************************************


	public function actionCh()
	{
		Post::catch(function ()
		{
			Ajax::catch(function ()
			{
				$this->requireUser();

				$email = Post::str('email');
				$phone = Post::str('phone');
				$errors = [];

				if (!filter_var($email, FILTER_VALIDATE_EMAIL))
				{
					$errors['email'] = 'Введите корректный Email';
				}

				if ($phone !== '' && !preg_match('/^\+?[0-9\s\-()]{10,20}$/', $phone))
				{
					$errors['phone'] = 'Введите номер телефона, например +7 701 123 45 67';
				}

				foreach (['name' => 'Имя', 'surname' => 'Фамилия', 'patronymic' => 'Отчество'] as $field => $label)
				{
					if (mb_strlen(Post::str($field)) > 50)
					{
						$errors[$field] = "$label: не больше 50 символов";
					}
				}

				if ($errors)
				{
					json_response($errors);
				}

				$user = R::load('users', auth_user('id'));
				$user->email = $email;
				R::store($user);
				login_user($user);

				$profile = $this->profile() ?: R::dispense('profile');
				$profile->login      = $user->login;
				$profile->name       = Post::str('name');
				$profile->surname    = Post::str('surname');
				$profile->patronymic = Post::str('patronymic');
				$profile->phone      = $phone;
				R::store($profile);

				json_response(['success' => 'Сохранено!']);
			});
		});
	}


//	**************************************************
//	**************************************************


	public function actionLogout()
	{
		Post::catch(function ()
		{
			Session::forget('user');
			session_regenerate_id(true);
			redirect(safe_path($_POST['redirect'] ?? '', '/login'));
		});
	}


//	**************************************************
//	**************************************************


	public function actionTaken()
	{
		Post::catch(function ()
		{
			Ajax::catch(function ()
			{
				$this->requireUser();

				$profile = $this->profile();

				if (!$profile || trim($profile->name) === '' || trim($profile->phone) === '')
				{
					json_response(['error' => 'Заполните имя и номер телефона в профиле: по нему оператор подтвердит бронь.']);
				}

				[$ok, $result] = Booking::create(auth_user('login'), $_POST['data'] ?? []);

				if (!$ok)
				{
					json_response(['error' => $result]);
				}

				json_response(['success' => 'Запрос отправлен. Ждите звонка!']);
			});
		});
	}


//	**************************************************
//	**************************************************


	public function actionSeats()
	{
		Post::catch(function ()
		{
			Ajax::catch(function ()
			{
				$this->requireUser();
				json_response(['occupied' => Hall::occupiedSeats()]);
			});
		});
	}


//	**************************************************
//	**************************************************


	public function actionApprove()
	{
		$this->changeStatus(Booking::CONFIRMED, 'Бронь подтверждена, билет выдан.');
	}

	public function actionReject()
	{
		$this->changeStatus(Booking::REJECTED, 'Бронь отклонена, места снова свободны.');
	}

	private function changeStatus($to, $message)
	{
		Post::catch(function () use ($to, $message)
		{
			Ajax::catch(function () use ($to, $message)
			{
				$this->requireAdmin();

				if (!Booking::setStatus($_POST['id'] ?? 0, Booking::PENDING, $to))
				{
					json_response(['error' => 'Заявка не найдена или уже обработана.']);
				}

				json_response(['success' => $message]);
			});
		});
	}


//	**************************************************
//	**************************************************


}
