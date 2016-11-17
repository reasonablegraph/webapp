<?php
use Illuminate\Support\ServiceProvider;
//use Illuminate\Support\Facades\Log;

class PrepareServiceProvider extends ServiceProvider {

	public function register(){
		$this->app->bind('preparest', function (){

			if (isset($_REQUEST['prepareService'])){
				//Log::info("OLD prepareService");
				return $_REQUEST['prepareService'];
			}
			//Log::info("NEW prepareService");
			$ps =  new PrepareService();
			$_REQUEST['prepareService'] = $ps;
			return $ps;
			//return new PrepareService();
		});


	}


}

?>