<?php




class Displaycontext {

	private $map;
	private $stacks = array();

	public function __construct($options) {
		$this->map = $options;
	}


	public function has($key){
		return isset($this->map[$key]);
	}

	public function get($key,$default_value = null){
		if (!isset($this->map[$key])){
			return $default_value;
		}
		return $this->map[$key];
	}

	public function set($key,$val){
		$this->map[$key] = $val;
	}

	private function initStack($key){
		if (!isset($this->stacks[$key])){
			$this->stacks[$key] = array();
		}
		return $this->stacks[$key];
	}

	public function push($key,$val){
		$stack  = $this->initStack($key);
		return array_push($stack,$val);
	}
	public function pop($key,$val){
		$stack  = $this->initStack($key);
		return array_pop($stack,$val);
	}
	public function shift($key,$val){
		$stack  = $this->initStack($key);
		return array_shift($stack,$val);
	}
	public function unshift($key,$val){
		$stack  = $this->initStack($key);
		return array_unshift($stack,$val);
	}


/**
 * @return ItemMetadataAccess
 */
	public function getItemMetadata(){
		return $this->get('___@idata');
	}

/**
 * @return array
 */
	public function getItemBasics(){
		return $this->get('___@item_basics');
	}

	public function getFlags(){
		$ib  = $this->getItemBasics();
		$flags = $ib['flags_json'];
		if (empty($flags)){
			return new ItemFlags(array());
		}
		return new ItemFlags(json_decode($flags));
	}

	/**
	 * @return OpacHelper
	 */
	public function getOpac(){
		$ib  = $this->getItemBasics();
		if (empty($ib) || empty($ib['jdata'])){
			return new OpacHelper(null);
		} else {
			return new OpacHelper($ib['jdata']);
		}
	}



	/**
	 * @return array
	 */
	public function getThumbsBig(){
		$tmp = $this->get('___@thumbs');
		if (!empty($tmp) && isset($tmp['big'])){
			return $tmp['big'];
		}
		return array();
	}

	/**
	 * @return array
	 */
	public function getThumbsDescription(){
		$tmp = $this->get('___@thumbs');
		if (!empty($tmp) && isset($tmp['thumbs_description'])){
			return $tmp['thumbs_description'];
		}
		return array();
	}

	/**
	 * @return array
	 */
	public function getBitstreams(){
		return $this->get('___@bitstreams');
	}
	/**
	 * @return array
	 */
	public function getArticles(){
		return $this->get('___@articles');
	}

	/**
	 *
	 * @return ItemRelations
	 */
	public function getItemRelations(){
		return $this->get('___@item_relations');
	}

	/**
	 * @return array
	 */
	public function getThumbsSmall(){
		$tmp = $this->get('___@thumbs');
		if (!empty($tmp) && isset($tmp['small'])){
			return $tmp['small'];
		}
		return array();
	}

	public function dump(){
		echo('<table>');
			foreach ($this->map as $k=>$v){
				if (!PUtil::strBeginsWith($k, '___@')){
					echo('<tr>');
					echo('<td>');
					echo($k);
					echo('</td>');
					echo('<td>');
					print_r($v);
					echo('</td>');
					echo('</tr>');

				}
			}
		echo('</table>');

	}


}



class ItemRelations {

	private $relationsFrom;
	private $relationsTo;

	public function __construct($relationsFrom,$relationsTo) {
		$this->relationsFrom = $relationsFrom;
		$this->relationsTo = $relationsTo;
	}

	//TODO: optimize this
	private function proc($rel_arr,$relType= null,$sort_comparator =null, $inferred = null){
		if (!empty($relType)){

			if (is_array($relType)){
// 				$rep = array_filter($rel_arr, function($rel) use ($relType) {
// 					foreach ($relType as $relTypeOne){  if ($rel['rel_type'] == $relTypeOne){ return true; }; }
// 					return false;
// 				});
				$rep=array();
				foreach ($relType as $relTypeOne){
					$rep_tmp = array_filter($rel_arr, function($rel) use ($relTypeOne) { return ($rel['rel_type'] == $relTypeOne); });
					$rep = array_merge($rep,$rep_tmp);
				}
			} else {
				$rep = array_filter($rel_arr, function($rel) use ($relType) { return ($rel['rel_type'] == $relType); });
			}
		} else {
			$rep = $rel_arr;
		}

		if (!is_null($inferred)){
			if ($inferred){
				$rep = array_filter($rep, function($rel){ return ($rel['inferred']); });
			} else {
				$rep = array_filter($rep, function($rel){ return (! $rel['inferred']); });
			}
		}

		if (! empty($sort_comparator)){
			usort($rep, $sort_comparator);
		}
		return $rep;
	}
	public function getRelationsFrom($relType  = null,$sort_comparator =null, $inferred = null){
		return $this->proc($this->relationsFrom, $relType,$sort_comparator,$inferred);
	}

	public function getRelationsTo($relType  = null,$sort_comparator = null, $inferred = null){
		return $this->proc($this->relationsTo, $relType,$sort_comparator,$inferred);
	}

	public function getRelationsBoth($relType = null,$sort_comparator  = null, $inferred = null){
		$arr = array_merge($this->relationsFrom,$this->relationsTo);
		return $this->proc($arr, $relType,$sort_comparator,$inferred);
	}


}



class DisplayDispatcher {

	private $commands;
	/**
	 *
	 * @var Displaycontext
	 */
	private $context;

	public function __construct($obj_type, $initOptions = array()) {
	//	echo("INIT $obj_type\n");
		//$this->position = 0;

// 		$dconf = variable_get('patron_display',array());
		$dconf = Config::get('arc_display_command.patron_display');

		$this->commands = $dconf[$obj_type]['commands'];

		$this->context =  new Displaycontext( array_merge($initOptions, $dconf['general_options'], $dconf[$obj_type]['options']));

	}



// EXAMPLE CONFIG
//
// 	$conf['patron_display']['general_options'] = array(
// 	);
// 	$conf['patron_display']['manuscript'] = array(
// 			'options'=>array(
// 			),
// 			'commands'=>array(
// 					array('name'=>'init','options'=>array()),
// 					array('name'=>'hasFoto','options'=>array('response'=>'hasFoto')),
// 					array('name'=>'isUserRepoMaintainer','options'=>array('response'=>'isUserRepoMaintainer')),
// 					array('name'=>'openDiv','options'=>array('condition'=>'hasFoto')),
// 					array('name'=>'title','options'=>array()),
// 					array('name'=>'lineList','options'=>array('key'=>'marc:contributor:scribe','label'=>'scribe')),
// 					array('name'=>'closeDiv','options'=>array('condition'=>'hasFoto')),
// 					array('name'=>'openDiv','options'=>array('condition'=>'hasFoto','id'=>'testId','class'=>'testClass','foo'=>'bar')),
// 					array('name'=>'foto','options'=>array('condition'=>'hasFoto')),
// 					array('name'=>'closeDiv','options'=>array('condition'=>'hasFoto')),
// 					array('name'=>'itemRelations','options'=>array('relation_id'=>'26','direction'=>'from')),
// 					array('name'=>'bitstreams','options'=>array()),

// 			)
// 	);




/**
 *
 * @param DisplayCommand $cmd
 */
	private function executeCmd($cmd_name,$options){
		$condition = null;
		$condition_not = null;
		if (isset($options['condition'])){
			$condition = $options['condition'];
		}
		if (isset($options['condition_not'])){
			$condition_not = $options['condition_not'];
		}

		$context = $this->context;
		$execFlag = true;
		if ($condition != null && $context->has($condition)){
			$execFlag = $context->get($condition);
		}elseif ($condition_not != null && $context->has($condition_not)){
			$execFlag = ! $context->get($condition_not);
		}
		if ($execFlag){
			//error_log('EXEC: '. $cmd_name);
				//echo('<pre>');
				//echo "# $cmd_name \n";
				//print_r($options);
				//cho('</pre>');
			call_user_func('DisplayCommands::' . $cmd_name, $context, $options);
		}
	}


	public function executeCommands(){
		foreach ($this->commands as $idx => $v){
			$cmd_name = $v['name'];
			$options = $v['options'];
			$this->executeCmd($cmd_name, $options);

		}


	}



}

class OpacHelper {
	private $json;


	public function __construct($jdata) {
		if (empty($jdata)) {
			$this->json = array();
		} else {
			$this->json = json_decode($jdata, true);
		}
	}

	/**
	 * gia debuging
	 * @return array
	 */
	public function getJSON() {
		return $this->json;
	}

	/**
	 *
	 * @param string $key
	 * @return boolean
	 */
	public function hasOpac1($key=null) {
		$json = $this->json;
		if (empty($key)) {
			return isset($json['opac1']);
		}
		return (isset($json['opac1']) && isset($json['opac1'][$key]));
	}

	/**
	 *
	 * @param string $key
	 * @return boolean
	 */
	public function hasOpac2($key=null) {
		$json = $this->json;
		if (empty($key)) {
			return isset($json['opac2']);
		}
		return (isset($json['opac2']) && isset($json['opac2'][$key]));
	}


	public function opac1($key) {
		if ($this->hasOpac1($key)) {
			return $this->json['opac1'][$key];
		}
		return null;
	}


	public function opac2($key) {
		if ($this->hasOpac2($key)) {
			return $this->json['opac2'][$key];
		}
		return null;
	}

	public function value($key) {
			return isset($this->json[$key]) ? $this->json[$key] : null;
	}

}





?>