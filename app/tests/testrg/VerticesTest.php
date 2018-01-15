<?php

require_once __DIR__ . '/GenericRGTestCase.php';

/**
 * @runTestsInSeparateProcesses
 */
class VerticesTest extends GenericRGTestCase {

	public function setUp() {
		parent::setUp();
	}

	private function validateVertex($vkey, $vdata) {
		Log::info("T: VALIDATE VERTEX: " . $vkey);
		//PUtil::logRed(print_r($vdata, true));

		$vertex = $this->test->getVertexByContextKey($vkey);
		$this->assertNotNull($vertex, 'VERTEX: ' . $vkey . ' NOT FOUND');
		if (empty($vertex)){
			return;
		}
		$title = $vertex->getAttribute('title');
		$this->_assertNotEmpty($title, "VERTEX: " . $vkey . " HAS NO TITLE");
		$status = $vertex->getPropertyValue('ea:status:');
		$this->_assertNotEmpty($status, "VERTEX: " . $vkey . " HAS NO STATUS");
		$ot = $vertex->getPropertyValue('ea:obj-type:');
		$this->_assertNotEmpty($ot, "VERTEX: " . $vkey . " HAS NO OBJECT-TYPE");

		$dc_title = $vertex->getPropertyValue('dc:title:');
		$props = $vdata['properties'];
		$this->_assertNotEmpty($props,'validation data has no properties for vertex: ' . $vkey);

		$self = $this;


		$ob_type_prop = null;
		$status_prop = null;
		$actual_dc_title=null;

		$msg1 ="    #ACTUAL FACT RELATIONS:\n";
		$msg2 ="    #ACTUAL INFERENCE RELATIONS:\n";

		$relations_actual=$vertex->getEdges(GDirection::OUT);
		$relations_fact_count_actual  = 0;
		$relations_inf_count_actual = 0;
		foreach ($relations_actual as $edge){
			//Log::info("2: ". $edge->getVertexFrom()->getPropertyValue('ea:test:key1') . ' -[ '. $edge->element() . ' ]-> ' . $edge->getVertexTo()->getPropertyValue('ea:test:key1') . ' : ' . $edge->label());
			$msg= '     ' . $edge->getVertexFrom()->getPropertyValue('ea:test:key1') . ' -[ '. $edge->element() . ' ]-> ' . $edge->getVertexTo()->getPropertyValue('ea:test:key1') . ' : ' . $edge->label()."\n";
			if (!$edge->isInferred()){
				$msg1 .= $msg;
				$relations_fact_count_actual+=1;
			} else {
				$msg2 .= $msg;
				$relations_inf_count_actual+=1;
			}
		}



		$relations_fact_count = 0;
		$relations_inf_count = 0;
		$relations_expected = array();

		$msg1 .= "    #EXPECTED FACT RELATIONS:\n";
		$msg2 .= "    #EXPECTED INFERENCE RELATIONS:\n";
		foreach ($props as $p){

			if ($p['element'] == 'ea:obj-type:'){
				$ob_type_prop = $p['text_value'];
			}
			if ($p['element'] == 'dc:title:'){
				$actual_dc_title = $p['text_value'];
			}
			if ($p['element'] == 'ea:status:'){
				$status_prop = $p['text_value'];
			}


			if (! empty($p['ref_key'])){
				$relations_expected[]= $p;
				//Log::info("1: " . $p['from_key'] . ' -[ '. $p['element'] . ' ]-> ' . $p['ref_key'] . ' : ' . $p['text_value']);
				$msg="     " . $p['from_key'] . ' -[ '. $p['element'] . ' ]-> ' . $p['ref_key'] . ' : ' . $p['text_value']."\n";
				if (!$p['inferred']){
					$relations_fact_count+=1;
					$msg1 .= $msg;
				} else {
					$relations_inf_count+=1;
					$msg2 .= $msg;
				}
			}

		}


		$this->_assertSame($relations_fact_count,$relations_fact_count_actual,"fact relation count differs for: " . $vkey . ' ('. $relations_fact_count_actual . ' <> ' . $relations_fact_count .  ") \n". rtrim($msg1));
		$this->_assertSame($relations_inf_count,$relations_inf_count_actual,"inference relation count differs: "  . $vkey . ' ('. $relations_inf_count_actual . ' <> ' . $relations_inf_count .  ") \n". rtrim($msg2));




		$this->_assertNotEmpty($status_prop, "no property status found for vertex: " + $vkey);
		$this->_assertSame($status_prop,$status, "property & item status differ: " + $vkey);
		$this->_assertNotEmpty($ob_type_prop, "no property obj_type found for vertex: " + $vkey);
		$this->_assertSame($ob_type_prop, $ot, "property & item obj_type differ: " + $vkey);


		$this->_assertSame($dc_title, $actual_dc_title, "DC:TITLE NOT MATCH: " . $vkey ) ;

		if (isset($vdata['title'])) {
			$c_tmp = $vdata['title'];
			//Log::info($c_tmp);
			$this->_assertSame($c_tmp, $title, "TITLE NOT MATCH: " . $vkey );
		}
//		if (isset($vdata['label'])) {
//			$label = $vertex->getAttribute('label');
//			$c_tmp = $vdata['label'];
//			$this->_assertSame($c_tmp, $label, "label NOT MATCH: "  . $vkey );
//		}
		if (isset($vdata['obj_type'])) {
			$obj_type = $vertex->getObjectType();
			$c_tmp = $vdata['obj_type'];
			$this->_assertSame($c_tmp, $obj_type, "obj_type NOT MATCH: " . $vkey );
		}

		if (isset($vdata['jdata'])) {
			$jdata = $vdata['jdata'];
			$attrs= $vertex->getAttributes();
			//Log::info(print_r($attrs,true));
			$this->_assertTrue(isset($attrs['opac1']),'NODE HAS NO OPAC1 ' . $vkey );
			$this->_assertTrue(isset($jdata['opac1']),'JDATA HAS NO OPAC1 ' . $vkey );
			$org_opac1 = $attrs['opac1'];
			$valid_opac1=$jdata['opac1'];


			$check_opac1_key = function($key)use($valid_opac1,$org_opac1,$self){
				if (isset($valid_opac1[$key]) || isset($org_opac1[$key])){
					$self->_assertTrue(isset($valid_opac1[$key]),'expected jdata ' . $key . ' missing');
					$self->_assertTrue(isset($org_opac1[$key]),'jdata ' . $key . ' missing');
					if (isset($valid_opac1[$key]) && isset($org_opac1[$key])){
						$self->_assertSame($valid_opac1[$key], $org_opac1[$key], 'jdata ' . $key . ' differs') ;
					}
				}
			};

			$check_opac1_counts = function($key)use($valid_opac1,$org_opac1,$self){
				if (isset($valid_opac1[$key]) || isset($org_opac1[$key])){
					$self->_assertTrue(isset($valid_opac1[$key]),'expected jdata ' . $key . ' missing');
					$self->_assertTrue(isset($org_opac1[$key]),'jdata ' . $key . ' missing');
					if (isset($valid_opac1[$key]) && isset($org_opac1[$key])){
						$self->_assertSame(count($valid_opac1[$key]), count($org_opac1[$key]), 'jdata ' . $key . ' count differs') ;
					}
				}
			};

			$check_opac1_key('title');
			$check_opac1_key('label');
			$check_opac1_key('obj_type');
			$check_opac1_key('publication');
			$check_opac1_key('as_subj');
			$check_opac1_counts('items');
			$check_opac1_counts('public_title');
			$check_opac1_counts('authors');


			//Log::info(json_encode($valid_opac1));
			//$this->_assertSame($valid_opac1, $org_opac1, "opac1 NOT MATCH");

			if (isset($valid_opac1['manifestations']) && ! empty($valid_opac1['manifestations'])){
				Log::info("T: --> check manifestations");
				$this->_assertTrue(isset($org_opac1['manifestations']),'NODE HAS NO OPAC1 manifestations ' . $vkey );
				$valid_manif =  $valid_opac1['manifestations'];
				$org_manif = $org_opac1['manifestations'];
				$valid_manif_count = count($valid_manif);
				$org_manif_count =  count($org_manif);
				$this->_assertSame($valid_manif_count,$org_manif_count,"MANIFESTATIONS COUNT DIFFERS " . $vkey );
				$valid_m1 = $valid_manif[0];
				$org_m1 = $org_manif[0];
				$this->_assertTrue(isset($org_m1['title']),'NODE HAS NO manif[0] title ' . $vkey );
				Log::info("T: NODE MANIF TITLE: " . $org_m1['title']);
				$this->_assertSame($valid_m1['title'],$org_m1['title'], 'manif[0] titles differ ' . $vkey );
			}


			$skip_neigborhood  = $this->contextGet('PHPUNIT_SKIP_NEIGBORHOOD',false);
			if (!$skip_neigborhood) {
				if (isset($jdata['neighbourhood']) && isset($jdata['neighbourhood']['def'])) {
					$valid_neighborhood = $jdata['neighbourhood']['def'];
					//PUtil::logRed(print_r($valid_neighborhood,true));

					$org_neighborhood = null;
					if (!(isset($attrs['neighbourhood']) && isset($attrs['neighbourhood']['def']))) {
						$self->_assertNotNull($org_neighborhood, 'vertex neighbourhood data missing');
					} else {
						//PUtil::logRed("#2 ...");
						$org_neighborhood = $attrs['neighbourhood']['def'];
						$c1 = count($valid_neighborhood);
						$c2 = count($org_neighborhood);
						$self->_assertSame($c1, $c2, "neighborhood size differs");
						foreach ($valid_neighborhood as $vn) {
							$vd = $this->getValidVeticeVdataWithId($vn);
							$valid_key = $vd['TEST_KEY'];
							$gvertex = $this->test->getVertexByContextKey($valid_key);
							$this->_assertNotNull($gvertex, 'canot find neigbor: ' . $vn);
						}
					}
				}
			}


		}







	}

	private function validateRelation($rel) {
		$inferred = (isset($rel['inferred']) && $rel['inferred'] === true) ? true : false;
		$reltype =  (isset($rel['rel'])) ? $rel['rel'] : null;
		$this->test->checkRelation($rel['from'], $rel['to'], $rel['element'], $inferred, $reltype,$rel['text_value']);
	}

	private function getVdata(){
		$vdata = $this->contextGet('validation_data');
		if (empty($vdata)) {
			throw new Exception('validation_data MISSING');
		}
		return $vdata;
	}

	private function getValidVeticeVdataWithId($id){
		$vdata = $this->getVdata();
		$vertices_vdata = $vdata['vertices'];
		foreach ($vertices_vdata as $k => $vertice_data){
			if (! isset($vertice_data['id'])) {
				throw new Exception('validation data id missing');
			}
			if ($vertice_data['id'] == $id){
				$vertice_data['TEST_KEY'] = $k;
				return $vertice_data;
			}
		}
		return null;
	}








	/////////////////////////////////////////////////////////////////////////////////////////
	//TEST1
	/////////////////////////////////////////////////////////////////////////////////////////
	public function testRelations() {
		Log::info("T: testRelations");
		$vdata = $this->getVdata();
		if (isset($vdata['relations'])) {
			foreach ($vdata['relations'] as $rkey => $rdata) {
				$this->validateRelation($rdata);
			}
		}

	}

	/////////////////////////////////////////////////////////////////////////////////////////
	//TEST2
	/////////////////////////////////////////////////////////////////////////////////////////
	public function testVertices() {
		Log::info("T: testVertices");
		//$this->contextDump();

		$vdata = $this->getVdata();

		$vertices_vdata = $vdata['vertices'];
		if (empty($vertices_vdata)) {
			return null;
		}

		$properties_vdata = $vdata['properties'];


		foreach ($vertices_vdata as $vkey => $verdata) {
			$props = array();
			foreach ($properties_vdata as $pk => $pv) {
				if ($pv['from_key'] == $vkey) {
					$props[] = $pv;
				}
			}
			$verdata['properties'] = $props;
			$this->validateVertex($vkey, $verdata);
		}
	}




}