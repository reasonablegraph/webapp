<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;


class AuthoritiesImport extends Command {

	protected $name = 'authorities:import';
	protected $description = 'Import Authorities from pre-defined MARC.';

	public static $DIRTY_FLAG = 'MARC_IMPORT_PENDING';


	public function __construct(){
		parent::__construct();
	}


	protected function getOptions() {
		return array (
				array (
						'username', // name
						null, // shortcut
						InputOption::VALUE_OPTIONAL, // mode
						'UserName to be used.', // description
						'cliuser' // defaultValue
				),
				array (
						'provider',
						null,
						InputOption::VALUE_OPTIONAL,
						'Provider to be used.',
						'EVE'
				),
				array (
						'filepath',
						null,
						InputOption::VALUE_OPTIONAL,
						'full path to imported file.',
						'/opt/ins/import/EVE.mrc'
				),
				array (
						'importlimit',
						null,
						InputOption::VALUE_OPTIONAL,
						'Limit on number of Marc items to import.',
						3000
				),
				array (
						'generatelimit',
						null,
						InputOption::VALUE_OPTIONAL,
						'Limit on number of items to generated from imported MARC nodes.',
						1000
				),
				array (
						'skip-import',
						'si',
						InputOption::VALUE_NONE,
						'Skip MARC import action.'
				),
				array (
						'skip-generate',
						'sg',
						InputOption::VALUE_NONE,
						'Skip Generate action.'
				)
		);
	}




	private function generateNodesFromMarc() {

		$info = function ($msg) {
			$this->info($msg);
		};

		$option_username = $this->option('username');
		$option_generatelimit = intval($this->option('generatelimit'));
		$dbh = dbconnect();

		$info('generating nodes from imported marc records ...');
		$LIMIT = '';
		if ($option_generatelimit > 0) {
			$LIMIT = 'LIMIT ' . $option_generatelimit . ' OFFSET 0';
		}

		// fetch a batch of marc-import nodes marked as dirty
		$SQL = "SELECT item_id FROM dsd.item2 WHERE obj_type = 'marc-import' AND flags @> ARRAY['" . self::$DIRTY_FLAG . "'] ORDER BY item_id " . $LIMIT;
		$stmt = $dbh->prepare($SQL);
		$stmt->execute();
		$vcnt = 0; // count valid generations
		$t0 = microtime(true);

		while ($r = $stmt->fetch()) {
			$item_id = $r[0];

			// fetch specific metadata keys
			$keys = array(
				DataFields::ea_obj_type,
				DataFields::ea_status,
				DataFields::ea_marc_id,
				DataFields::ea_marc_record_xml,
				DataFields::ea_marc_urn,
				DataFields::dc_title
			);
			$idata = PDao::get_item_metadata($item_id, $keys);

			$xml_data = $idata->getFirstItemValue(DataFields::ea_marc_record_xml)->textValue();
			$journals = new File_MARCXML($xml_data, File_MARC::SOURCE_STRING);
			$record = $journals->next();

			if ($record) {

				$imp = new MarcImportHelper($item_id, $record);
				$imp->setUserName($option_username);
				$result = $imp->process();

				// remove dirty flag if node generation was successful for this marc record
				if ($result !== false) {
					$vcnt ++;
					PDao::item_remove_flag($item_id, self::$DIRTY_FLAG);
				}
				
				if (true) {
					$nm = $imp->getNodeMemory();
					$nmcount = $nm->count();
					if ($nmcount > 0){

						foreach ($result as $ri){
							$g0 = GGraphIO::loadNodeNeighbourhood($ri);
							$rule_context = ItemSave::rule_engine($g0,$ri,$result);
						}
//	 					$g0 = GGraphIO::loadNodeNeighbourhood($item_id);
//	 					$ref_items = $result;
//	 					$rule_context = ItemSave::rule_engine($g0,$item_id,$ref_items);
					}
				}
			}

			// echo progress every 1000 records
			$batch_size = 1000;
			if ($vcnt % $batch_size == 0) {
				$t1 = microtime(true);
				$info("nodes so far generated: " . $vcnt . " at: " . round(($batch_size / ($t1 - $t0)), 2) . " nodes/sec");
				$t0 = $t1;
			}
		}

		$gen_count = $stmt->rowCount();
		$info('processed: ' . $gen_count . ' marc-import items');
		$info('generated: ' . $vcnt . ' nodes');
	}



	private function importMarc($importUuid) {

		$info = function ($msg) {
			$this->info($msg);
		};


		$now = new DateTime(null, new DateTimeZone('UTC'));
		$option_provider = $this->option('provider');
		$option_filepath = $this->option('filepath');
		$option_username = $this->option('username');
		$option_importlimit = intval($this->option('importlimit'));

		$dbh = dbconnect();
		$qid = $dbh->prepare("select item_id from dsd.metadatavalue2 where obj_type = 'marc-import' and element = 'ea:marc:id' and text_value = ?");
		$qmd5 = $dbh->prepare("select item_id from dsd.metadatavalue2 where item_id = ? and obj_type = 'marc-import' and element = 'ea:marc:record:xmlmd5' and text_value = ?");

		$journals = new File_MARC($option_filepath);
		$rcnt = 0;
		$t0 = microtime(true);
		while ($record = $journals->next()) {

			$marcid = null;
			$marcscn = null;
			$marcids = $record->getFields('001');
			$marcscns = $record->getFields('035');

			if ($marcids) {
				foreach ($marcids as $marcidf) {
					$marcid = $marcidf->getData();
				}
			}

			if ($marcscns) {
				foreach ($marcscns as $marcscnf) {
					$marcscn = $marcscnf->getSubfield('a')->getData();
				}
			}

			if ($marcid == null) {
				continue;
			}
			$rcnt ++;

			// calculate marc xml and md5 of xml
			$marcXml = $record->toXML();
			$marcXmlMd5 = md5($marcXml);
			$md5match = false;

			// locate item_id from marc_id
			$qid->bindParam(1, $marcid);
			$qid->execute();
			$resid = $qid->fetch();

			// locate if md5 matches for this item_id
			if ($resid) {
				$qmd5->bindParam(1, $resid[0]);
				$qmd5->bindParam(2, $marcXmlMd5);
				$qmd5->execute();
				$resmd5 = $qmd5->fetch();
				if ($resmd5) {
					$md5match = true;
				}
			}
			
			if (!$md5match) {
				$idata = new ItemMetadata();
				$w = 0;
				$marcurn = ($marcscn == null) ? null : 'urn:marc:' . $marcscn;
				$title = "MARC:" . $option_provider . ":" . $marcid;

				$idata->addValueSK(DataFields::ea_obj_type, "marc-import", null, null, null, null, null, null, $w ++);
				$idata->addValueSK(DataFields::dc_title, $title, null, null, null, null, null, null, $w ++);
				$idata->addValueSK('ea:label:', $title, null, null, null, null, null, null, $w ++);
				$idata->addValueSK(DataFields::ea_marc_id, $marcid, null, null, null, null, null, null, $w ++);
				$idata->addValueSK(DataFields::ea_marc_provider, $option_provider, null, null, null, null, null, null, $w ++);
				$idata->addValueSK(DataFields::ea_marc_record_xml, $marcXml, null, null, null, null, null, null, $w ++);
				$idata->addValueSK(DataFields::ea_marc_record_xml_md5, $marcXmlMd5, null, null, null, null, null, null, $w ++);
				if ($marcurn != null) {
					$idata->addValueSK(DataFields::ea_marc_urn, $marcurn, null, null, null, null, null, null, $w ++);
				}
				$idata->addValueSK(DataFields::ea_marc_import_date, $now->format('Y-m-d\TH:i:s\Z'), null, null, null, null, null, null, $w ++);
				$idata->addValueSK(DataFields::ea_marc_import_uuid, $importUuid, null, null, null, null, null, null, $w ++);
				$idata->addValueSK(DataFields::ea_source_filename, basename($option_filepath), null, null, null, null, null, null, $w ++);
				$idata->addValueSK(DataFields::ea_status, 'finish', null, null, null, null, null, null, $w ++);

				$is = new ItemSave();
				$is->setIdata($idata);
				$is->setUserName($option_username);

				// if already exists, delete and recreate as new instead of updating, but keep the same item_id
				$already_item_id = null;
				if ($resid) {
					$already_item_id = $resid[0];
					PDao::delete_item($already_item_id);
				}

				// create marc-import node with existing id or new id (in case of already_item_id is null)
				$new_id = $is->insert_new_item_batch_simple('ea:label:', $already_item_id);

				// FLAG item_id for the generating nodes step
				PDao::item_add_flag($new_id, self::$DIRTY_FLAG);
			}

			// echo progress every 1000 records
			$batch_size = 1000;
			if ($rcnt % $batch_size == 0) {
				$t1 = microtime(true);
				$info("marc-import nodes so far processed: " . $rcnt . " at: " . round(($batch_size / ($t1 - $t0)), 2) . " nodes/sec");
				$t0 = $t1;
			}

			// finish if we reached import limit (command line argument)
			if ($option_importlimit > 0 && $rcnt >= $option_importlimit) {
				break;
			}
		}

		return $rcnt;
	}

	private function log_import_action($importUuid) {
		$option_provider = $this->option('provider');
		$option_filepath = $this->option('filepath');
		$option_username = $this->option('username');
		$now = new DateTime(null, new DateTimeZone('UTC'));
		$dbh = dbconnect();
		
		$cr1 = $dbh->prepare("insert into dsd.marc_import (uuid, ts, provider, filepath, username) values (?, ?, ?, ?, ?)");
		$cr1->bindParam(1, $importUuid);
		$cr1->bindValue(2, $now->format('Y-m-d H:i:s'));
		$cr1->bindParam(3, $option_provider);
		$cr1->bindParam(4, $option_filepath);
		$cr1->bindParam(5, $option_username);
		$cr1->execute();
	}



	public function fire() {
		$FIRE_MARC_IMPORT = !($this->option('skip-import'));
		$FIRE_GENERATE = !($this->option('skip-generate'));

		$option_provider = $this->option('provider');
		$option_filepath = $this->option('filepath');

		$info = function($msg) {
			$this->info($msg);
		};

		require_once 'File/MARC.php';
		require_once 'File/MARCXML.php';
		
		$info('source file: ' . $option_filepath);
		$info('provider: ' . $option_provider);

		$t1 = microtime(true);

		if ($FIRE_MARC_IMPORT > 0) {
			$importUuid = PDao::createUUID();
			$this->log_import_action($importUuid); // log this marc-import action into the database
			$info('importing marc records ...');
			$rcnt = $this->importMarc($importUuid);

			$t2 = microtime(true);
			$info('DONE! imported ' . $rcnt . ' records in: ' . intval($t2 - $t1) . ' secs at: ' . round(($rcnt / ($t2 - $t1)), 2) . ' nodes/sec');
		}

		$t2 = microtime(true);

		if ($FIRE_GENERATE > 0) {
			$this->generateNodesFromMarc();
			$t3 = microtime(true);
			$info('nodes generated in: ' . intval($t3 - $t2) . ' secs');
		}

		return;
	}

}
