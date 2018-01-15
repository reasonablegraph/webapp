<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class TestrgDeleteTestVertices extends Command {

	protected $name = 'testrg:deleteTestVertices';
	protected $description = 'Delete Test Vertices from previous test-run.';

	public function __construct(){
		parent::__construct();
	}

//	protected function getOptions() {
//		return array (
//				array (
//						'method', // name
//						'm', // shortcut
//						InputOption::VALUE_OPTIONAL, // mode
//						'Delete method (1, 2 or 3).', // description
//						0 // defaultValue
//				)
//		);
//	}

	public function fire() {
		$info = function ($msg) {
			$this->info($msg);
		};

//		$t0 = microtime(true);
//		$info('deleting ...');

		$this->deleteTestVertices();

//		$t1 = microtime(true);
//		$info("done deleting in: " . intval($t1 - $t0) . " secs");
	}

	private function deleteTestVertices() {
		$dbh = dbconnect();
		// select i.item_id,i.obj_type,i.label from dsd.item2 i JOIN dsd.metadatavalue2 v ON (i.item_id = v.item_id) WHERE v.element='ea:test:key1'
		$SQL = "select dsd.delete_item(item_id) from dsd.metadatavalue2 WHERE element='ea:test:key1'";
		$dbh->query($SQL);
	}

}