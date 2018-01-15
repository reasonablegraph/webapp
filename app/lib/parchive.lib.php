<?php
class PConstants {



	const NBSP = '&#160;';
	const COPYRIGHT = '&#169;';
	const EURO = '&#8364;';


}

class PgFtsConstants {

	const COLUMN_STUFF = 'fts';
	const COLUMN_OPAC = 'fts2';


	const COLUMN_SUBJECT     = 'prop_fts[1]';
	const COLUMN_CONTRIBUTOR = 'prop_fts[2]';
	const COLUMN_ISBN        = 'prop_fts[3]';
	const COLUMN_LABEL       = 'prop_fts[4]';


}

class PgPropIntConstants {

	const COLUMN_SUBJECT_COUNT = 'prop_int[1]';
}


class DataFields
{
	const Sunday = 0;
	const Monday = 1;

	const dc_identifier_uri = 'dc:identifier:uri';
	const ea_identifier_id = 'ea:identifier:id';
	const dc_title = 'dc:title:';
	const dc_date_available = 'dc:date:available';
	const dc_date_accessioned = 'dc:date:accessioned';
	const dc_language_iso = "dc:language:iso";
	const dc_contributor_author = "dc:contributor:author";
	const dc_identifier_isbn = "dc:identifier:isbn";
	const dc_descrption = "dc:description:";
	const dc_publisher = "dc:publisher:";
	const dc_subject = "dc:subject:";

	const ea_uuid = 'ea:uuid:';
	const ea_type = 'ea:type:';
	const ea_subtitle = 'ea:subtitle:';
	const ea_date = 'ea:date:';
	const ea_date_orgissued = 'ea:date:orgissued';
	const ea_size = 'ea:size:';
	const ea_place = 'ea:place:';
	const ea_desc = 'ea:desc:';
	const ea_abstract = 'ea:abstract:';
	const ea_year = 'ea:year:';
	const ea_author = 'ea:author:';
	const ea_pages = 'ea:pages:';
	const ea_related_urls = 'ea:related_urls:';
	//const ea_origin_url = 'ea:origin_url';
	const ea_origin_url = 'ea:url:origin';
	const ea_url_related = 'ea:url:related';
	const ea_thumbs_small = 'ea:thumbs:small';
	const ea_thumbs_big = 'ea:thumbs:big';
	const ea_bitstreams = 'ea:bitstreams';
	const ea_tags = 'tags';
	const ea_obj_type = "ea:obj-type:";
	const ea_status = "ea:status:";
	const ea_status_comment = "ea:status:comment";
	const ea_edoc_pages = 'ea:edoc:Pages';
	const ea_size_pages = 'ea:size:pages';
	const ea_description_abstract = 'dc:description:abstract';
	const ea_oring_comment = 'ea:origin:comment';
	const ea_title_uniform = 'ea:title:uniform';
	const ea_expres_Language = 'ea:expres:Language';
	const ea_auth_NotePublic = 'ea:auth:NotePublic';
	const ea_form_type = 'ea:form-type:';
	const ea_has_issue = 'ea:hasIssue:';
	const ea_key1 = 'ea:key1:';
	const ea_key2 = 'ea:key2:';
	const ea_key3 = 'ea:key3:';
	const ea_key4 = 'ea:key4:';
	//



	//den iparxoun ston public.metadatafieldregistry
	// const trn_item_id = 'trn:item_id';
	// const trn_uuid = 'trn:uuid';
	// const trn_id = 'trn:id';

	//ARTIFACT RELATED
	const ea_sn = 'ea:sn:';
	const ea_sn_prefix = 'ea:sn:prefix';
	const ea_sn_suffix = 'ea:sn:suffix';
	const ea_call_number_ea = 'ea:call_number:ea';

	const ea_call_number_prefix = 'ea:call_number:prefix';
	const ea_call_number_main = 'ea:call_number:main';
	const ea_call_number_suffix = 'ea:call_number:suffix';

	const ea_call_number_lcc = 'ea:call_number:LCC';
	const ea_call_number_ddc = 'ea:call_number:DDC';
	const ea_call_number_cc = 'ea:call_number:CC';
	const ea_material_type = 'ea:material:type';
	const ea_artifact_location = 'ea:artifact:location';
	const ea_artifact_of = 'ea:artifact-of:';
	const ea_owner = 'ea:owner';

	// MARC RELATED
	const ea_marc_id = 'ea:marc:id';
	const ea_marc_provider = 'ea:marc:provider';
	const ea_marc_record_xml = 'ea:marc:record:xml';
	const ea_marc_record_xml_md5 = 'ea:marc:record:xmlmd5';
	const ea_marc_urn = 'ea:marc:urn';
	const ea_marcref1 = 'ea:marc-ref1:';
	const ea_marc_import_date = 'ea:marc:import:date';
	const ea_marc_import_uuid = 'ea:marc:import:uuid';
	const ea_marc_last_update = 'ea:marc:last:update';
	const ea_marc_search_title = 'ea:marc:search:title';
	const ea_source_filename = 'ea:source:filename';


	// PERIODIC RELATED
	const ea_periodic_frequency = 'ea:periodic:frequency';
	const ea_periodic_ISSN_Number = 'ea:periodic:ISSN_Number';
	const ea_periodic_publication_place = 'ea:periodic:publication_place';
	const ea_periodic_publisher_name = 'ea:periodic:publisher_name';
	const ea_periodic_category = 'ea:periodic:category';



	// MAINIFESTATION RELATED
	const ea_manif_Title_Remainder = 'ea:manif:Title_Remainder';
	const ea_manif_Title_PartNumber = 'ea:manif:Title_PartNumber';
	const ea_manif_Title_PartName = 'ea:manif:Title_PartName';
	const ea_manif_Series_Title = 'ea:manif:Series_Title';
	const ea_manif_subscription = 'ea:manif:subscription';
	const ea_manif_pages = 'ea:manif:pages';
	const ea_manif_Edition_Statement = 'ea:manif:Edition_Statement';
	const ea_manif_Edition_Remainder = 'ea:manif:Edition_Remainder';
	const ea_manif_ISBN_Number = 'ea:manif:ISBN_Number';
	const ea_manif_ISBN_Terms = 'ea:manif:ISBN_Terms';
	const ea_manif_ISBN_Qualify = 'ea:manif:ISBN_Qualify';
	const ea_manif_active = 'ea:manif:active';
	const ea_manif_approved = 'ea:manif:approved';
	const ea_manif_Publication_Date = 'ea:manif:Publication_Date';
	const ea_manif_Book_Type = 'ea:manif:Book_Type';
	const ea_manif_Title_Responsibility = 'ea:manif:Title_Responsibility';
	const ea_manif_Publication_Place = 'ea:manif:Publication_Place';
	const ea_manif_Publisher_Name = 'ea:manif:Publisher_Name';
	const ea_manif_subjectCategory = 'ea:manif:subjectCategory';
	const ea_manif_Publication = 'ea:manif:Publication';
	const ea_manif_Physical_Description = 'ea:manif:Physical_Description';
	const ea_manif_Physical_Description_Details = 'ea:manif:Physical_Description_Details';
	const ea_manif_Physical_Description_Dimensions = 'ea:manif:Physical_Description_Dimensions';
	const ea_manif_Physical_Description_Accompanying = 'ea:manif:Physical_Description_Accompanying';



	// WORK RELATED
	const ea_work = 'ea:work:';
	const ea_workof = 'ea:workOf:';
	const ea_work_subjectCategory = 'ea:work:subjectCategory';
	const ea_work_authorWork = 'ea:work:authorWork';



	// ITEM RELATED
	const ea_item_ownerItem = 'ea:item:ownerItem';
	const ea_item_location = 'ea:item:location';
	const ea_item_sublocation = 'ea:item:sublocation';
	const ea_item_acquisitionDate = 'ea:item:acquisitionDate';
	const ea_item_type = 'ea:item:type';
	const ea_item_barcode = 'ea:item:barcode';
	const ea_item_copyNumber = 'ea:item:copyNumber';
	const ea_item_classification = 'ea:item:Classification';



	// CONCEPT RELATED
	const ea_concept_category_child = 'ea:concept:category_child';
	const ea_concept_category_parent = 'ea:concept:category_parent';



	const url_related = 'url_related';
	const url_origin = 'url_origin';
	const tag_name = 'name';
	const tag_url = 'url';
	const thumb_index = 'index';
	const thumb_url = 'url';


	const ITEM_STATUS_FINISH  = 'finish';
	const ITEM_STATUS_INCOMPLETE = 'incomplete';
	const ITEM_STATUS_PENDING = 'pending';
	const ITEM_STATUS_PRIVATE = 'private';
	const ITEM_STATUS_ERROR = 'error';
	const ITEM_STATUS_INTERNAL = 'internal';
	const ITEM_STATUS_HIDDEN = 'hidden';

	const DB_OBJ_TYPE_WEBSITE = 'web-site';
	const DB_OBJ_TYPE_WEBSITE_INSTANCE = 'web-site-instance';
	const DB_OBJ_TYPE_SILOGI = 'silogi';
	const DB_OBJ_TYPE_PERSON = 'actor';
	const DB_OBJ_TYPE_BITSTREAM = 'bitstream';
	const DB_OBJ_TYPE_ARTICLE = 'article';
	const DB_OBJ_TYPE_NOTE = 'note';


	const DB_MEDIA_THUMB_TYPE_MAX = 5;
	const DB_MEDIA_THUMB_TYPE_SMALL = 1;
	const DB_MEDIA_THUMB_TYPE_BIG = 2;
	const DB_MEDIA_THUMB_TYPE_ICON_SMALL = 3;
	const DB_MEDIA_THUMB_TYPE_ICON_BIG = 4;
	const DB_MEDIA_THUMB_TYPE_CUSTOM = 10;

	////

	const DB_item_realtion_type_member_of  = 'member_of';
	const DB_item_realtion_type_member_of_id  = '2';
	//const = '';
	//const = '';


	const DB_content_ctype_article = 1;
	const DB_content_ctype_note =  2;

	const DB_visibility_public = 1;
	const DB_visibility_private = 2;
	const DB_visibility_hidden = 3;
	const DB_visibility_deleted = 20;



	// public static function  getContributorElements(){
		// $dbh = dbconnect();
		// $SQL = "select distinct element from dsd.element_contributors"; // where obj_class ='printed'
		// $stmt = $dbh->prepare($SQL);
		// $stmt->execute();
		// $rep = array();
		// if ($row = $stmt->fetch()){
			 	// $rep[] = $row[0];
		// }
		// return $rep;
		// // return array(
			// // 'dc:contributor:author',
			// // 'ea:contributor:responsible',
			// // 'ea:contributor:editor',
			// // 'ea:contributor:translator',
			// // 'dc:contributor:illustrator',
			// // 'dc:contributor:advisor' ,
			// // 'dc:contributor:other',
			// // 'dc:publisher:',
		// // );
	// }


}

class Lookup {


	/**
	 *  elements gia xrisi se item view
 	 *
	 * @param string $obj_class
	 * @return multitype:unknown
	 */
	public static function getItemContributors($obj_class='printed'){

	$dbh = dbconnect();
		$SQL = "select element,item_label from dsd.element_contributors where obj_class =? ORDER BY w";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1,$obj_class);
		$stmt->execute();
		$rep = array();
		while ($row = $stmt->fetch()){
				$rep[$row[0]]=$row[1];
		}
		return $rep;
	}

	/**
	 *  elements gia xrisi se item view
	 *
	 * @param string parent_element
	 * @return multitype:unknown
	 */
	public static function getRelationElementsWithParentItemLabelMap($parent_element){
		$SQL='SELECT element,item_label from dsd.item_relation_type where parent_element = ?  ORDER BY w';
		$dbh = dbconnect();
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1,$parent_element);
		$stmt->execute();
		$rep = array();
		while ($row = $stmt->fetch()){
				$rep[$row[0]]=$row[1];
		}
		return $rep;
	}
	/**
	 *  elements gia xrisi se item view
	 *
	 * @return multitype:unknown
	 */
	public static function getRelationElementsItemLabelMap(){
		$SQL='SELECT element,item_label from dsd.item_relation_type ORDER BY w';
		$dbh = dbconnect();
		$stmt = $dbh->prepare($SQL);
		$stmt->execute();
		$rep = array();
		while ($row = $stmt->fetch()){
			$rep[$row[0]]=$row[1];
		}
		return $rep;
	}


	public static function getRelationElementsWithParentMap(){
		$SQL='SELECT element,parent_element from dsd.item_relation_type where parent_element is not null ORDER BY parent_element,w';
		$dbh = dbconnect();
		$stmt = $dbh->prepare($SQL);
		$stmt->execute();
		$rep = array();
		while ($row = $stmt->fetch()){
			$e = $row[0];
			$pe = $row[1];
			if ( ! isset($rep[$pe])){
				$rep[$pe] = array($e);
			} else {
				$rep[$pe][] =$e;
			}

		}
		return $rep;
	}
	/**
	 *
	 * @param unknown $parent
	 * @return multitype:unknown
	 */
	public static function getRelationElementsWithParent($parent){
		$SQL='SELECT element from dsd.item_relation_type where parent_element = ? ORDER BY w';
		$dbh = dbconnect();
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1,$parent);
		$stmt->execute();
		$rep = array();
		while ($row = $stmt->fetch()){
			$rep[] = $row[0];
		}
		return $rep;
	}

	/**
	 *  elements gia xrisi se item data-entry form
	 *
	 * @param string $obj_class
	 * @return multitype:unknown
	 */
// 	public static function getContributors($obj_class='printed'){

// 	$dbh = dbconnect();
// 		$SQL = "select element,label from dsd.element_contributors where obj_class =? ORDER BY w";
// 		$stmt = $dbh->prepare($SQL);
// 		$stmt->bindParam(1,$obj_class);

// 		error_log('getContributo111');

// 		$stmt->execute();
// 		$rep = array();
// 		while ($row = $stmt->fetch()){
// 				$rep[$row[0]]=$row[1];
// 		}
// 		return $rep;
// 	}


	/**
	 *  elements gia xrisi se item data-entry form
	 *
	 * @param string $obj_class
	 * @return multitype:unknown
	 */
	public static function getContributors(){

		$dbh = dbconnect();
		//$SQL = "select element,label from dsd.element_contributors where obj_class =? ORDER BY w";
		$SQL = "SELECT element,label from dsd.item_relation_type where parent_element = 'dc:contributor:' AND cw is not null ORDER BY cw";
		$stmt = $dbh->prepare($SQL);
		//$stmt->bindParam(1,$obj_class);
		$stmt->execute();
		$rep = array();
		while ($row = $stmt->fetch()){
			$rep[$row[0]]=$row[1];
		}
		return $rep;
	}

	public static function get_bitstream_bundles(){
		$rep = array(
				'ORIGINAL' => 'ORIGINAL',
				'ALT' => 'ALT',
				'SRC' => 'SRC',
				'SAMPLE' => 'SAMPLE',
				'PRIVATE' => 'PRIVATE',
				'INTERNAL' => 'INTERNAL'
		);

		return $rep;
	}

	public static function get_bitstream_bundles_create_item(){
		$rep = array(
				'ORIGINAL_CI' => 'ORIGINAL (creates Item)',
				'ALT' => 'ALT',
				'SRC' => 'SRC',
				'SAMPLE' => 'SAMPLE',
				'PRIVATE' => 'PRIVATE',
				'INTERNAL' => 'INTERNAL'
		);

		return $rep;
	}

// 	public static function get_bitstream_bundles_digital_item(){
// 		$rep = array(
// 				'INTERNAL' => 'INTERNAL',
// 				'ORIGINAL' => 'ORIGINAL',
// 		);

// 		return $rep;
// 	}

	public static function get_bitstream_bundles_lock_internal(){
		$rep = array(
				'INTERNAL' => 'INTERNAL',
				'ORIGINAL' => 'ORIGINAL',
		);

		return $rep;
	}

	public static function get_content_bundles(){
		$rep = array(
				'ORIGINAL' => 'ORIGINAL',
				'ALT' => 'ALT',
				'SRC' => 'SRC',
				'SAMPLE' => 'SAMPLE',
				'PRIVATE' => 'PRIVATE',
				'INTERNAL' => 'INTERNAL',
		);
		return $rep;
	}

	public static function get_content_type_values(){
		$rep = array(
				1 => 'article',
				2 => 'note',
		);
		return $rep;
	}

	public static function get_content_src_type_values(){
		$rep = array(
				1 => 'xml-src',
				2 => 'text'
		);
		return $rep;
	}

	// public static function get_artifact_status_values(){
		// $rep = array(
				// 1 => 'available',
				// 2 => 'non-available'
		// );
		// return $rep;
	// }


	public static function get_media_type_values($include_custom = false){
		$rep = array(
				1 => 'small',
				2 => 'big',
				5 => 'max',
				3 => 'icon_small',
				4 => 'icon_big',
		);
		if ($include_custom){
			$rep[10] = 'custom';
		}
		return $rep;
	}
	public static function get_media_type_values_reverse($include_custom = false){
		$rep = array(
				'small' => 1,
				'big' => 2,
				'max' => 5,
				'icon_small' => 3,
				'icon_big' => 4
		);
		if ($include_custom){
			$rep['custom'] = '10';
		}
		return $rep;
	}

	public static function get_visibility_values($include_deleted = false){
		$rep = array(
				1 => 'public',
				2 => 'private',
//				5 => 'personal',
				3 => 'hidden',
		);
		if ($include_deleted){
			$rep[20] = 'deleted';
		}
		return $rep;
	}

	public static function get_item_statuses(){
		$rep = array(
				'finish'=>'finish',
				'pending'=>'pending',
				'incomplete'=>'incomplete',
				'error'=>'error',
				'direct_only'=>'direct_only',
				'private'=>'private',
				'hidden'=>'hidden',
		);
		return $rep;
	}

}


class PDrupal {


	public static function createContentNodel($title){

		$user_name = get_user_name();
		$uid = get_current_user_id();
		$node = new stdClass();
		$node->type = "parchive_content";
		$node->title = $title;
		$node->language = LANGUAGE_NONE;
		$node->uid = $uid;
		$node->promote = 0;
		$node->status = 0;

		node_object_prepare($node);
		$node = node_submit($node);
		node_save($node);
		$nid = $node->nid;
		return $nid;
		#echo("<pre>");
		#print_r($node);
		#echo("</pre>");
		#return;
	}


//$content =PDao::getContent($cid);
	public static function sync_drupal_node($content, $create_flag){

		$dbh = dbconnect();
		$pc = $content;
		$cid = $pc['content_id'];
		$nid = $pc['drupal_node'];


		if (empty($nid)){
			if (!$create_flag){
				return;
			} else {
				$tmp_title = 'content: ' . $cid;
				$nid = PDrupal::createContentNodel($tmp_title);
			}
		}
		$node = node_load($nid);
		if(empty($node)){
			echo("ERROR");
			return;
		}
		$item = $pc['item'];
		$citem = Pdao::getItem($item);

		$tags = $citem['keywords'];
		$node->parchive_tags[$node->language] = ARRAY();
		if (!empty($tags)){
			foreach ($tags as $k => $v){
				$node->parchive_tags[$node->language][$k]['value'] = $v;
				$node->parchive_tags[$node->language][$k]['safe_value'] = $v;
			}
		}

		$ptags = $citem['pkeywords'];
		$node->parchive_ptags[$node->language] = ARRAY();
		if (!empty($ptags)){
			foreach ($ptags as $k => $v){
				$node->parchive_ptags[$node->language][$k]['value'] = $v;
				$node->parchive_ptags[$node->language][$k]['safe_value'] = $v;
			}
		}
		$body_text = $pc['content'];
		$summary = $pc['content_summary'];
		if (empty($summary)){
			$summary = text_summary($body_text);
		}
		$node->body[$node->language][0]['value']   = $body_text;
		$node->body[$node->language][0]['summary'] = $summary;
		$node->body[$node->language][0]['format']  = 'php_code'; //'full_html';

		$CONTENT_TYPE_MAP = Lookup::get_content_type_values();
		$ctype = $CONTENT_TYPE_MAP[$pc['content_type']];
		$bundle = $pc['bundle_name'];


		$node->parchive_item_id[$node->language][0]['value']   = $item;
		$node->parchive_item_id[$node->language][0]['safe_value']   = $item;

		$node->parchive_ctype[$node->language][0]['value']   = $ctype;
		$node->parchive_ctype[$node->language][0]['safe_value']   = $ctype;

		$node->parchive_bundle[$node->language][0]['value']   = $bundle;
		$node->parchive_bundle[$node->language][0]['safe_value']   = $bundle;


		$node_path = $pc['node_path'];
		if (! empty($node_path)){
			$tmp = drupal_get_normal_path( $node_path);
			if (empty($tmp) || $tmp == $node_path){
				$node->path = array('alias' => $node_path);
			}
		}

		$promot_fp = $pc['promote_fp'];
		$node->promote = $promot_fp;

		$publish_user = $pc['publish_user'];
		if (! empty($publish_user)){
			$account = user_load_by_name($publish_user);
			if (! empty($account)){
				$node->uid = $account->uid;
			}
		}


		$publish_ts = strtotime($pc['publish_dt']);
		$node->created = $publish_ts;

		$dtitle = $pc['title'];
		if (empty($dtitle)){
			$dtitle = $pc['description'];
		}
		if (empty($dtitle)){
			$dtitle = $ctype . ':' . $item;
		}
		$node->title = $dtitle;
		if ($pc['visibility'] == DataFields::DB_visibility_public){
			$node->status = 1;
		} else {
			$node->status = 0;
		}

		//echo("############ SAVE DRUPAL NODE ############");
		node_save($node);
		$nid = $node->nid;
		return $nid;
	}




}



//@DocGroup(module="util", group="archive", comment="ArrayToXML")
class ArrayToXML
{
	/**
	 * The main function for converting to an XML document.
	 * Pass in a multi dimensional array and this recrusively loops through and builds up an XML document.
	 *
	 * @param array $data
	 * @param string $rootNodeName - what you want the root node to be - defaultsto data.
	 * @param SimpleXMLElement $xml - should only be used recursively
	 * @return string XML
	 */
	public static function toXML( $data, $rootNodeName = 'ResultSet', &$xml=null ) {

		// turn off compatibility mode as simple xml throws a wobbly if you don't.
		if ( ini_get('zend.ze1_compatibility_mode') == 1 ) ini_set ( 'zend.ze1_compatibility_mode', 0 );
		if ( is_null( $xml ) ) //$xml = simplexml_load_string( "" );
			$xml = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$rootNodeName />");

		// loop through the data passed in.
		foreach( $data as $key => $value ) {

			$numeric = false;

			// no numeric keys in our xml please!
			if ( is_numeric( $key ) ) {
				$numeric = 1;
				$key = $rootNodeName;
			}

			// delete any char not allowed in XML element names
			$key = preg_replace('/[^a-z0-9\-\_\.\:]/i', '', $key);

			// if there is another array found recrusively call this function
			if ( is_array( $value ) ) {
				$node = ArrayToXML::isAssoc( $value ) || $numeric ? $xml->addChild( $key ) : $xml;

				// recrusive call.
				if ( $numeric ) $key = 'anon';
				ArrayToXML::toXml( $value, $key, $node );
			} else {

				// add single node.
				$value = htmlentities( $value );
				$xml->addChild( $key, $value );
			}
		}

		// pass back as XML
		return $xml->asXML();

		// if you want the XML to be formatted, use the below instead to return the XML
		//$doc = new DOMDocument('1.0');
		//$doc->preserveWhiteSpace = false;
		//$doc->loadXML( $xml->asXML() );
		//$doc->formatOutput = true;
		//return $doc->saveXML();
	}


	/**
	 * Convert an XML document to a multi dimensional array
	 * Pass in an XML document (or SimpleXMLElement object) and this recrusively loops through and builds a representative array
	 *
	 * @param string $xml - XML document - can optionally be a SimpleXMLElement object
	 * @return array ARRAY
	 */
	public static function toArray( $xml ) {
		if (empty($xml)){
			return array();
		}
		if ( is_string( $xml ) ) $xml = new SimpleXMLElement( $xml );
		$children = $xml->children();
		if ( !$children ) return (string) $xml;
		$arr = array();
		foreach ( $children as $key => $node ) {
			$node = ArrayToXML::toArray( $node );

			// support for 'anon' non-associative arrays
			if ( $key == 'anon' ) $key = count( $arr );

			// if the node is already set, put it into an array
			if ( isset( $arr[$key] ) ) {
				if ( !is_array( $arr[$key] ) || $arr[$key][0] == null ) $arr[$key] = array( $arr[$key] );
				$arr[$key][] = $node;
			} else {
				$arr[$key] = $node;
			}
		}
		return $arr;
	}

	// determine if a variable is an associative array
	public static function isAssoc( $array ) {
		return (is_array($array) && 0 !== count(array_diff_key($array, array_keys(array_keys($array)))));
	}
}




class PContentCompiler {

	const COMPILE_MODE_DRUPAL = 1;
	const COMPILE_MODE_HTML = 2;

	public $content = null;
	public $summary = null;

	public $messages = ARRAY();
	public $errors = ARRAY();


	public $parent_item = null;
	public $content_item = null;
	public $compile_mode = null;



  ## ATRIBUTES
	private function handle_attribute_item($l){
		$oitem = null;
		$item="content";
		if (preg_match('/item="([\d\w]+)"/',$l,$m)){
			$item = $m[1];
		}
		if ($item == 'parent'){
			$oitem = $this->parent_item;
		} elseif ($item == 'content'){
			$oitem = $this->content_item;
		} else {
			$item = PUtil::extract_int($item);
			if (! empty($item)){
				$oitem = PDao::getItem($item);
			} else {
				$oitem = null;
			}
		}
		return $oitem;
	}

	##TAGS
	private function handle_img($l){
		$index = 1;
		if (preg_match('/index="(\d+)"/',$l,$m)){
			$index = $m[1];
		}
		$item="content";
		if (preg_match('/item="([\d\w]+)"/',$l,$m)){
			$item = $m[1];
		}
		$size=1;
		if (preg_match('/size="(\d+)"/',$l,$m)){
			$size = $m[1];
		}
		$class="";
		if (preg_match('/class="([\d\w\s\-_]+)"/',$l,$m)){
			$class = $m[1];
		}
		$id="";
		if (preg_match('/id="([\d\w\s\-_]+)"/',$l,$m)){
			$id = $m[1];
		}
		$alt="";
		if (preg_match('/alt="([\d\w\s\-_]+)"/',$l,$m)){
			$alt = $m[1];
		}

		$oitem = $this->handle_attribute_item($l);
		if (empty($oitem)){
			return "";
		}
		$item = $oitem['id'];


		$thumb = null;
		$bitstreams = $oitem['bitstreams'];
		$i = 0;
		foreach ($bitstreams as $b){
			$i +=1;
			if ($i == $index){
				$bitem = PDao::getItem($b['item']);
				if ($size == 1){
					$thumb = $bitem['thumb'];
				}elseif ($size == 2){
					$thumb = $bitem['thumb1'];
				}elseif ($size == 3){
					$thumb = $bitem['thumbs_small'][0];
				}elseif ($size == 4){
					$thumb = $bitem['thumbs_big'][0];
				}
			}
		}

		if (! empty($id)){
			$id = sprintf('id="%s"',$id);
		}
		if (! empty($class)){
			$class = sprintf('class="%s"',$class);
		}
		if (! empty($alt)){
			$alt = sprintf('id="%s"',$alt);
		}

		$out = sprintf('<img %s %s %s src="/media/%s"/>',$id,$class,$alt, $thumb);
		return $out;
	}


	private function handle_tags($l){
		$oitem = $this->handle_attribute_item($l);
		if (empty($oitem)){
			return "";
		}


		$out = "\n";
		if ($this->compile_mode == PContentCompiler::COMPILE_MODE_HTML){
			$out .="<style>";
			$out .='
			div.field-name-field-tags h3.field-label {
				font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
				color: #3B3B3B;
				float: left;
				font-weight: normal;
				margin: 0;
				padding-right: 5px;
				font-size: 0.8em;
			}
			div.field-name-field-tags ul.links.inline {
				padding: 0;
				margin: 0;
				list-style: none;
				display: inline;
				font-size:0.8em;
				color: #68696B;
				line-height: 1.2;
			}
			div.field-name-field-tags ul.links.inline li {
				float: left;
				padding: 0 1em 0 0;
				white-space: nowrap;
				display: inline;
				list-style-type: none;
			}
			div.field-name-field-tags ul.links.inline li a {
				text-decoration: none;
				color: #0072B6;
				white-space: nowrap;
				font-size: 0.8em;
			}
			';
			$out .="</style>";
			$out .="\n";
		}
		$out .= '<div class="field field-name-field-tags field-type-taxonomy-term-reference field-label-inline clearfix">';
		$out .= '<h3 class="field-label">Tags: </h3>';
		$out .= '<ul class="links inline">';
		$out .="\n";
		$tags = $oitem['keywords'];
		foreach ($tags as $k => $v){
			$out .= sprintf('<li class="taxonomy-term-reference-%s" rel="dc:subject">',$k);
			$out .= sprintf('<a href="/archive/term/%s" typeof="skos:Concept" property="rdfs:label skos:prefLabel">%s</a></li>',urlencode($v),htmlspecialchars($v));
			$out .="\n";
		}


		$out .= '</ul>';
		$out .="\n";
		$out .= '</div>';
		$out .="\n";
		return $out;
	}



	private function handle_colorboximg($l){
		$index = 1;
		if (preg_match('/index="(\d+)"/',$l,$m)){
			$index = $m[1];
		}
		$item="content";
		if (preg_match('/item="([\d\w]+)"/',$l,$m)){
			$item = $m[1];
		}
		$size=1;
		if (preg_match('/size="(\d+)"/',$l,$m)){
			$size = $m[1];
		}
		$class="";
		if (preg_match('/class="([\d\w\s\-_]+)"/',$l,$m)){
			$class = $m[1];
		}
		$id="";
		if (preg_match('/id="([\d\w\s\-_]+)"/',$l,$m)){
			$id = $m[1];
		}
		$alt="";
		if (preg_match('/alt="([\d\w\s\-_]+)"/',$l,$m)){
			$alt = $m[1];
		}
		$rel="";
		if (preg_match('/rel="([\d\w\s\-_]+)"/',$l,$m)){
			$rel = $m[1];
		}



		$oitem = $this->handle_attribute_item($l);
		if (empty($oitem)){
			return "";
		}
		$item = $oitem['id'];


		$url = "#";
		$thumb = null;
		$bitstreams = $oitem['bitstreams'];
		$i = 0;
		foreach ($bitstreams as $b){
			$i +=1;
			if ($i == $index){
				//print_r($b);
				$bitem = PDao::getItem($b['item']);
				$ext = PUtil::image_extension_from_mimetype($b['mimetype']);
				$url=sprintf("/archive/item/%s/%s.%s?photo=true",$b['item'],$b['artifact_id'],$ext);
				if ($size == 1){
					$thumb = $bitem['thumb'];
				}elseif ($size == 2){
					$thumb = $bitem['thumb1'];
				}elseif ($size == 3){
					$thumb = $bitem['thumbs_small'][0];
				}elseif ($size == 4){
					$thumb = $bitem['thumbs_big'][0];
				}
			}
		}
		//print_r($oitem);

		$img_id = "";
		if (! empty($id)){
			$img_id = sprintf('id="%s-img"',$id);
		}

		if (! empty($id)){
			$id = sprintf('id="%s"',$id);
		}
		if (! empty($class)){
			$class = sprintf('class="%s"',$class);
		}
		if (! empty($alt)){
			$alt = sprintf('alt="%s"',$alt);
		}
		if (! empty($rel)){
			$rel = sprintf('rel="%s"',$rel);
		}

		$out = sprintf('<a %s class="colorbox-load" %s href="%s"><img %s %s %s src="/media/%s"/></a>',$id,$rel,$url,$img_id, $class,$alt, $thumb);
		return $out;
	}



	function compile($src,  $compile_mode = PContentCompiler::COMPILE_MODE_HTML){
		$out = "";
		$arr = preg_split("/\n/", $src);
		$this->messages = ARRAY();
		$this->errors = ARRAY();
		$this->compile_mode = $compile_mode;

		$only_content_flag = ($compile_mode == PContentCompiler::COMPILE_MODE_DRUPAL);

		$end = false;
		if ($only_content_flag) {
			$start = false;
		} else {
			$start = true;
		}
		$c = 0;
		foreach ($arr as $ln => $l) {
			//echo(htmlspecialchars($l));
			if (preg_match('/^\s*$/',$l)){
				continue;
			}
			if ($only_content_flag && preg_match('/<!--\s*content\s+end\s*-->/',$l)){
				$end =true;
			}
			if ($start && ! $end){
				 $c++;
				if (preg_match_all('/(<arc\:img[\s\w\d\-\:="_]+\/>)/',$l,$m)){
					foreach ($m[1] as $k=>$t){
						$replacement = $this->handle_img($t);
						$l = str_replace($t, $replacement, $l);
					}
				}

				if (preg_match_all('/(<arc\:colorboximg[\s\w\d\-\:="_]+\/>)/',$l,$m)){
					foreach ($m[1] as $k=>$t){
						$replacement = $this->handle_colorboximg($t);
						$l = str_replace($t, $replacement, $l);
					}
				}

				if (preg_match_all('/((<arc\:tags[\s\w\d\-\:="_]*\/>))/',$l,$m)){
					foreach ($m[1] as $k=>$t){
						$replacement = $this->handle_tags($t);
						$l = str_replace($t, $replacement, $l);
					}
				}


				$out .= $l;
			}
			if ($only_content_flag && preg_match('/<!--\s*content\s+start\s*-->/',$l)){
				$start =true;
			}
		}
		if (! $start){
			$this->content = $src;
		} else {
			$this->content = $out;
		}

		$msg = "compile $c lines";
		$this->messages[] = $msg;
	}



}



class FuzzyDate {

	public $date;
	public $y1;
	public $y2;
	public $yearsRange;

// 	public function getYearsRange(){
// 		if ($this->y1 == null){
// 			return  null;
// 		}
// 		if ($this->y2 == null){
// 			return  $this->y1;
// 		}

// 		return $this->y1 . "-" . $this->y2;
// 	}


	public function __construct($y,$m,$d) {
		$y1=null;
		$m1=1;
		$d1=1;
		$y2=null;
		$m2=1;
		$d2=1;

		$m1 = PUtil::extract_int(trim($m));
		$d1 = PUtil::extract_int(trim($d));
		if (empty($m1)){$m1 = 1; };
		if (empty($d1)){$d1 = 1; };

		$y = trim($y);
		if (preg_match("/^(-?\d[\d\?]+)$/", $y, $ma)) {

			if (preg_match("/^(-?\d+)(\?*)$/", $y, $ma )) {
				if (empty($ma[2])){//aplo etos ari8mitiko
					$this->y1 = $y;
					$this->y2 = $y;
					$this->yearsRange = $y;
					$date = new DateTime();
					$date->setDate($y,$m1,$d1);
					$this->date = $date;
					return;
				}
				$s = $ma[1];
				$b = $ma[2];
				$sl = strlen($s);
				$bl = strlen($b);
				$tl = $sl + $bl;
				$n = PUtil::extract_int($s);
				if ( $n >0 && $tl > 4 ){
					return;
				} else if ( $tl > 5 ){
					return;
				}

				$pol =  pow(10,$bl);
				$fn = $n * $pol;
				$this->y1 = $fn;
				$this->y2 = $fn+$pol-1;
				$this->yearsRange = $this->y1 . '-' . $this->y2;
				$date = new DateTime();
				$date->setDate($fn,$m1,$d1);
				$this->date = $date;


			} else {
				return;
			}

		}

		if (preg_match("/^([\d\?]+)\-([\d\?]+)$/", $y, $ma)) {
			$y1 = PUtil::extract_int($ma[1]);
			$y2 = PUtil::extract_int($ma[2]);
			$this->y1 = $y1;
			$this->y2 = $y2;
			$this->yearsRange = $this->y1 . '-' . $this->y2;
			$date = new DateTime();
			$date->setDate($y1,$m1,$d1);
			$this->date = $date;
			return;
		}

	}



}



/*
class DAVMyCollection extends Sabre\DAV\Collection {

	private $myPath;

	function __construct($myPath) {

		$this->myPath = $myPath;

	}

	function getChildren() {

		$children = array();
		// Loop through the directory, and create objects for each node
		foreach(scandir($this->myPath) as $node) {

			// Ignoring files staring with .
			if ($node[0]==='.') continue;

			$children[] = $this->getChild($node);

		}

		return $children;

	}

	function getChild($name) {

		$name =
		$path = $this->myPath . '/' . $name;

		// We have to throw a NotFound exception if the file didn't exist
		if (!file_exists($this->myPath)) throw new \Sabre\DAV\Exception\NotFound('The file with name: ' . $name . ' could not be found');
		// Some added security

		if ($name[0]=='.')  throw new \Sabre\DAV\Exception\Forbidden('Access denied');

		if (is_dir($path)) {

			return new \DAVMyCollection($name);

		} else {

			return new \DAVMyFile($path);

		}

	}

	function getName() {

		return basename($this->myPath);

	}

}

class DAVMyFile extends \Sabre\DAV\File {

	private $myPath;

	function __construct($myPath) {

		$this->myPath = $myPath;

	}

	function getName() {

		return basename($this->myPath);

	}

	function get() {

		return fopen($this->myPath,'r');

	}

	function getSize() {

		return filesize($this->myPath);

	}

}



class DAVBitstreamCollection extends Sabre\DAV\Collection {


	function __construct() {

	}

	function getChildren() {

		$children = array();

		$dbh = dbconnect();
		$sql = "SELECT internal_id, mimetype , name, bitstream_id, download_fname, size_bytes
		FROM dsd.item_bitstream_ext limit 20 offset 180";
		$stmt = $dbh->prepare($sql);

		$stmt->execute();
		while ($r = $stmt->fetch()){
			$children[] = $this->getChild($r[0]);
		}

		return $children;
	}




	function getChild($name) {

			return new \DAVBitstream($name);

	}

 //	public function getETag() {
 	//	return null;
 //	}

	function getName() {

		return "koko";

	}

}


class DAVBitstream extends \Sabre\DAV\File {

	private $internal_id;
	private $size;
	private $file_ext;
	private $p;

	function __construct($internal_id) {
		if (PUtil::strEndsWith($internal_id, '.pdf')
				|| PUtil::strEndsWith($internal_id, '.png')
				||PUtil::strEndsWith($internal_id, '.jpg')
		){
			$internal_id = substr($internal_id,0, strlen($internal_id)-4);
		} else if  (PUtil::strEndsWith($internal_id, '.html')
				||PUtil::strEndsWith($internal_id, '.jpeg') ){
			$internal_id = substr($internal_id,0, strlen($internal_id)-5);
		}
		$this->internal_id = $internal_id;
		$this->p = bitream2filename($internal_id);
		$dbh = dbconnect();

		$sql = "SELECT internal_id, mimetype , name, bitstream_id, download_fname, size_bytes, file_ext
		FROM dsd.item_bitstream_ext
		WHERE internal_id = ? ";
		$stmt = $dbh->prepare($sql);
		$stmt->bindParam(1, $internal_id);

		$stmt->execute();
		if ($r = $stmt->fetch()){
			$this->size = $r[5];
			$this->file_ext = $r[6];

		}


	}

	function getName() {
		if (empty($this->file_ext)){
			return $this->internal_id;
		} else {
			$n = $this->internal_id . '.' . $this->file_ext;
			return $n;
		}

	}

	function get() {
		return fopen($this->p,'r');

	}

	function getSize() {
		return $this->size;

	}

}
*/



class StaffUtil {



	public static function isJsonText($str){
		if ($str == null || $str == '' ){
			return false;
		}
		return (PUtil::strBeginsWith($str, '{') && PUtil::strEndsWith($str,'}'));
	}


	public static function isMarcText($str){
		if ($str == null || $str == '' ){
			return false;
		}
		return MarcUtil::isMnemonicMarc($str);
	}



	public static function isStaffOnly($ob){
		if ($ob == null || $ob == '' ){
			return false;
		}

		if (is_array($ob)){
			return true;
		}
		return StaffUtil::isStaffOnlyText($ob);
	}

	public static function isStaffOnlyText($str){
		if ($str == null || $str == '' ){
			return false;
		}
		if (StaffUtil::isJsonText($str)){
			return true;
		}
		if (MarcUtil::isMnemonicMarc($str)){
			return true;
		}
		return false;
	}


	public static function toMnem($s) {
		$map = array(
				'{' => '{lcub}',
				'}' => '{rcub}',
				'$' => '{dollar}',
				'>' => '{gt}',
				'<' => '{lt}'
		);
		$t = '';
		while (strlen($s)) {
			$did_subst = False;
			foreach($map as $from => $to) {
				if (substr($s, 0, strlen($from)) == $from) {
					$t .= $to;
					$s = substr($s, strlen($from));
					$did_subst = True;
					break;
				}
			}
			if (!$did_subst) {
				//if (ereg('^  +', $s, $m)) {
				//	$t .= str_repeat('\\', strlen($m[0]));
				//	$s = substr($s, strlen($m[0]));
				//} else {
					$t .= $s{0};
					$s = substr($s, 1);
				//}
			}
		}
		return $t;
	}

	public static function fromMnem($s) {
		$map = array(
				'{lcub}' => '{',
				'{rcub}' => '}',
				'{dollar}' => '$',
				'{lt}' => '<',
				'{gt}' => '>'
		);
		$t = '';
		while (strlen($s)) {
			$did_subst = False;
			foreach($map as $from => $to) {
				if (substr($s, 0, strlen($from)) == $from) {
					$t .= $to;
					$s = substr($s, strlen($from));
					$did_subst = True;
					break;
				}
			}
			if (!$did_subst) {
				$t .= $s{0};
				$s = substr($s, 1);
			}
		}
		return $t;
	}


}







class ClientJsonValueUtil {


	//migration helper convert oldValues to Client JSON VALUES
	public static function patronValue2JsonClientValue($key,$text_value){
		if ($text_value == null || $text_value == '') {
			return null;
		}

		if ($key == 'ea:url:related' || $key == 'ea:url:origin'){
			if (strrpos($text_value,"|") !== false){
				list($url,$desc) = explode("|",$text_value);
				return array('u'=>$url, 'd'=>$desc);
			}
		}
		if ($key == 'ea:date:start' || $key == 'ea:date:end' || $key == 'ea:date:orgissued'){
			$tar = explode("-",$text_value,3);
			$y = isset($tar[0]) ? $tar[0] : null;
			$m = isset($tar[1]) ? $tar[1] : null;
			$d = isset($tar[2]) ? $tar[2] : null;
			return array('y'=>$y,'m'=>$m,'d'=>$d);
		}

		return $text_value;
	}

};




class MarcUtil
{

	#	private $mnemParser;
	#	public function __construct() {
	#		$this->mnemParser = new MarcMnemParser();
	#	}


	public static function marc2staff($marc, $include_indicators = false, $include_tag = false){
		$out = '';
		$sfs = $marc->subfields;
		$delimiter = '';
		foreach ($sfs as $sf){
			$out .= ($delimiter . '$' . $sf->identifier . ' ' . trim($sf->data));
			$delimiter = ' ';
		}
		return $out;
	}



// 	public static function marc2str($str){
// 		$out = $str;
// 		$i = strpos($str,'$');
// 		if (! ($i === FALSE)){
// 			$out=preg_replace('/^\s*\$./', '', $out);
// 			$out=preg_replace('/\$./', ',', $out);
// 			$out=preg_replace('/\s+,/', ',', $out);
// 			$out=MarcHelpers::fromMnem($out);
// 			//$out=preg_replace('/\{dollar\}/', '\$', $out);
// 			//$out=preg_replace('/\{lcub\}/', '{', $out);
// 			//$out=preg_replace('/\{rcub\}/', '}', $out);
// 			//$out=preg_replace('/\{bsol\}/', '  ', $out);
// 		}
// 		return $out;
// 	}

		public static function isMnemonicMarc($str){
			return false; //DISABLE  MnemonicMarc
// 			if (PUtil::strBeginsWith($str,'$')){
// 				return true;
// 			}
// 			return false;
		}


		public static function normalizeMarcMnemonic($marc_key, $indicators, $str){
			return $str; //DISABLE
			if (PUtil::strBeginsWith($str,'$')){
				if(empty($marc_key)){$marc_key = '   ';};
				if(empty($indicators)){$indicators = '  ';};
				$str = '='. $marc_key . '  ' . $indicators . $str;
			}
			return $str;
		}


// 		public static function marc2jsonData($marc_mnemonic_str){
// 			$tmp = MarcUtil::parseMarcMnemonic($marc_mnemonic_str);
// 			if (! empty($tmp) &&  isset($tmp[0])){
// 				$marc = $tmp[0]->fields[0];
// 				$data = json_encode(array('marc' => $marc));
// 				return $data;
// 			}
// 			return null;
// 		}

		public static function parseMarcMnemonic($str){
			$str = MarcUtil::normalizeMarcMnemonic(null, null, $str);
			$p = new MarcMnemParser();
			$err = $p->parse($str);
			if(is_a($err, 'MarcParseError')){
				return null;
			}
			$p->eof();
			$recs =  $p->records;
			return $recs;
		}

		public static function validateMarcMnemonic($str,$subfield_data, $msg_prefix = null){
			if (empty($msg_prefix)){
				$msg_prefix = '';
			} else {
				$msg_prefix = $msg_prefix . ': ';
			}
			$msg = array();
			if (MarcUtil::isMnemonicMarc($str)){
				$str = MarcUtil::normalizeMarcMnemonic(null,null,$str);
				$p = new MarcMnemParser();
				$err = $p->parse($str);
				if(is_a($err, 'MarcParseError')){
					$msg[] = ARRAY($msg_prefix . "marc error " .$err->toStr() ,'error');
				} else {
					$p->eof();
					$recs =  $p->records;
					$subfields = $recs[0]->fields[0]->subfields;
					foreach ($subfields as $sf){
						$identifier = $sf->identifier;
						if (!isset($subfield_data[$identifier])){
							$msg[] = ARRAY($msg_prefix . "Wrong \$$identifier : $str",'error');
						}
					}
				}
			}
			return $msg;
		}







}













class ImportUtil {
	public static function importIsisJson2($json_file) {
		$data = json_decode ( file_get_contents ( $json_file ), true );
		// print_r($data);
		// echo("\n\n#1=========================================\n\n");
		// echo("\n\n#2=========================================\n\n");
		function fn1($key, $book, $field_no, $reg) {
			if (isset ( $book [$field_no] )) {
				$d = $book [$field_no];
				foreach ( $d as $arr ) {
					$flag = false;
					// $arr = $d[0];
					ksort ( $arr );
					$sub = null;
					foreach ( $arr as $k => $v ) {
						if (is_int ( $k )) {
							if ($k == 1) {
								$sub = $v;
							} else {
								$sub .= ' >> ' . $v;
							}
						}
						if (preg_match ( "/$reg/i", $v )) {
							$flag = true;
						}
					}
					if ($flag) {
						$l = strlen ( $key );
						$d = 4 - $l;
						for($i = 0; $i < $d; $i ++) {
							echo ' ';
						}
						;
						echo $key;
						echo ": ";
						echo $sub;
						// echo ' [' . "$key" . ']';
						echo "\n";
						// print_r($arr);
					}
				}
			}
		}
		function fn2($key, $book, $field_no, $reg) {
			if (isset ( $book [$field_no] )) {
				$d = $book [$field_no];
				foreach ( $d as $arr ) {
					$flag = false;
					// $arr = $d[0];
					ksort ( $arr );
					$sub = null;
					foreach ( $arr as $k => $v ) {
						if (empty ( $sub )) {
							$sub = $v;
						} else {
							$sub .= ' >> ' . $v;
						}
						if (preg_match ( "/$reg/i", $v )) {
							$flag = true;
						}
					}
					if ($flag) {
						$l = strlen ( $key );
						$df = 4 - $l;
						for($i = 0; $i < $df; $i ++) {
							echo ' ';
						}
						;
						echo $key;
						echo ": ";
						echo $sub;
						echo "\n";
						// print_r($d);
					}
				}
			}
		}
		function print_subj($data, $field_no, $subj) {
			echo ("#$field_no : $subj #\n");
			foreach ( $data ['docs'] as $k => $book ) {
				fn1 ( $k, $book, $field_no, $subj );
			}
			echo "\n####################################################################\n";
		}
		;
		function print_fi($data, $field_no, $subj) {
			echo ("#$field_no : $subj #\n");
			foreach ( $data ['docs'] as $k => $book ) {
				fn2 ( $k, $book, $field_no, $subj );
			}
			echo "\n####################################################################\n";
		}
		;

		print_subj ( $data, 15, 'Trasporto' );
		print_subj ( $data, 15, 'Sec' );
		print_subj ( $data, 15, 'Massa-Carrara' );
		print_subj ( $data, 15, 'Resistenza' );
		print_subj ( $data, 15, 'Marmo' );
		print_subj ( $data, 15, 'Toscana' );
		print_subj ( $data, 15, 'Fotografie' );
		print_subj ( $data, 15, 'Fascismo' );
		print_subj ( $data, 15, 'Antifascismo' );
		print_subj ( $data, 15, 'Storia' );
		print_subj ( $data, 15, 'Anarchici' );
		print_subj ( $data, 15, 'Anarchia' );
		print_subj ( $data, 15, 'sindacalismo' );
		print_subj ( $data, 15, 'Italia' );
		print_subj ( $data, 15, 'Grecia' );
		print_subj ( $data, 15, 'Messico' );
		print_subj ( $data, 15, 'Russia' );
		print_subj ( $data, 15, 'Documenti' );
		print_subj ( $data, 15, 'Biografie' );
		print_subj ( $data, 15, 'Biografia' );
		print_subj ( $data, 15, 'Autobiografia' );
		print_subj ( $data, 15, 'Autobiografie' );
		print_subj ( $data, 15, 'Malatesta' );
		print_subj ( $data, 15, 'Provincia' );
		print_subj ( $data, 15, '1946' );
		print_subj ( $data, 15, '1921' );

		echo "\n";
		echo "####################################################################\n";
		echo "####################################################################\n";
		echo "####################################################################\n";
		echo "####################################################################\n";

		print_fi ( $data, 12, 'Ambrosoli' );
		print_fi ( $data, 12, 'Bertolucci' );
		print_fi ( $data, 12, 'Morelli' );
		print_fi ( $data, 12, 'Carlo' );
		function print_field($book, $field_no) {
			echo "   " . $field_no . ": ";
			if (isset ( $book [$field_no] )) {
				$d = $book [$field_no];
				// $d = $book[$field_no][0][1];
				print_r ( $d );
			} else {
				echo "FIELD NOT FOUND ($field_no) \n";
			}
			echo "\n";
			// echo "\n====================\n";
		}
		function print_field_line($er, $book, $field_no) {
			if (isset ( $book [$field_no] )) {
				$d = $book [$field_no];
				foreach ( $d as $arr ) {
					$flag = false;
					ksort ( $arr );
					$sub = null;
					foreach ( $arr as $k => $v ) {
						if (empty ( $sub )) {
							$sub = $v;
						} else {
							$sub .= ' >> ' . $v;
						}
					}

					$l = strlen ( $er );
					$df = 5 - $l;
					for($i = 0; $i < $df; $i ++) {
						echo ' ';
					}
					;
					echo $er;
					echo ": ";
					echo $sub;
					echo "\n";
					// print_r($d);
				}
			}
			// echo "\n";
			// echo "\n====================\n";
		}

		// PRINT ALL FIELDS

		echo ("\n\n#1=========================================\n\n");
		foreach ( $data ['docs'] as $k => $book ) {
			print_field_line ( $k, $book, 12 );
		}
		echo ("\n\n#2=========================================\n\n");

		// EXAMPLE

		// echo "\n===========================================\n";
		// echo "EXAMPLE";
		// echo "\n===========================================\n";
		// $book = $data['docs'][16];
		// echo("TITLE / SUBTITLE\n");
		// print_field($book, 1);

		// echo("AUTHOR\n");
		// print_field($book, 12);

		// echo("SUBJECTS\n");
		// print_field($book, 15);

		// echo("NOTES\n");
		// print_field($book, 7);

		// echo("PUBLICATION\n");
		// print_field($book, 4);

		// echo("PHYSICAL DESCRIPTION\n");
		// print_field($book, 5);

		// echo("TYPE OF DOCUMENT\n");
		// print_field($book, 30);

		// echo "\n===========================================\n";
		// print_r($data['docs'][16]);

		echo "\n===========================================\n";
		// print_r($data['docs'][1]);
		echo "\n===========================================\n";
		// print_r($data['docs'][28]);

		$book = $data ['docs'] [171];
		echo ("SUBJECTS 171\n");
		print_field_line ( 171, $book, 15 );

		$book = $data ['docs'] [951];
		echo ("SUBJECTS 951\n");
		print_field_line ( 951, $book, 15 );
	}
	public static function importIsisJson($json_file) {
		ini_set ( 'max_execution_time', 300 );
		$data = json_decode ( file_get_contents ( $json_file ), true );
		function print_field_line($er, $book, $field_no, $print_field_no = false, $subfield = null, $print_value = true) {
			$out = "";
			// echo("=BOOK: $er \n");
			// echo "============================================================================\n";
			if (isset ( $book [$field_no] )) {
				$d = $book [$field_no];
				foreach ( $d as $arr ) {
					ksort ( $arr );
					$sub = null;
					foreach ( $arr as $k => $v ) {
						if ($subfield === null || ($subfield === '0' && $subfield == $k) || $subfield === $k) {
							$v = trim ( $v );
							$v = htmlspecialchars ( $v );
							if (! empty ( $v )) {
								if (! $print_value) {
									$v = '';
								}
								;
								if (empty ( $sub )) {
									$sub = "|$k|" . $v;
								} else {
									$sub .= "  |$k| " . $v;
								}
							}
						}
					}

					$er = trim ( $er );
					if ($er != '') {
						$l = strlen ( $er );
						$df = 5 - $l;
						for($i = 0; $i < $df; $i ++) { // echo ' ';
							$out .= ' ';
						}
						;
						$out .= $er;
						$out .= ": ";
					}

					if ($print_field_no) {
						$field_no = '' . $field_no;
						$l = strlen ( $field_no );
						$df = 5 - $l;
						for($i = 0; $i < $df; $i ++) { // echo ' ';
							if ($i == 0) {
								$out .= '*';
							} else {
								$out .= ' ';
							}
							;
						}
						;
						$out .= $field_no;
						$out .= ": ";
					}
					$out .= $sub;
					$out .= "\n";
					// print_r($d);
				}
				// echo "\n";
				// echo "\n########################################\n";
			}
			// echo "\n";
			// echo "\n============================================================================\n";
			return $out;
		}
		function print_book($bn, $book, $stdoutFlag = true) {
			$out = "";
			if ($stdoutFlag) {
				echo ("\nBOOK: $bn\n\n");
			}
			ksort ( $book );
			foreach ( $book as $fn => $tmp ) {
				$out .= print_field_line ( null, $book, $fn, true );
			}
			if ($stdoutFlag) {
				echo ($out);
			}
			return $out;
			echo ("-------------------------------------------------\n");
		}
		function print_field($book, $field_no) {
			echo "#### " . $field_no . ":\n";
			if (isset ( $book [$field_no] )) {
				$d = $book [$field_no];
				// $d = $book[$field_no][0][1];
				print_r ( $d );
			} else {
				echo "FIELD NOT FOUND ($field_no) \n";
			}
			echo "\n";
			echo "\n#############################\n";
		}

		// //EXAMPLE
		// echo "\n===========================================\n";
		// echo "EXAMPLE";
		// echo "\n===========================================\n";
		// $book = $data['docs'][16];
		// echo("TITLE / SUBTITLE\n");
		// print_field($book, 1);
		// echo("AUTHOR\n");
		// print_field($book, 12);
		// echo("SUBJECTS\n");
		// print_field($book, 15);
		// echo("NOTES\n");
		// print_field($book, 7);
		// echo("PUBLICATION\n");
		// print_field($book, 4);
		// echo("PHYSICAL DESCRIPTION\n");
		// print_field($book, 5);
		// echo("TYPE OF DOCUMENT\n");
		// print_field($book, 30);
		// echo "\n===========================================\n";
		// echo "\n===========================================\n";
		// print_r($data['docs'][16]);

		// //////////////////////
		// // EXAMPLE: $book[1]
		// [1] => Array
		// (
		// [0] => Array
		// (
		// [a] => A<'>m<'>arcord... : poesie dialettali
		// [g] => introduzione di Giuseppe Andreani
		// [_] =>
		// [f] => Antonio Doretti
		// )
		//
		// [1] => Array
		// (
		// [g] => [disegni di Mario Masetti]
		// [_] =>
		// )
		//
		// )
		function normalizeVal1($val) {
			if (empty ( $val ))
				return null;
			$val = trim ( $val );
			if ($val == '')
				return null;
			$val = str_replace ( "<'>", "'", $val );
			$val = str_replace ( "< >", " ", $val );
			// $val = str_replace("<", " ", $val);
			// $val = str_replace(">", " ", $val);
			$val = str_replace ( "[ ", "[", $val );
			$val = str_replace ( " ]", "]", $val );
			// $val = preg_replace('/^\s+/', '', $val);
			// $val = preg_replace('/\s+$/', '', $val);
			$val = preg_replace ( '/\s\s+/', ' ', $val );
			$val = trim ( $val );
			return $val;
		}
		function normalizeVal2($val) {
			if (empty ( $val ))
				return null;
			$val = trim ( $val );
			if ($val == '')
				return null;
			$val = str_replace ( "<", " ", $val );
			$val = str_replace ( ">", " ", $val );
			$val = preg_replace ( '/\s\s+/', ' ', $val );
			$val = trim ( $val );
			return $val;
		}
		function removeSB($val) {
			$val = str_replace ( "[", '', $val );
			$val = str_replace ( "]", '', $val );
			return $val;
		}
		function fixSB($str) {
			$val = $str;
			if (PUtil::strBeginsWith ( $val, '[' ) && ! PUtil::strContains ( $val, ']' )) {
				$val = $val . ']';
			}
			if (PUtil::strEndsWith ( $val, ']' ) && ! PUtil::strContains ( $val, '[' )) {
				$val = '[' . $val;
			}

			return $val;
		}
		function import_book($idx, $book, $txt, $print_data = false) {
			// $pre_text = '<pre>' . $txt . '</pre>';
			// print_r($book);
			$userName = get_user_name ();
			$dbh = dbconnect ();

			$status = 'finish';
			$obj_type = 'book';
			$idata = new ItemMetadata ();
			$w = 0;
			// $srid = 9200;
			$addValue = function ($key, $v, $params = null) use($idata) {
				// print_r($idata);
				if ($params == null) {
					$params = array ();
				}
				if (isset ( $params ['idata'] )) {
					$idata = $params ['idata'];
				}
				if (PUtil::isEmpty ( $v )) {
					return;
				}
				// ///XX

				if (isset ( $params ['subject'] ) && $params ['subject']) {
					return $idata->addStaffValueSK ( $key, $v, array () );
				}

				if (! isset ( $params ['skipFixSB'] ) || ! $params ['skipFixSB']) {
					$v = fixSB ( $v );
				}
				$props = null;
				if (PUtil::strBeginsWith ( $v, '[' ) || PUtil::strEndsWith ( $v, ']' )) {
					if (isset ( $params ['removeSB'] ) && $params ['removeSB']) {
						$v = preg_replace ( '/^\[/', '', $v );
						$v = preg_replace ( '/\]$/', '', $v );
					}
					if (isset ( $params ['props'] )) {
						$params ['props'] ['prsd'] = 'n';
					} else {
						$params ['props'] = array (
								'prsd' => 'n'
						);
					}
				}

				if (isset ( $params ['type'] ) && $params ['type'] == 'date') {
					preg_match ( '/(\d{4})/', $v, $matches );
					$year = null;
					if (isset ( $matches [0] )) {
						$year = $matches [0];
					}
					$prsd = 'p';
					if (isset ( $params ['props'] ) && isset ( $params ['props'] ['prsd'] )) {
						$prsd = $params ['props'] ['prsd'];
					}
					$varr = array (
							'y' => null,
							'd' => null,
							'm' => null,
							't' => null,
							'prsd' => $prsd,
							'z' => 'date'
					);
					// printf("@@ %12s : %s : %s\n",'pub year',$year_str, $matches[0]);
					if (! empty ( $year )) {
						$varr ['y'] = $year;
					} else {
						$varr ['y'] = '?';
					}
					$varr ['t'] = $v;
					$vok = json_encode ( $varr );
					$v = $vok;
				}

				if (PUtil::strBeginsWith ( $v, '<' ) && PUtil::strContains ( $v, '>' )) {
					$ar = str_split ( $v );
					$i = 0;
					$sc = 0;
					foreach ( $ar as $ch ) {
						if ($i > 0) {
							if ($ch == '>') {
								break;
							}
							$sc ++;
						}
						$i ++;
					}
					// echo("#SC2#: " . $sc . " : " . substr($v,0,80). "\n");
					if ($sc > 0) {
						if (isset ( $params ['props'] )) {
							$params ['props'] ['skip'] = $sc;
						} else {
							$params ['props'] = array (
									'skip' => $sc
							);
						}
					}
				}
				$v = normalizeVal2 ( $v );
				if ($key == 'isis:book:record') {
					$v = htmlspecialchars_decode ( $v );
				}
				;

				$v = str_replace ( "[ ", "[", $v );
				$v = str_replace ( " ]", "]", $v );
				// echo("@ADD $key $v\n");
				// if ($key == 'ea:status:'){
				// $srid +=1;
				// $params['record_id'] = $srid;
				// }
				return $idata->addStaffValueSK ( $key, $v, $params );
			};

			$addValue ( DataFields::ea_obj_type, $obj_type );

			$addValue ( 'isis:book:id', $idx );
			$addValue ( 'isis:mfn:', $idx + 1 );
			$addValue ( 'isis:book:record', $txt );
			// $addValue('isis:book:record-json',json_encode($book));
			$idata->addValueSK ( 'isis:book:record-json', 'ISIS-DATA-JSON: ' . json_encode ( $book ) );
			// $addValue('isis:book:record-json',json_encode($tmp));

			// $idata->addValueSK("dc:description:abstract", $pre_text,null,null,null,null,null,null,$w++ );

			$field_no = null;
			$field = array ();
			$set_field = function ($fn) use($book, &$field, &$field_no) {
				// echo("#SET FIELD $fn\n");
				$field_no = $fn;
				if (isset ( $book [$fn] )) {
					$field = $book [$fn];
				} else {
					$field = array ();
					$field_no = null;
				}
				// print_r($field);
			};

			$is_single = function () use(&$field) {
				return ($field == null || count ( $field == 1 ));
			};

			$get_value = function ($record, $sf) {
				$val = isset ( $record [$sf] ) ? $record [$sf] : null;
				return normalizeVal1 ( $val );
			};

			$get_record_value_concatenated = function ($record, $sf, $init_value = null, $seperator = '; ') use($get_value) {
				// echo("#1");
				// print_r($record);
				if (empty ( $record )) {
					return $init_value;
				}
				$str = $init_value;
				$tmp = $get_value ( $record, $sf );
				// echo("#2, " . $tmp);
				if ($str == null || $str === '') {
					$str = $tmp;
				} else if ($tmp != null && $tmp !== '') {
					$str = $str . $seperator . $tmp;
				}
				return $str;
			};

			$get_field_value_concatenated = function ($sf, $init_value = null) use(&$field, $get_record_value_concatenated) {
				if (empty ( $field )) {
					return $init_value;
				}
				$str = $init_value;
				foreach ( $field as $record ) {
					// $tmp = $get_value($record, $sf);
					// if ($str == null || $str === ''){
					// $str = $tmp;
					// } else if ($tmp != null && $tmp !== ''){
					// $str = $str . '; ' . $tmp;
					// }
					$str = $get_record_value_concatenated ( $record, $sf, $str );
				}
				return $str;
			};

			$get_field_single_value = function ($sf) use($get_field_value_concatenated) {
				return $get_field_value_concatenated ( $sf );
			};

			$get_fields_values_concatenated = function ($farr) use($get_field_value_concatenated) {
				$rep = null;
				foreach ( $farr as $sf ) {
					$rep = $get_field_value_concatenated ( $sf, $rep );
				}
				return $rep;
			};
			$get_record_values_concatenated = function ($record, $farr) use($get_record_value_concatenated) {
				$rep = null;
				foreach ( $farr as $sf ) {
					$rep = $get_record_value_concatenated ( $record, $sf, $rep );
				}
				return $rep;
			};

			$get_value_concatenated = function ($seperatorMap = array()) use(&$field, $get_value) {
				// if (empty($field)) { return $init_value;};
				$str = null;
				foreach ( $field as $record ) {
					ksort ( $record );
					foreach ( $record as $sf => $v ) {
						$tmp = $get_value ( $record, $sf );
						if ($str == null || $str === '') {
							$str = $tmp;
						} else if ($tmp != null && $tmp !== '') {
							$sep = '; ';
							if (isset ( $seperatorMap [$sf] )) {
								$sep = $seperatorMap [$sf];
							}
							$str = $str . $sep . $tmp;
						}
					}
				}
				return $str;
			};

			// $a - Title (NR)
			// $b - Remainder of title (NR)
			// $c - Statement of responsibility, etc. (NR)
			// $f - Inclusive dates (NR)
			// $g - Bulk dates (NR)
			// $h - Medium (NR) $k - Form (R)
			// $n - Number of part/section of a work (R)
			// $p - Name of part/section of a work (R)

			// 214 | marc:title-statement:bulk-dates
			// 213 | marc:title-statement:inclusive-dates
			// 215 | marc:title-statement:medium
			// 211 | marc:title-statement:remainder
			// 212 | marc:title-statement:responsibility
			// 218 | marc:title-statement:version
			// 217 | marc:title-statement:work-section-name
			// 216 | marc:title-statement:work-section-number
			// 210 | marc:title-statement:
			//

			// MARC
			// a : b ; [h] ; n ; p ; k ; f ; g /C
			//
			// a: b /C
			// a: b: k f p /C
			// a [h] : b /C
			// a.n, p[h] : b /C
			// a.p : b /C
			// a: b: k f g /C

			// ISIS to f paei me to g (f ; g) sto marc C

			// FIELD 1 TTILE
			// ############################################
			$set_field ( 1 );
			$sepMap = array (
					'f' => ' / ',
					b => ': ',
					'c' => '. '
			);
			$title = $get_value_concatenated ( $sepMap );
			$addValue ( DataFields::dc_title, $title );
			$a = $get_field_single_value ( 'a' );
			$id = $addValue ( 'marc:title-statement:title', $a );
			$other_title_info = $get_fields_values_concatenated ( array (
					'e',
					'c',
					'i',
					'd'
			) );
			$addValue ( 'isbd:title:other-info', $other_title_info, array (
					'link' => $id
			) );
			// $tr= $get_field_value_concatenated('f');
			$tr = $get_fields_values_concatenated ( array (
					'f',
					'g'
			) );
			$addValue ( 'marc:title-statement:responsibility', $tr, array (
					'link' => $id
			) );

			// FIELD: 2 EDITION
			// #############################################
			$set_field ( 2 );
			$a = $get_field_single_value ( 'a' );
			$b = $get_fields_values_concatenated ( array (
					'b',
					'f'
			) );
			$id = $addValue ( 'marc:edition:statement', $a );
			$addValue ( 'marc:edition:remainder', $b, array (
					'link' => $id
			) );

			// #############################################
			// FIELD: 4 PUBLICATION
			// #############################################
			// [a] => 1174 publication place
			// [c] => 1180 ekdotis
			// [d] => 1151 etos ekdosis
			// [e] => 428 place of printing (?)
			// [g] => 427 name of printer (?)
			// [h] => 2 etos
			// [s] => 3 other info ?
			// [1] => 1 other info ?
			// [f] => 3 other info ?
			// [b] => 3 other info ?
			// [p] => 1 other info ?

			// ea:publication:statement
			// dc:publisher:
			// ea:publication:place
			// ea:publication:printing-place
			// ea:publication:printer-name
			// ea:publication:other-info
			// ea:date:orgissued
			// ea:publication:distributor

			// ea:date:orgissued
			// dc:publisher:
			// ea:publication:place
			// ea:publication:distributor

			// a 4.1 Place of publication and/or distribution
			// c 4.2 Name of publisher and/or distributor
			// 4.3 Statement of function of distributor
			// d 4.4 Date of publication and/or distribution
			// e 4.5 Place of printing or manufacture
			// g 4.6 Name of printer or manufacturer
			// h 4.7 Date of printing or manufacture

			// a : 1174
			// c : 1180
			// d : 1151
			// e : 428
			// g : 427
			// h : 2
			// s : 3 DEN EMFANIZETE STIN EKTIPOSI
			// 1 : 1 DEN EMFANIZETE STIN EKTIPOSI
			// f : 3 DEN EMFANIZETE STIN EKTIPOSI
			// b : 3 DEN EMFANIZETE STIN EKTIPOSI
			// p : 1 DEN EMFANIZETE STIN EKTIPOSI

			// dc:publisher: | 232
			// ea:publication:place | 547

			$pub_final_year_str = null;
			// $pub_final_place_id = null;
			// $pub_final_place_str = null;
			$set_field ( 4 );
			$st = null;
			$a = null;
			$c = null;
			$d = null;
			$e = null;
			$g = null;
			$h = null;
			$s = null;
			$x1 = null;
			$f = null;
			$b = null;
			$p = null;
			$info = null;
			$add_pubstatement = function () use(&$st, &$c, &$d, &$e, &$g, &$h, &$s, &$x1, &$f, &$b, &$p, &$info, $addValue) {
				if (PUtil::isEmpty ( $st )) {
					return;
				}
				// echo("@ |st| $st |c| $c |d| $d |e| $e |g| $g\n");
				// $st = $createStatement($st,$c,$d,$e,$g);
				$stok = PUtil::concatSeperator ( ' : ', array (
						$st,
						$c
				) );
				$stok = PUtil::concatSeperator ( ', ', array (
						$stok,
						$d
				) );
				$eg = PUtil::concatSeperator ( ' : ', array (
						$e,
						$g
				) );
				if (! PUtil::isEmpty ( $eg )) {
					$stok = $stok . ' (' . $eg . ')';
				}
				$stok = $stok . '.';
				$id = $addValue ( 'ea:publication:statement', $stok );
				$year_str = empty ( $d ) ? $h : $d;
				$addValue ( "ea:date:orgissued", $year_str, array (
						'type' => 'date',
						'link' => $id
				) );
				// if ($pub_final_year_str == null && !PUtil::isEmpty($year_str)){
				// $pub_final_year_str = $year_str;
				// }

				// echo("ADD ea:publication:place $st\n");

				// $place_id = $addValue('ea:publication:place',$st,array('link'=>$id,'removeSB'=>true));
				// $actor_id = $addValue(DataFields::dc_publisher, $c,array('link'=>$id));
				$place_id = $addValue ( 'ea:publication:place', $st, array (
						'removeSB' => true,
						'link' => $id
				) );
				if (! empty ( $place_id )) {
					$addValue ( 'ea:status:', 'incomplete', array (
							'link' => $place_id
					) );
				}
				$actor_id = $addValue ( DataFields::dc_publisher, $c, array (
						'removeSB' => true,
						'link' => $id
				) );
				if (! empty ( $actor_id )) {
					$addValue ( 'ea:status:', 'incomplete', array (
							'link' => $actor_id
					) );
					$addValue ( 'ea:contributor-type:', 'collective', array (
							'link' => $actor_id
					) );
					// $addValue('ea:place:',$a,array('link'=>$actor_id));
				}
				$place_id = $addValue ( 'ea:publication:printing-place', $e, array (
						'link' => $id,
						'removeSB' => true
				) );
				if (! empty ( $place_id )) {
					$addValue ( 'ea:status:', 'incomplete', array (
							'link' => $place_id
					) );
				}
				$actor_id = $addValue ( 'ea:publication:printer-name', $g, array (
						'link' => $id
				) );
				if (! empty ( $actor_id )) {
					$addValue ( 'ea:status:', 'incomplete', array (
							'link' => $actor_id
					) );
				}
				$poi = PUtil::concatSeperator ( '; ', array (
						$h,
						$s,
						$f,
						$b,
						$p,
						$x1
				) );
				$addValue ( 'ea:publication:other-info', $poi, array (
						'link' => $id
				) );
				// echo("#add_statement: $stok \n");
				$st = null;
				$c = null;
				$d = null;
				$e = null;
				$g = null;
				$h = null;
				$s = null;
				$x1 = null;
				$f = null;
				$b = null;
				$p = null;
				$info = null;
			};

			$c = 0;
			foreach ( $field as $rec ) {
				// print_r($rec);
				$a = $get_value ( $rec, 'a' );
				if (! PUtil::isEmpty ( $a )) {
					$add_pubstatement ();
					$st = $a;
				}
				$a = $get_record_value_concatenated ( $rec, 'a', $c, ' : ' );
				$c = $get_record_value_concatenated ( $rec, 'c', $c, ' : ' );
				$d = $get_record_value_concatenated ( $rec, 'd', $d );
				$e = $get_record_value_concatenated ( $rec, 'e', $e );
				$g = $get_record_value_concatenated ( $rec, 'g', $g );
				$h = $get_record_value_concatenated ( $rec, 'h', $h );
				$s = $get_record_value_concatenated ( $rec, 's', $s );
				$x1 = $get_record_value_concatenated ( $rec, 'x1', $x1 );
				$f = $get_record_value_concatenated ( $rec, 'f', $f );
				$b = $get_record_value_concatenated ( $rec, 'b', $b );
				$p = $get_record_value_concatenated ( $rec, 'p', $p );
			}
			// echo("## FINISH |st| $st |a| $a \n");
			if (PUtil::isEmpty ( $st ) && ! PUtil::isEmpty ( $a )) {
				$st = $a;
			}
			$add_pubstatement ();

			// $sepMap=array('c'=> ' : ','g' => ' : ');
			// $pub_statement = $get_value_concatenated($sepMap);
			// $addValue('ea:publication:statement',$pub_statement);
			//
			// $pub_place = $get_field_single_value('a');
			// $publisher = $get_field_single_value('c');
			// $year_str1 = $get_field_single_value('d');
			// $year_str2 = $get_field_single_value('h');
			// $year_str = empty($year_str1) ? $year_str2 : $year_str1;
			// $place_id = $addValue('ea:publication:place',$pub_place);
			// $addValue(DataFields::dc_publisher, $publisher);
			// $e = $get_field_single_value('e');
			// $g = $get_field_single_value('g');
			// $addValue('ea:publication:printing-place',$e);
			// $addValue('ea:publication:printer-name',$g);
			// $poi = $get_fields_values_concatenated(array('s','f','b','p','1'));
			// $addValue('ea:publication:other-info',$poi);
			//
			// preg_match('/(\d{4})/', $year_str, $matches);
			// $year = null;
			// if (isset($matches[0])){
			// $year = $matches[0];
			// }
			// $v = array('y'=>null,'d'=>null,'m'=>null,'t'=>null,'z'=>'date');
			// #printf("@@ %12s : %s : %s\n",'pub year',$year_str, $matches[0]);
			// if (! empty($year)){
			// $v['y']=$year;
			// }
			// $v['t']=$year_str;
			// if (! empty($year_str)){
			// $vok = json_encode($v);
			// $addValue("ea:date:orgissued",$vok);
			// }
			//

			// 5 # PHYSICAL DESCRIPTION
			// #############################################
			// [a] => 1151 pages
			// [c] => 337 other phisical details
			// [d] => 1148 size
			// [e] => 10 other phisical details
			// [2] => 1 other phisical details
			// [i] => 1 other phisical details
			// [1] => 1 other phisical details
			$set_field ( 5 );
			$a = $get_field_single_value ( 'a' );
			$d = $get_field_single_value ( 'd' );
			$oi = $get_fields_values_concatenated ( array (
					'c',
					'e',
					'i',
					'1',
					'2'
			) );
			$id = $addValue ( 'ea:dimensions:extent', $a );

			preg_match ( '/(\d+)\s+p\./', $a, $matches );
			if (isset ( $matches [1] )) {
				$page_no = $matches [1];
				$addValue ( 'ea:size:pages', $page_no );
			}
			$addValue ( 'ea:dimensions:dimensions', $d, array (
					'link' => $id
			) );
			$addValue ( 'ea:dimensions:other-physical-datails', $oi, array (
					'link' => $id
			) );

			// 7 # NOTES
			// #############################################
			// [_] => 841 notes
			// [a] => 1 notes
			$set_field ( 7 );
			// $note = $get_value_concatenated();
			// $id = $addValue('ea:note:generic',$note);
			foreach ( $field as $rec ) {
				// print_r($rec);
				$n1 = $get_value ( $rec, '_' );
				$n2 = $get_value ( $rec, 'a' );
				$addValue ( 'ea:note:generic', $n1 );
				$addValue ( 'ea:note:generic', $n2 );
			}

			// FIELD: 8 ISBN
			// #############################################
			$set_field ( 8 );
			foreach ( $field as $rec ) {
				$isbn = $get_value ( $rec, '_' );
				$addValue ( 'dc:identifier:isbn', $isbn );
			}

			// #############################################
			// FIELD: 6
			// #############################################
			// 6 # SERIE
			// [a] => 542 Title proper of series or sub-series
			// [e] => 44 Parallel title of series or sub-series
			// [f] => 29 Statements of responsibility relating to the series or sub-series
			// [h] => 1 INFO?
			// [i] => 47 Other title information of series or sub-series
			// [v] => 440 Numbering within series or sub-series
			// isbd:series:title-proper
			// isbd:series:title-parallel
			// isbd:series:other-info
			// isbd:series:responsibility-statement
			// isbd:series:numbering
			$set_field ( 6 );
			$st = null;
			$a = null;
			$e = null;
			$f = null;
			$h = null;
			$i = null;
			$v = null;
			$info = null;
			$add_series = function () use(&$st, &$a, &$e, &$f, &$h, &$i, &$v, &$info, $addValue) {
				if (! PUtil::isEmpty ( $st )) {
					$id = $addValue ( 'isbd:series:title-proper', $st );
					$addValue ( 'isbd:series:title-parallel', $e, array (
							'link' => $id
					) );
					$addValue ( 'isbd:series:responsibility-statement', $f, array (
							'link' => $id
					) );
					$addValue ( 'isbd:series:numbering', $v, array (
							'link' => $id
					) );
					$info = PUtil::concatSeperator ( "; ", array (
							$h,
							$i
					) );
					$addValue ( 'isbd:series:other-info', $info, array (
							'link' => $id
					) );
					// echo("#add_series |a| $st |v| $v |e| $e |f| $f |hi| $info\n");
					$e = null;
					$f = null;
					$h = null;
					$i = null;
					$v = null;
					$info = null;
					$st = null;
				}
			};

			$c = 0;
			foreach ( $field as $rec ) {
				// print_r($rec);
				$a = $get_value ( $rec, 'a' );
				if (! PUtil::isEmpty ( $a )) {
					$add_series ();
					$st = $a;
				}
				$e = $get_record_value_concatenated ( $rec, 'e', $e );
				$f = $get_record_value_concatenated ( $rec, 'f', $f );
				$h = $get_record_value_concatenated ( $rec, 'h', $h );
				$i = $get_record_value_concatenated ( $rec, 'i', $i );
				$v = $get_record_value_concatenated ( $rec, 'v', $v );
			}
			// echo("## FINISH |st| $st |a| $a \n");
			if (! PUtil::isEmpty ( $a )) {
				$st = $a;
			}
			$add_series ();

			// #############################################
			// FIELD: 9
			// #############################################
			// # AUTOR MAIN
			// [a] => 917 surname
			// [b] => 895 name
			// [d] => 2
			// [f] => 12 date1-date2, ex1: 1925-1999, ex2: 1944-
			// [x] => 14 MIDLE NAME
			// [c] => 3 TITLE
			// [i] => 1
			// #############################################
			// FIELD: 12
			// #############################################
			// 12 # AUTHOR
			// [a] => 788 surname
			// [s] => 743 number (?) TIPOS AUTHOR
			// [b] => 772 given name
			// [_] => 6 name unknown order
			// [g] => 2 name given first
			// [f] => 5 date1-date2, ex1: 1925-1999, ex2: 1944-
			// [c] => 1 TITLE
			// [v] => 1 number (?)
			$set_author = function ($key, $record) use($addValue, $get_value) {
				$ln = $get_value ( $record, 'a' );
				$gn = $get_value ( $record, 'b' );
				$title = $get_value ( $record, 'c' );
				$midle = $get_value ( $record, 'x' );
				$dates = $get_value ( $record, 'f' );
				$fn = $ln . ", " . $gn;
				if (! empty ( $midle )) {
					$fn = $ln . ", " . $gn . ' ' . $midle;
				}
				$id = $addValue ( $key, $fn );
				$addValue ( 'ea:contributor-type:', 'person', array (
						'link' => $id
				) );
				$addValue ( 'ea:name:_type', 'surname_first', array (
						'link' => $id
				) );
				if (! empty ( $title )) {
					$addValue ( 'ea:person:name-titles', $title, array (
							'link' => $id
					) );
				}
				if (! empty ( $dates )) {
					$addValue ( 'ea:comment:', $dates, array (
							'link' => $id
					) );
				}
				$addValue ( 'ea:status:', 'incomplete', array (
						'link' => $id
				) );
			};

			$get_contrubutor_type = function ($t) {
				// $t = $get_value($record, 's');
				// 1 = co-author (who is at the same level of field 9)
				// 2 = editor
				// 3 = translator
				// 4 = introducer (who writes the preface)
				// 5 = illustrator
				if (empty ( $t ))
					return DataFields::dc_contributor_author;
				if ($t == 1)
					return DataFields::dc_contributor_author;
				if ($t == 2)
					return 'ea:contributor:editor';
				if ($t == 3)
					return 'ea:contributor:translator';
				if ($t == 4)
					return 'ea:contributor:introducer';
				if ($t == 5)
					return 'dc:contributor:illustrator';
				return DataFields::dc_contributor_author;
			};
			$set_field ( 9 );
			foreach ( $field as $record ) {
				// $type = $get_value($record, 's');
				$key = DataFields::dc_contributor_author;
				$set_author ( $key, $record );
			}
			$set_field ( 12 );
			foreach ( $field as $record ) {
				$type = $get_value ( $record, 's' );
				$key = $get_contrubutor_type ( $type );
				$set_author ( $key, $record );
			}

			// #############################################
			// FIELD: 10
			// #############################################
			// # COLECTIVE BODY
			// [a] => 25 name
			// [e] => 6 PLACE
			// [f] => 6 date
			// [d] => 3 ? number
			// [s] => 1 ? 1 value:Congreso
			// [c] => 2 ?
			// [b] => 1 ?
			//
			// #############################################
			// FIELD: 13
			// #############################################
			// # COLECTIVE BODY
			//
			// [a] => 121
			// [s] => 73
			// [e] => 32
			// [1] => 1
			// [c] => 5
			// [b] => 3
			// [f] => 1

			$set_collective_body = function ($key, $record) use($addValue, $get_value, $get_record_values_concatenated) {
				$name = $get_value ( $record, 'a' );
				$place = $get_value ( $record, 'e' );
				$info = $get_record_values_concatenated ( $record, array (
						'b',
						'c',
						'd',
						'f',
						'1'
				) );

				$id = $addValue ( $key, $name );
				$addValue ( 'ea:contributor-type:', 'collective', array (
						'link' => $id
				) );
				if (! empty ( $place )) {
					$addValue ( 'ea:place:', $place, array (
							'link' => $id,
							'removeSB' => true
					) );
				}
				if (! empty ( $info )) {
					$addValue ( 'ea:comment:', $info, array (
							'link' => $id
					) );
				}
				$addValue ( 'ea:status:', 'incomplete', array (
						'link' => $id
				) );
			};

			$set_field ( 10 );
			foreach ( $field as $record ) {
				$key = DataFields::dc_contributor_author;
				$set_collective_body ( $key, $record );
			}
			$set_field ( 13 );
			foreach ( $field as $record ) {
				$type = $get_value ( $record, 's' );
				$key = $get_contrubutor_type ( $type );
				$set_collective_body ( $key, $record );
			}

			// #############################################
			// FIELD: 15
			// #############################################
			// 15 # SUBJECT

			$set_field ( 15 );
			$map = array ();
			if (! empty ( $field )) {
				foreach ( $field as $record ) {
					$subject = '';
					$sep = '';
					foreach ( $record as $key => $value ) {
						$value = normalizeVal2 ( normalizeVal1 ( $value ) );
						// echo("#DEBUG#1 $key => $value\n");

						if (! empty ( $value )) {
							if (is_numeric ( $key )) {
								// echo("#DEBUG#2 $key => $value\n");
								if (! isset ( $map [$value] )) {
									$map [$value] = '';
									// echo("#DEBUG#3XX $key => $value\n");
									$addValue ( DataFields::dc_subject, $value );
								}
								;
								$subject .= $sep . $value;
								$sep = '>';
							}
						}
					}
					if (! isset ( $map [$subject] )) {
						$map [$value] = '';
						// echo("#DEBUG#4XX $key => $subject\n");
						$addValue ( DataFields::dc_subject, $subject, array (
								'subject' => true
						) );
					}
					// printf("#DEBUG# %12s : %s\n",'subject',$subject);
				}
			}

			// #############################################
			// FIELD: 16
			// #############################################
			// 16 # DDC NUMBER
			// ea:classification:ddc
			$set_field ( 16 );
			$ddc = $get_field_single_value ( '_' );
			$addValue ( 'ea:classification:ddc', $ddc );

			// #############################################
			// FIELD: 18
			// #############################################
			// 18 # LANGUAGE
			// [_] => 1153

			$set_field ( 18 );
			$lang = $get_field_single_value ( '_' );
			$ok_lang = 'ita';
			if ($lang == 'ITA') {
				$ok_lang = 'ita';
			} elseif ($lang == 'CAT') {
				$ok_lang = 'cat';
			} elseif ($lang == 'ENG') {
				$ok_lang = 'eng';
			} elseif ($lang == 'ESP') {
				$ok_lang = 'epo';
			} elseif ($lang == 'FRA') {
				$ok_lang = 'fre';
			} elseif ($lang == 'FRE') {
				$ok_lang = 'fre';
			} elseif ($lang == 'GER') {
				$ok_lang = 'ger';
			} elseif ($lang == 'HOL') {
				$ok_lang = 'dut';
			} elseif ($lang == 'POR') {
				$ok_lang = 'por';
			} elseif ($lang == 'SPA') {
				$ok_lang = 'spa';
			} elseif ($lang == 'SVE') {
				$ok_lang = 'swe';
			} elseif ($lang == 'SWE') {
				$ok_lang = 'swe';
			} else {
				$msg = "ERROR LANG" . $lang;
				error_log ( $msg );
				throw new Exception ( $msg );
			}
			$addValue ( 'dc:language:iso', $ok_lang );

			// ITA Italian ita
			// CAT Catalan cat
			// ENG English eng
			// ESP Esperanto epo
			// FRA ?French (3) fre
			// FRE French fre
			// GER German ger
			// HOL ? Dutch dut
			// POR Portuguese por
			// SPA Spanish spa
			// SVE Swedish (1) swe
			// SWE Swedish (1) swe
			//

			// #############################################
			// FIELD: 19
			// #############################################
			// 19 # COUNTRY
			// [_] => 1153
			$set_field ( 19 );
			$iso_country = $get_field_single_value ( '_' );
			$country = null;
			if ($iso_country == 'IT') {
				$country = 'Italia';
			} elseif ($iso_country == 'FR') {
				$country = 'Francia';
			}

			$iso_country = strtolower ( $iso_country );
			$pub_place = $idata->getValueTextSK ( 'ea:publication:place' );
			// echo("#DEBUG#1# $pub_place : $place_id : $iso_country\n");
			$pub_place = removeSB ( $pub_place );
			// echo("#DEBUG#2# $pub_place : $place_id : $iso_country\n");
			if (empty ( $pub_place ) || ($pub_place == 'S. l.') || ($pub_place == 'S. l') || ($pub_place == 'S.l.') || ($pub_place == 'S.l') || ($pub_place == 'S.l. : s.n.')) {
				$pub_place = $idata->setValueSK ( 'ea:publication:place', 'S.l.' );
				$place_id = null;
			}
			// echo("#DEBUG#3# $pub_place : $place_id : $iso_country\n");

			if (! empty ( $iso_country )) {
				// echo("#DEBUG#4# $pub_place : $place_id : $iso_country\n");
				$addValue ( 'dc:country:iso', $iso_country );
				$pvs = $idata->getValuesByKey ( 'ea:publication:place' );
				foreach ( $pvs as $k => $v ) {
					$place_id = $v [6];
					$addValue ( 'dc:country:iso', $iso_country, array (
							'link' => $place_id
					) );
					if (! empty ( $country )) {
						// //echo("#DEBUG#6# $pub_place : $place_id : $iso_country\n");
						$addValue ( 'ea:country:name', $country, array (
								'link' => $place_id
						) );
					}
				}
			}
			$set_field ( 23 );
			$v = $get_field_single_value ( '_' );
			$addValue ( 'isis:field-23:_', $v );

			$set_field ( 24 );
			$v = $get_field_single_value ( '_' );
			$addValue ( 'isis:field-24:_', $v );
			$set_field ( 25 );
			$v = $get_field_single_value ( '_' );
			$addValue ( 'isis:field-25:_', $v );
			$set_field ( 100 );
			$v = $get_field_single_value ( '_' );
			$addValue ( 'isis:field-100:_', $v );

			// $set_field(22);
			// $call_number =$get_field_single_value('_');
			// if (!empty($call_number)){
			// $idata->addValueSK("ea:call_number:ddc", $call_number,null,null,null,null,null,null,$w++ );
			// if (PUtil::strBeginsWith($call_number, 'FF/A/')){
			// $call_number = substr($call_number, 5);
			// $idata->addValueSK("ea:call_number:ddc", $call_number,null,null,null,null,null,null,$w++ );
			// } elseif(PUtil::strBeginsWith($call_number, 'FF/')){
			// $call_number = substr($call_number, 3);
			// $idata->addValueSK("ea:call_number:ddc", $call_number,null,null,null,null,null,null,$w++ );
			// }
			// }

			if ($print_data) {
				$values = $idata->values;
				foreach ( $values as $k => $vr ) {
					foreach ( $vr as $idx => $v ) {
						if (! PUtil::isEmpty ( $v )) {
							// * 6: record_id
							// * 8: link (pointer) diktis st parent
							if ($v [6] != 0) {
								printf ( "|%3d", $v [6] );
							} else {
								echo "|   ";
							}
							if ($v [8] != 0) {
								printf ( "|%3d", $v [8] );
							} else {
								echo "|   ";
							}
							printf ( "|%-30s", $k );
							echo (' | ');
							$str = $v [0];
							if (strpos ( $str, "\n" ) === FALSE) {
								echo ($str);
							} else {
								echo "\n";
								echo ($str);
								echo "\n";
							}
							echo ("\n");
						}
					}
				}
			}
			;

			$addValue ( DataFields::ea_status, 'finish' );

			if (true) {
				$item_id = PDAO::insert_item ( $userName, $obj_type );
				// $item_id = insert_item($dbh, $userName);
				echo ("INSERT  NEW BOOK: $item_id\n");
				// foreach ($idata->values as $key => $values) {
				// PDao::update_item_metadata($item_id, $key, $values);
				// }
				$out = "";
				save_basic_metadata ( $dbh, $item_id, $idata, $out, array (
						'skip_create_subitems' => true
				) );
				insert_generated_metadata ( $dbh, $item_id, $userName );
				update_bibref ( $dbh, $item_id, true );
				insert_collection ( $dbh, $item_id, $obj_type );
				PDao::touch_item ( $item_id );

				$set_field ( 22 );
				$call_number = $get_field_single_value ( '_' );
				$call_number = str_replace ( ' ', '', $call_number );

				// -------------------------------
				// n : 1431
				// b : 1120
				// v : 33
				// 0 : 5
				// a : 4
				// m : 1
				// _ : 21
				// -------------------------------
				$set_field ( 21 );
				foreach ( $field as $rec ) {
					// $n = $get_value($rec, 'n');
					// $sn = intval($n);
					$sn = intval ( $get_value ( $rec, 'n' ) );
					if (empty ( $sn )) {
						$sn = intval ( $get_value ( $rec, '0' ) );
					}
					if (empty ( $sn )) {
						$sn = intval ( $get_value ( $rec, 'a' ) );
					}
					if (empty ( $sn )) {
						$sn = intval ( $get_value ( $rec, 'm' ) );
					}
					if (! empty ( $sn )) {
						$artifact_uuid = PDao::createUUID ();
						$title = 'artifact: ' . $artifact_uuid;
						$obj_type = 'artifact1';
						$status = 'internal';
						$artifact_idata = new ItemMetadata ();
						$addValue ( DataFields::ea_obj_type, $obj_type, array (
								'idata' => $artifact_idata
						) );
						$addValue ( DataFields::ea_status, $status, array (
								'idata' => $artifact_idata
						) );
						$addValue ( DataFields::dc_title, $title, array (
								'idata' => $artifact_idata
						) );
						$addValue ( 'ea:artifact:uuid', $artifact_uuid, array (
								'idata' => $artifact_idata
						) );
						$addValue ( 'ea:artifact:status', 'available', array (
								'idata' => $artifact_idata
						) );
						$addValue ( 'ea:artifact-of:', $item_id, array (
								'idata' => $artifact_idata
						) );

						// $addValue('ea:call_number:prefix',$call_number,array('idata'=>$artifact_idata));
						// $addValue('ea:call_number:ea',$call_number,array('idata'=>$artifact_idata));

						$cn_prefix = null;
						if (PUtil::strBeginsWith ( $call_number, 'FF/A/' )) {
							$tmp = substr ( $call_number, 5 );
							$cn_prefix = 'FF/A';
						} elseif (PUtil::strBeginsWith ( $call_number, 'FF/' )) {
							$tmp = substr ( $call_number, 3 );
							$cn_prefix = 'FF';
						} else {
							$tmp = $call_number;
						}
						$cnar = explode ( "/", $tmp );
						if (! empty ( $call_number ) && isset ( $cnar [1] )) {
							$ddc = $cnar [0];
							$addValue ( 'ea:call_number:part-a', $cn_prefix, array (
									'idata' => $artifact_idata
							) );
							$addValue ( 'ea:call_number:ddc', $ddc, array (
									'idata' => $artifact_idata
							) );
							$addValue ( 'ea:call_number:ea', $call_number, array (
									'idata' => $artifact_idata
							) );
							$tmp = $cnar [1];
							if (PUtil::strContains ( $tmp, ',' )) {
								$cnar = explode ( ",", $tmp );
								$addValue ( 'ea:call_number:part-c', trim ( $cnar [0] ), array (
										'idata' => $artifact_idata
								) );
								$addValue ( 'ea:call_number:part-d', trim ( $cnar [1] ), array (
										'idata' => $artifact_idata
								) );
							} else {
								$addValue ( 'ea:call_number:part-c', $tmp, array (
										'idata' => $artifact_idata
								) );
							}
						} else {
							echo ("#EEROR: wrong unkown call number: $call_number ITEM: ");
						}

						$addValue ( 'ea:sn:prefix', 'BAG', array (
								'idata' => $artifact_idata
						) );
						$addValue ( 'ea:sn:suffix', $sn, array (
								'idata' => $artifact_idata
						) );
						$sn_full = 'BAG ' . $sn;
						$addValue ( 'ea:sn:', $sn_full, array (
								'idata' => $artifact_idata
						) );

						$note = null;
						$note = $get_record_value_concatenated ( $rec, 'v', $note );
						$note = $get_record_value_concatenated ( $rec, 'a', $note );
						$note = $get_record_value_concatenated ( $rec, 'm', $note );
						$note = $get_record_value_concatenated ( $rec, '0', $note );
						$note = $get_record_value_concatenated ( $rec, '_', $note );
						$addValue ( 'ea:note:generic', $note, array (
								'idata' => $artifact_idata
						) );

						$artifact_item_id = PDAO::insert_item ( $userName, $obj_type );
						echo ("INSERT NEW ARTIFACT: $artifact_item_id\n");
						save_basic_metadata ( $dbh, $artifact_item_id, $artifact_idata, $out );
						insert_generated_metadata ( $dbh, $artifact_item_id, $userName );
						insert_collection ( $dbh, $artifact_item_id, $obj_type );
						// print_r($idata->values);
					} else {
						echo ("#WARN EMPTY SN $sn\n");
						print_r ( $rec );
					}
				}
			} else {
				echo "## SKIP IMPORT ##\n";
				// print_r($idata->values);
			}
			echo ("=====================================================\n");
		}

		$print_one_book = function ($i) use($data) {
			print_book ( $i, $data ['docs'] [$i] );
			echo ("\n=====================================================\n\n");
		};

		$get_subkeys_for_field = function ($field_no) use($data) {
			$skeys = array ();
			foreach ( $data ['docs'] as $i => $book ) {
				if (isset ( $book [$field_no] )) {
					$d = $book [$field_no];
					foreach ( $d as $arr ) {
						foreach ( $arr as $k => $v ) {
							$v = trim ( $v );
							if (! empty ( $v )) {
								if (isset ( $skeys [$k] )) {
									$skeys [$k] += 1;
								} else {
									$skeys [$k] = 1;
								}
							}
						}
					}
				}
			}
			return $skeys;
		};

		$print_subkeys_for_field = function ($field_no) use($data, $get_subkeys_for_field) {
			$skeys = $get_subkeys_for_field ( $field_no );
			echo ("#############################################\n");
			echo ("FIELD: $field_no\n");
			echo ("#############################################\n");
			print_r ( $skeys );
		};

		// PRINT public.metadatafieldregistry INSERT RECORDS
		if (false) {
			$field_numbers = array ();
			foreach ( $data ['docs'] as $i => $book ) {
				foreach ( $book as $fn => $fv ) {
					if (isset ( $field_numbers [$fn] )) {
						$field_numbers [$fn] += 1;
					} else {
						$field_numbers [$fn] = 1;
					}
				}
			}
			ksort ( $field_numbers );
			$schemaId = 5;
			$schema = "isis";
			foreach ( $field_numbers as $field_no => $s1 ) {
				$skeys = $get_subkeys_for_field ( $field_no );
				foreach ( $skeys as $sk => $s2 ) {
					$main = "field-" . $field_no;
					$element = $schema . ':' . $main . ':' . $sk;
					$SQL = sprintf ( "INSERT INTO public.metadatafieldregistry (metadata_schema_id,mschema,element,qualifier,full_element ) values ('%s','%s','%s','%s','%s');", $schemaId, $schema, $main, $sk, $element );
					echo ($SQL);
					echo ("\n");
				}
			}
		}

		// //FIELD NUMBERS STATS
		// /////////////////////////////////
		if (false) {
			$field_numbers = array ();
			foreach ( $data ['docs'] as $i => $book ) {
				foreach ( $book as $fn => $fv ) {
					if (isset ( $field_numbers [$fn] )) {
						$field_numbers [$fn] += 1;
					} else {
						$field_numbers [$fn] = 1;
					}
				}
			}
			ksort ( $field_numbers );
			print_r ( $field_numbers );
		}
		// [1] => 1153
		// [2] => 126
		// [4] => 1153
		// [5] => 1152
		// [6] => 524
		// [7] => 824
		// [8] => 66
		// [9] => 917
		// [10] => 25
		// [12] => 488
		// [13] => 84
		// [15] => 920
		// [16] => 1129
		// [18] => 1153
		// [19] => 1153
		// [21] => 1153
		// [22] => 1122
		// [23] => 1122
		// [24] => 1153
		// [25] => 1153
		// [26] => 1123
		// [30] => 1152
		// [100] => 25

		// ////////////////////////////////////////////////
		// PRINT ALL DATA FOR ONE FIELD TYPE
		// ////////////////////////////////////////////////
		if (false) {
			$field_no = 1;
			$print_subkeys_for_field ( $field_no );
			$subfield = null;
			$subfield = 'h';
			$print_values = true;
			// $print_values = false;
			echo ("#############################################\n");
			echo ("FIELD: $field_no\n");
			echo ("#############################################\n");
			foreach ( $data ['docs'] as $k => $book ) {
				$line1 = print_field_line ( $k, $book, $field_no, true, $subfield, $print_values );
				if (strpos ( $line1, '|' ) !== FALSE) {
					echo ($line1);
				}
			}
		}

		// ////////////////////////////////////////////////
		// FIELD REPORT
		// ////////////////////////////////////////////////
		if (false) {
			$field_no = 100;
			echo ("#############################################\n");
			echo ("FIELD: $field_no\n");
			echo ("#############################################\n");
			$skeys = $get_subkeys_for_field ( $field_no );
			echo ("\n");
			echo ("-------------------------------\n");
			echo ("subfield statistics\n");
			echo ("-------------------------------\n");
			echo ("  : records found\n");
			echo ("-------------------------------\n");
			foreach ( $skeys as $sk => $stats ) {
				echo ("$sk : $stats \n");
			}
			echo ("-------------------------------\n");

			echo ("\n\n");
			echo ("-------------------------------\n");
			echo ("detailed field values\n");
			echo ("-------------------------------\n");
			echo ("book : value\n");
			echo ("-------------------------------\n");
			foreach ( $skeys as $sk => $stats ) {
				foreach ( $data ['docs'] as $k => $book ) {
					$line1 = print_field_line ( $k, $book, $field_no, false, $sk, true );
					if (strpos ( $line1, '|' ) !== FALSE) {
						echo ($line1);
					}
				}
				echo ("\n-------------------------------\n\n");
			}
		}

		// PRINT SPECIFIC BOOKS
		// //////////////////////////////////
		// TITLE
		// $print_one_book(13);
		// $print_one_book(14);
		// $print_one_book(111);
		// $print_one_book(240);
		// $print_one_book(822);
		// $print_one_book(596);
		// $print_one_book(244);

		// $print_one_book(485);

		// $print_one_book(112);

		// $print_one_book(1103);
		// $print_one_book(847);

		// $print_one_book(1118);

		// $print_one_book(1);

		// $print_one_book(1060);
		//
		// $print_one_book(40);
		//
		// $print_one_book(27);

		// IMPORT
		if (true) {
			$c = 0;
			foreach ( $data ['docs'] as $k => $book ) {
				$c ++;
				// if ($c < 20){
				$txt = print_book ( $k, $book, false );
				import_book ( $k, $book, $txt );
				// }
			}
		}

		// ONLY PRINT
		if (false) {
			foreach ( $data ['docs'] as $k => $book ) {
				print_book ( $k, $book, true );
			}
		}

		$import_book_debug = function ($book_no) use($data) {
			echo ("#################################################################\n");
			echo ("BOOK: $book_no\n");
			echo ("#################################################################\n");
			$book = $data ['docs'] [$book_no];
			// print_r($book);
			$txt = print_book ( $book_no, $book, false );
			// echo("-----------------------------------\n");
			import_book ( $book_no, $book, $txt, true );
			echo ("#################################################################\n");
		};

		$print_book = function ($book_no) use($data) {
			echo ("#################################################################\n");
			echo ("BOOK: $book_no\n");
			echo ("#################################################################\n");
			$book = $data ['docs'] [$book_no];
			print_book ( $book_no, $book, true );
			echo ("#################################################################\n");
		};

		// foreach ($data['docs'] as $bn => $book) {
		// $print_book = false;
		// foreach ($book as $k=>$rec){
		// if ($k == 12){
		// foreach($rec as $idx=>$val){
		// if (isset($val['s']) && $val['s']=='9' ){
		// $print_book = true;
		// }
		// }
		// //print_r($rec);
		// }
		// }
		// if ($print_book){
		// print_book($bn,$book,true);
		// echo("#################################################################\n");
		// }
		// }

		//
		// //DEMO IMPORT

		// $print_book(801);
		// $print_book(804);
		// $print_book(816);
		// $print_book(857);

		// $print_book(972);
		// $print_book(1146);
		// $print_book(1135);
		// $print_book(1126);
		// $print_book(1123);
		// $print_book(1124);

		// 1123: |s|3
		// 1124: |s|4
		// 1126: |s|2
		// 1126: |s|5
		// 1135: |s|1

		// IMPORT_BOOK_DEBUG

		// $import_book_debug(69);
		// $import_book_debug(1084);

		// $import_book_debug(317);
		// $import_book_debug(898);
		// $import_book_debug(895);
		// $import_book_debug(104);
		// $import_book_debug(381);

		// $import_book_debug(445);
		// $import_book_debug(1088);
		// $import_book_debug(156);

		// $import_book_debug(832);

		// $import_book_debug(804);
		// $import_book_debug(112);

		// $import_book_debug(1142);
		// $import_book_debug(375);
		// $import_book_debug(77);
		// $import_book_debug(795);

		//
		// $import_book_debug(816);
		// $import_book_debug(275);
		// $import_book_debug(276);
		// $import_book_debug(1016);
		// $import_book_debug(967);
		// $import_book_debug(73);
		// $import_book_debug(514);
		// $import_book_debug(1070);
		//
		//

		//
		//
		//
		//
		// $import_book_debug(1);
		// $import_book_debug(2);
		// $import_book_debug(3);
		// $import_book_debug(5);
		// $import_book_debug(13);
		// $import_book_debug(20);
		// $import_book_debug(51);
		// $import_book_debug(69);
		// $import_book_debug(75);
		// $import_book_debug(104);
		// $import_book_debug(112);
		// $import_book_debug(120);
		// $import_book_debug(121);
		// $import_book_debug(135);
		// $import_book_debug(155);
		// $import_book_debug(230);
		// $import_book_debug(235);
		// $import_book_debug(239);
		// $import_book_debug(262);
		// $import_book_debug(295);
		// $import_book_debug(301);
		// $import_book_debug(304);
		// $import_book_debug(312);
		// $import_book_debug(334);
		// $import_book_debug(338);
		// $import_book_debug(347);
		// $import_book_debug(386);
		// $import_book_debug(398);
		// $import_book_debug(445);
		// $import_book_debug(458);
		// $import_book_debug(485);
		// $import_book_debug(563);
		// $import_book_debug(578);
		// $import_book_debug(580);
		// $import_book_debug(583);
		// $import_book_debug(666);
		// $import_book_debug(693);
		// $import_book_debug(750);
		// $import_book_debug(895);
		// $import_book_debug(898);
		// $import_book_debug(972);
		// $import_book_debug(981);
		// $import_book_debug(1016);
		// $import_book_debug(1073);
		// $import_book_debug(1077);
		// $import_book_debug(1079);
		// $import_book_debug(1095);
		// $import_book_debug(1096);
		// $import_book_debug(1108);
		// $import_book_debug(1118);
		// $import_book_debug(1123);
		// $import_book_debug(1124);
		// $import_book_debug(1126);
		// $import_book_debug(1135);
		// $import_book_debug(1137);
		// $import_book_debug(1146);
		// $import_book_debug(1151);
		//
		//

		// $import_book_debug(69);
	}
}




class RelationDef {
	private $element;
	private $definition;
	/**
	 *
	 * @var RelationControl
	 */
	private $relationControl;
	/**
	 *
	 * @param RelationControl $relationControl
	 * @param array $definition
	 */
	public function __construct($relationControl,$element,$definition=null) {
		$this->element = $element;
		$this->relationControl = $relationControl;
		if (empty($definition)){
			$definition = array();
		}
		$this->definition= $definition;
	}

	public function getRelType(){

		if ($this->isSimetric()){
			return 1;
		}
		if ($this->hasReverseRelation()){
			return 2;
		}

	}


	public function forInferenceReverse(){
		return $this->isSimetric() || $this->hasReverseRelation();
	}

	public function forStep1Rename(){
		return isset($this->definition['step1_rename']);
	}
	public function forStep2Rename(){
		return isset($this->definition['step2_rename']);
	}

	public function getStep1Rename(){
		return isset($this->definition['step1_rename'])? $this->definition['step1_rename'] : null;
	}
	public function getStep2Rename(){
		return isset($this->definition['step2_rename'])? $this->definition['step2_rename'] : null;
	}

	public function getSkipItemLoadRemove(){
		//return  (! ($this->isDirected() || $this->isSimetric()));
		return isset($this->definition['skip_item_load_remove'])? $this->definition['skip_item_load_remove'] : (! ($this->hasReverseRelation() || $this->isSimetric()));
	}

	public function getElement(){
		return $this->element;
	}

	public function isDirected(){
		return isset($this->definition['directed'])? $this->definition['directed'] : true;
	}

	public function isSimetric(){
		return ! $this->isDirected();
	}

	public function isTransitive(){
		return isset($this->definition['transitive'])? $this->definition['transitive'] : false;
	}

	public function getParentElement(){
		return isset($this->definition['parent_element'])? $this->definition['parent_element'] : null;
	}


	public function hasReverseRelation(){
		return isset($this->definition['reverse_relation']) ? ! empty($this->definition['reverse_relation']) : false;
	}

	public function getReverseRelation(){
		if ($this->hasReverseRelation() ){
			$reverse_element = $this->definition['reverse_relation'];
			$reverse_rel = $this->relationControl->getRelation($reverse_element);
			if (empty($reverse_rel)){
				throw new Exception ( 'reverse relation: ' . $reverse_element . ' NOT EXISTS' );
			}
			return $reverse_rel;
		}
		return null;
	}
	public function getTrnasitiveElement(){
		return isset($this->definition['transitive_relation']) ? $this->definition['transitive_relation'] : null;
	}

}



class RelationControl {
	private $rels;

	public function __construct() {
		$this->rels = Setting::get('relation_elements');
		//Log::info(print_r($this->rels,true));
	}


	public function getRelationKeys( $directed= true, $transitive=false){
		$rep = array();
		foreach ($this->rels as $k=>$v){
			if (isset($v['directed']) && !$v['directed'] ){
				$v_directed = false;
			} else {
				$v_directed = true;
			}
			if(isset($v['transitive']) && $v['transitive']){
				$v_transitive = true;
			} else {
				$v_transitive = false;
			}
			if (($directed === $v_directed) && ($transitive === $v_transitive)){
				$rep[] = $k;
			}
		}
		return $rep;
	}

	public function isControledRelation($key){
		$rel = $this->getRelation($key);
		if (emtpy($rel)){
			return false;
		}

		if ($rel->isSimetric()){
			return true;
		}

		if ($rel->hasReverseRelation()){
			return true;
		}

		return false;

	}


	public function isRelation($key){
		return isset($this->rels[$key]);
	}

	public function getRelation($key){
		if (isset($this->rels[$key])){
			return new RelationDef($this, $key, $this->rels[$key]);
		}
		return null;
		//return new RelationDef($this,$key,null);
	}

}




