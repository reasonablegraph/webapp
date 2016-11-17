<?php

use Illuminate\Support\Facades\Log;


class PrepareService {

	public static $METADATAVALUE2_EUROPEANA = 'PS#1';
	public static $SAVE_GRAPH_EDGE = 'PS#2';
	private $sql;

	//$this->SQL1=$dbh->prepare("SELECT dsd.save_graph_edge(?,?,?,false,?,null,null,null)");
	function __construct() {
		$this->sql = array(
				$this::$METADATAVALUE2_EUROPEANA => "SELECT item_id FROM dsd.metadatavalue2 WHERE element = 'ea:europeana:key1' AND text_value = ?",
				$this::$SAVE_GRAPH_EDGE => 'SELECT dsd.save_graph_edge(?,?,?,false,?,null,null,null)',
		);
	}

	public function prepareNamed($name){
		$dbh = dbconnect();


		if (isset($_REQUEST[$name])){
			//Log::info("OLD prepare");
			return $_REQUEST[$name];
		}
		//Log::info('NEW prepare');
		if (!isset($this->sql[$name])){
			throw new Exception($name .' NOT FOUND');
		}

		$sql = $this->sql[$name];
		$st = $dbh->prepare($sql);
		$_REQUEST[$name] = $st;
		return $st;
	}



	public function prepare($SQL){
		$dbh = dbconnect();

		if (isset($_REQUEST[$SQL])){
			//Log::info("@@@@@ OLD prepare SQL: " . $SQL);
			return $_REQUEST[$SQL];
		}

		Log::info("@@@@@ NEW prepare SQL: " . $SQL);
		$st = $dbh->prepare($SQL);
		$_REQUEST[$SQL] = $st;
		return $st;

	}



}

