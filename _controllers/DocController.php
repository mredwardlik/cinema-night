<?php
/**
 * 
 */
class DocController 
{


//	**************************************************
//	**************************************************


	public function actionIndex()
	{
		G::setvar(True, [

			'menu__active' => 'doc',
			'css' => [
				'parts/wrapper.css',
				'parts/nav.css',
				'parts/footer.css',
				'doc.css'
			]

		]);

		view('doc.php');
	}


//	**************************************************
//	**************************************************


}