<?php

use Rhumsaa\Uuid\Uuid;

class GURN {

	//urn:<NID>:<NSS>
	//<NID> is the namespace identifier, which determines the syntactic interpretation of
	//<NSS>, the namespace-specific string.

	const TEMPORAL_TYPE_OLD = 'OLD';
	const TEMPORAL_TYPE_TMP = 'TMP';
	const TEMPORAL_TYPE_NEW = 'NEW';

	const FORM_ID = 'ID';
	const FORM_UUID = 'UUID';
	const FORM_VKEY = 'VKEY';


	private $nid; //namespace
	private $nss; //identifier
	private $urn;
	private $temporalType;
	private $form;

	public function __construct($temporalType, $form, $nid,$nss) {
		$this->temporalType = $temporalType;
		$this->form = $form;
		$this->nid = $nid;
		$this->nss = $nss;
		$this->urn = $nid . ':' . $nss;
	}

	public function nid(){
		return $this->nid;
	}
	public function nss(){
		return $this->nss;
	}

	public function temporalType(){
		return $this->temporalType;
	}
	public function form(){
		return $this->form;
	}

	public function urn(){
		return $this->urn;
	}

	public function instanceClass(){
		return $this->instanceClass;
	}


	public function toString($withUrnPrefix = false){
		if ($withUrnPrefix){
			return 'urn:' . $this->urn;
		}
		return $this->urn;
	}

	public function __toString(){
		return $this->toString(false);
	}


	// 	private static function appendInstanceType($nid,$instanceType = null){
	// 		if ($instanceType == GVertex::OBJECT_TYPE){
	// 			$nid .= ':v';
	// 		} elseif ($instanceType == GEdge::OBJECT_TYPE){
	// 			$nid .= ':e';
	// 		}
	// 		return $nid;
	// 	}
	/**
	* @return GURN
	*/
	public static  function createOLDWithId($id){
		return new GURN(GURN::TEMPORAL_TYPE_OLD,GURN::FORM_ID, 'oi', $id);
	}
	/**
	 * @return GURN
	 */
	public static  function createOLDWithUUID($id){
		return new GURN(GURN::TEMPORAL_TYPE_OLD,GURN::FORM_UUID, 'ou', $id);
	}

	/**
	 * @return GURN
	 */
	public static  function createOLDWithVKEY($id){
		return new GURN(GURN::TEMPORAL_TYPE_OLD,GURN::FORM_VKEY, 'ov', $id);
	}
	/**
	 * @return GURN
	 */
	public static  function createNEWWithVKEY($id){
		return new GURN(GURN::TEMPORAL_TYPE_NEW,GURN::FORM_VKEY, 'nv', $id);
	}
	/**
	 * @return GURN
	 */
	public static  function createTMPWithVKEY($vkey){
		return new GURN(GURN::TEMPORAL_TYPE_TMP,GURN::FORM_VKEY, 'tv', $vkey);
	}

// 	public static function createVKEY($element,$fromUrnStr, $toUrnStr){
// 		return ('‡' . $element . '‡' . $fromUrnStr . '‡' . $toUrnStr);
// 	}
/**
 *	parentTreeId xriazete gia edges pou den 3ekinane katef8ian apo root property tou komvou ala apo filo
 *
 * @param string $element
 * @param string $fromUrnStr
 * @param string $toUrnStr
 * @param integer $parentTreeId
 */
	public static function createVKEY($element,$fromUrnStr, $toUrnStr,$parentTreeId = null){
		return ('‡' . $element . '‡' . $fromUrnStr . '‡' . $toUrnStr . '‡' . $parentTreeId);
	}


	/**
	 * @return GURN
	 */
	public static  function createNew($id){

		if (empty($id)){
			return new GURN(GURN::TEMPORAL_TYPE_NEW,GURN::FORM_UUID, 'nu', Uuid::uuid1());
		}
		return new GURN(GURN::TEMPORAL_TYPE_NEW,GURN::FORM_ID,'ni', $id);
	}

	/**
	 * @return GURN
	 */
	public static  function createTmp($id){
		if (empty($id)){
			return new GURN(GURN::TYPE_TMP,GURN::FORM_UUID,'tu', Uuid::uuid1());
		}
		return new GURN(GURN::TYPE_TMP,GURN::FORM_ID,'ti', $id);
	}


}

