<?php

class KohaSyncHelper {

	public static $allCodes = "abcdefghijklmnpqtuvxyz";
	public static $chainCodes = "vxyz";
	public static $objectTypes = array(
		"100" => "auth-person",
		"130" => "auth-work",
		"650" => "auth-concept",
		"651" => "auth-place",
		"700" => "auth-expression",
		"020" => "auth-manifestation"
	);
	public static $commonTags = array(
		"100" => array(), // special handling, see processCommonTags()
		"650" => array(),
		"651" => array(),
		"700" => array(),
		"020" => array()
	);

	const LANGVAL_MAP = array(
		'eng' => 'Αγγλική',
		'gre' => 'Ελληνική',
		'ger' => 'Γερμανική',
		'fre' => 'Γαλλική',
		'lat' => 'Λατινική',
		'rus' => 'Ρωσική',
		'ita' => 'Ιταλική',
		'spa' => 'Ισπανική'
	);

	// TAG 100, 200 ... klp
	// code $a, $b, $x ... klp

	private $europeana = array(); // MAP pliroforion gia ton node europeana
	private $record;              // marc record
	private $physical_items;      // koha physical items
	private $sublocation;         // library sublocation
	private $nodeMemory;          // array of nodes pou 8a ftiaxtoun gia to trexon marc record
	private $basicField;
	private $basicTag;            // marc tag me vasi to opio krinoume to OT tou kentrikou komvou
	private $basicSubs;           // array of subfields of $basicField gia to  $basicTag
	private $basicSubsCnt;        // array($code of subfield => count(subfields)); (code = $a,$b,$c, ..) gia to  $basicTag

	private $isValid;
	private $isChain;             // ean to marc-record apikonizete se mas os chain of subject

	private $dbh;
	private $username;
	private $now;
	private $work_lang;           // h timh toy tag 041$h sto marc record
	private $work_title;


	public function __construct($marcRecord, $physical_items, $sublocation) {
		$this->nodeMemory = new ArrayObject();
		$this->record = $marcRecord;
		$this->physical_items = $physical_items;
		$this->sublocation = $sublocation;

		$this->isValid = false;
		$this->isChain = false;

		$this->basicSubs = array();
		$this->basicSubsCnt = array();

		$this->username = null;
	}


	/* @var $idata ItemMetadata */
	public function delete() {
		$this->init();

		if (!$this->isValid) {
			Log::info("invalid record detected during koha-sync:" . $this->record->toXML());
			return false;
		}

		if (!isset(self::$objectTypes[$this->basicTag])) {
			Log::info("invalid object type detected during koha-sync:" . $this->record->toXML());
			return false;
		}

		if (!isset($this->work_lang)) {
			Log::info("work lang field missing: " . $this->record->toXML());
			return false;
		}

		// init vars and helper vars
		$rgtrim = function($str) {
			return $this->rgtrim($str);
		};
		$f = $this->basicSubs;

		$a = $rgtrim($f['a'][0]->getData());
		$node_item_id = PDao::getFirstItemIdByMetadataKey(self::$objectTypes[$this->basicTag], DataFields::dc_title, $a);
		if (empty($node_item_id)) {
			return false;
		}

		$idata = PDao::get_item_metadata($node_item_id);
		$wl = $idata->getValueSK("ea:work:Language")[0];
		if ($wl == $this->work_lang) {
			$idata->addValueSK_DBK(DataFields::ea_status, 'error');
			$this->itemUpdate($node_item_id, $idata, 2);
		}

		return true;
	}


	/* @var $idata ItemMetadata */
	public function insertOrUpdate() {
		$this->init();

		if (!$this->isValid) {
			Log::info("invalid record detected during koha-sync:" . $this->record->toXML());
			return false;
		}

		if (!isset(self::$objectTypes[$this->basicTag])) {
			Log::info("invalid object type detected during koha-sync:" . $this->record->toXML());
			return false;
		}

		if (!isset($this->work_lang)) {
			Log::info("work lang field missing: " . $this->record->toXML());
			return false;
		}

		// init vars and helper vars
		$rgtrim = function($str) {
			return $this->rgtrim($str);
		};
		$w = 0;
		$f = $this->basicSubs;
		$sn = $this->basicSubsCnt;
		$item_ids = array();

		// create new ItemMetadata object or fetch existing
		$a = $rgtrim($f['a'][0]->getData());
		$node_item_id = PDao::getFirstItemIdByMetadataKey(self::$objectTypes[$this->basicTag], DataFields::dc_title, $a);
		$idata = (empty($node_item_id))
				? new ItemMetadata()
				: PDao::get_item_metadata($node_item_id);
		// create new ItemMetadata if work language is different
		if (!empty($node_item_id) && ($this->work_lang != $idata->getValueSK("ea:work:Language")[0])) {
			$idata = new ItemMetadata();
			$node_item_id = null;
		}

		// set object type from basic tag
		$idata->addValueSK_DBK(DataFields::ea_obj_type, self::$objectTypes[$this->basicTag], null, null, null, null, null, null, $w++);
		// set work language
		$idata->addValueSK_DBK("ea:work:Language", $this->work_lang, null, null, null,null,array("prps" => array("selected_value" => self::LANGVAL_MAP[$this->work_lang])),null, $w++);

		// handle object type specifics
		switch($this->basicTag) {
			case "130": // work
				$this->work_title = $title = $a;

				$idata->addValueSK_DBK(DataFields::dc_title, $title, null, null, null, null, null, null, $w++);
				$idata->addValueSK_DBK('ea:label:', $title, null, null, null, null, null, null, $w++);
				break;
			default:
				break; // invalid object type
		}

		$idata->addValueSK_DBK(DataFields::ea_marc_search_title, $this->rgtrimSearch($title), null, null, null, null, null, null, $w++);
		// gia ta ypoloipa koina pedia aneksarthtws object-type
		if (!$this->isChain) {
			// an den einai chain tote 8eloume kai ta koina pedia
			$this->processCommonTags($idata, $w, $item_ids);
		}
		$idata->addValueSK_DBK(DataFields::ea_marc_import_date, $this->now->format('Y-m-d\TH:i:s\Z'), null, null, null, null, null, null, $w++);
		$idata->addValueSK_DBK(DataFields::ea_status, 'finish', null, null, null, null, null, null, $w++);

		$node_item_id = (empty($node_item_id))
				? $this->itemSave($idata, 2)
				: $this->itemUpdate($node_item_id, $idata, 2);
		$item_ids[] = $node_item_id;

		// end of processing a marc record for generating one or more nodes
		// the item ids for the nodes generated are returned in an array
		return $item_ids;
	}


	/**
	 * COMMON MARC TAGS: notes klp
	 *
	 * @param ItemMetadata $idata
	 * @param int $w
	 */
	public function processCommonTags(&$idata, &$w, &$item_ids) {
		$expression_id = null;

		foreach (self::$commonTags as $oCode => $oKey) {
			$oFields = $this->record->getFields($oCode);
			if ($oFields) {
				foreach ($oFields as $oField) {
					if ($oField instanceof File_MARC_Control_Field) {
						$idata->addValueSK_DBK($oKey, $this->rgtrim($oField->getData()), null, null, null, null, null, null, $w++);
					} else { // if ($oField instanceof File_MARC_Data_Field) {
						switch($oCode) {

							case "100": // person - this is the work author
								$this->createOrUpdatePerson($oCode, $oField, $idata, $item_ids);
								break;

							case "650": // concept
								$this->createOrUpdateConcept($oCode, $oField, $idata, $item_ids);
								break;

							case "651": // place
								$this->createOrUpdatePlace($oCode, $oField, $idata, $item_ids);
								break;

							case "700": // expression
								$exp_id = $this->createOrUpdateExpression($oCode, $idata, $item_ids);
								if (!empty($exp_id)) {
									$expression_id = $exp_id;
								}
								break;

							case "020": // manifestation
								$this->createOrUpdateManifestation($oCode, $idata, $item_ids, $expression_id);
								break;

							default:
								foreach($oKey as $oSubCode => $oSubKey) {
									foreach($oField->getSubfields($oSubCode) as $oSubField) {
										$idata->addValueSK_DBK($oSubKey, $this->rgtrim($oSubField->getData()), null, null, null, null, null, null, $w++);
									}
								}
								break;
						}
					}
				}
			}
//			else {
//				switch($oCode) {
//					default:
//						Log::info("did not find fields for code:" . $oCode);
//						break;
//				}
//			}
		}
	}


	public function setUserName($username) {
		$this->username = $username;
	}


	/**
	 *
	 * @return ArrayObject
	 */
	public function getNodeMemory() {
		return $this->nodeMemory;
	}


	// PRIVATES


	private function init() {
		$this->dbh = prepareService();
		$basics = $this->record->getFields('130');
		$nbasics = count($basics);

		// validation
		// invalid if no 130 field exists
		// invalid if repeatable 130 fields
		if ($nbasics <= 0 || $nbasics > 1) {
			return;
		}

		$this->basicField = $basics[0];
		$tag = $this->basicTag = $this->basicField->getTag();

		$this->extractBasicSubfields();
		$sn = $this->basicSubsCnt;
		$this->now = new DateTime(null, new DateTimeZone('UTC'));

		switch($tag) {
			case "130":
				// invalid if no a
				// invalid if repeatable a
				if ($sn['a'] <= 0 || $sn['a'] > 1) {
					return;
				}
				break;
			default:
				break;
		}

		// validation
		// invalid if no 041 field exists
		$wl = $this->extractMarcField("041", "h");
		if (empty($wl)) {
			return;
		}
		$this->work_lang = $wl;

		$this->isValid = true;
	}


	private function extractMarcField($tag, $sub, $default = null) {
		$field = $this->record->getFields($tag);
		$nfield = count($field);
		if ($nfield <= 0) {
			return $default;
		}

		$field0 = $field[0];
		$field0_sub = $field0->getSubfields($sub);
		$field0_sub_cnt = count($field0_sub);
		if ($field0_sub_cnt <= 0) {
			return $default;
		}

		return $field0_sub[0]->getData();
	}


	private function extractMarcControlField($tag, $default = null) {
		$ret = $default;
		$fields = $this->record->getFields($tag);
		if ($fields) {
			foreach ($fields as $field) {
				$ret = $field->getData();
			}
		}
		return $ret;
	}


	private function linkOnceByTitle($key, &$idata, $item_title, $item_id) {
		$curr_found = false;
		$curr_items = $idata->getArrayValues($key);
		foreach($curr_items as $curr_item) {
			$curr_item_title = $curr_item[0];
			if ($curr_item_title == $item_title) {
				$curr_found = true;
			}
		}
		if (!$curr_found) {
			$idata->addValueSK($key, $item_title, null, null, null, $item_id);
		}
		return $curr_found;
	}


	private function linkManifestationOnce($key, &$idata, $manifestation_title, $manifestation_isbn, $manifestation_item_id) {
		$curr_found = false;
		$curr_items = $idata->getArrayValues($key);
		foreach($curr_items as $curr_item) {
			$curr_item_title = $curr_item[0];
			$curr_item_id = $curr_item[4];
			if ($curr_item_title == $manifestation_title) {
				// also compare isbns
				$curr_item_data = PDao::get_item_metadata($curr_item_id);
				$manif_items = $curr_item_data->getArrayValues(DataFields::ea_manif_ISBN_Number);
				foreach ($manif_items as $manif_item) {
					$manif_isbn = $manif_item[0];
					if ($manif_isbn == $manifestation_isbn) {
						$curr_found = true;
					}
				}
			}
		}
		if (!$curr_found) {
			$idata->addValueSK($key, $manifestation_title, null, null, null, $manifestation_item_id);
		}
		return $curr_found;
	}


	private function createOrUpdatePerson($oCode, $oField, &$idata, &$item_ids) {
		$a = ($oField->getSubfield('a')) ? $this->rgtrim($oField->getSubfield('a')->getData()) : '';
		$person_item_id = PDao::getFirstItemIdByMetadataKey(self::$objectTypes[$oCode], DataFields::dc_title, $a);
		$person_data = (empty($person_item_id))
			? new ItemMetadata()
			: PDao::get_item_metadata($person_item_id);

		$person_w = 0;
		$person_title = $a;
		$person_etitle = preg_replace('/,/', ' ', trim($person_title));
		$person_data->addValueSK_DBK(DataFields::ea_obj_type, self::$objectTypes[$oCode], null, null, null, null, null, null, $person_w++);
		$person_data->addValueSK_DBK(DataFields::dc_title, $person_title, null, null, null, null, null, null, $person_w++);
		$person_data->addValueSK_DBK('ea:label:', $person_title, null, null, null, null, null, null, $person_w++);
		$person_data->addValueSK_DBK(DataFields::ea_marc_search_title, $this->rgtrimSearch($person_title), null, null, null, null, null, null, $person_w++);
		$person_data->addValueSK_DBK(DataFields::ea_marc_import_date, $this->now->format('Y-m-d\TH:i:s\Z'), null, null, null, null, null, null, $person_w++);
		$person_data->addValueSK_DBK(DataFields::ea_status, 'finish', null, null, null, null, null, null, $person_w++);

		// save author
		$person_item_id = (empty($person_item_id))
			? $this->itemSave($person_data, 2)
			: $this->itemUpdate($person_item_id, $person_data, 2);
		$item_ids[] = $person_item_id;

		// add author to work
		$this->linkOnceByTitle(DataFields::ea_work_authorWork, $idata, $person_title, $person_item_id);

		$europeana_key2 = $person_etitle;
		$this->europeana['ea:europeana:type'] = 'agent';
		$this->europeana['marc-title'] = $this->field2String('100');
		$this->europeana['title'] = $person_title;
		$this->europeana['type-comment'] = 'person';

		$this->createEuropeanaNode($europeana_key2, $person_data);
	}


	private function createOrUpdateConcept($oCode, $oField, &$idata, &$item_ids) {
		$a = ($oField->getSubfield('a')) ? $this->rgtrim($oField->getSubfield('a')->getData()) : '';
		$concept_item_id = PDao::getFirstItemIdByMetadataKey(self::$objectTypes[$oCode], DataFields::dc_title, $a);
		$concept_data = (empty($concept_item_id))
			? new ItemMetadata()
			: PDao::get_item_metadata($concept_item_id);

		$concept_w = 0;
		$concept_title = $a;
		$concept_data->addValueSK_DBK(DataFields::ea_obj_type, self::$objectTypes[$oCode], null, null, null, null, null, null, $concept_w++);
		$concept_data->addValueSK_DBK(DataFields::dc_title, $concept_title, null, null, null, null, null, null, $concept_w++);
		$concept_data->addValueSK_DBK('ea:label:', $concept_title, null, null, null, null, null, null, $concept_w++);
		$concept_data->addValueSK_DBK(DataFields::ea_marc_search_title, $this->rgtrimSearch($concept_title), null, null, null, null, null, null, $concept_w++);
		$concept_data->addValueSK_DBK(DataFields::ea_marc_import_date, $this->now->format('Y-m-d\TH:i:s\Z'), null, null, null, null, null, null, $concept_w++);
		$concept_data->addValueSK_DBK(DataFields::ea_status, 'finish', null, null, null, null, null, null, $concept_w++);

		// save concept
		$concept_item_id = (empty($concept_item_id))
			? $this->itemSave($concept_data, 2)
			: $this->itemUpdate($concept_item_id, $concept_data, 2);
		$item_ids[] = $concept_item_id;

		// add concept to work
		$this->linkOnceByTitle(DataFields::ea_work_subjectCategory, $idata, $concept_title, $concept_item_id);
	}


	public function createOrUpdatePlace($oCode, $oField, &$idata, &$item_ids) {
		$a = ($oField->getSubfield('a')) ? $this->rgtrim($oField->getSubfield('a')->getData()) : '';
		$place_item_id = PDao::getFirstItemIdByMetadataKey(self::$objectTypes[$oCode], DataFields::dc_title, $a);
		$place_data = (empty($place_item_id))
			? new ItemMetadata()
			: PDao::get_item_metadata($place_item_id);

		$place_w = 0;
		$place_title = $a;
		$place_data->addValueSK_DBK(DataFields::ea_obj_type, self::$objectTypes[$oCode], null, null, null, null, null, null, $place_w++);
		$place_data->addValueSK_DBK(DataFields::dc_title, $place_title, null, null, null, null, null, null, $place_w++);
		$place_data->addValueSK_DBK('ea:label:', $place_title, null, null, null, null, null, null, $place_w++);
		$place_data->addValueSK_DBK(DataFields::ea_marc_search_title, $this->rgtrimSearch($place_title), null, null, null, null, null, null, $place_w++);
		$place_data->addValueSK_DBK(DataFields::ea_marc_import_date, $this->now->format('Y-m-d\TH:i:s\Z'), null, null, null, null, null, null, $place_w++);
		$place_data->addValueSK_DBK(DataFields::ea_status, 'finish', null, null, null, null, null, null, $place_w++);

		// save place
		$place_item_id = (empty($place_item_id))
			? $this->itemSave($place_data, 2)
			: $this->itemUpdate($place_item_id, $place_data, 2);
		$item_ids[] = $place_item_id;

		// add place to work
		$this->linkOnceByTitle("ea:work:geographical", $idata, $place_title, $place_item_id);
	}


	private function createOrUpdateExpression($oCode, &$idata, &$item_ids) {
		$rgtrim = function($str) {
			return $this->rgtrim($str);
		};

		$prefix = "Translation of «" . $this->work_title . "»";
		$a = $rgtrim($this->extractMarcField("245", "a", ""));
		$expression_title = $prefix . " " . $a;

		$expression_item_id = PDao::getFirstItemIdByMetadataKey(self::$objectTypes[$oCode], DataFields::dc_title, $expression_title);
		$expression_data = (empty($expression_item_id))
			? new ItemMetadata()
			: PDao::get_item_metadata($expression_item_id);

		$expression_w = 0;
		$expression_data->addValueSK_DBK(DataFields::ea_obj_type, self::$objectTypes[$oCode], null, null, null, null, null, null, $expression_w++);
		$expression_data->addValueSK_DBK(DataFields::dc_title, $expression_title, null, null, null, null, null, null, $expression_w++);
		$expression_data->addValueSK_DBK('ea:label:', $expression_title, null, null, null, null, null, null, $expression_w++);
		$expres_lang = $rgtrim($this->extractMarcField("041", "a", $this->work_lang));

		if (array_key_exists($expres_lang, self::LANGVAL_MAP)) {
			$expression_data->addValueSK_DBK(DataFields::ea_expres_Language, $expres_lang, null, null, null, null, array("prps" => array("selected_value" => self::LANGVAL_MAP[$expres_lang])), null, $expression_w++);
		}

		$expression_data->addValueSK_DBK(DataFields::ea_marc_search_title, $this->rgtrimSearch($expression_title), null, null, null, null, null, null, $expression_w++);
		$expression_data->addValueSK_DBK(DataFields::ea_marc_import_date, $this->now->format('Y-m-d\TH:i:s\Z'), null, null, null, null, null, null, $expression_w++);
		$expression_data->addValueSK_DBK(DataFields::ea_status, 'finish', null, null, null, null, null, null, $expression_w++);

		// 700 TRANSLATOR
		$e700 = $rgtrim($this->extractMarcField("700", "e", ""));
		if ($e700 == "Translator") {
			$translator_name = $rgtrim($this->extractMarcField("700", "a", ""));
			$translator_id = $this->createOrReUseSimpleItem($translator_name, 'auth-person', $item_ids);
			if (!empty($translator_id)) {
				$expression_data->addValueSK_DBK('ea:expres:translator', $translator_name, null, null, null, $translator_id);
			}
		}

		// save expression
		$expression_item_id = (empty($expression_item_id))
			? $this->itemSave($expression_data, 2)
			: $this->itemUpdate($expression_item_id, $expression_data, 2);
		$item_ids[] = $expression_item_id;

		// add expression to work
		$this->linkOnceByTitle("ea:expression:", $idata, $expression_title, $expression_item_id);

		return $expression_item_id;
	}


	private function createOrUpdateManifestation($oCode, &$idata, &$item_ids, $expression_id) {
		$rgtrim = function($str) {
			return $this->rgtrim($str);
		};

		$isbn = $rgtrim($this->extractMarcField("020", "a", ""));
		$manifestation_item_id = PDao::getFirstItemIdByMetadataKey(self::$objectTypes[$oCode], DataFields::ea_manif_ISBN_Number, $isbn);
		$manifestation_data = (empty($manifestation_item_id))
			? new ItemMetadata()
			: PDao::get_item_metadata($manifestation_item_id);

		$manifestation_w = 0;
		$manifestation_title = $rgtrim($this->extractMarcField("245", "a", ""));
		$manifestation_data->addValueSK_DBK(DataFields::ea_obj_type, self::$objectTypes[$oCode], null, null, null, null, null, null, $manifestation_w++);
		$manifestation_data->addValueSK_DBK(DataFields::dc_title, $manifestation_title, null, null, null, null, null, null, $manifestation_w++);
		$manifestation_data->addValueSK_DBK('ea:label:', $manifestation_title, null, null, null, null, null, null, $manifestation_w++);

		// 020 ISBN
		$manifestation_data->addValueSK_DBK(DataFields::dc_date_accessioned. $rgtrim($this->extractMarcControlField("005", "")), null, null, null, null, null, null, $manifestation_w++);
		$manifestation_data->addValueSK_DBK(DataFields::ea_manif_ISBN_Number, $isbn, null, null, null, null, null, null, $manifestation_w++);
		$manifestation_data->addValueSK_DBK(DataFields::ea_manif_ISBN_Terms, $rgtrim($this->extractMarcField("020", "c", "")), null, null, null, null, null, null, $manifestation_w++);
		$manifestation_data->addValueSK_DBK(DataFields::ea_manif_ISBN_Qualify, $rgtrim($this->extractMarcField("020", "q", "")), null, null, null, null, null, null, $manifestation_w++);

		// 245 MANIFESTATION
		$manifestation_data->addValueSK_DBK(DataFields::ea_manif_Title_Remainder, $rgtrim($this->extractMarcField("245", "b", "")), null, null, null, null, null, null, $manifestation_w++);
		$manifestation_data->addValueSK_DBK(DataFields::ea_manif_Title_Responsibility, $rgtrim($this->extractMarcField("245", "c", "")), null, null, null, null, null, null, $manifestation_w++);
		$manifestation_data->addValueSK_DBK(DataFields::ea_manif_Title_PartNumber, $rgtrim($this->extractMarcField("245", "n", "")), null, null, null, null, null, null, $manifestation_w++);
		$manifestation_data->addValueSK_DBK(DataFields::ea_manif_Title_PartName, $rgtrim($this->extractMarcField("245", "p", "")), null, null, null, null, null, null, $manifestation_w++);

		// 250 EDITION
		$manifestation_data->addValueSK_DBK(DataFields::ea_manif_Edition_Statement, $rgtrim($this->extractMarcField("250", "a", "")), null, null, null, null, null, null, $manifestation_w++);
		$manifestation_data->addValueSK_DBK(DataFields::ea_manif_Edition_Remainder, $rgtrim($this->extractMarcField("250", "b", "")), null, null, null, null, null, null, $manifestation_w++);

		// 260 PUBLICATION
		$pubplace_name = $rgtrim($this->extractMarcField("260", "a", ""));
		$publisher_name = $rgtrim($this->extractMarcField("260", "b", ""));
		$pubdatec = $rgtrim($this->extractMarcField("260", "c", ""));

		$pubplace_id = $this->createOrReUseSimpleItem($pubplace_name, 'auth-place', $item_ids);
		$publisher_id = $this->createOrReUseSimpleItem($publisher_name, 'auth-organization', $item_ids);
		$pubdate = intval(preg_replace('/©/', '', trim($pubdatec)));

		$pubRecId = $manifestation_data->getNextClientId();
		$manifestation_data->addValueSK_DBK(DataFields::ea_manif_Publication, $pubdate, null, null, null, null, null, $pubRecId);
		if (!empty($pubdate)) {
			$manifestation_data->addValueSK_DBK(DataFields::ea_manif_Publication_Date, $pubdate, null, null, null, null, array("json" => array("y" => $pubdate, "m" => null, "d" => null, "t" => null, "p" => "p", "z" => "date")), $manifestation_data->getNextClientId(), null, $pubRecId);
		}
		if (!empty($pubplace_id)) {
			$manifestation_data->addValueSK(DataFields::ea_manif_Publication_Place, $pubplace_name, null, null, null, $pubplace_id, null, $manifestation_data->getNextClientId(), null, $pubRecId);
		}
		if (!empty($publisher_id)) {
			$manifestation_data->addValueSK(DataFields::ea_manif_Publisher_Name, $publisher_name, null, null, null, $publisher_id, null, $manifestation_data->getNextClientId(), null, $pubRecId);
		}

		// 300 PHYSICAL DESCRIPTION
		$manifestation_data->addValueSK_DBK(DataFields::ea_manif_Physical_Description, $rgtrim($this->extractMarcField("300", "a", "")), null, null, null, null, null, null, $manifestation_w++);
		$manifestation_data->addValueSK_DBK(DataFields::ea_manif_Physical_Description_Details, $rgtrim($this->extractMarcField("300", "b", "")), null, null, null, null, null, null, $manifestation_w++);
		$manifestation_data->addValueSK_DBK(DataFields::ea_manif_Physical_Description_Dimensions, $rgtrim($this->extractMarcField("300", "c", "")), null, null, null, null, null, null, $manifestation_w++);
		$manifestation_data->addValueSK_DBK(DataFields::ea_manif_Physical_Description_Accompanying, $rgtrim($this->extractMarcField("300", "e", "")), null, null, null, null, null, null, $manifestation_w++);

		$manifestation_data->addValueSK_DBK(DataFields::ea_marc_search_title, $this->rgtrimSearch($manifestation_title), null, null, null, null, null, null, $manifestation_w++);
		$manifestation_data->addValueSK_DBK(DataFields::ea_marc_import_date, $this->now->format('Y-m-d\TH:i:s\Z'), null, null, null, null, null, null, $manifestation_w++);
		$manifestation_data->addValueSK_DBK(DataFields::ea_status, 'finish', null, null, null, null, null, null, $manifestation_w++);

		// save manifestation
		$manifestation_item_id = (empty($manifestation_item_id))
			? $this->itemSave($manifestation_data, 2)
			: $this->itemUpdate($manifestation_item_id, $manifestation_data, 2);
		$item_ids[] = $manifestation_item_id;

		// take account isbn during linkOnce
		if (empty($expression_id)) {
			// add manifestation to work
			$this->linkManifestationOnce(DataFields::ea_workof, $idata, $manifestation_title, $isbn, $manifestation_item_id);
		} else {
			// add manifestation to expression
			$expression_data = PDao::get_item_metadata($expression_id);
			$result = $this->linkManifestationOnce(DataFields::ea_workof, $expression_data, $manifestation_title, $isbn, $manifestation_item_id);
			if (!$result) {
				$this->itemUpdate($expression_id, $expression_data, 2);
			}
		}

		// PHYSICAL ITEMS
		foreach ($this->physical_items as $phy) {
			$phy_item_id = PDao::getFirstItemIdByMetadataKey('physical-item', DataFields::ea_item_barcode, $phy->barcode);
			$phy_idata = (empty($phy_item_id))
				? new ItemMetadata()
				: PDao::get_item_metadata($phy_item_id);

			$phy_w = 0;
			$phy_idata->addValueSK_DBK(DataFields::ea_obj_type, 'physical-item', null, null, null, null, null, null, $phy_w++);
			$phy_idata->addValueSK_DBK(DataFields::dc_title, $manifestation_title, null, null, null, null, null, null, $phy_w++);
			$phy_idata->addValueSK_DBK(DataFields::ea_item_type, 'book', null, null, null, null, array("prps" => array("selected_value" => "Βιβλίο")), null, $phy_w++);
			$phy_idata->addValueSK_DBK(DataFields::ea_item_location, 'amelib', null, null, null, null, array("prps" => array("selected_value" => "Κεντρική Βιβλιοθήκη")), null, $phy_w++);
			$subl_data = (intval($this->sublocation) == 1) ? array("prps" => array("selected_value" => "Αριστοτέλειο Πανεπιστήμιο Θεσσαλονίκης")) : array("prps" => array("selected_value" => "Γεωπονικό Πανεπιστήμιο Αθηνών"));
			$phy_idata->addValueSK_DBK(DataFields::ea_item_sublocation, $this->sublocation, null, null, null, null, $subl_data, null, $phy_w++);
			$phy_idata->addValueSK_DBK(DataFields::ea_item_barcode, $phy->barcode, null, null, null, null, null, null, $phy_w++);
			$phy_idata->addValueSK_DBK(DataFields::ea_item_copyNumber, $phy->copynumber, null, null, null, null, null, null, $phy_w++);
			$phy_idata->addValueSK_DBK(DataFields::ea_item_classification, $phy->itemcallnumber, null, null, null, null, null, null, $phy_w++);
			$phy_idata->addValueSK_DBK(DataFields::ea_artifact_of, $manifestation_title, null, null, null, $manifestation_item_id);
			$phy_idata->addValueSK_DBK(DataFields::ea_status, 'finish', null, null, null, null, null, null, $phy_w++);

			// save physical item
			$phy_item_id = (empty($phy_item_id))
				? $this->itemSave($phy_idata, 2)
				: $this->itemUpdate($phy_item_id, $phy_idata, 2);
			$item_ids[] = $phy_item_id;
		}
	}


	private function createOrReUseSimpleItem($title, $obj_type, &$item_ids) {
		$idata = new ItemMetadata();
		$idata->addValueSK(DataFields::ea_obj_type, $obj_type);
		$idata->addValueSK(DataFields::dc_title, $title);
		$idata->addValueSK(DataFields::ea_status, 'finish');

		// save item
		$item_id = $this->saveOrReuseItem($idata, $obj_type, DataFields::dc_title, $title, $item_ids);

		return $item_id;
	}


	private function saveOrReuseItem($idata, $obj_type, $element, $value, &$item_ids) {
		$is = new ItemSave();
		$is->setIdata($idata);
		$is->setUserName($this->username);

		$idata->validate();
		$errors = $idata->getErrors();
		$err_counter = count($errors);

		if ($err_counter > 0) {
			return null;
		}

		$item_id = PDao::getFirstItemIdByMetadataKey($obj_type, $element, $value);
		if (empty($item_id)) {
			$item_id = $is->insert_new_item_batch_simple();
			$item_ids[] = $item_id;
		}

		return $item_id;
	}


	/**
	 *
	 * @param ItemMetadata $idata
	 * @param number $refType
	 * @return unknown|NULL|number
	 */
	private function itemSave($idata, $refType = 0) {
		$this->nodeMemory[] = $idata;

		$is = new ItemSave();
		$is->setIdata($idata);
		if ($this->username !== null) {
			$is->setUserName($this->username);
		}
		$item_id = $is->insert_new_item_batch_simple();

		return $item_id;
	}


	private function itemUpdate($item_id, $idata, $refType = 0) {
		$this->nodeMemory[] = $idata;

		$is = new ItemSave();
		$is->setIdata($idata);
		if ($this->username !== null) {
			$is->setUserName($this->username);
		}
		$is->setItemId($item_id);
		$is->update_item();

		return $item_id;
	}


	private function extractBasicSubfields() {
		for ($i = 0; $i < strlen(self::$allCodes); $i++) {
			$code = self::$allCodes[$i];
			$this->basicSubs[$code] = $this->basicField->getSubfields($code);
			$this->basicSubsCnt[$code] = count($this->basicSubs[$code]);
			if ($this->basicSubsCnt[$code] <= 0) {
				$this->basicSubs[$code] = false;
			}
		}
	}


	private function field2String($tag) {
		$rep = '';
		$sep = '';
		$field = $this->record->getField($tag);

		if ($field && $field->isDataField()) {
			$subfields = $field->getSubfields();
			$rep = "$tag" . ': ';
			foreach ($subfields as $k => $sf) {
				$data = $this->rgtrim($sf->getData());
				$rep .= ($sep . '‡'. $k . ' '. $data);
				$sep = ' ';
			}
		}

		return $rep;
	}


	private function createEuropeanaNode($europeana_key2, &$idata) {
		// (edw apokleietai to $ot (tou krikou 0) na ine subject-chain, giati to xeirizomaste me eidiko tropo sthn createChainNode())
//		$this->europeana['ea:europeana:key1'] = $this->marc_id;
		$this->europeana['ea:europeana:key2'] = preg_replace('/\s+/', '', $europeana_key2);
		$ot = $idata->getObjectType();
		if ($ot == 'auth-person') {
			$date = $idata->getFirstItemValueOrEmpty('ea:auth:Person_DatesAssociated')->textValue();
			if (!empty($date)){
				$this->europeana['dc:date'] = $date;
			}
		}
		$notes = $idata->getItemValues('ea:auth:NotePublic');
		if (!empty($notes)) {
			$this->europeana['notes'] = array();
			foreach ($notes as $noteiv) {
				$this->europeana['notes'][] = $noteiv->textValue();
			}
		}

		$eType = "europeana";
//		$euro_id = PDao::getFirstItemIdByMetadataKey($eType, 'ea:europeana:key1', $this->europeana['ea:europeana:key1']);
		$euro_id = PDao::getFirstItemIdByMetadataKey($eType, 'ea:europeana:key2', $this->europeana['ea:europeana:key2']);
		$eIdata = (empty($euro_id))
			? new ItemMetadata()
			: PDao::get_item_metadata($euro_id);

		// $etitle = $this->europeana['title'];
		$emarc_title = $this->europeana['marc-title'];
		$w = 0;
		$eIdata->addValueSK_DBK(DataFields::ea_obj_type, $eType, null, null, null, null, null, null, $w++);
		$eIdata->addValueSK_DBK(DataFields::dc_title, $emarc_title, null, null, null, null, null, null, $w++);
		$eIdata->addValueSK_DBK('ea:label:', $emarc_title, null, null, null, null, null, null, $w++);
		$eIdata->addValueSK_DBK('ea:europeana:type', $this->europeana['ea:europeana:type'], null, null, null, null, null, null, $w++);
//		$eIdata->addValueSK_DBK('ea:europeana:key1', $this->europeana['ea:europeana:key1'], null, null, null, null, null, null, $w++);
		$eIdata->addValueSK_DBK('ea:europeana:key2', $this->europeana['ea:europeana:key2'], null, null, null, null, null, null, $w++);
		$eIdata->addValueSK_DBK(DataFields::ea_status, 'finish', null, null, null, null, null, null, $w++);
		unset($this->europeana['marc-title']);
		unset($this->europeana['ea:europeana:type']);
//		unset($this->europeana['ea:europeana:key1']);
		unset($this->europeana['ea:europeana:key2']);
		$europeanaItemValue = ItemValue::cKeyTextValue('ea:europeana:json-data', json_encode($this->europeana));
		$europeanaItemValue->setToJData('srlzn', 'json');
		$eIdata->addItemValueNK($europeanaItemValue);

		$euro_id = (empty($euro_id))
			? $this->itemSave($eIdata, 1)
			: $this->itemUpdate($euro_id, $eIdata, 1);

		return $euro_id;
	}


	private function rgtrim($str) {
		return preg_replace('/\s{2,}/', ' ', trim($str));
	}


	private function rgtrimSearch($str) {
		return preg_replace('/\s+/', '', mb_strtolower(trim($str)));
	}


}