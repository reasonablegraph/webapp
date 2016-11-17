<?php

class MarcImportHelper {

	public static $allCodes = "abcdefghijklmnqtxyz";
	public static $chainCodes = "jxyz";
	public static $objectTypes = array(
		"200" => "auth-person",
		"210" => "auth-organization",
		"215" => "auth-place",
		"220" => "auth-family",
		"230" => "auth-work",
		"240" => "auth-work",
		"250" => "auth-concept",
		"280" => "auth-genre",
		"j" => "auth-genre",
		"x" => "auth-concept",
		"y" => "auth-place",
		"z" => "auth-event"
	);
	public static $otherTags = array(
		"001" => DataFields::ea_marc_id,
		"005" => DataFields::ea_marc_last_update,
		"152" => array(
			"b" => "ea:classification:CatalogingSource_Rules"
		),
		"300" => array(
			"a" => "ea:auth:NotePublic",
			"b" => "ea:auth:NotePublic"
		),
		"305" => array(
			"a" => "ea:auth:NotePublic",
			"b" => "ea:auth:NotePublic"
		),
		"310" => array(
			"a" => "ea:auth:NotePublic",
			"b" => "ea:auth:NotePublic"
		),
		"320" => array(
			"a" => "ea:auth:NotePublic",
			"b" => "ea:auth:NotePublic"
		),
		"330" => array(
			"a" => "ea:auth:NotePublic",
			"b" => "ea:auth:NotePublic"
		),
		"340" => array(
			"a" => "ea:auth:NotePublic",
			"b" => "ea:auth:NotePublic"
		),
		"801" => array(), // special handling, see processCommonTags()
		"810" => array(
			"a" => "ea:auth:NotePublic",
			"b" => "ea:auth:NotePublic",
			"e" => "ea:auth:NotePublic"
		),
		"815" => array(
			"a" => "ea:auth:NotePublic"
		),
		"820" => array(
			"a" => "ea:auth:NotePublic"
		),
		"825" => array(
			"a" => "ea:auth:NotePublic"
		),
		"830" => array(
			"a" => "ea:auth:NotePublic"
		),
		"856" => array(
			"u" => "ea:authElectronic:Location",
			"d" => "ea:authElectronic:Location"
		)
	);

	// TAG 100, 200 ... klp
	// code $a, $b, $x ... klp

	private $europeana = array(); // MAP pliroforion gia ton node europeana
	private $record;              // marc record
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
	private $marc_id;             // h timh tou tag 001 sto marc record
	private $marc_item_id;        // to item_id tou marc-import node


	public static function extractMarcID($record) {
		$marcid = null;

		$marcids = $record->getFields('001');
		if ($marcids) {
			foreach ($marcids as $marcidf) {
				$marcid = $marcidf->getData();
			}
		}

		return $marcid;
	}


	public function __construct($marc_item_id, $marcRecord) {
		$this->marc_item_id = $marc_item_id;
		$this->nodeMemory = new ArrayObject();
		$this->record = $marcRecord;

		$this->isValid = false;
		$this->isChain = false;

		$this->basicSubs = array();
		$this->basicSubsCnt = array();

		$this->username = null;
		$this->marc_id = MarcImportHelper::extractMarcID($marcRecord);
	}


	/* @var $idata ItemMetadata */
	public function process() {
		$this->init();

		if (!$this->isValid) {
			Log::info("invalid record detected during marc-import:" . $this->record->toXML());
			return false;
		}

		if (!isset(self::$objectTypes[$this->basicTag])) {
			Log::info("invalid object type detected during marc-import:" . $this->record->toXML());
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
		$node_item_id = $this->itemLocateByMetadataKey(self::$objectTypes[$this->basicTag], DataFields::ea_marc_id, $this->marc_id);
		$idata = (empty($node_item_id))
				? new ItemMetadata()
				: PDao::get_item_metadata($node_item_id);

		// set object type from basic tag
		$idata->addValueSK_DBK(DataFields::ea_obj_type, self::$objectTypes[$this->basicTag], null, null, null, null, null, null, $w++);

		// handle object type specifics
		switch($this->basicTag) {
			case "200": // person
				$title = ($sn['b'] > 0)
						? $rgtrim($f['a'][0]->getData()) . ", " . $rgtrim($f['b'][0]->getData())
						: $rgtrim($f['a'][0]->getData());

				$etitle = ($sn['b'] > 0)
						? $rgtrim($f['a'][0]->getData()) .  " " . $rgtrim($f['b'][0]->getData())
						: $rgtrim($f['a'][0]->getData());

				$europeana_key2 = $etitle;
				$this->europeana['ea:europeana:type'] = 'agent';
				$this->europeana['marc-title'] = $this->field2String('200');
				$this->europeana['title'] = $title;
				$this->europeana['type-comment'] = 'person';

				$this->deletePersonKeys($idata);

				$idata->addValueSK(DataFields::dc_title, $title, null, null, null, null, null, null, $w++);
				$idata->addValueSK('ea:label:', $title, null, null, null, null, null, null, $w++);
				$this->repetitiveField('c', 'ea:auth:Person_TitlesAssociated', $idata, $w);
				$this->optionalField('d', 'ea:auth:Person_Numeration', $idata, $w);
				$this->optionalField('f', 'ea:auth:Person_DatesAssociated', $idata, $w, array('remove_chars'=>array('(',')')));
				$this->optionalField('g', 'ea:auth:Person_FullerName', $idata, $w);
				$ind2 = $this->basicField->getIndicator(2);
				if ($ind2 == 0 || $ind2 == 1) {
					$idata->addValueSK('ea:auth:Person_Ind1', 'typePersonalName_' . $ind2, null, null, null, null, null, null, $w++);
				}

				// 120 tag (gender) handling
				$genderField = $this->record->getField("120");
				if ($genderField) {
					$genderData = ($genderField->getSubfield('a')) ? $genderField->getSubfield('a')->getData() : '';
					if ($genderData[0] == "a") { // female
						$idata->addValueSK('ea:authPerson:Gender_Name', 'female', null, null, null, null, null, null, $w++);
					} else if ($genderData[0] == "b") { // male
						$idata->addValueSK('ea:authPerson:Gender_Name', 'male', null, null, null, null, null, null, $w++);
					}
				}

				// 400 tag (alternative name) handling
				$altNameFields = $this->record->getFields("400");
				if ($altNameFields) {
					foreach ($altNameFields as $altNameField) {
						$altA = ($altNameField->getSubfield('a')) ? $rgtrim($altNameField->getSubfield('a')->getData()) : '';
						$altB = ($altNameField->getSubfield('b')) ? $rgtrim($altNameField->getSubfield('b')->getData()) : '';
						$altName = $altA;
						if (strlen($altB) > 0) {
							$altName .= ", " . $altB;
						}

						$altNameRecId = $idata->getNextClientId();
						$idata->addValueSK('ea:relation:PersonAlterOther_alter', $altName, null, null, null, null, null, $altNameRecId, $w++);
						$this->repetitiveChildField($altNameField, 'c', 'ea:auth:Person_TitlesAssociated', $idata, $w, $altNameRecId);
						$this->optionalChildField($altNameField, 'd', 'ea:auth:Person_Numeration', $idata, $w, $altNameRecId);
						$this->optionalChildField($altNameField, 'f', 'ea:auth:Person_DatesAssociated', $idata, $w, $altNameRecId, array('remove_chars'=>array('(',')')));
						$this->optionalChildField($altNameField, 'g', 'ea:auth:Person_FullerName', $idata, $w, $altNameRecId);
						$altNameInd2 = $altNameField->getIndicator(2);
						if ($altNameInd2 == 0 || $altNameInd2 == 1) {
							$idata->addValueSK('ea:auth:Person_Ind1', 'typePersonalName_' . $altNameInd2, null, null, null, null, null, null, $w++, $altNameRecId);
						}
					}
				}
				break;
			case "210": //organization
				$title = $rgtrim($f['a'][0]->getData());
				$europeana_key2 = $title;
				$this->europeana['marc-title'] = $this->field2String('210');
				$this->europeana['title'] = $title;
				$this->europeana['ea:europeana:type'] = 'agent';
				$this->europeana['type-comment'] = 'organization';

				$this->deleteOrganizationKeys($idata);

				$idata->addValueSK(DataFields::dc_title, $title, null, null, null, null, null, null, $w++);
				$idata->addValueSK('ea:label:', $title, null, null, null, null, null, null, $w++);
				$this->repetitiveField('b', 'ea:auth:Organization_Subdivision', $idata, $w);
				$this->repetitiveField('c', 'ea:auth:Organization_Addition', $idata, $w);
				$this->repetitiveField('d', 'ea:auth:Organization_Number', $idata, $w);
				$this->repetitiveField('e', 'ea:auth:Organization_Location', $idata, $w);
				$this->repetitiveField('f', 'ea:auth:Organization_Date', $idata, $w);
				$ind1 = $this->basicField->getIndicator(1);
				$ind2 = $this->basicField->getIndicator(2);
				if ($ind1 == 0) {
					switch($ind2) {
						case 0:
							$idata->addValueSK('ea:auth:Organization_Ind1', 'ControlSubfieldRelation_a', null, null, null, null, null, null, $w++);
							break;
						case 1:
							$idata->addValueSK('ea:auth:Organization_Ind1', 'ControlSubfieldRelation_b', null, null, null, null, null, null, $w++);
							break;
						case 2:
							$idata->addValueSK('ea:auth:Organization_Ind1', 'ControlSubfieldRelation_d', null, null, null, null, null, null, $w++);
							break;
						default:
							break;
					}
				} else if ($ind1 == 1) {
					switch($ind2) {
						case 0:
							$idata->addValueSK('ea:auth:Organization_Ind1', 'ControlSubfieldRelation_e', null, null, null, null, null, null, $w++);
							break;
						case 1:
							$idata->addValueSK('ea:auth:Organization_Ind1', 'ControlSubfieldRelation_f', null, null, null, null, null, null, $w++);
							break;
						case 2:
							$idata->addValueSK('ea:auth:Organization_Ind1', 'ControlSubfieldRelation_g', null, null, null, null, null, null, $w++);
							break;
						default:
							break;
					}
				}
				break;
			case "215": // place
				$title = $rgtrim($f['a'][0]->getData());
				$this->europeana['marc-title'] = $this->field2String('215');
				$this->europeana['title'] = $title;
				$europeana_key2 = $title;
				$this->deletePlaceKeys($idata);
				$idata->addValueSK(DataFields::dc_title, $title, null, null, null, null, null, null, $w++);
				$idata->addValueSK('ea:label:', $title, null, null, null, null, null, null, $w++);
				break;
			case "220": // family
				$title = $rgtrim($f['a'][0]->getData());
				$europeana_key2 = $title;
				$this->europeana['marc-title'] = $this->field2String('220');
				$this->europeana['title'] = $title;
				$this->europeana['ea:europeana:type'] = 'agent';
				$this->europeana['type-comment'] = 'family';
				$this->deleteFamilyKeys($idata);
				$idata->addValueSK(DataFields::dc_title, $title, null, null, null, null, null, null, $w++);
				$idata->addValueSK('ea:label:', $title, null, null, null, null, null, null, $w++);
				$this->repetitiveField('f', 'ea:auth:Family_DatesAssociated', $idata, $w);
				break;
			case "230": // work as subject xoris sigrafis
				$title = $rgtrim($f['a'][0]->getData());
				$this->europeana['marc-title'] = $this->field2String('230');
				$this->europeana['title'] = $title;
				$europeana_key2 = $title;
				$this->deleteWorkNoAuthorsKeys($idata);
				$idata->addValueSK(DataFields::dc_title, $title, null, null, null, null, null, null, $w++);
				$idata->addValueSK('ea:label:', $title, null, null, null, null, null, null, $w++);
				$this->optionalField('b', 'ea:work:Version', $idata, $w);
				$this->optionalField('h', 'ea:work:Title_PartNumber', $idata, $w);
				$this->optionalField('i', 'ea:work:Title_PartName', $idata, $w);
				$this->optionalField('k', 'ea:work:Date', $idata, $w);
				$this->optionalField('l', 'ea:work:Form', $idata, $w);
				$this->optionalField('m', 'ea:work:Language', $idata, $w);
				$this->optionalField('n', 'ea:work:Version', $idata, $w);
				$this->optionalField('q', 'ea:work:Version', $idata, $w);
				break;
			case "240": // work as subject me sigrafis
				$title = $rgtrim($f['a'][0]->getData());
				$this->europeana['marc-title'] = $this->field2String('240');
				$this->europeana['title'] = $title;
				$europeana_key2 = $title;
				$this->deleteWorkWithAuthorsKeys($idata);
				$idata->addValueSK(DataFields::dc_title, $title, null, null, null, null, null, null, $w++);
				$idata->addValueSK('ea:label:', $title, null, null, null, null, null, null, $w++);
				$this->optionalField('t', 'ea:work:title', $idata, $w);
				break;
			case "250": // concept
				$title = $rgtrim($f['a'][0]->getData());
				$this->europeana['marc-title'] = $this->field2String('250');
				$this->europeana['title'] = $title;
				$europeana_key2 = $title;
				$this->deleteConceptKeys($idata);
				$idata->addValueSK(DataFields::dc_title, $title, null, null, null, null, null, null, $w++);
				$idata->addValueSK('ea:label:', $title, null, null, null, null, null, null, $w++);
				break;
			case "280": // genre
				$title = $rgtrim($f['a'][0]->getData());
				$this->europeana['marc-title'] = $this->field2String('280');
				$this->europeana['title'] = $title;
				$europeana_key2 = $title;
				$this->deleteGenreKeys($idata);
				$idata->addValueSK(DataFields::dc_title, $title, null, null, null, null, null, null, $w++);
				$idata->addValueSK('ea:label:', $title, null, null, null, null, null, null, $w++);
				break;
			default:
				break; // invalid object type
		}

		$idata->addValueSK(DataFields::ea_marc_search_title, $this->rgtrimSearch($title), null, null, null, null, null, null, $w++);
		// gia ta ypoloipa koina pedia aneksarthtws object-type
		if ($this->isChain) {
			// an einai chain, tote edw eimaste akoma ston kriko 0 kai 8eloume mono to marc_id sta metadata
			// ta koina pedia 8eloume na mpoun sto kentriko subject-chain node, epomenws h processCommonTags() den kaleitai edw, alla mesa sthn createChainNode
			$idata->addValueSK_DBK(DataFields::ea_marc_id, $this->marc_id, null, null, null, null, null, null, $w++);
		} else {
			// an den einai chain tote 8eloume kai ta koina pedia
			$this->processCommonTags($idata, $w);
		}
		$idata->addValueSK_DBK(DataFields::ea_marc_import_date, $this->now->format('Y-m-d\TH:i:s\Z'), null, null, null, null, null, null, $w++);
		$idata->addValueSK_DBK(DataFields::ea_status, 'finish', null, null, null, null, null, null, $w++);

		
		// sthn periptwsh poy exoume chain: create OR syndesh me yparxon
		if ($this->isChain) { // ean einai chain kai yparxei hdh den to peirazoume
			$node_chain_id = $this->itemLocateByMetadataKey(self::$objectTypes[$this->basicTag], DataFields::dc_title, $title);
			$node_item_id = (empty($node_chain_id)) ? $node_item_id : $node_chain_id;
			// an to node_item_id einai hdh empty (dhladh o krikos 0 den yparxei hdh) tote 8a parameinei empty kai ginetai elegxos parakatw
		}

		$node_item_id = (empty($node_item_id))
				? $this->itemSave($idata, 2)
				: $this->itemUpdate($node_item_id, $idata, 2);
		$item_ids[] = $node_item_id;


		// apo do ke kato paragonte epipleon komvoi pou simetexoun se chain kai o chain komvos
		if ($this->isChain) {

			$chain_links = array();//ARRAY DEDOMENON KRIKON GIA NA SINDE8OUN ME ARC STON CHAIN KOMVO
			$chain_links[] = array(
				'id'      => $node_item_id,
				'title'   => $idata->getFirstItemValue(DataFields::dc_title)->textValue(),
				'ot'      => $idata->getFirstItemValue(DataFields::ea_obj_type)->textValue(),
				'primary' => true,
				'ord'     => 0,
			);
			$titlesArr = array();
			$titlesArr[] = $title;

			$this->createChainLinks($titlesArr, $chain_links, $item_ids);      // EDW FTIAXNONTAI OI KRIKOI TOY SUBJECT-CHAIN
			$chain_item_id = $this->createChainNode($titlesArr, $chain_links); // EDW FTIAXNETAI TO (kentriko) SUBJECT-CHAIN NODE
			$item_ids[] = $chain_item_id;
		}


		// EUROPEANA
		if (isset($this->europeana['ea:europeana:type'])) {  
			$euro_id = $this->createEuropeanaNode($europeana_key2, $idata); // EDO FTIAXNETAI O EUROPEANA KOMVOS GIA TON PRIMARY KOMVO POU ANTISTIXI STO MARC RECORD
			$item_ids[] = $euro_id;
		}

		
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
	public function processCommonTags(&$idata, &$w) {
		foreach (self::$otherTags as $oCode => $oKey) {
			$oFields = $this->record->getFields($oCode);
			if ($oFields) {
				foreach ($oFields as $oField) {
					if ($oField instanceof File_MARC_Control_Field) {
						$idata->addValueSK_DBK($oKey, $this->rgtrim($oField->getData()), null, null, null, null, null, null, $w++);
					} else { // if ($oField instanceof File_MARC_Data_Field) {
						if ($oCode == "801") {
							$a = ($oField->getSubfield('a')) ? $this->rgtrim($oField->getSubfield('a')->getData()) : '';
							$b = ($oField->getSubfield('b')) ? $this->rgtrim($oField->getSubfield('b')->getData()) : '';
							$c = ($oField->getSubfield('c')) ? $this->rgtrim($oField->getSubfield('c')->getData()) : '';
							$allValue = $a . " : " . $b . " : " . $c;
							$ind2 = $oField->getIndicator(2);
							if ($ind2 == 0 || $ind2 == "" || $ind2 == " " || empty(trim($ind2))) {
								$idata->addValueSK_DBK("ea:classification:CatalogingSource_Original", $allValue, null, null, null, null, null, null, $w++);
							} else if ($ind2 == 1 || $ind2 == 2 || $ind2 == 3) {
								$idata->addValueSK_DBK("ea:classification:CatalogingSource_Modifying", $allValue, null, null, null, null, null, null, $w++);
							}
						} else {
							foreach($oKey as $oSubCode => $oSubKey) {
								foreach($oField->getSubfields($oSubCode) as $oSubField) {
									$idata->addValueSK_DBK($oSubKey, $this->rgtrim($oSubField->getData()), null, null, null, null, null, null, $w++);
								}
							}
						}
					}
				}
			}
		}
	}


	public function setUserName($username) {
		$this->username = $username;
	}


	// PRIVATES


	private function init() {
		$this->dbh = prepareService();
		$basics = $this->record->getFields('2..', true);
		$nbasics = count($basics);

		// validation
		// invalid if no 2XX field exists
		// invalid if repeatable 2XX fields
		if ($nbasics <= 0 || $nbasics > 1) {
			return;
		}

		$this->basicField = $basics[0];
		$tag = $this->basicTag = $this->basicField->getTag();

		// validate tag
		// ATM 225 and 212 are not supported
		if ($tag == "225" || $tag == "212") {
			return;
		}

		$this->extractBasicSubfields();
		$sn = $this->basicSubsCnt;
		$this->now = new DateTime(null, new DateTimeZone('UTC'));

		switch($tag) {
			case "200":
				// invalid if no a
				// invalid if repeatable a or b or d or f or g
				if ($sn['a'] <= 0 || $sn['a'] > 1 || $sn['b'] > 1 || $sn['d'] > 1 || $sn['f'] > 1 || $sn['g'] > 1) {
					return;
				}
				break;
			case "210":
				// invalid if no a
				// invalid if repeatable a or d or e or f
				if ($sn['a'] <= 0 || $sn['a'] > 1 || $sn['d'] > 1 || $sn['e'] > 1 || $sn['f'] > 1) {
					return;
				}
				break;
			case "215":
				// invalid if no a
				// invalid if repeatable a
				if ($sn['a'] <= 0 || $sn['a'] > 1) {
					return;
				}
				break;
			case "220":
				// invalid if no a
				// invalid if repeatable a or f
				if ($sn['a'] <= 0 || $sn['a'] > 1 || $sn['f'] > 1) {
					return;
				}
				break;
			case "230":
				// invalid if no a
				// invalid if repeatable a or k or l or m or q
				if ($sn['a'] <= 0 || $sn['a'] > 1 || $sn['k'] > 1 || $sn['l'] > 1 || $sn['m'] > 1 || $sn['q'] > 1) {
					return;
				}
				break;
			case "240":
				// invalid if no a
				// invalid if repeatable a or t
				if ($sn['a'] <= 0 || $sn['a'] > 1 || $sn['t'] > 1) {
					return;
				}
				break;
			case "250":
				// invalid if no a
				// invalid if repeatable a
				if ($sn['a'] <= 0 || $sn['a'] > 1) {
					return;
				}
				break;
			case "280":
				// invalid if no a
				// invalid if repeatable a
				if ($sn['a'] <= 0 || $sn['a'] > 1) {
					return;
				}
				break;
			default:
				break;
		}

		$this->isValid = true;
		$this->detectChain();
	}


	/* @var $st PDOStatement */
	private function itemLocateByMetadataKey($obj_type, $element, $value) {
		$item_id = null;

		$st = $this->dbh->prepare("SELECT item_id FROM dsd.metadatavalue2 WHERE obj_type = ? AND element = ? AND text_value = ? ORDER BY item_id desc LIMIT 1");
		$st->bindParam(1, $obj_type);
		$st->bindParam(2, $element);
		$st->bindValue(3, trim($value));
		$st->execute();
		$result = $st->fetch();

		if (!empty($result)) {
			$item_id = $result[0];
		}

		return $item_id;
	}


	/**
	 *
	 * @return ArrayObject
	 */
	public function getNodeMemory() {
		return $this->nodeMemory;
	}


	private function executeSaveGraphEdge($item_id, $element, $ref_item, $text_value) {
		$ps1 = $this->dbh;
		$stmt = $ps1->prepareNamed($ps1::$SAVE_GRAPH_EDGE);
		$stmt->bindParam(1, $item_id);
		$stmt->bindParam(2, $element);
		$stmt->bindParam(3, $ref_item);
		$stmt->bindParam(4, $text_value);
		$stmt->execute();
		return $stmt;
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

		$this->saveGraphEdge($item_id, $refType);
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

		$this->saveGraphEdge($item_id, $refType);
		return $item_id;
	}


	private function saveGraphEdge($item_id, $refType) {
		if ($refType == 1) {
			$this->executeSaveGraphEdge($item_id, 'ea:marc-ref1:', $this->marc_item_id, $this->marc_id);
		} elseif ($refType == 2) {
			$this->executeSaveGraphEdge($item_id, 'ea:marc-ref2:', $this->marc_item_id, $this->marc_id);
		} elseif ($refType == 3) {
			$this->executeSaveGraphEdge($item_id, 'ea:marc-ref3:', $this->marc_item_id, $this->marc_id);
		} elseif ($refType == 4) {
			$this->executeSaveGraphEdge($item_id, 'ea:marc-ref4:', $this->marc_item_id, $this->marc_id);
		}
	}


	private function itemSaveOrLocateBySearch($idata, $obj_type, $title) {
		$this->nodeMemory[] = $idata;

		$is = new ItemSave();
		$is->setIdata($idata);
		if ($this->username !== null) {
			$is->setUserName($this->username);
		}

		// try to find item by searchable marc title special element
		$item_id = $this->itemLocateByMetadataKey($obj_type, DataFields::ea_marc_search_title, $this->rgtrimSearch($title));

		// if not found, try to match the title itself
		if (empty($item_id)) {
			$item_id = $this->itemLocateByMetadataKey($obj_type, DataFields::dc_title, $title);
			if (empty($item_id)) { // save a new item if not found
				$item_id = $is->insert_new_item_batch_simple();
				$this->executeSaveGraphEdge($item_id, 'ea:marc-ref3:', $this->marc_item_id, $this->marc_id);
			}
		}
		
		// do nothing (just return item_id) if it already exists
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


	private function detectChain() {
		for ($i = 0; $i < strlen(self::$chainCodes); $i++) {
			$code = self::$chainCodes[$i];
			if ($this->basicSubsCnt[$code] > 0) {
				$this->isChain = true;
				break;
			}
		}
	}


	private function optionalField($code, $key, &$idata, &$w, $options = array()) {
		if ($this->basicSubsCnt[$code]) {
			$data = $this->rgtrim($this->basicSubs[$code][0]->getData());
			if (isset($options['remove_chars'])){
				$remove_chars = $options['remove_chars'];
				foreach ($remove_chars as $c) {
					$data = str_replace($c, '', $data);
				}
			}
			$idata->addValueSK($key, $data, null, null, null, null, null, null, $w++);
		}
	}


	private function optionalChildField($marcField, $code, $key, &$idata, &$w, $link = null, $options = array()) {
		if ($marcField->getSubfield($code)) {
			$data = $this->rgtrim($marcField->getSubfield($code)->getData());
			if (isset($options['remove_chars'])){
				$remove_chars = $options['remove_chars'];
				foreach ($remove_chars as $c) {
					$data = str_replace($c, '', $data);
				}
			}
			$idata->addValueSK($key, $data, null, null, null, null, null, null, $w++, $link);
		}
	}


	private function repetitiveField($code, $key, &$idata, &$w) {
		if ($this->basicSubsCnt[$code]) {
			foreach($this->basicSubs[$code] as $field) {
				$data = $this->rgtrim($field->getData());
				$idata->addValueSK($key, $data, null, null, null, null, null, null, $w++);
			}
		}
	}


	private function repetitiveChildField($marcField, $code, $key, &$idata, &$w, $link = null) {
		if ($marcField->getSubfields($code)) {
			foreach($marcField->getSubfields($code) as $subfield) {
				$data = $this->rgtrim($subfield->getData());
				$idata->addValueSK($key, $data, null, null, null, null, null, null, $w++, $link);
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


	private function createChainNode($titles, $chain_links) {
		$chainObjType = "subject-chain";
		$chainTitle = implode(", ", $titles);

		$chain_item_id = $this->itemLocateByMetadataKey('subject-chain', DataFields::ea_marc_id, $this->marc_id);
		$chainIdata = (empty($chain_item_id))
				? new ItemMetadata()
				: PDao::get_item_metadata($chain_item_id);

		$w = 0;

		$chainIdata->addValueSK_DBK(DataFields::ea_marc_id, $this->marc_id, null, null, null, null, null, null, $w++);
		$chainIdata->addValueSK_DBK(DataFields::ea_obj_type, $chainObjType, null, null, null, null, null, null, $w++);
		$chainIdata->addValueSK_DBK(DataFields::dc_title, $chainTitle, null, null, null, null, null, null, $w++);
		$chainIdata->addValueSK_DBK('ea:label:', $chainTitle, null, null, null, null, null, null, $w++);
		$this->processCommonTags($chainIdata, $w);

		$otKeys = array(
			'auth-concept' => 'ea:subj:concept',
			'auth-event' => 'ea:subj:event',
			'auth-genre' => 'ea:subj:form',
			'auth-general' => 'ea:subj:general',
			'auth-object' => 'ea:subj:object' ,
			'auth-person' => 'ea:subj:person',
			'auth-organization' => 'ea:subj:person',
			'auth-family' => 'ea:subj:person',
			'auth-place' => 'ea:subj:place',
			'auth-work' => 'ea:subj:work'
		);

		foreach ( $otKeys as $v) {
			$chainIdata->deleteByKey($v);
			$chainIdata->deleteByKey($v . ':primary');
		}

		foreach ($chain_links as $chain_link) {
			$ord = $chain_link['ord'];
			$ot = $chain_link['ot'];
			$ctitle = $chain_link['title'];
			$link_id = $chain_link['id'];
			$subjkey = $otKeys[$ot];
			if (!empty($subjkey)) {
				if ($ord == 0) {
					$subjkey .= ':primary';
				}
				$chainIdata->addValueSK($subjkey, $ctitle, null, null, null, $link_id, null, null, $w++);
			} else {
				echo "ERROR: UNKNOWN $ot as subject type";
			}
		}

		$chainIdata->addValueSK_DBK(DataFields::ea_marc_import_date, $this->now->format('Y-m-d\TH:i:s\Z'), null, null, null, null, null, null, $w++);
		$chainIdata->addValueSK_DBK(DataFields::ea_status, 'finish', null, null, null, null, null, null, $w++);

		if (empty($chain_item_id)) {
			$chain_item_id = $this->itemSave($chainIdata, 2);
		} else {
			$this->itemUpdate($chain_item_id, $chainIdata, 2);
		}

		return $chain_item_id;
	}


	private function createChainLinks(&$titlesArr, &$chain_links, &$item_ids) {
		$f = $this->basicSubs;
		$sn = $this->basicSubsCnt;

		for ($i = 0; $i < strlen(self::$chainCodes); $i++) { // EDW FTIAXNONTAI OI KRIKOI TOY CHAIN
			$code = self::$chainCodes[$i];
			$ringOT = self::$objectTypes[$code];
			if ($sn[$code] > 0) {
				$c = 1;
				foreach($f[$code] as $subfield) {

					$chain_link_title = $this->rgtrim($subfield->getData()); // titlos
					$idata = new ItemMetadata();
					$w = 0;
					$idata->addValueSK(DataFields::ea_marc_id, $this->marc_id, null, null, null, null, null, null, $w++);
					$idata->addValueSK(DataFields::ea_obj_type, $ringOT, null, null, null, null, null, null, $w++);
					$idata->addValueSK(DataFields::dc_title, $chain_link_title, null, null, null, null, null, null, $w++);
					$idata->addValueSK('ea:label:', $chain_link_title, null, null, null, null, null, null, $w++);
					$idata->addValueSK(DataFields::ea_marc_import_date, $this->now->format('Y-m-d\TH:i:s\Z'), null, null, null, null, null, null, $w++);
					$idata->addValueSK(DataFields::ea_status, 'finish', null, null, null, null, null, null, $w++);

					$chain_link_id = $this->itemSaveOrLocateBySearch($idata, $ringOT, $chain_link_title);
					$primary = false;
					$chain_links[] = array(
						'id'      => $chain_link_id,
						'title'   => $chain_link_title,
						'ot'      => $ringOT,
						'primary' => $primary,
						'ord'     => $c,
					);
					$item_ids[] = $chain_link_id;
					$titlesArr[] = $chain_link_title;
					$c+=1;
				}
			}
		}
	}


	private function createEuropeanaNode($europeana_key2, &$idata) {
		// (edw apokleietai to $ot (tou krikou 0) na ine subject-chain, giati to xeirizomaste me eidiko tropo sthn createChainNode())
		$this->europeana['ea:europeana:key1'] = $this->marc_id;
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
		$euro_id = $this->itemLocateByMetadataKey($eType, 'ea:europeana:key1', $this->europeana['ea:europeana:key1']);
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
		$eIdata->addValueSK_DBK('ea:europeana:key1', $this->europeana['ea:europeana:key1'], null, null, null, null, null, null, $w++);
		$eIdata->addValueSK_DBK('ea:europeana:key2', $this->europeana['ea:europeana:key2'], null, null, null, null, null, null, $w++);
		$eIdata->addValueSK_DBK(DataFields::ea_status, 'finish', null, null, null, null, null, null, $w++);
		unset($this->europeana['marc-title']);
		unset($this->europeana['ea:europeana:type']);
		unset($this->europeana['ea:europeana:key1']);
		unset($this->europeana['ea:europeana:key2']);
		$europeanaItemValue = ItemValue::cKeyTextValue('ea:europeana:json-data', json_encode($this->europeana));
		$europeanaItemValue->setToJData('srlzn', 'json');
		$eIdata->addItemValueNK($europeanaItemValue);

		$euro_id = (empty($euro_id))
					? $this->itemSave($eIdata, 1)
					: $this->itemUpdate($euro_id, $eIdata, 1);

		return $euro_id;
	}


	/**
	 *
	 * @param ItemMetadata $idata (tou komvou oxi tou markimport)
	 */
	private function deletePersonKeys(&$idata) {
		$altNameC = false;
		$altNameD = false;
		$altNameF = false;
		$altNameG = false;
		$altInd2 = false;

		$altNameFields = $this->record->getFields("400");
		if ($altNameFields) {

			$idata->deleteByKey('ea:relation:PersonAlterOther_alter');

			foreach ($altNameFields as $altNameField) {

				if ($altNameField->getSubfields('c')) {
					$altNameC = true;
				}

				if ($altNameField->getSubfields('c')) {
					$altNameD = true;
				}

				if ($altNameField->getSubfields('c')) {
					$altNameF = true;
				}

				if ($altNameField->getSubfields('c')) {
					$altNameG = true;
				}

				$altNameInd2 = $altNameField->getIndicator(2);
				if ($altNameInd2 == 0 || $altNameInd2 == 1) {
					$altInd2 = true;
				}
			}
		}

		$idata->deleteByKey(DataFields::dc_title);
		$idata->deleteByKey('ea:label:');

		if ($this->basicSubsCnt['c'] || $altNameC) {
			$idata->deleteByKey('ea:auth:Person_TitlesAssociated');
		}

		if ($this->basicSubsCnt['d'] || $altNameD) {
			$idata->deleteByKey('ea:auth:Person_Numeration');
		}

		if ($this->basicSubsCnt['f'] || $altNameF) {
			$idata->deleteByKey('ea:auth:Person_DatesAssociated');
		}

		if ($this->basicSubsCnt['g'] || $altNameG) {
			$idata->deleteByKey('ea:auth:Person_FullerName');
		}

		$ind2 = $this->basicField->getIndicator(2);
		if ($ind2 == 0 || $ind2 == 1 || $altInd2) {
			$idata->deleteByKey('ea:auth:Person_Ind1');
		}
		
		$genderField = $this->record->getField("120");
		if ($genderField) {
			$idata->deleteByKey('ea:authPerson:Gender_Name');
		}
	}


	/**
	 *
	 * @param ItemMetadata $idata (tou komvou oxi tou markimport)
	 */
	private function deleteOrganizationKeys(&$idata) {
		$idata->deleteByKey(DataFields::dc_title);
		$idata->deleteByKey('ea:label:');

		if ($this->basicSubsCnt['b']) {
			$idata->deleteByKey('ea:auth:Organization_Subdivision');
		}

		if ($this->basicSubsCnt['c']) {
			$idata->deleteByKey('ea:auth:Organization_Addition');
		}

		if ($this->basicSubsCnt['d']) {
			$idata->deleteByKey('ea:auth:Organization_Number');
		}

		if ($this->basicSubsCnt['e']) {
			$idata->deleteByKey('ea:auth:Organization_Location');
		}

		if ($this->basicSubsCnt['f']) {
			$idata->deleteByKey('ea:auth:Organization_Date');
		}

		$ind1 = $this->basicField->getIndicator(1);
		$ind2 = $this->basicField->getIndicator(2);
		if ( ($ind1 == 0 || $ind1 == 1) && ($ind2 >= 0 && $ind2 <= 2) ) {
			$idata->deleteByKey('ea:auth:Organization_Ind1');
		}
	}


	/**
	 *
	 * @param ItemMetadata $idata (tou komvou oxi tou markimport)
	 */
	private function deletePlaceKeys(&$idata) {
		$idata->deleteByKey(DataFields::dc_title);
		$idata->deleteByKey('ea:label:');
	}


	/**
	 *
	 * @param ItemMetadata $idata (tou komvou oxi tou markimport)
	 */
	private function deleteFamilyKeys(&$idata) {
		$idata->deleteByKey(DataFields::dc_title);
		$idata->deleteByKey('ea:label:');

		if ($this->basicSubsCnt['f']) {
			$idata->deleteByKey('ea:auth:Family_DatesAssociated');
		}
	}


	/**
	 *
	 * @param ItemMetadata $idata (tou komvou oxi tou markimport)
	 */
	private function deleteWorkNoAuthorsKeys(&$idata) {
		$idata->deleteByKey(DataFields::dc_title);
		$idata->deleteByKey('ea:label:');

		if ($this->basicSubsCnt['b']) {
			$idata->deleteByKey('ea:work:Version');
		}

		if ($this->basicSubsCnt['h']) {
			$idata->deleteByKey('ea:work:Title_PartNumber');
		}

		if ($this->basicSubsCnt['i']) {
			$idata->deleteByKey('ea:work:Title_PartName');
		}

		if ($this->basicSubsCnt['k']) {
			$idata->deleteByKey('ea:work:Date');
		}

		if ($this->basicSubsCnt['l']) {
			$idata->deleteByKey('ea:work:Form');
		}

		if ($this->basicSubsCnt['m']) {
			$idata->deleteByKey('ea:work:Language');
		}

		if ($this->basicSubsCnt['n'] || $this->basicSubsCnt['q']) {
			$idata->deleteByKey('ea:work:Version');
		}
	}


	/**
	 *
	 * @param ItemMetadata $idata (tou komvou oxi tou markimport)
	 */
	private function deleteWorkWithAuthorsKeys(&$idata) {
		$idata->deleteByKey(DataFields::dc_title);
		$idata->deleteByKey('ea:label:');

		if ($this->basicSubsCnt['t']) {
			$idata->deleteByKey('ea:work:title');
		}
	}


	/**
	 *
	 * @param ItemMetadata $idata (tou komvou oxi tou markimport)
	 */
	private function deleteConceptKeys(&$idata) {
		$idata->deleteByKey(DataFields::dc_title);
		$idata->deleteByKey('ea:label:');
	}

	
	/**
	 *
	 * @param ItemMetadata $idata (tou komvou oxi tou markimport)
	 */
	private function deleteGenreKeys(&$idata) {
		$idata->deleteByKey(DataFields::dc_title);
		$idata->deleteByKey('ea:label:');
	}


	private function rgtrim($str) {
		return preg_replace('/\s{2,}/', ' ', trim($str));
	}


	private function rgtrimSearch($str) {
		return preg_replace('/\s+/', '', mb_strtolower(trim($str)));
	}


	private function debug() {
		// debug
		echo '<pre>';
		echo $this->record;
		echo "\n";
		if ($this->isChain) {
			echo "isChain";
		} else {
			echo "is Not Chain";
		}
		echo "\n";
		echo '</pre>';
		// end debug
	}


}