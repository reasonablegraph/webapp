<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;


class AuthoritiesCorrectMarcId extends Command {

	protected $name = 'authorities:correctMarcId';
	protected $description = 'Correct Authorities MARC Id.';

	public function __construct(){
		parent::__construct();
	}


	public function fire() {
        $info = function ($msg) {
            $this->info($msg);
        };

        $t00 = $t0 = microtime(true);
        $dbh = dbconnect();
        $st = $dbh->prepare("SELECT item_id FROM dsd.item2 WHERE obj_type <> 'marc-import' ORDER BY item_id");
        $st->execute();
        $cnt = 0;

        while ($r = $st->fetch()) {
            $node_item_id = $r[0];
            /* @var $idata ItemMetadata */
            $idata = PDao::get_item_metadata($node_item_id);
            $marc_id_value = $idata->getFirstItemValue(DataFields::ea_marc_id);

            if ($marc_id_value != null) {
                $refs = $idata->getItemValues('ea:marc-ref2:');

                if (count($refs) > 1) {

//                    $info("item id: " . $node_item_id);
//                    $info("marc id: " . $marc_id_value->textValue());
                    $marcItems = array();

                    foreach ($refs as $ref) {
//                        $info("ref2: " . $ref->textValue() . " ref-item: " . $ref->refItem());
                        $marcItems[] = intval($ref->textValue());
                    }

                    $minMarcItem = min($marcItems);
//                    $info("min marc-item: " . $minMarcItem);
                    if ($minMarcItem != false) {
                        $idata->addValueSK_DBK(DataFields::ea_marc_id, $minMarcItem);
                        $is = new ItemSave();
                        $is->setIdata($idata);
                        $is->setItemId($node_item_id);
                        $is->update_item();
                    }

                    $cnt++;
//                    break;

                    // echo progress every 1000 records
                    $batch_size = 1000;
                    if ($cnt % $batch_size == 0) {
                        $t1 = microtime(true);
                        $info("nodes so far generated: " . $cnt . " at: " . round(($batch_size / ($t1 - $t0)), 2) . " nodes/sec");
                        $t0 = $t1;
                    }
                }
            }
        }

        $t11 = microtime(true);
        $info("total items: " . $cnt . "(" . intval($t11 - $t00) . " secs)");
		return;
	}

}
