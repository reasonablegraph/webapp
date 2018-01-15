<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

// App::before(function($request)
// {
// 	//
// });


// App::after(function($request, $response)
// {
// 	//
// });

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (Auth::guest())
	{
		if (Request::ajax())
		{
			return Response::make('Unauthorized', 401);
		}
		else
		{
			return Redirect::guest('login');
		}
	}
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});











// View::creator('layouts.standalone', function($view)
// {
// 	error_log("CREATOR");
// 	$view->with('data2', "CREATOR MENU");
// });




// View::composer('layouts.theme', function($view){
// 	error_log('menu composer');
// // 		$menus[] = array('title' => 'Logout', 'link' => URL::to('logout'));
// // 		$view->with('menus', $menus);
// });




App::before(function($request)
{
	//Log::info("#BEFOR#");

	App::singleton('arc', function(){
		//Log::info("@@1 ARC SINGLETON");
		$app = new stdClass;
		$app->title = "archive";
		$app->template = null;
		$app->user_access = null;
		$app->username = null;
		$app->uid = null;
		$app->user = null;
		// 		if (Auth::check()) {
		// 			$app->user = Auth::User();
		// 			$app->isLogedin = TRUE;
		// 		}
		// 		else
			// 		{
			// 			$app->isLogedin = FALSE;
			// 			$app->user = FALSE;
			// 		}
		return $app;
	});
	$app = App::make('arc');


//	$forwardedHost = $request->header('X-Forwarded-Host');

	$arcfe = $request->header('X-ARCFE');
	$app->arcfe = empty($arcfe)? null : $arcfe;

	$lang = $request->header('X-DRUPAL-LANG');
	//Log::info("X-DRUPAL-LANG: " . $lang);
	if ($lang){
		$app->language = $lang;
		App::setLocale($app->language);
	} else {
		$app->language = App::getLocale();
	}

	$username = $request->header('X-DRUPAL-USERNAME');
	if ($username){
		$app->username = $username;
	}
	$uid = $request->header('X-DRUPAL-UID');
	if($uid){
		$app->uid  = $uid;
	}
	$user_access = Request::header('X-DRUPAL-ACCESS');
	$app->user_access  = ! empty($user_access) ? $user_access : null;

// 	if (! empty($fdata)){
// 		$fc = count($fdata);
// 		$request->setHeader('X-DRUPAL-FU-COUNT',  $fc);
// 		foreach ($fdata as $idx=>$ff){
// 			$request->setHeader('X-DRUPAL-FU-FIELD-' . $idx, urlencode($ff['field']) );
// 			$request->setHeader('X-DRUPAL-FU-NAME-' . $idx, urlencode($ff['name']) );
// 			$request->setHeader('X-DRUPAL-FU-TMP-' . $idx, urlencode($ff['tmp_name']) );
// 			$request->setHeader('X-DRUPAL-FU-SIZE-' . $idx, urlencode($ff['size']) );
// 			$request->setHeader('X-DRUPAL-FU-TYPE-' . $idx, urlencode($ff['type']) );
// 		}
// 	}

	//$app->has_upload_files = false;
	$upload_files = array();
	$fc = $request->header('X-DRUPAL-FU-COUNT');
	if (!empty($fc)){
		for ($i=0;$i<$fc;$i++){
			$field =  urldecode($request->header('X-DRUPAL-FU-FIELD-' . $i));
			$name  =  urldecode($request->header('X-DRUPAL-FU-NAME-' . $i));
			$tmp   =  urldecode($request->header('X-DRUPAL-FU-TMP-' . $i));
			$size  =  urldecode($request->header('X-DRUPAL-FU-SIZE-' . $i));
			$type  =  urldecode($request->header('X-DRUPAL-FU-TYPE-' . $i));
			$ext = null;
			if (preg_match('/\.(\w+)$/', $name, $matches)){
				$ext = strtolower($matches[1]);
			}
			if (!isset($upload_files[$field])){
				$upload_files[$field] = array();
			}


			$upload_files[$field][] = array('name'=>$name, 'tmp_name'=>$tmp, 'size'=>$size, 'type'=>$type,'extension' =>$ext);
		}
// 		echo("<PRE>");
// 		print_r($upload_files);
// 		echo("</PRE>");
		//$app->has_upload_files = true;
// 		$app->upload_files = $upload_files;
	}
	$app->upload_files = $upload_files;

	$app = App::make('arc');
	View::share('arc', $app);
});









Route::filter('argv', function(){
	$argc = Request::header('X-DRUPAL-ARGC');
	if ($argc && $argc > 0){
		$argv = array();
		for ($i = 0; $i < $argc; $i++) {
			$argv[] = urldecode(Request::header('X-DRUPAL-ARGV-' . $i));
		}
		$_REQUEST['ARGV'] = $argv;
	}
});

