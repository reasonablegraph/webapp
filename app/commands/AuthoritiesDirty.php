<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class AuthoritiesDirty extends Command {

	protected $name = 'authorities:dirty';
	protected $description = 'Mark all MARC records as dirty to allow re-generation of nodes.';

	public function __construct(){
		parent::__construct();
	}

	protected function getOptions() {
		return array (
				array (
						'yes', // name
						'y', // shortcut
						InputOption::VALUE_NONE, // mode
						'Answer yes to all prompts to user for confirmation.' // description
				)
		);
	}

	public function fire() {
		$info = function ($msg) {
			$this->info($msg);
		};

		$option_yes = $this->option('yes');
		$confirm = false;

		if (!$option_yes) {
			if ($this->confirm('Do you wish to continue? [yes|no]')) {
				$confirm = true;
			}
		} else {
			$confirm = true;
		}

		if ($confirm) {

			$t0 = microtime(true);
			$info('marking as dirty ...');

			$dbh = dbconnect();
			$dbh->query("select dsd.item_add_flag(item_id, '" . AuthoritiesImport::$DIRTY_FLAG . "') FROM dsd.item2 WHERE obj_type = 'marc-import'");

			$t1 = microtime(true);
			$info("done marking as dirty in: " . intval($t1 - $t0) . " secs");
		}
	}
	
}