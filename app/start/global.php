<?php


/*
|--------------------------------------------------------------------------
| Register The Laravel Class Loader
|--------------------------------------------------------------------------
|
| In addition to using Composer, you may use the Laravel class loader to
| load your controllers and models. This is useful for keeping all of
| your classes in the "global" namespace without Composer updating.
|
*/

ClassLoader::addDirectories(array(

	app_path().'/commands',
	app_path().'/controllers',
	app_path().'/models',
	app_path().'/database/seeds',

));

/*
|--------------------------------------------------------------------------
| Application Error Logger
|--------------------------------------------------------------------------
|
| Here we will configure the error logger setup for the application which
| is built on top of the wonderful Monolog library. By default we will
| build a basic log file setup which creates a single file for logs.
|
*/

Log::useFiles(storage_path().'/logs/laravel.log');

/*
|--------------------------------------------------------------------------
| Application Error Handler
|--------------------------------------------------------------------------
|
| Here you may handle any errors that occur in your application, including
| logging them or displaying custom views for specific errors. You may
| even register several error handlers to handle different types of
| exceptions. If nothing is returned, the default error view is
| shown, which includes a detailed stack trace during debug.
|
*/

App::error(function(Exception $exception, $code)
{
	Log::error($exception);
});

/*
|--------------------------------------------------------------------------
| Maintenance Mode Handler
|--------------------------------------------------------------------------
|
| The "down" Artisan command gives you the ability to put an application
| into maintenance mode. Here, you will define what is displayed back
| to the user if maintenance mode is in effect for the application.
|
*/

App::down(function()
{
	return Response::make("Be right back!", 503);
});

/*
|--------------------------------------------------------------------------
| Require The Filters File
|--------------------------------------------------------------------------
|
| Next we will load the filters file for the application. This gives us
| a nice separate location to store our route and application filter
| definitions instead of putting them all in the main routes file.
|
*/

require app_path().'/filters.php';


// App::bind('mustache', function(){
// 	return new Mustache_Engine;
// });

// App::bind('handlebars', function(){
// 	return new Handlebars;
// });

// App::bind('template-engine', function(){
// 	return new Mustache_Engine;
// });

App::bind('arc-template-engine', function(){
	return new ArcTemplateEngineImpl;
});
	App::bind('arc-app', function(){
		return new ArcAppImpl();
	});



//////////////////////////////////////////////////////////////////
// GLOBAL FUNCTIONS
////////////////////////////////////////////////////////////////


//Log::info("INIT REQUEST SCOPE");
$GLOBALS['_request_'] = array();

/**
 * @return PrepareService
 */
function prepareService(){
	return App::make('preparest');
}

function dbconnect(){

// 	if (isset($_REQUEST['connection'])){
// 		#echo("<br/>DB CONNECTION FROM REQUEST<br/>");
// 		return $_REQUEST['connection'];
// 	}

// 	$ARCHIVE_DB_HOST = Config::get('arc.ARCHIVE_DB_HOST');
// 	$ARCHIVE_DB_DATABASE = Config::get('arc.ARCHIVE_DB_DATABASE');
// 	$ARCHIVE_DB_USER = Config::get('arc.ARCHIVE_DB_USER');
// 	$ARCHIVE_DB_PASS = Config::get('arc.ARCHIVE_DB_PASS');

// 	#echo ("<br/>INIT DB CONNECTION<br/>");
// 	$con_str = sprintf('pgsql:host=%s;dbname=%s',$ARCHIVE_DB_HOST,$ARCHIVE_DB_DATABASE);
// 	$con = new PDO($con_str, $ARCHIVE_DB_USER, $ARCHIVE_DB_PASS);
// 	$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 	$_REQUEST['connection'] = $con;

//	$db = DB::connection();
		global $_request_;
		if (isset($_request_['connection'])){
			//error_log("FROM REQUEST");
			//Log::info("OLD DB CONNECTION");
			return $_request_['connection'];
		}



		//Log::info("NEW DB CONNECTION");
		//error_log("NEW CONN");

		$con = DB::connection()->getPdo();
		#$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$_request_['connection'] = $con;
		return $con;
}

function trChoise($str, $count, $context='opac',  $parameters = array(), $locale = null){
	//Log::info('trChoise');
	$id  =  $context . '.' . $str;
	$rep =  Lang::choice($id, $count, $parameters, $locale);
	if ($rep == $id){  return $str; }
	return $rep;
}

function tr($str, $context='opac',  $parameters = array(), $locale = null){
	$id = $context.'.'.$str;
	$rep =  Lang::get($id, $parameters, $locale);
	if ($rep == $id){  return $str; }
	return $rep;
}

//@DocGroup(module="util", group="general", comment="get_lang")
function get_lang(){
// 	global $language;
// 	$lang = isset($language->language) ? $language->language : 'en';
	//return 'el';
		//Log::info("GET LANG: ". App::make('arc')->language);
		return App::make('arc')->language;
}

//@DocGroup(module="util", group="general", comment="get_get")
function get_get($name, $default = null){

	if (isset($_GET[$name])){
		return $_GET[$name];
	}
	return $default;
}

//@DocGroup(module="util", group="general", comment="get_post")
function get_post($name,$default = null){

	if (isset($_POST[$name])){
		return $_POST[$name];
	}
	return $default;
}

//@DocGroup(module="util", group="general", comment="get_post_get")
function get_post_get($name,$default = null){
	if (isset($_POST[$name])){
		return $_POST[$name];
	} else if (isset($_GET[$name])){
		return $_GET[$name];
	}
	return $default;
}

function drupal_set_title($title){
	error_log("SET TITLE : " . $title);
//	$_REQUEST['HTML-TITLE'] = $title;

}

function set_lang(){
	$lang = App::make('arc')->language;
	App::setLocale($lang);
	return true;
}

function html_echo($string){
	echo(htmlspecialchars($string));
}

function html_data_view($string){
	return htmlspecialchars($string);
}

function variable_get($param, $defaultValue= null){
	return Config::get('arc.'.$param, $defaultValue);
}

function get_browse_filter(){
	return Config::get('arc_search.BROWSE_FILTERS');
};

function get_menu_lines(){

	return Config::get('arc_search.BROWSE_lines');

}


function get_menu_line_field($index,$field_name){
	$menu_lines = get_menu_lines();
	if (!empty($menu_lines[$index])){
		if (isset($menu_lines[$index][$field_name])){
			return $menu_lines[$index][$field_name];
		}
	}

	return null;
}


function get_menu_name($index){
	return get_menu_line_field($index, 'name');
}

function get_menu_browse_filter($index){
	$menu_lines = get_menu_lines();
	if (!empty($menu_lines[$index])){
		if (isset($menu_lines[$index]['filter'])){
			$filter_idx = $menu_lines[$index]['filter'];
			$browse_filters = get_browse_filter();
			if (isset($browse_filters[$filter_idx])){
				$rep =  $browse_filters[$filter_idx];
				return $rep;
			}
		}
	}
	return null;
}

function get_menu_browse_filter_field($index,$field_name){
	$tmp = get_menu_browse_filter($index);
	if(! empty($tmp)){
		if (isset($tmp[$field_name])){
			$rep =$tmp[$field_name];
			return $rep;
		}
	}
	return null;
}

function user_access_mentainer(){
	return ArcApp::user_access_mentainer();
}

function auth_check_mentainer(){
	return ArcApp::auth_check_mentainer();
}

function user_access_admin(){
	return ArcApp::user_access_admin();
}

function user_access_login(){
	return ArcApp::user_access_login();
}
// function  user_access( $permision){

// 	if(Config::get('arc.SKIP_PERMISSION')){
// 		return true;
// 	}

// 	//$app = App::make('arc');
// 	//$user_access = $app->user_access;
// 	$user_access = ArcApp::user_access();

// 	//Log::info( "@PERM: <" . $permision .">  @UA:<" .  $user_access .">");
// 	if (empty($permision)){

// 		if ($user_access == 'admin' || $user_access == 'mentainer' || $user_access == 'item_submiter' ){
// 			return true;
// 		}
// 		return false;
// 	}


// 	if ($permision == 'repo_maintainer' && ($user_access == 'admin' || $user_access == 'mentainer')){
// 		return true;
// 	}
// 	if ($permision == 'repo_login' && ! empty($user_access)){
// 		return true;
// 	}
// 	if ($permision == 'admin' && $user_access == 'admin' ){
// 		return true;
// 	}

// 	if ($permision == 'item_submiter'
// 			&& ($user_access == 'item_submiter' || $user_access == 'admin'  || $user_access == 'mentainer')){
// 		return true;
// 	}

// 	return false;
// }


function auth_check_all(){
	return ArcApp::auth_check();
}

function auth_check(){
	return ArcApp::auth_check();
}


function drupal_add_css($path,$defaultValue= null){
	echo '<link rel="stylesheet" type="text/css" href="'.$path.'">';

}

function get_object_type_names($dbh = null){
	$dbh = empty($dbh) ? dbconnect() : $dbh;
	$obj_type_names = null;
	if (isset($_REQUEST['get_object_type_names'])){
		return $_REQUEST['get_object_type_names'];
	} else {
		$obj_type_names = array();
	}
	$SQL="SELECT name,mime_label from dsd.obj_type";
	$stmt = $dbh->prepare($SQL);
	$stmt->execute();
	while ($row = $stmt->fetch()){
		$n = $row[1];
		if ($n == 'web-page'){
			$n = 'Web';
		}
		$obj_type_names[$row[0]] = $n;
	}

	$_REQUEST['get_object_type_names'] = $obj_type_names;
	return $obj_type_names;
}

function drupal_add_library($module, $name, $every_page = NULL){
	$msg = sprintf('drupal_add_library: %s : %s ', $module , $name);
// 	Log::info($msg);
	return true;
}

function drupal_add_js($path = NULL, $options = NULL){
		echo '<script type="text/javascript" src="'.$path.'"></script>';
}

function get_user_name(){
	return ArcApp::username();
// 	if(Config::get('arc.SKIP_PERMISSION')){
// 		return 'laravel';
// 	}

// 	if (isset($_SERVER['PHP_AUTH_USER'])){
// 		$user =  $_SERVER['PHP_AUTH_USER'];
// 		return $user;
// 	}

// 	if (isset($GLOBALS['user'])){
// 		#		echo("<pre>");
// 		#		print_r($GLOBALS['user']);
// 		#		echo("</pre>");
// 		$user = $GLOBALS['user'];
// 		if (property_exists($user,'name')){
// 			$name = $user->name;
// 			return $name;
// 		}
// 	}
// 	return null;
}

function search_subject_db($dbh, $term, $limit ,$exact = false){
	$ss = trim($term);
	if ($ss == ''){
		return ARRAY();
	}
	$SQL  = "SELECT m.subject as value ";
	if ($exact){
		$SQL .= " FROM dsd.subject m, dsd.to_gr_tsquery(?,true) as q";
	} else {
		$SQL .= " FROM dsd.subject m, dsd.to_gr_tsquery(?,false) as q";
	}
	$SQL .= " where ";
	$SQL .= " q @@ subject_fst limit ? ";

	$stmt = $dbh->prepare($SQL);
	$stmt->bindParam(1, $ss);
	$stmt->bindParam(2, $limit);

	$stmt->execute();
	$rep = $stmt->fetchAll();
	return $rep;
}

function drupal_add_http_header($con1 = null, $con3 = null, $con2 = null){
	return true;
}








