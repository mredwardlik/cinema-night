<?php
/**
 * Landing page.
 */
class MainController
{


//	**************************************************
//	**************************************************


	public function actionIndex()
	{
		G::setvar(true, [

			'menu__active' => 'main',
			'css' => [
				'parts/wrapper.css',
				'parts/nav.css',
				'main.css',
				'parts/footer.css'
			]

		]);

		G::addvar([
			'films' => R::find('films', 'status = 1 ORDER BY id LIMIT 3'),
			'price' => Booking::currentPrice()
		]);

		view('main.php');
	}


//	**************************************************
//	**************************************************


}
