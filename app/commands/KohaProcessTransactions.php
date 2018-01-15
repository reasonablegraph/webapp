<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class KohaProcessTransactions extends Command {

	protected $name = 'koha:processTransactions';
	protected $description = 'Sync with KOHA DB based on custom implemented transactions DB.';

	public function __construct(){
		parent::__construct();
	}

	protected function getOptions() {
		return array (
			array (
				'dbhost', // name
				null, // shortcut
				InputOption::VALUE_OPTIONAL, // mode
				'DB Host to be used.', // description
				'127.0.0.1' // defaultValue
			),
			array (
				'dbport', // name
				null, // shortcut
				InputOption::VALUE_OPTIONAL, // mode
				'DB Port to be used.', // description
				'13306' // defaultValue
			),
			array (
				'dbname',
				null,
				InputOption::VALUE_OPTIONAL,
				'DB Name to be used.',
				'rg_transaction_db'
			),
			array (
				'kohadbname',
				null,
				InputOption::VALUE_OPTIONAL,
				'KOHA DB Name to be used.',
				'koha_library1'
			),
			array (
				'dbuser',
				null,
				InputOption::VALUE_OPTIONAL,
				'DB Username to be used.',
				'root'
			),
			array (
				'dbpass',
				null,
				InputOption::VALUE_OPTIONAL,
				'DB Password to be used.',
				'supersecret'
			),
			array (
				'username', // name
				null, // shortcut
				InputOption::VALUE_OPTIONAL, // mode
				'UserName to be used.', // description
				'cliuser' // defaultValue
			),
			array (
				'sublocation', // name
				null, // shortcut
				InputOption::VALUE_OPTIONAL, // mode
				'Library SubLocation to be used.', // description
				'1' // defaultValue
			)
		);
	}

	public function fire() {
		$info = function ($msg) {
			$this->info($msg);
		};

		$info('hello koha:processTransactions');
		$gl_count = 0;
		$dbh = $this->dbc();
		$dbk = $this->dbk();
		$stmt = $dbh->prepare("select * from transactions where harvested = false order by transaction_id asc");
		$stmt->execute();
		$items_stmt = $dbk->prepare("select barcode, copynumber, itemcallnumber from items where biblionumber = ?");

		while ($row = $stmt->fetch()) {
			$transactionId = $row[0];
			$action = $row[1];
//			$transactionDate = $row[2];
//			$biblioMetadataId = $row[3];
			$biblioNumber = $row[4];
			$metadata = $row[5];
//			$harvested = $row[6];

			$journals = new File_MARCXML($metadata, File_MARC::SOURCE_STRING);
			$record = $journals->next();
			if (empty($record)) {
				continue;
			}

			$items = array();
			$items_stmt->bindValue(1, intval($biblioNumber));
			$items_stmt->execute();
			while ($item_row = $items_stmt->fetch()) {
				$phy = new stdClass();
				$phy->barcode = $item_row[0];
				$phy->copynumber = $item_row[1];
				$phy->itemcallnumber = $item_row[2];
				$items[] = $phy;
			}

			$khs = new KohaSyncHelper($record, $items, $this->option('sublocation'));
			$khs->setUserName($this->option('username'));
			$result = false;
			$info("found biblio: " . $biblioNumber . " with Action: " . $action);

			switch ($action) {
				case "insert":
				case "update":
					$result = $khs->insertOrUpdate();
					break;
				case "delete":
					$result = $khs->delete();
					break;
				default:
					break;
			}

			// finish
			if ($result !== false) {
				$hupd = $dbh->prepare("update transactions set harvested = true where transaction_id = ?");
				$hupd->bindParam(1, $transactionId);
				$hupd->execute();

				// RUN Rule Engine
				$nm = $khs->getNodeMemory();
				$nmcount = $nm->count();
				$gl_count += $nmcount;
			}
		}

//		if ($gl_count > 0) {
//			$info("Reseting Graph (Full) ...");
//			$rlock = new GRuleEngineLock();
//			$rlock->lock(null);
//			$context = GGraphUtil::graphResetFull();
//			$rlock->release();
//		}
	}

	private function dbc() {
		$con_str = sprintf('mysql:host=%s;port=%s;charset=utf8;dbname=%s',$this->option('dbhost'), $this->option('dbport'), $this->option('dbname'));
		$params = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");
		$con = new PDO($con_str, $this->option('dbuser'), $this->option('dbpass'), $params);
		$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $con;
	}

	private function dbk() {
		$con_str = sprintf('mysql:host=%s;port=%s;charset=utf8;dbname=%s',$this->option('dbhost'), $this->option('dbport'), $this->option('kohadbname'));
		$params = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");
		$con = new PDO($con_str, $this->option('dbuser'), $this->option('dbpass'), $params);
		$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $con;
	}

}