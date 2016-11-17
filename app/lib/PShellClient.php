<?php
class PShellClient {

	//const CMD_FILE_INFO = 'file-info';

	protected $client_options = null;
	protected $root_dir = null;

	protected function getScriptFullPath($script_name){
		return $this->root_dir . $script_name;
	}

	private function createCMD($script_name, $args){
		$cmd = $this->getScriptFullPath($script_name);
		foreach ($args as $arg){
			if (! empty($arg)){
				$cmd .= ' "' . $arg . '"';
			}
		}
		return $cmd;
	}

	public function __construct($options=array()) {
		$this->client_options = $options;
		if ($options['bin_dir']){
			$bin_dir = $options['bin_dir'];
		} else {
			$bin_dir = 'bin';
		}
		$ARC_ROOT_DIR = Config::get('arc.ROOT_DIR');
		$this->root_dir = $ARC_ROOT_DIR  . $bin_dir . '/';
		putenv('ARC_HOME='.$ARC_ROOT_DIR);
		putenv('ARC_ROOT_DIR='.$this->root_dir);
	}

	public  function exec( $cmd_name,$args ){

		$cmd = $this->createCMD($cmd_name, $args);
		$cmd_out = array();
		$status = 0;
		Log::info('exec: '. $cmd_name . ' : ' . $cmd);
		$tmp = exec($cmd,$cmd_out,$status);
		$rep = '';
		if ($status == 0){
			foreach ($cmd_out as $line)
			{
				$rep .= $line . "\n";
			}
		} else {
			Log::info("ERROR exec script: " . $cmd_name .  " exit-status= " . $status);
			Log::info(print_r($cmd_out,true));
		}
		return $rep;
	}




}
?>