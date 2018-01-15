<?php
class ArcAppImpl {

	private $app;

	function __construct() {
		//Log::info("@@: ArcAppImpl");
		$this->app =  App::make('arc');
	}


	function isAnonymous(){
		return empty($this->app->username);
	}

	function username(){
		$rep = $this->app->username;
		if (empty($rep)){
			$rep = 'anonymous';
		}
		return $rep;
	}

	function uid(){
		return $this->app->uid;
	}


	function user(){
		if ($this->isAnonymous()){
			return null;
		}
		if (empty($this->app->user)){
			$username = $this->username();
			$dbh = dbconnect();
			$SQL = "SELECT * FROM dsd.arc_user WHERE username = ?";
			$stmt = $dbh->prepare ( $SQL );
			$stmt->bindParam ( 1,  $username);
			$stmt->execute();
			if ($rep = $stmt->fetch(PDO::FETCH_ASSOC)){
				$this->app->user = $rep;
				return $rep;
			}
		}
		return $this->app->user;
	}

	function template($template = null){
		if (! empty($template)){
			$this->app->template = $template;
		}
		return $this->app->template;
	}

// 	function user_access(){
// 		return $this->app->user_access;
// 	}

	function title($title = null){
		if (! empty($title)){
			$this->app->title = $title;
		}
		return $this->app->title;
	}


	function auth_check(){
		$ok = $this->has_permission('repo_login');
		if (!$ok){
			//header('Status: 403 Forbidden');
			#watchdog('access denied', 'authorize.php', NULL, WATCHDOG_WARNING);
			echo tr('You are not allowed to access this page.');
			echo("\n");
			echo '<br/><a href="/user/">'.tr('Login').'</a>';
			die();
		}
	}


	function user_access_login(){
			return $this->has_permission('repo_login');
	}


	function auth_check_mentainer(){
		$ok = $this->has_permission('repo_maintainer');
		if (!$ok){
			echo tr('You are not allowed to access this page.');
			die();
		}
	}

	function user_access_mentainer(){
		// 		$ac = $this->app->user_access;
		// 		if ($ac == 'admin' || $ac == 'mentainer'){
		// 			return true;
		// 		}
		// 		return false;

		return $this->has_permission('repo_maintainer');
	}

	function user_access_admin(){

		// 		if ($this->app->user_access == 'admin'){
		// 			return true;
		// 		}
		// 		return false;

		return $this->has_permission('admin');
	}

	public function  has_permission($permision){

		if(Config::get('arc.SKIP_PERMISSION')){
			return true;
		}

		$user_access = $this->app->user_access;

		//Log::info( "@PERM: <" . $permision .">  @UA:<" .  $user_access .">");
		if (empty($permision)){
			throw new Exception('user_access permision expected');
		}

		$isMentainer = function() use ($user_access){
			return ($user_access == 'admin' || $user_access == 'mentainer');
		};


		if ($permision == Permissions::$REPO_MAINTAINER && $isMentainer()){
			return true;
		}

		if ($permision == Permissions::$REPO_LOGIN && ! empty($user_access)){
			return true;
		}

		if ($permision == Permissions::$ADMIN && $user_access == 'admin' ){
			return true;
		}

		if ($permision == Permissions::$ITEM_SUBMITER
				&& ($user_access == 'item-submiter' || $user_access == 'admin'  || $user_access == 'mentainer')){
			return true;
		}


		if ($permision == Permissions::$BITSTREAM_DELETE && $isMentainer()){
			return true;
		}

		if ($permision == Permissions::$VIEW_ITEMS_ALL_STATUS && $isMentainer()){
			return true;
		}

		return false;
	}







}