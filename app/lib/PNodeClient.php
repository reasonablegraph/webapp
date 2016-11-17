<?php


class PNodeClient {


	//const CMD_FILE_INFO = 'file-info';

	protected $client_options = null;
	protected $node_root_dir = null;

	private function getScriptFullPath($script_name){
		return $this->node_root_dir . $script_name;
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
		$ARC_ROOT_DIR = Config::get('arc.ROOT_DIR');
		$this->node_root_dir = $ARC_ROOT_DIR  . '/node/';
		putenv('ARC_HOME='.$ARC_ROOT_DIR);
		putenv('ARC_NODE_HOME='.$this->node_root_dir);
	}

	protected  function exec_json($options ){
		throw new Exception('UNIMPLEMENTED exec_json');
	}


	public  function exec( $options ){
		$json = $this->exec_json($options);
		return json_decode($json,true);
	}


	protected  function _exec_cmd_json( $cmd_name,$args ){

		$cmd = $this->createCMD($cmd_name, $args);
		$cmd_out = array();
		$status = 0;
		Log::info('exec node: '. $cmd);
		$tmp = exec($cmd,$cmd_out,$status);
		$json_rep = '';
		if ($status == 0){
			foreach ($cmd_out as $line)
			{
				$json_rep .= $line . "\n";
			}
		} else {
			Log::info("ERROR exec node script: " . $cmd_name .  " exit-status= " . $status);
			Log::info(print_r($cmd_out,true));
		}
		return $json_rep;
	}


}



class PNodeClientFileInfo extends PNodeClient {

	public function __construct($options=array()) {
			parent::__construct($options);
	}

	public  function exec_json($options ){

		$args = array($options['file']);
		if (isset($options['mimetype']) && !empty($options['mimetype'])){
			$args[] = $options['mimetype'];
		}
		$this->_exec_cmd_json('file-info.js', $args);
	}
}



class PNodeClientEpubInstall extends PNodeClient {

	public function __construct($options=array()) {
		parent::__construct($options);
	}

	public  function exec_json($options ){

		$args = array(
				$options['file'],
				$options['f_dir'],
				$options['f_name'],
				$options['reader_content_dir'],
		);
		$this->_exec_cmd_json('epub-install.js', $args);
	}
}



?>
