<?php
/**
 * Sign in / sign up.
 */
class LoginController
{


//	**************************************************
//	**************************************************

	public function actionIndex()
	{
		if (session_is('user'))
		{
			redirect('/cabinet');
		}

		G::setvar(true, [
			'css' => [
				'login.css'
			]
		]);

		view('login.php');
	}


//	**************************************************
//	**************************************************


	public function actionChlogin()
	{
		Post::catch(function ()
		{
			Ajax::catch(function ()
			{
				$login    = Post::str('login');
				$password = Post::str('password');

				if ($login === '' || $password === '')
				{
					json_response(['error' => 'Введите логин и пароль']);
				}

				$user = R::findOne('users', 'login = ?', [$login]);

				if (!$user)
				{
					json_response(['thereis' => 'Пользователя с таким логином не существует']);
				}

				if (!password_verify($password, $user->password))
				{
					json_response(['password' => 'Неверно введен пароль!']);
				}

				login_user($user);
				json_response(['success' => 'Вы авторизованы!', 'redirect' => '/cabinet']);
			});
		});
	}


//	**************************************************
//	**************************************************


	public function actionChreg()
	{
		Post::catch(function ()
		{
			Ajax::catch(function ()
			{
				$login     = Post::str('login');
				$email     = Post::str('email');
				$password  = $_POST['password'] ?? '';
				$passwordR = $_POST['password_r'] ?? '';
				$errors    = [];

				if (!preg_match('/^[A-Za-z0-9_.\-]{3,32}$/', $login))
				{
					$errors['login'] = 'Логин: 3–32 символа, латинские буквы, цифры, «_», «-» или «.»';
				}

				if (!filter_var($email, FILTER_VALIDATE_EMAIL))
				{
					$errors['email'] = 'Введите корректный Email';
				}

				if (!is_string($password) || mb_strlen($password) < 6)
				{
					$errors['password'] = 'Пароль должен быть не короче 6 символов';
				}

				if ($passwordR !== $password)
				{
					$errors['password_r'] = 'Повторный пароль введен неверно';
				}

				if (!isset($errors['login']) && R::count('users', 'login = ?', [$login]) > 0)
				{
					$errors['thereis'] = 'Такой пользователь уже существует';
				}

				if ($errors)
				{
					json_response($errors);
				}

				$user = R::dispense('users');
				$user->login    = $login;
				$user->email    = $email;
				$user->password = password_hash($password, PASSWORD_DEFAULT);
				$user->role     = 'user';
				$user->date     = date('Y-m-d H:i:s');
				R::store($user);

				$profile = R::dispense('profile');
				$profile->login      = $login;
				$profile->name       = '';
				$profile->surname    = '';
				$profile->patronymic = '';
				$profile->phone      = '';
				R::store($profile);

				login_user($user);
				json_response(['success' => 'Регистрация прошла успешно!', 'redirect' => '/cabinet']);
			});
		});
	}


//	**************************************************
//	**************************************************


}
