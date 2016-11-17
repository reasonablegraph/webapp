<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class AuthoritiesDelete extends Command {

	protected $name = 'authorities:delete';
	protected $description = 'Delete Authorities previously imported from MARC.';

	public function __construct(){
		parent::__construct();
	}

	protected function getOptions() {
		return array (
				array (
						'method', // name
						'm', // shortcut
						InputOption::VALUE_OPTIONAL, // mode
						'Delete method (1, 2 or 3).', // description
						0 // defaultValue
				),
				array (
						'yes', // name
						'y', // shortcut
						InputOption::VALUE_NONE, // mode
						'Answer yes to all prompts to user for confirmation.' // description
				),
				array (
						'nextseq', // name
						null,
						InputOption::VALUE_OPTIONAL,
						'psql setval() sequence to this value after delete (valid only for method 2).',
						9
				)
		);
	}

	public function fire() {
		$info = function ($msg) {
			$this->info($msg);
		};

		$option_method = intval($this->option('method'));
		$option_yes = $this->option('yes');
		$confirm = false;

		if ($option_method == 0) {
			$info('Please choose a delete method from the following (1 or 2 or 3):');
			$info('1: delete all items EXCEPT marc-import object types (WARNING!!!).');
			$info('2: delete all items in database (WARNING!!!).');
			$info('3: delete/undo last marc-import action (deletes all marc-import items).');
			$delete_method = intval($this->ask('What is your choise (1, 2 or 3)?'));
		} else {
			$delete_method = $option_method;
		}

		if (!$option_yes) {
			if ($this->confirm('Do you wish to continue? [yes|no]')) {
				$confirm = true;
			}
		} else {
			$confirm = true;
		}

		if ($confirm) {

			$t0 = microtime(true);
			$info('deleting ...');

			switch($delete_method) {
				case 1:
					$this->deleteAllButMarc();
					break;
				case 2:
					$this->deleteAll();
					break;
				case 3:
					$this->deletePreviousImportAction();
					break;
				default:
					break;
			}

			$t1 = microtime(true);
			$info("done deleting in: " . intval($t1 - $t0) . " secs");
		}
	}

	private function deleteAllButMarc() {
		$dbh = dbconnect();
		$SQL = "SELECT dsd.delete_item(item_id) FROM dsd.item2 WHERE obj_type <> 'marc-import'";
		$dbh->query($SQL);
	}

	private function deleteAll() {
		$dbh = dbconnect();
		$dbh->query('SELECT dsd.delete_item(item_id) FROM dsd.item2');

		$option_nextseq = intval($this->option('nextseq'));
		if ($option_nextseq > 0) {
			$q = $dbh->prepare("select setval('dsd.item2_id_seq', ?)");
			$q->bindParam(1, $option_nextseq);
			$q->execute();
		}

		$dbh->query("delete from dsd.marc_import");
	}

	private function deletePreviousImportAction() {
		$dbh = dbconnect();
		$qimp1 = $dbh->prepare("select id, uuid from dsd.marc_import order by ts desc limit 1");
		$qimp1->execute();
		$lastimp = $qimp1->fetch();
		if ($lastimp) {
			$oldimp_id = $lastimp[0];
			$olduuid = $lastimp[1];
			$qimp2 = $dbh->prepare("select item_id from dsd.metadatavalue2 where element = 'ea:marc:import:uuid' and text_value = ?");
			$qimp2->bindParam(1, $olduuid);
			$qimp2->execute();
			$olditems = array();
			while ($rimp2 = $qimp2->fetch()) {
				$olditems[] = $rimp2[0];
			}

			foreach ($olditems as $item_id) {
				$q = $dbh->prepare('SELECT dsd.delete_item(?)');
				$q->bindParam(1, $item_id);
				$q->execute();
			}

			$ql = $dbh->prepare("delete from dsd.marc_import where id = ?");
			$ql->bindParam(1, $oldimp_id);
			$ql->execute();
		}
	}
}