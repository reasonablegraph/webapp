<?php

class BaseController extends Controller {


	// 	public function __construct(){
	// 	}

	public function show($data = null){
		if (empty($data)){
			$data = array();
		}
		$cms = Config::get('arc.CMS','laravel');
		if ($cms == 'drupal'){
			$this->showDrupal($data);
		} else {
			$this->showLaravel($data);
		}
	}

	public function showLaravel($data){
		//$action = Route::currentRouteAction();

		$menus[] = array('title' => 'Logout', 'link' => URL::to('logout'));

		$app= App::make('arc');
		//$app->user_access = 'admin';


		$this->layout= View::make(Config::get('arc.DEFAULT_LAYOUT','layouts.theme'),array('title'=>$app->title))->with('menus', $menus);
 		if ($app->template != null){
 			$this->layout->content = View::make($app->template,$data);
 		} else {
 			$name = Route::currentRouteName();
			$this->layout->content = View::make($name,$data);
 		}

	}

	public function showDrupal($data){
		//$action = Route::currentRouteAction();
 		$app= App::make('arc');
// 		$app->user_access = Request::header('X-DRUPAL-ACCESS');
		$this->layout= View::make('layouts.drupal');
		if ($app->template != null){
			$this->layout->content = View::make($app->template,$data);
		} else {
			$name = Route::currentRouteName();
			$this->layout->content = View::make($name,$data);
		}

	}




	//protected $layout = 'layouts.standalone';

// 	/**
// 	 * Setup the layout used by the controller.
// 	 *
// 	 * @return void
// 	 */
// 	protected function setupLayout()
// 	{
// 		if ( ! is_null($this->layout))
// 		{
// 			$this->layout = View::make($this->layout);
// 		}
// 	}

}
