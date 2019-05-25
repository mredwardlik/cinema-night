<?php
/**
 * 
 */
class CabinetController 
{

//	**************************************************
//	**************************************************

	public function actionIndex()
	{
		if (session_is("user"))
		{
			G::setvar(True, [
				'menu__active' => 'cabinet',
				'css' => [
					'parts/wrapper.css',
					'parts/nav.css',
					'cabinet.css',
					'parts/footer.css']
				]);

			view("cabinet.php");
		}
		else
		{
			header("Location: /login");
		}
	}

	public function actionCh()
	{
		Post::catch(function()
		{
			Ajax::catch(function()
			{
				$data = $_POST;
				$user = R::count("users", "login = ?", array($data["login"]));

				if ( ($user > 0) && ( Session::get("user", "login") != $data["login"] ) )
				{
					echo json_encode(["error" => "Логин " . $data["login"] . " уже занят!"]);
				}
				else
				{
					$oldlogin = Session::get("user", "login");
					unset($_SESSION["user"]);
					unset($user);

					$user = R::findOne("users", "login = ?", array($oldlogin));
					$user->login = $data["login"];
					$user->email = $data["email"];
					unset($_SESSION["user"]);
					$_SESSION["user"] = serialize($user);
					R::store($user);

					$profile = R::findOne("profile", "login = ?", array($oldlogin));
					$profile->login      = $data["login"];
					$profile->name       = $data["name"];
					$profile->surname    = $data["surname"];
					$profile->patronymic = $data["patronymic"];
					$profile->phone      = $data["phone"];
					unset($_SESSION["profile"]);
					$_SESSION["profile"] = serialize($profile);
					R::store($profile);
					
					echo json_encode(["success" => "Сохранено!"]);
				}

			});
		}, False);
	}


//	**************************************************
//	**************************************************

	public function actionLogout()
	{
		unset($_SESSION["user"]);
		unset($_SESSION["profile"]);

		if (!($_GET["redirect"] === ""))
		{
			$redirect = $_GET["redirect"];
		}
		else
		{
			$redirect = "/login";
		}

		header("Location: " . $redirect);
	}

//	**************************************************
//	**************************************************












//	**************************************************
//	**************************************************

	public function actionTaken()
	{
		Post::catch(function() {
			Ajax::catch(function() {

				if (!isset($_POST["data"]))
				{
					echo json_encode(["error" => "Выберите место!"]);
				}
				else
				{
					$data = $_POST["data"];
					$seat = R::dispense('seat');
					$seat->login  = Session::get('user', 'login');
					$seat->seat   = json_encode($data);
					$seat->iden   = Str::random(0,0,9);
					$seat->code   = Str::random(2,2,6);
					$seat->status = 0;
					R::store($seat);
					/*unset($seat);
					$seat = R::find("seat", "login = ?", array(Session::get('user', 'login')));
					echo(json_encode($seat));*/
					echo json_encode(["success" => "Запрос отправлен. Ждите звонка!"]);
				}

			});
		});
	}







	public function actionSeats()
	{
		Post::catch(function() {
			Ajax::catch(function() {
	
				$seat = R::findAll('seat');
				echo(json_encode($seat));
			});
		});
	}

//	**************************************************
//	**************************************************

}