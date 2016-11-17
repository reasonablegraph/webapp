<?php

interface  GRuleContext {

	/**
	 * @return GGraph;
	 */
	public function graph();

	/**
	 * @return GGraph;
	 */
	public function getGraph($index);



	/**
	 * @return string[]
	 */
	public function getEditPropUrns();

	/**
	 * arrayof(element)
	 *
	 * @return string[]
	 */
	public function getEditProps($urnStr);



	public function get($k);
	public function has($k);
	public function getMessages();
	public function getErrors();
	public function getDebugMessages();
	public function clearCommands();
	public function hasCommands();
	public function executeCommands();


	public function getDebugFlag();

}


interface  GRuleContextR extends GRuleContext {

// 	public function graph();
// 	public function getNewEdges();


	/**
	 *
	 * @param unknown $index
	 * @param GGraph $g
	 */
	public function addGraph($index,$g);


	/**
	 *
	 * @param String $v1UrnStr
	 * @param STring $v2UrnStr
	 * @param string $element
	 * @param GURN[] $deps
	 * @return GEdge
	 */
	public function addNewEdge($v1UrnStr, $v2UrnStr, $element, $derivative = true, $deps = null, $label=null);

	public function put($k,$v);
	public function get($k,$default_value = null);
	public function has($k);

	public function getMessages();
	public function addMessage($msg);
	public function getErrors();
	public function addError($msg);
	public function getDebugMessages();
	public function addDebugMessage($msg);
	/**
	 * @return GRule[]
	 */
	public function getPostRules();
	public function hasPostRules();
	public function clearPostRules();


	/**
	 * @param GRule $rule
	 */
	public function addPostRule($rule);

	/**
	 *
	 * @return GCommand[]
	 */
	public function getCommands();
	public function hasCommands();

	/**
	 *
	 * @param string $key
	 * @param GCommand $command
	 */
	public function putCommand($key, $command);
	public function clearCommands();

// 	public function executeCommands();
// 	public function executePostRules();



	/**
	 * @param string $urnStr
	 * @param string $element
	 * @param GPValue $value
	 */
	public function addProperty($urnStr, $element, $value);

	/**
	 * @param string $urnStr
	 * @param string $element
	 * @param string $value
	 */
	public function addPropertyValue($urnStr, $element, $value, $data=null,$lang = null, $weight = null);

	/**
	 * @param string $urnStr
	 * @param string $element
	 * @param string $value
	 */
	public function removePropertyValue($urnStr, $element, $value);


	public function clearProperty($urnStr, $element);


	/**
	 * @param string $urnStr
	 * @param string $element
	 * @param GPValue $value
	 */
	public function resetProperty($element,$value);



	/**
	 *
	 * @param string $urnStr
	 */
	public function removeVertext($urnStr,$syncPersistence);


	/**
	 *
	 * @param string||Gedge $vkey
	 */
	public function removeEdge($vkey);

}


interface GCommand {

	/**
	 *
	 * @param GRuleContextR $context
	 */
	public function execute($context);
//	public function execute();

}

//interface GRule extends GCommand {
interface GRule  {

	public function execute();
}



class GRuleContextO implements GRuleContext, GRuleContextR {

	/**
	 * @var GGraph
	 */
	protected $_graph;
	protected $graphs = array();

	/**
	 * @var GEdge[]
	 */
	//protected $newEdges = array();
	protected $mem;
	protected $messages = array();
	protected $errors = array();
	protected $debugMessages = array();
	protected $debugFlag = false;
	/**
	 * @var GRule[]
	 */
	protected $postRules = array();
	/**
	 * @var GCommand[]
	 */
	protected $commands = array();




	private $main_rules_start;
	private $main_rules_end;

	/**
	 * @param GGraph $graph
	*/
	public function __construct($main_graph,$mem,$complementary_graphs) {
		$this->_graph = $main_graph;
		$this->graphs = $complementary_graphs;
		$this->mem = $mem;
		if (isset($mem['DEBUG_FLAG'])){
			$this->debugFlag = $mem['DEBUG_FLAG'];
		}
// 		$this->main_rules_start = $main_rules_start;
// 		$this->main_rules_end = $main_rules_end;
	}

	public function getDebugFlag(){
		return $this->debugFlag;
	}
	/**
	 * (non-PHPdoc)
	 * @see GRuleContext::graph()
	 */
	public function graph(){
		return $this->_graph;
	}

	/**
	 * (non-PHPdoc)
	 * @see GRuleContext::getGraph()
	 */
	public function getGraph($index){
		return $this->graphs[$index];
	}

	/**
	 * (non-PHPdoc)
	 * @see GRuleContext::addGraph()
	 */
	public function addGraph($index,$g){
		return $this->graphs[$index] = $g;
	}

// 	public function getNewEdges(){
// 		return $this->newEdges;
// 	}


	/**
	 *
	 * @param String $v1UrnStr
	 * @param STring $v2UrnStr
	 * @param string $element
	 * @param GURN[] $deps
	 * @return GEdge
	 */
	public function addNewEdge($v1UrnStr, $v2UrnStr, $element, $derivative = true ,$deps = null,$label=null){
		//Log::info("@@: addNewEdge: " . $v1UrnStr . ' --[' . $element . ']--> ' . $v2UrnStr . ' I: ' .($derivative ? 'T':'F'));
		$ne = $this->_graph->addEdge($v1UrnStr, $v2UrnStr, $element,$derivative, $deps,$label);
		//$this->newEdges[$ne->urnStr()] = $ne;
		return $ne;
	}

	/**
	 *
	 * @param string $urnStr
	 */
	public function removeVertext($urnStr,$syncPersistence){
		$this->_graph->removeVertex($urnStr,$syncPersistence);
	}

	/**
	 *
	 * @param string||Gedge $vkey
	 */
	public function removeEdge($vkey){
		$this->_graph->removeEdge($vkey);
	}

	/**
	 * arrayof(urnStr=>element)
	 *
	 * @var string[]
	 */
	private $props_edit = array();

	/**
	 * @return string[]
	 */
	public function getEditPropUrns(){
		return array_keys($this->props_edit);
	}

	/**
	 * arrayof(element)
	 *
	 * @return string[]
	 */
	public function getEditProps($urnStr){
			return isset($this->props_edit[$urnStr]) ? array_keys($this->props_edit[$urnStr]) : array();
	}


	/**
	 *
	 * @param unknown $urnStr
	 * @throws Exception
	 * @return GVertexO
	 */
	private function getVertex($urnStr){
		$v = $this->_graph->getVertex($urnStr);
		if (empty($v)){
			throw new Exception("canot find vertext $urnStr ");
		}
		return $v;
	}

	private function addPropEditElement($urnStr,$element){
		$this->props_edit[$urnStr][$element]=null;
	}
	/**
	 * @param string $urnStr
	 * @param string $element
	 * @param GPValue $value
	 */
	public function addProperty($urnStr, $element, $value){
		$this->getVertex($urnStr)->addProperty($element, $value);
		$this->addPropEditElement($urnStr, $element);
	}

	/**
	 * @param string $urnStr
	 * @param string $element
	 * @param string $value
	 */
	public function addPropertyValue($urnStr, $element, $value, $data=null,$lang = null, $weight = null){
		$this->getVertex($urnStr)->addPropertyValue($element, $value, $data,$lang,$weight);
		$this->addPropEditElement($urnStr, $element);
	}

	/**
	 * @param string $urnStr
	 * @param string $element
	 * @param string $value
	 */
	public function removePropertyValue($urnStr, $element, $value){
		$v = $this->getVertex($urnStr)->removePropertyValue($element, $value);
		$this->addPropEditElement($urnStr, $element);
	}


	public function clearProperty($urnStr, $element){
		$this->getVertex($urnStr)->clearProperty($element);
		$this->addPropEditElement($urnStr, $element);
	}


	/**
	 * @param string $urnStr
	 * @param string $element
	 * @param GPValue $value
	 */
	public function resetProperty($element,$value){
		$this->getVertex($urnStr)->resetProperty($element, $value);
		$this->addPropEditElement($urnStr, $element);
	}

	public function put($k,$v){
		$this->mem[$k] = $v;
	}
	public function get($k,$default_value =null){
		return isset($this->mem[$k]) ? $this->mem[$k] : $default_value;
	}

	public function has($k){
		return isset($this->mem[$k]);
	}

	public function getMessages(){
		return $this->messages;
	}

	public function addMessage($msg){
		$this->messages[] = $msg;
	}

	public function getErrors(){
		return $this->errors;
	}

	public function addError($msg){
		$this->errors[] = $msg;
	}

	public function getDebugMessages(){
		return $this->debugMessages;
	}

	public function addDebugMessage($msg){
		if ($this->debugFlag){
			$this->debugMessages[] = $msg;
		}
	}

	/**
 	* @return GRule[]
 	*/
	public function getPostRules(){
		return $this->postRules;
	}

	public function hasPostRules(){
		return !empty($this->postRules);
	}

	public function clearPostRules(){
		$this->postRules = array();
	}



	/**
	 * @param GRule $rule
	 */
	public function addPostRule($rule){
		$this->postRules[]=$rule;
	}

	/**
	 *
	 * @return GCommand[]
	 */
	public function getCommands(){
		return $this->commands;
	}

	public function hasCommands(){
		return !empty($this->commands);
	}

	/**
	 *
	 * @param string $key
	 * @param GCommand $command
	 */
	public function putCommand($key, $command){
		$this->commands[$key] = $command;
	}

	public function clearCommands(){
		$this->commands = array();
	}

	public function executeCommands(){
		//$this->addDebugMessage("executeCommands:");
		//Log::info(":executeCommands");
		$commands =  $this->getCommands();
		foreach($commands as $key=>$cmd){
			$this->addDebugMessage("execute command: $key");
			if ($this->debugFlag) {
				Log::info("@@@@execute command: $key");
			}

			try {

				//$t1 = microtime(true);
				$cmd->execute($this);
				//$t2 = microtime(true);
				//Log::info("@@@@COMMAND FINISH: $key :: " . ($t2 - $t1));

			} catch (Exception $e) {

				$err_msg = 'COMMAND: ' . $key . ' ERROR AT EXECUTE exception: ' . $e->getMessage();
				$this->addDebugMessage($err_msg);
				error_log($err_msg);
				Log::error($err_msg);
				Log::info($e);
				if (isset($GLOBALS['GRuleSaveFlagsCmd-ITEMID'])) {
					$err_msg .= ' GRuleSaveFlagsCmd-ITEMID: ' . $GLOBALS['GRuleSaveFlagsCmd-ITEMID'];
				}
				PDao::ruleengine_log($err_msg, $e, 'error');


				$erules = Config::get('arc.RULE_ENGINE_CATCH_EXCEPTION_ON_RULES');
				if ($erules == 'none' ||(is_array($erules) &&  ! in_array($key, $erules) )  ) {
					throw new Exception("RULE ENGINE EXECUTE RULE ERROR");
				} else {
					Log::info("SKIP RULE ENGINE EXCEPTION  at execute rule: " . $key);
				}
			}
		}
		$this->clearCommands();
	}

	public function executePostRules(){
		//Log::info(":executePostRules");
		$c = 0;
		while($this->hasPostRules()){
			$c+=1;
			if ($c > 40){
				throw new Exception("POST RULES EXEC ERROR max loop count");
			}
			$postRules = $this->getPostRules();
			$this->clearPostRules();
			foreach ($postRules as $rule){
				$this->addDebugMessage("execute postRule: " . get_class($rule));
				$rule->execute();
			}
		}
	}




}



class GRuleEngine {

	/**
	 *
	 * @var string[]
	 */
	private $rules;
	private $mem;
	private $graph;
	private $graphs;
	private $main_rules_start;
	private $main_rules_end;

	/**
	 *
	 * @param string[] $rules
	 * @param array $mem
	 * @param GGraph $main_graph
	 * @param GGraph[] $complementary_graphs
	 */
	public function __construct($rules, $mem, $main_graph, $complementary_graphs=array()) {
		$debug =  Config::get('arc_rules.DEBUG',false);
		$mem['DEBUG_FLAG'] = $debug;
		$this->rules = $rules;
		$this->mem = $mem;
		$this->graph = $main_graph;
		$this->graphs = $complementary_graphs;

// 		$this->main_rules_start = Config::get('arc_rules.MAIN_RULES_START',0);
// 		$this->main_rules_end = Config::get('arc_rules.MAIN_RULES_END',0);

	}

	/**
	 * @return string[]
	 */
	public function getRules(){
		return $this->rules;
	}



	private $next_rules = array();

// 	private function execute_rule($ruleName,$rule,$context,$args){
// 		$msg = "execute Rule: " . $ruleName;
// 		Log::info($msg);
// 		$context->addDebugMessage($msg);
// 		$className = 'GRule' . $ruleName;
// 		$rule = new $className($context,$args);
// 		$rule->execute();

// 	}


	private function execute_rule($context,$rule) {
		//$phase = rule[0];
		$ruleName = $rule[1];
		$args = isset($rule[2]) ? $rule[2] : array();


		$msg = "execute Rule: " . $ruleName;
		Log::info('@:: ' . $msg);
		$context->addDebugMessage($msg);


		$className = 'GRule' . trim($ruleName);
		//$orule = new $className($context,$args);
		$orule = (new ReflectionClass($className))->newInstance($context, $args);


		try {

			$orule->execute();

		} catch (Exception $e) {

			$err_msg = 'RULE: ' . $ruleName . ' ERROR AT EXECUTE exception: ' . $e->getMessage();
			$context->addDebugMessage($err_msg);
			error_log($err_msg);
			Log::error($err_msg);
			Log::info($e);
			PDao::ruleengine_log($err_msg, $e, 'error');


			$erules = Config::get('arc.RULE_ENGINE_CATCH_EXCEPTION_ON_RULES');
			if ($erules == 'none' ||(is_array($erules) &&  ! in_array($ruleName, $erules) )  ) {
				throw new Exception("RULE ENGINE EXECUTE RULE ERROR");
			} else {
				Log::info("SKIP RULE ENGINE EXCEPTION  at execute rule: " . $ruleName);
			}
		}


	}

	/**
	 * @param GGraph $g
	 * @return GRuleContext
	 */
	public function execute($init_context_params = array()){
		//Log::info("EXECUTE RULEENGINE");
		$rc =0;
		//$g->removeInferredEdges();
		//Log::info("MEMORY:1:" . memory_get_usage(TRUE) . '  :  ' . memory_get_usage() . '   :  ' . memory_get_peak_usage(). '   :  ' . memory_get_peak_usage(true));
		$context = new GRuleContextO($this->graph, $this->mem,$this->graphs);
		foreach ($init_context_params as $key=>$value){
			//Log::info("INIT CONTEXT PARAM: " . $key . ' : ' . $value);
			$context->put($key, $value);
		}
//  		$context->put('main_rules_start', $this->main_rules_start);
//  		$context->put('main_rules_end',$this->main_rules_end);

		$context->addDebugMessage("# EXECUTE RULES PHASE 0");
		foreach ($this->rules as $rule){
			$phase = $rule[0];
			if (!isset($this->next_rules[$phase])){
					$this->next_rules[$phase] = array();
				}
				$this->next_rules[$phase][]=$rule;
		 }
		//$context->executePostRules();

		$DUMP_GRAPHVIZ = false;
    $DUMP_neighbourhood = false;
		if ($DUMP_GRAPHVIZ) {
			Log::info("********** DUMP GRAPHIZ RULE ENGINE**********");
			Log::info("rm -rf /tmp/rules");
			$tmp = exec('rm -rf /tmp/rules');
		}

		ksort($this->next_rules);
		foreach ($this->next_rules as $k=>$nrules){
			$context->addDebugMessage("# EXECUTE RULES PHASE " . $k);
			$kt = $k < 10 ? '0' . $k : $k;
			foreach ($nrules as $rule){
				$rc+=1;
				//$ruleName = $rule[0];
				//Log::info("execute rule: " . $ruleName);
				//$args = isset($rule[1])?$rule[1] : array();
					//$t1 = microtime(true);
				//print_r($rule);
				//echo "$rule[0]|";
				$this->execute_rule($context, $rule);
				if ($DUMP_GRAPHVIZ) {
					if (!file_exists('/tmp/rules/')){
						mkdir('/tmp/rules/');
					}
					$ruleName = $rule[1];
					$rct = $rc < 10 ? '0' . $rc : $rc;
					//$glabel = 'phase: ' . $kt . ' ord: ' . $rct . ' RULE: ' . $ruleName . ' (after)';
					$glabel = 'ord: ' . $rct . ' phase: ' . $kt . ' RULE: ' . $ruleName . ' (after)';
					$dot_file = '/tmp/rules/rule-' . $kt . '-' . $rct . '_after.dot';
					$dump_file = '/tmp/rules/rule-' . $kt . '-' . $rct . '_after.txt';
					GGraphUtil::dumpDOT($this->graph,array('file'=>$dot_file,'label'=>$glabel,'inferredFlag'=>true,'neighbourhoodFlag'=>$DUMP_neighbourhood,'graph_dump_file'=>$dump_file));
					Log::info("@@:: EXECUTE RULE> PHASE: " . $kt . ' RC: ' . $rct . ' NAME: ' . $ruleName) ;
				}

				//$t2 = microtime(true);
				//Log::info("@@::time RULE FINISH: " . ($t2 - $t1) . '  1:  ' . $rule[0] . '  2: ' . $rule[1]);
			}
			$context->executePostRules();
			if ($DUMP_GRAPHVIZ) {
				$dot_file = '/tmp/rules/phase-' . $kt . '_after.dot';
				$glabel='PHASE ' . $kt . ' AFTER';
				$dump_file = '/tmp/rules/phase-' . $kt . '_after.txt';
				GGraphUtil::dumpDOT($this->graph,array('file'=>$dot_file,'label'=>$glabel,'inferredFlag'=>true,'neighbourhoodFlag'=>true,'graph_dump_file'=>$dump_file));
			}
		}
		$context->executePostRules();
		if ($DUMP_GRAPHVIZ) {
			$dot_file = '/tmp/rules/post_rules_after.dot';
			$glabel='POST RULES AFTER';
			$dump_file = '/tmp/rules/post_rules_after.txt';
			GGraphUtil::dumpDOT($this->graph,array('file'=>$dot_file,'label'=>$glabel,'inferredFlag'=>true,'neighbourhoodFlag'=>$DUMP_neighbourhood,'graph_dump_file'=>$dump_file));
		}

		//$t1 = microtime(true);
		$context->executeCommands();
		if ($DUMP_GRAPHVIZ) {
			$dot_file = '/tmp/rules/commands_after.dot';
			$glabel='COMMANDS AFTER';
			$dump_file = '/tmp/rules/commands_after.txt';
			GGraphUtil::dumpDOT($this->graph,array('file'=>$dot_file,'label'=>$glabel,'inferredFlag'=>true,'neighbourhoodFlag'=>$DUMP_neighbourhood,'graph_dump_file'=>$dump_file));
		}

		//$t2 = microtime(true);
	//	Log::info("@@::time COMMANDS FINISH: " . ($t2 - $t1));
		//Log::info("@@::mem " . memory_get_usage(TRUE) . '  :  ' . memory_get_usage() . '   :  ' . memory_get_peak_usage(). '   :  ' . memory_get_peak_usage(true));
		return $context;
	}







}


class GRuleEngineUtil {



	/**
	 *
	 * @param GGraph $g1
	 * @param GGraph $g2
	 * @param callable $callback
	 */
	public static function findDiff($g1,$g2,$callback){
		$debug = (Config::get('arc.DEBUG_RELATIONS',0) > 0);
		if (!empty($callback)) {
			throw new Exception('findDiff callback expected');
		}
		$des1 = $g1->getInferredEdges();
		$des2 = $g2->getInferredEdges();

		if ($debug){
			//LOGING///////////////////////////////////////////////////////
			Log::info('@@: findDiff: -----------------------------------------------------------');
			foreach ($des1 as $e){
				Log::info('@@: ' . $e->urnStr() .  ' i: ' . ($e->isInferred()?'T':'F'));
			}
			Log::info('@@: findDiff: ##############################################################');
			foreach ($des2 as $e){
				Log::info('@@: ' . $e->urnStr() .  ' i: ' . ($e->isInferred()?'T':'F'));
				//Log::info('@@: ' . $e->urnStr() .  ' i: ' . $e->isInferred()?'T':'F' . ' | ' . print_r($e->data(), true));
			}
			Log::info('@@: findDiff: -----------------------------------------------------------');
			///////////////////////////////////////////////////////////////
		}

		foreach ($des2 as $e){
			$vk = $e->vkey();
			if (isset($des1[$vk])){
				unset($des1[$vk]);
				$callback('OLD',$e);
			}else {
				$callback('NEW',$e);
			}
		}

		foreach ($des1 as $e){
			$callback('DEL',$e);
		}

	}


}





// update dsd.ruleengine_lock set pid = -1, ts_start = (now() - '20 days'::interval) where id = 1;
class GRuleEngineLock {

	private $pid;
	private $locked;
	private $enabled = true;
	public function __construct() {
		$this->pid = getmypid ();
		$this->locked = false;
		if(empty(Config::get('arc.CRUD_LOCK'))){
			$this->enabled = false;
		}
	}

	public function lock() {
		if (!$this->enabled){ return;}
		if ($this->locked){
			$this->insert_log($this->pid . ': ' . 'SKIP LOCK ALLREDY LOCKED');
			return;
		}
		$this->locked = true;
		$sleep_time = 2;
		$exist_max = 8; //* sleep_time = seconds
 		$init_max = 60; //* sleep_time = seconds
 		$max_max = 120; //* sleep_time = seconds
// 		$init_max = 30; //* sleep_time = seconds
// 		$max_max = 40; //* sleep_time = seconds

		$pid = $this->pid;
		//Log::info($pid . ': ' . 'LOCK ');
		$this->insert_log($pid . ': ' .'LOCK ');

		$max = $init_max;
		$c = 0;
		$finish = false;
		while (! $finish){
			while ( ! $this->_lock () && $c < $max ) {
				$c ++;
				Log::info ($pid . ': ' . 'lock sleep : ' . $c );
				sleep($sleep_time);
				//Log::info ( 'ruleengine lock sleep 2: ' . $pid . ' : ' . $c );
			}
			if ($c == $max) {
				Log::info($pid . ': ' . 'FORCE RELESE LOCK: ');
				if (! $this->_lock ( true )){
					$max += $exist_max;
					Log::info($pid . ': ' . 'C: ' . $c . '  NEW MAX: ' . $max);
					if ($max > $max_max){
						$msg = $pid . ': ERROR: ' . 'C: ' . $c . '  NEW MAX TOO BIG ABORD';
						//Log::info($msg);
						$this->insert_log($msg);
						$this->locked = false;
					} else {
						continue;
					}
				}
			}
			$finish = true;
		}
	}

	public function release() {
		if (!$this->enabled){ return;}
 		if (!$this->locked ){
 			//Log::info($this->pid . ': ' . 'RELEASE, ALLREDY UNLOCKED');
 			$this->insert_log($this->pid . ': ' . 'RELEASE, ALLREDY UNLOCKED');
// 			return;
 		}
 		$this->locked = false;


		$pid = $this->pid;

		$SQL = 'UPDATE dsd.ruleengine_lock SET ts_start = null, pid = null WHERE id = 1 AND pid = ?';
		$dbh = dbconnect ();
		$stmt = $dbh->prepare ( $SQL );
		$stmt->bindParam ( 1, $pid, PDO::PARAM_INT );
		$stmt->execute ();
		$count = $stmt->rowCount();
		Log::info($this->pid . ': ' . 'RELEASE ');
	}


	public function _lock($force = false) {
		//Log::info($this->pid . ': ' . '_lock: ' . $force);
		$pid = $this->pid;

		// $SQL="update dsd.ruleengine_lock set ts_start = now() where id = 1 AND (ts_start is null OR ts_start < (now() - '2 minutes'::interval))";
		if ($force) {
			$rep = false;
			//posix_getpgid($pid);
			try {
				$dbh = dbconnect ();
				$dbh->beginTransaction();
				$force_ok = false;
				$SQL = "SELECT pid FROM dsd.ruleengine_lock WHERE id = 1 AND ts_start is not null FOR UPDATE";
				$stmt = $dbh->prepare($SQL);
				$stmt->execute();
				$dpid = null;
				if($rec = $stmt->fetch()){
					$dpid = $rec[0];
					Log::info($pid . ': ' ."DB PID FOUND: " . $dpid);
					if ($pid == $dpid || posix_getpgid($dpid) === false){
						$force_ok = true;
					} else {
						//Log::info($pid . ': ' .'HOST PID FOUND: ' . $dpid . ' RELEASE LOCK ABORD');
						//$this->insert_log($pid . ': ' .'HOST PID FOUND: ' . $dpid . ' RELEASE LOCK ABORD');
					}
				}
				if ($force_ok){
					Log::info($pid . ': ' ."FORCE RELESE LOCK OK: " . $dpid );
					$SQL = 'UPDATE dsd.ruleengine_lock SET ts_start = now(), pid = ? WHERE  id = 1';
					$stmt = $dbh->prepare ( $SQL );
					$stmt->bindParam ( 1, $pid, PDO::PARAM_INT );
					$stmt->execute ();
					$count = $stmt->rowCount ();
					if ($count > 0) {
						$this->insert_log($pid . ': ' .'RELEASE (forced)');
						$rep = true;
					}
				}
				$dbh->commit();
			} catch ( PDOException $e ) {
				$dbh->rollback();
				$error = $e->getMessage ();
				error_log ( $error, 0 );
				throw new Exception ( 'rule engine lock db error: ' . $error );
			}
			return $rep;
		}

		//$SQL = 'UPDATE dsd.ruleengine_lock SET ts_start = now(), pid = ? WHERE  id = 1 AND ts_start is null';
		$SQL = "UPDATE dsd.ruleengine_lock SET ts_start = now(), pid = ? WHERE  id = 1 AND (ts_start is null OR ts_start < (now() - '20 minutes'::interval))";
		try {
			$dbh = dbconnect ();
			// $dbh->beginTransaction();
			$stmt = $dbh->prepare ( $SQL );
			$stmt->bindParam ( 1, $pid, PDO::PARAM_INT );
			$stmt->execute ();
			$count = $stmt->rowCount ();
			if ($count > 0) {
				$this->insert_log($pid . ': ' .'RELEASE');
				return true;
			}
			// $dbh->commit();
		} catch ( PDOException $e ) {
			// $dbh->rollback();
			$error = $e->getMessage ();
			error_log ( $error, 0 );
			throw new Exception ( 'rule engine lock db error: ' . $error );
		}
		return false;
	}




	public function insert_log($msg){
		Log::info($msg);
		//CREATE TABLE dsd.ruleengine_lock_log (id serial primary key, msg varchar, ts timestamp with time zone default now());
		$dbh = dbconnect();
		$SQL = "INSERT INTO dsd.ruleengine_lock_log (msg) VALUES (?)";
		$stmt = $dbh->prepare($SQL);
		$stmt->bindParam(1,$msg);
		$stmt->execute();
	}



}