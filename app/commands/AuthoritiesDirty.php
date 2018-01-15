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
                'method', // name
                'm', // shortcut
                InputOption::VALUE_OPTIONAL, // mode
                'Dirty method (1 or 2).', // description
                0 // defaultValue
            ),
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

        $option_method = intval($this->option('method'));
		$option_yes = $this->option('yes');
		$confirm = false;

        if ($option_method == 0) {
            $info('Please choose a dirty method from the following (1 or 2):');
            $info('1: dirty all marc-import items in database (WARNING!!!).');
            $info('2: dirty all marc-import items that correspond to 200/Person obj-type with $f subfield.');
            $dirty_method = intval($this->ask('What is your choise (1 or 2)?'));
        } else {
            $dirty_method = $option_method;
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
			$info('marking as dirty ...');

            switch($dirty_method) {
                case 1:
                    $this->dirtyAll();
                    break;
                case 2:
                    $this->dirtyPersonsWithDates();
                    break;
                default:
                    break;
            }

			$t1 = microtime(true);
			$info("done marking as dirty in: " . intval($t1 - $t0) . " secs");
		}
	}

	private function dirtyAll() {
        $dbh = dbconnect();
        $dbh->query("select dsd.item_add_flag(item_id, '" . AuthoritiesImport::$DIRTY_FLAG . "') FROM dsd.item2 WHERE obj_type = 'marc-import'");
    }

    private function dirtyPersonsWithDates() {
        $info = function ($msg) {
            $this->info($msg);
        };

        $dbh = dbconnect();
        $toDirtyIds = array();
        $dirtySt = $dbh->prepare("select dsd.item_add_flag(?, '" . AuthoritiesImport::$DIRTY_FLAG . "')");
        $marcSt = $dbh->prepare("select item_id from dsd.metadatavalue2 where obj_type = 'marc-import' and element = 'ea:marc:id' and text_value = ?");

        $personsSt = $dbh->prepare("select item_id from dsd.item2 WHERE obj_type = 'auth-person'");
        $personsSt->execute();

        $cnt = 0;
        $t0 = microtime(true);

        while ($r = $personsSt->fetch()) {
            $item_id = $r[0];
            /* @var $idata ItemMetadata */
            $idata = PDao::get_item_metadata($item_id);
            $marc_id_value = $idata->getFirstItemValue(DataFields::ea_marc_id);

            if ($marc_id_value != null) {
                $marc_id = intval($marc_id_value->textValue());
                $marcSt->bindParam(1, $marc_id);
                $marcSt->execute();
                $marcRes = $marcSt->fetch();

                if ($marcRes) {
                    $marc_item_id = $marcRes[0];
                    $idata = PDao::get_item_metadata($marc_item_id);
                    $xml_data = $idata->getFirstItemValue(DataFields::ea_marc_record_xml)->textValue();
                    $journals = new File_MARCXML($xml_data, File_MARC::SOURCE_STRING);
                    $record = $journals->next();

                    if ($record) {
                        $fPers = $record->getField('200');
                        if ($fPers) {
                            $fSub = $fPers->getSubfield('f');
                            if ($fSub) {
                                $toDirtyIds[] = $marc_item_id;
                            }
                        }
                    }
                }
            }

            $cnt++;

            // echo progress every 10000 records
            $batch_size = 10000;
            if ($cnt % $batch_size == 0) {
                $t1 = microtime(true);
                $info("nodes so far processed: " . $cnt . " at: " . round(($batch_size / ($t1 - $t0)), 2) . " nodes/sec");
                $t0 = $t1;
            }
        }

        $this->info("toDirtyIds count: " . count($toDirtyIds));

        foreach ($toDirtyIds as $id) {
            $dirtySt->bindParam(1, $id);
            $dirtySt->execute();
        }
    }
	
}