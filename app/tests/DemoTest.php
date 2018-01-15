<?php

/**
 * @runTestsInSeparateProcesses
 */
class DemoTest extends TestCase {

    public function testDemo() {

        $dbh = dbconnect();
        $st = $dbh->prepare("SELECT count(item_id) as count FROM dsd.item2 WHERE obj_type = 'marc-import'");
        $st->execute();
        $r = $st->fetch();
        $cnt = $r[0];
        //echo "\ncnt: $cnt \n";
        $this->assertEquals($cnt, 299382);


        $st = $dbh->prepare("SELECT item_id FROM dsd.item2 WHERE obj_type = 'auth-person' ORDER BY item_id LIMIT 1");
        $st->execute();
        $r = $st->fetch();
        $item_id = $r[0];
        //echo "\n item: $item_id \n";
        $this->assertEquals($item_id, 300725);

        $idata = PDao::get_item_metadata($item_id);
        $marc_id_value = $idata->getFirstItemValue(DataFields::ea_marc_id);
        //echo "\n marc_id: " . $marc_id_value->textValue() . "\n";
        $this->assertEquals($marc_id_value->textValue(), 1138);
    }

}