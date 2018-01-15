<?php


// cat $INS_HOME/dev/db/src/sql/views.sql |psql  $ARC_DB
// cat $INS_HOME/dev/db/src/sql/functions-solr.sql  |psql  $ARC_DB
//
//  SELECT solr_rec_type, obj_type, form_type, count(*) from dsd.admin_item_v5 group by 1,2,3 order by 1,2,3;
//
//SELECT * from dsd.solr_data1('categories_l1');
//SELECT * from dsd.solr_data2('categories_l1','m_categories_l1');
//SELECT * from dsd.solr_data3('categories_l1','m_categories_l1','w_categories_l1');
//SELECT * from dsd.solr_data4('categories_l1','m_categories_l1','w_categories_l1','categories_l2');
//SELECT * from dsd.solr_data5('categories_l1','m_categories_l1','w_categories_l1','categories_l2','w_categories_l2');
//SELECT * from dsd.solr_data6('categories_l1','m_categories_l1','w_categories_l1','categories_l2','w_categories_l2','m_categories_l2');
//
// SELECT item_id,solr_rec_type,form_type,label,s1,s2 from dsd.solr_data2('m_categories_l1','publishers') WHERE s2::jsonb ?|array['PUBLISHER2‡13'] order by 3,4;
//


class GRuleSolr extends AbstractGruleProcessVertice {

  private $work_contributor_elements = null;
  private $expression_contributor_elements = null;
  private $manifestation_contributor_elements = null;


  private $cache = array();
  /**
   * trexon vertex
   * @var GVertex
   */
  private $v;

  /**
   *trexon solrData for vertex (processVertex)
   * @var VertexSolrWorkData
   */
  private $solrData;

  private $solrDataArray;


  public function __construct(GRuleContextR $context, $args) {
    $this->solrDataArray = new ArrayObject();
    parent::__construct($context, $args);
  }


  protected function init() {

    $this->context->addDebugMessage("SOLR RULE INIT");

    $this->work_contributor_elements = array_keys(Setting::get('contributor_work_type_map'));
    $this->expression_contributor_elements = array_keys(Setting::get('contributor_express_type_map'));
    $this->manifestation_contributor_elements = array_keys(Setting::get('contributor_manif_type_map'));

    if (Config::get('arc.ENABLE_SOLR', 1) > 0) {
      $cmd = new GRuleUpdateSolrCmd("opac_index", $this->context);
      //if (config::get('arc.FTS_METHOD',1) == 1) {
      $this->context->putCommand('V_UPDATE_SOLR', $cmd);
      //}
    }

    $this->skip_readonly = true;
  }

  private function getCached($key) {
    if (isset($this->cache[$key])) {
      return $this->cache[$key];
    }
    return null;
  }

  private function setCached($key, $value) {
    $this->cache[$key] = $value;
    return $value;
  }


  private function getManifestationsFromWork($v=null){
    if (empty($v)){
      $v = $this->v;
    }
    $id = $v->id();
    $key = 'work_manifs_' . $id;
    $rep = $this->getCached($key);
    if (!empty($rep)) {
      return $rep;
    }
    //////////////////////////////////////////////////////////////
    $manifestationsRaw = $v->getVertices(GDirection::IN, 'ea:work:');
    $manifestationsInferred = $v->getVertices(GDirection::IN, 'inferred:ea:work:');
    $rep = array_merge($manifestationsRaw, $manifestationsInferred);
    /////////////////////////////////////////
    return $this->setCached($key, $rep);
  }

  private function getWorksFromManifestation($v=null) {
    if (empty($v)){
      $v = $this->v;
    }
    $id = $v->id();
    $key = 'manif_works_' . $id;
    $rep = $this->getCached($key);
    if (!empty($rep)) {
      return $rep;
    }

    /////////////////////////////////////////
    $direct = array();
    $indirect = array();
    if (!empty($v->getVertices(GDirection::OUT, 'ea:work:'))) {
      $direct = $v->getVertices(GDirection::OUT, 'ea:work:');
    }
    if (!empty($v->getVertices(GDirection::OUT, 'inferred:ea:work:'))) {
      $indirect = $v->getVertices(GDirection::OUT, 'inferred:ea:work:');
    }
    $rep = array_merge($direct, $indirect);
    /////////////////////////////////////////

    return $this->setCached($key, $rep);
  }


  private function _addTermLang($v, $element) {
  	$term_languages = $v->getProperties($element);
  	if (!empty($term_languages)) {
  		foreach ($term_languages as $fprop) {
  			$tmp = $fprop->value();
  			if($tmp != 'undefined'){
  				$this->solrData->add("term_lang", $fprop->value());
  			}
  		}
  	}
  }


  private function _addSolrData($vertices, $nameIdKeys = null, $idKeys = null, $nameKeys = null, $valueKey='dc:title:') {
    //PUtil::logRed('_add_solr_data: ' . $nameIdKeys . ' :: ' . count($vertices));
    $solrData = $this->solrData;
    /* @var $rv GVertex */

    if (empty($vertices)) {
      return;
    }

    $fn = function ($keys, $value, $id = null) use ($solrData) {
      if (empty($keys)) { return; }
        if (is_array($keys)) {
          foreach ($keys as $key) {
            $solrData->add($key, $value, $id);
          }
        } else {
          $solrData->add($keys, $value, $id);
        }
    };

    foreach ($vertices as $rv) {
      $id = $rv->id();
      $value = $rv->getPropertyValue($valueKey);
      $fn($nameIdKeys, $value,$id);
      $fn($idKeys, $id);
      $fn($nameKeys, $value);
    }
  }

  private function _addRelation($v, $direction, $element, $solr_key_name_value = null, $solr_key_id = null) {
    $vertices = $v->getVertices(GDirection::OUT, $element);
    $this->_addSolrData($vertices, $solr_key_name_value, $solr_key_id);
  }


  private function getRecordType() {
    return $this->solrData->get('record_type');
  }


  private function _addContributors($v, $contributors) {
	  if (!empty($contributors)){
	  	foreach ($contributors as $w_contributor){
	  		$this->_addRelation($v, GDirection::OUT, $contributors, 'contributors');
	  	}
  	}
  }



//  $this->_addSubjects($v, true, true, false,true);//EXPRESS SUbJECTS FROM EXPRESS


//  //$this->_addSubjects($manif,false,false,false);//WORK SUBJECTS FROM MANIF

//  $this->_addSubjects($v, true, true, false,true);//WORK SUBJECTS FROM WORK
//  $this->_addSubjects($v, false, false, true,true); //MANIFESTATION SUBJECTS  FROM MANIF
//  $this->_addSubjects($work, true, false, false,false); //MANIFESTATION SUBJECTS  FROM WORKS


//
//_addSubjects($v, true, true, false);//WORK SUBJECTS FROM WORK
//_addSubjects($v, true, true, false);//EXPRESS SUbJECTS FROM EXPRESS
//_addSubjects($v, false, false, true); //MANIFESTATION SUBJECTS  FROM MANIF
//_addSubjects($work, true, false, false); //MANIFESTATION SUBJECTS  FROM WORKS

//  private function _addSubjects($v, $add_subjects = false, $add_subjects_ids = false, $add_subjects_manif = false, $add_subjects_all = false) {
//    $solrData = $this->solrData;
//    //SUBJECTS
//    $fn = function ($i, $link, $chain) use ($solrData, $add_subjects, $add_subjects_ids, $add_subjects_manif, $add_subjects_all) {
//      $lid = $link->id();
//      $value = GRuleUtil::getLabel($link);
//      if ($add_subjects) {
//        $solrData->add("subjects", $value, $lid);
//      }
//      if ($add_subjects_ids) {
//        $solrData->add("subjects_ids", $lid);
//      }
//      if ($add_subjects_manif) {
//        $solrData->add("subjects_manif", $value, $lid);
//      }
//      if ($add_subjects_all) {
//        $solrData->add("subjects_all", $value, $lid);
//      }
//
//    };
//    GRuleUtil::travesrseSubjectsLinks($v, $fn);
//  }



//   private function _addSubject($v, $add_w = false, $add_m = false) {
//     $solrData = $this->solrData;
//     //SUBJECTS
//     $fn = function ($i, $link, $chain) use ($solrData, $add_w, $add_m) {
//       $lid = $link->id();
//       $value = GRuleUtil::getLabel($link);
//       if ($add_w) {
//         $solrData->add("w_subjects", $value, $lid);
//       }
//       if ($add_m) {
//         $solrData->add("m_subjects", $value, $lid);
//       }
//     };
//     GRuleUtil::travesrseSubjectsLinks($v, $fn);
//   }



  // SUBJECTS

  /***
   * @param string $element
   */
  private function _addSubjects() {
  	//$self = $this;
  	$solrData = $this->solrData;

  	$add_subjects = function (GVertex $v, $solr_key_prefix = null, $exclude_ids = array()) use ($solrData) {
  		$rep_arr = array();
  		$add_subject = function ($solr_key, $subject) use ($solrData, &$rep_arr, $exclude_ids) {
  			$subj_id = $subject->id();
  			if (in_array($subj_id, $exclude_ids)) {
  				return null;
  			}
  			if ($solrData->add($solr_key, $subject->getPropertyValue('dc:title:'), $subj_id)) {
  				$rep_arr[] = $subj_id;
  				return $subj_id;
  			}
  			return null;
  		};

  		$ot = $v->getObjectType();
  		$subjects = $v->getVertices(GDirection::OUT, 'ea:subj:');
  		$solr_key = $solr_key_prefix . 'subjects';
  		foreach ($subjects as $subject) {
  			if ($subject->getObjectType() == 'subject-chain') {
  				$subject_links = $subject->getVertices(GDirection::OUT,'ea:inferred-chain-link:');
  				foreach ($subject_links as $sl){
  					$add_subject($solr_key, $sl);
  				}
  			} else {
						$add_subject($solr_key, $subject);
  			}
  		}
  		return $rep_arr;
  	};

  	$v = $this->v;
  	$rt = $this->getRecordType();
  	if ($rt == 'manifestation') {
  		$add_subjects($v, 'a_');
  		$add_subjects($v, 'm_');
  		$works = $this->getWorksFromManifestation();
  		foreach ($works as $w) {
  			$ids = $add_subjects($w, 'm_');
  		}
  	} elseif ($rt == 'work') {
  		$add_subjects($v, 'a_');
  		$add_subjects($v, 'm_');
  		}
  }


// CATEGORIES

  /***
   * @param string $element
   */
  private function _addCategories() {
    //$self = $this;
    $solrData = $this->solrData;

    $add_categories = function (GVertex $v, $solr_key_prefix = null, $exclude_ids = array()) use ($solrData) {
      $rep_arr = array();
      $add_category = function ($solr_key, $category) use ($solrData, &$rep_arr, $exclude_ids) {
        $cat_id = $category->id();
        if (in_array($cat_id, $exclude_ids)) {
          return null;
        }
        if ($solrData->add($solr_key, $category->getPropertyValue('dc:title:'), $cat_id)) {

          $rep_arr[] = $cat_id;
          return $cat_id;
        }
        return null;
      };

      $ot = $v->getObjectType();
      $category_elements = array('periodic' => 'ea:periodic:category', 'auth-manifestation' => 'ea:manif:subjectCategory','lemma' => 'ea:lemma:category',);
      $element = (isset($category_elements[$ot])) ? $category_elements[$ot] : 'ea:work:subjectCategory';
      $categories = $v->getVertices(GDirection::OUT, $element);
      $solr_key1 = $solr_key_prefix . 'categories_l1';
      $solr_key2 = $solr_key_prefix . 'categories_l2';
      foreach ($categories as $category) {
        if (!$category->hasFlag('IS:category')) {
          continue;
        }
        if ($category->hasFlag('LEVEL:1')) {
          $add_category($solr_key1, $category); //$sub_categories = $category->getVertices(GDirection::IN, 'ea:concept:category_parent');
        } elseif ($category->hasFlag('LEVEL:2')) {
          $add_category($solr_key2, $category);

          //$parent_category = $category->getFirstVertex(GDirection::OUT, 'ea:concept:category_parent');

          $parent_cat_elements = array('lemma' => array('element' => 'ea:category:child', 'direction'=>GDirection::IN));
          $element = (isset($parent_cat_elements[$ot]['element'])) ? $parent_cat_elements[$ot]['element'] : 'ea:concept:category_parent';
          $direction = (isset($parent_cat_elements[$ot]['direction'])) ? $parent_cat_elements[$ot]['direction'] : GDirection::OUT;
          $parent_category = $category->getFirstVertex( $direction , $element);

          if (!empty($parent_category) && ($parent_category->hasFlag('LEVEL:1'))) {
            $add_category($solr_key1, $parent_category);
          }
        }
      }
      return $rep_arr;
    };

    $v = $this->v;
    $rt = $this->getRecordType();
    if ($rt == 'manifestation') {
      $add_categories($v, 'a_');
      $add_categories($v, 'm_');
      $works = $this->getWorksFromManifestation();
      foreach ($works as $w) {
        $ids = $add_categories($w, 'm_');
      }
    } elseif ($rt == 'work') {
      $add_categories($v, 'a_');
      $add_categories($v, 'm_');
//      $manifs = $this->getManifestationsFromWork();
//      foreach ($manifs as $m){
//        $add_categories($m, 'm_');
//      }
      } elseif ($rt == 'lemma') {
      $add_categories($v, 'l_');
    }
  }




  private function _addOpacData($v, $key = 'opac1') {
    $opacdata = $v->getAttribute($key);
    $this->solrData->opac1 = json_encode($opacdata, JSON_UNESCAPED_UNICODE);
  }


  private function _addBasics($v, $record_type, $form_type = null) {
    $form_type = empty($form_type) ? $record_type : $form_type;
    // assign record type
    $this->solrData->set('record_type', $record_type);
    if (!empty($form_type)) {
      $this->solrData->set('form_type', $form_type);
    }
    // MAIN VERTEX ATTRIBUTES
    $this->solrData->set('id', $v->getPropertyValue('ea:identifier:id'));
    $this->solrData->set('object_type', $v->getPropertyValue('ea:obj-type:'));
    $this->_addOpacData($v);
    $this->solrData->set('title', $v->getPropertyValue('dc:title:'));
    $this->solrData->set('label', GRuleUtil::getLabel($v));
    return true;
  }




//
//  private function addPublicationInfo() {
//
//    /* @var GVertex $v */
//    $v = $this->v;
//    $solrData = $this->solrData;
//
//
//    $publishers = $v->getVertices(GDirection::OUT, 'ea:periodic:publisher_name');
//    foreach ($publishers as $publisher) {
//      $title = $publisher->getPropertyValue('dc:title:');
//      $id = $publisher->id();
//      if (!empty($key1)){$this->solrData->add($key1, $id);}
//      if (!empty($key2)){$this->solrData->add($key2, $title, $id);}
//    }
//
//  }
//





  private function _addPublicationInfoDirect($v, $prefix = '',$add_id_flag=true) {
//    $ot = $v->getPropertyValue('ea:obj-type:');
//    $add_id_flag = ($ot == 'auth-manifestation' || $ot == 'periodic');

    $publishers = $v->getVertices(GDirection::OUT, 'ea:periodic:publisher_name');
    if (!empty($publishers)) {
      foreach ($publishers as $publisher) {
        $title = $publisher->getPropertyValue('dc:title:');
        $id = $publisher->id();
//        $this->solrData->add($prefix . "publishers_id", $id);
        if ($add_id_flag) {
          $this->solrData->add("publishers_id", $id);
        }
        $this->solrData->add($prefix . "publishers", $title, $id);
        //$this->solrData->add("publishers", $title, $id);
      }
    }

    $publication_places = $v->getVertices(GDirection::OUT, 'ea:periodic:publication_place');
    if (!empty($publication_places)) {
      foreach ($publication_places as $publication_place) {
        $title = $publication_place->getPropertyValue('dc:title:');
        $id = $publication_place->id();
        //$this->solrData->add($prefix . "pubplaces_id", $id);
        if ($add_id_flag) {
          $this->solrData->add("pubplaces_id", $id);
        }
        $this->solrData->add($prefix . "pubplaces", $title, $id);
        //$this->solrData->add('pubplaces', $title, $id);
      }
    }
  }

  /**
   * @param GVertex $v
   * @param string $prefix
   * @param bool $add_id_flag
   */
  private function _addPublicationInfo($v, $prefix = '', $add_id_flag=true) {

    $g = $v->graph();
    //PUBLICATION PLACE - PUBLISHER NAME
    $ps = $v->getProperties('ea:manif:Publication');
    if (empty($ps)) {
      return;
    }
    foreach ($ps as $id => $p) {
      $tid = $p->treeId();
      if (empty($tid)) {
        continue;
      };
      $tps = $v->getChildProperties($tid);
      foreach ($tps as $tp) {
        //$prps = $tp->prps();
        $tp_ref = $tp->refItem();

        $tp_value = (empty($tp_ref) ? '[' . $tp->value() . ']' : $tp->value());
        //$tp_value = (empty($tp_ref) ? '[' . $tp->value() . ']' : $g->getVertexByPersisteceId($tp_ref)->getAttribute('dc:title:'));
        if ($tp->element() == 'ea:manif:Publication_Place') {
          if ($add_id_flag) {
            $this->solrData->add("pubplaces_id", $tp_ref);
          }
          $this->solrData->add($prefix . "pubplaces", $tp_value, $tp_ref);
        } elseif ($tp->element() == 'ea:manif:Publisher_Name') {
          //$tp_value = (empty($tp_ref) ? '[' . $tp->value() . ']' : $g->getVertexByPersisteceId($tp_ref)->getAttribute('dc:title:'));
          if ($add_id_flag) {
            $this->solrData->add("publishers_id", $tp_ref);
          }
          $this->solrData->add($prefix . "publishers", $tp_value, $tp_ref);
        }
      }
    }
  }


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//                                 __ ___  ____      _     _______   ______  _____ __
// _ __  _ __ ___   ___ ___  ___  | _/ _ \| __ )    | |   |_   _\ \ / /  _ \| ____|_ |
//| '_ \| '__/ _ \ / __/ _ \/ __| | | | | |  _ \ _  | |_____| |  \ V /| |_) |  _|  | |
//| |_) | | | (_) | (_|  __/\__ \ | | |_| | |_) | |_| |_____| |   | | |  __/| |___ | |
//| .__/|_|  \___/ \___\___||___/ | |\___/|____/ \___/      |_|   |_| |_|   |_____|| |
//|_|                             |__|                                            |__|
//
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

  /**
   *
   * @param GVertex $v
   */
  private function processWork($v) {

  	$project = Config::get('arc.PROJECT_NAME');

    $display_orphan_work = Config::get('arc.SOLR_DISPLAY_ORPHAN_WORK', 0);
    if (!(!empty($v->getVertices(GDirection::IN, 'inferred:ea:work:')) || !empty($v->getVertices(GDirection::IN, 'ea:work:')) || $display_orphan_work)) {
      return;
    }

    $solrData = $this->solrData;
    // assign record type
//     $solrData->record_type = 'work';
//     $solrData->form_type = 'work';
    $this->_addBasics($v, 'work', 'work');

    // MAIN VERTEX ATTRIBUTES
    $solrData->id = $v->getPropertyValue('ea:identifier:id');
    $solrData->object_type = $v->getPropertyValue('ea:obj-type:');
    $this->_addOpacData($v);
    $solrData->title = $v->getPropertyValue('dc:title:');
    $this->solrData->label = GRuleUtil::getLabel($v);

    // WORK_DESCRIPTIONS
    $work_descriptions = Config::get('arc.SOLR_LIST_FIELDS_DESCRIPTIONS');

    if (!empty($work_descriptions)) {
      foreach ($work_descriptions as $description_field) {
        $fieldProperties = $v->getProperties($description_field);
        if (!empty($fieldProperties)) {
          foreach ($fieldProperties as $fprop) {
            $this->solrData->add("descriptions", $fprop->value());
          }
        }
      }
    }

    $this-> _addSubjects();
    //$this->_addSubjects($v, true, true);//WORK SUBJECTS FROM WORK
    //$this->_addSubjects($v, true, true, false, true);//WORK SUBJECTS FROM WORK


    //WORK CONTRIBUTORS
    $w_contributors = $this->work_contributor_elements;
    $this->_addContributors($v, $w_contributors);

    // AUTHORS
    $this->_addRelation($v, GDirection::OUT, 'ea:work:authorWork', 'authors');//w_authors

    //WORK CATEGORIES
    $this->_addCategories();

    if ($project == 'humanities'){
    	$this->_addTermLang($v, 'ea:work:Language');
    }

    //ASELIS
    if ($project == 'ghr' || $project == 'unioncatalog'|| $project == 'humanities'){
	    $this->_addRelation($v, GDirection::OUT, 'ea:work:chronological', 'chronological');
	    $this->_addRelation($v, GDirection::OUT, 'ea:work:TypologyWork', 'typology');
	    $this->_addRelation($v, GDirection::OUT, 'ea:work:Form', 'w_form');
	    $this->_addRelation($v, GDirection::OUT, 'ea:work:PoliticalSupport', 'political_support');
	    $this->_addRelation($v, GDirection::OUT, 'ea:work:subjectCategory', 'w_subject_category');
    }

    // MANIFESTATIONS
//    $manifestationsRaw = $v->getVertices(GDirection::IN, 'ea:work:');
//    $manifestationsInferred = $v->getVertices(GDirection::IN, 'inferred:ea:work:');
//    $manifestations = array_merge($manifestationsRaw, $manifestationsInferred);
    $manifestations = $this->getManifestationsFromWork();
    $countDigitalItems = 0;
    $countBooks = 0;

    if (!empty($manifestations)) {
      foreach ($manifestations as $manif) {
        //$this->_addSubjects($manif,false,false,false);//WORK SUBJECTS FROM MANIF
        //$this->_addSubjects($manif,false,false,false,true);//WORK SUBJECTS FROM MANIF

        //WORK PUBLISHER FROM MANIF
        $this->_addPublicationInfo($manif, '',false);


        /* @var $manif GVertex */
        $this->solrData->add("secondaryTitles", $manif->getPropertyValue('dc:title:'));
        $work_manif_secondary_title = Config::get('arc.SOLR_LIST_FIELDS_WORK_MANIF_SECONDARY_TITLES');
        if (!empty($work_manif_secondary_title)) {
          foreach ($work_manif_secondary_title as $secondary_title) {
            $this->solrData->add("secondaryTitles", $manif->getPropertyValue($secondary_title));
          }
        }

// 				$this->solrData->publication_types[] = $manif->getPropertyValue('ea:manif:Type');
        $pub_type = $manif->getProperty('ea:manif:Type');
        if (!empty($pub_type)) {
          $tmp = $pub_type->value();
          $this->solrData->add("publication_types", ($tmp != 'undefined') ? $pub_type->prps('selected_value') : null);
        }

        $artifacts = $manif->getVertices(GDirection::IN, 'ea:artifact-of:');
        if (!empty($artifacts)) {
          foreach ($artifacts as $artf) {
            $this->solrData->add("digital_item_types", $artf->getPropertyValue('ea:item:type'));
          }
        }

        $ft = $manif->getPropertyValue('ea:form-type:');
        if (!empty($ft) && $ft == 'book') {
          $countBooks++;
        }

        $countDigitalItems += count($artifacts);

        //Lang
        $langs = $manif->getTmpAttribute('Manif_lang');
        if (!empty($langs)) {
          foreach ($langs as $lang) {
            $this->solrData->add("languages", $lang);
          }
        }

        //DRYLL
        if($project == 'dryl'){
        	$manif_content = $manif->getProperties('ea:manif:content');
        	if (!empty($manif_content)) {
        		foreach ($manif_content as $fprop) {
        			$this->solrData->add("contents", $fprop->value());
        		}
        	}
        	// LAWYER - SUBLOCATION
        	$artifacts = $manif->getVertices(GDirection::IN, 'ea:artifact-of:');
        	if (!empty($artifacts)) {
        		foreach ($artifacts as $artf) {
        			$lawyers = $artf->getVertices(GDirection::OUT, 'ea:item:ownerItem');
        			$this->_addSolrData($lawyers, 'lawyer');

        			$item_sublocation = $artf->getProperty('ea:item:sublocation');
        			if (!empty($item_sublocation)){
        				$tmp = $item_sublocation ->value();
        				$item_sublocation = ($tmp != 'undefined') ?  $item_sublocation->prps('selected_value') : null;
        			}
        			$this->solrData->add("item_sublocation", $item_sublocation);
        		}
        	}
        }
        //**

      }
    }

    //DRYLL;
    if($project == 'dryl'){
	    $this->solrData->add("pub_record_type",'work_m');

	    $this->_addRelation($v, GDirection::OUT, 'ea:work:conference_event', 'conference');
	    $conference = $v->getFirstVertex(GDirection::OUT,'ea:work:conference_event');
	    if (!empty($conference)){
	      $conf_coordinators= $conference->getVertices(GDirection::OUT,'ea:event:coordinator_of_conference');
	      $this->_addSolrData($conf_coordinators, 'conference_coordinator');
	    }
    }
    //**

    $this->solrData->num_of_digital_items = $countDigitalItems;
    $this->solrData->num_of_manifestations = count($manifestations);
    $this->solrData->num_of_books = $countBooks;


    // EXPRESSIONS
    $expressions = $v->getVertices(GDirection::IN, 'ea:expressionOf:');
    if (!empty($expressions)) {
      foreach ($expressions as $expr) {
        $this->solrData->add("secondaryTitles", $expr->getPropertyValue('dc:title:'));
        // TODO = get secondary titles of expression. Do these exist? How to get them?
      }
    }

    return true;
  }


  /**
   *
   * @param GVertex $v
   */
  private function processPeriodic($v) {

    $this->_addBasics($v, 'work', 'periodic');
    $project = Config::get('arc.PROJECT_NAME');
    // CATEGORIES
    //$categories = $v->getVertices(GDirection::OUT,'ea:periodic:category');
    //$this->_addCategories($v,'ea:periodic:category');
    $this->_addCategories();

//    //PUBlISHER/PUBPLACE
    $this->_addPublicationInfoDirect($v, '');

    // ISSUES
    $issues = $v->getVertices(GDirection::OUT, 'ea:hasIssue:');
    $this->solrData->num_of_issues = count($issues);

    //DRYLL
    if($project == 'dryl'){
    	$this->solrData->add("pub_record_type",'periodic');
    	foreach ($issues as $issue){
    		//To kanw active mono ena sto 'TERM_SEARCH_TOKEN' den exw to form_type:issue
	    	//$issue_content = $issue->getProperties('ea:manif:content');
	    	//if (!empty($issue_content)) {
	    		//foreach ($issue_content as $fprop) {
	    			//$this->solrData->add("contents", $fprop->value());
	    		//}
	    	//}

	    	// LAWYER - SUBLOCATION
	    	$artifacts = $issue->getVertices(GDirection::IN, 'ea:artifact-of:');
	    	if (!empty($artifacts)) {
	    		foreach ($artifacts as $artf) {
	    			$lawyers = $artf->getVertices(GDirection::OUT, 'ea:item:ownerItem');
	    			$this->_addSolrData($lawyers, 'lawyer');
	    			$item_sublocation = $artf->getProperty('ea:item:sublocation');
	    			if (!empty($item_sublocation)){
	    				$tmp = $item_sublocation ->value();
	    				$item_sublocation = ($tmp != 'undefined') ?  $item_sublocation->prps('selected_value') : null;
	    			}
	    			$this->solrData->add("item_sublocation", $item_sublocation);
	    		}
	    	}

    	}
    }//**

    return true;
  }


  /**
   *
   * @param GVertex $v
   */
  private function processPerson($v) {

  	$project = Config::get('arc.PROJECT_NAME');
    $this->_addBasics($v, 'person');

    $this->_addRelation($v, GDirection::OUT, 'ea:authPersonAssociated:Place_Birth', 'birthplace');
    $this->_addRelation($v, GDirection::OUT, 'ea:authAssociated:OtherPlace_Place', 'actionplace');
    $this->_addRelation($v, GDirection::OUT, 'ea:auth:Nationality', 'nationality');
    $this->_addRelation($v, GDirection::OUT, 'ea:authPerson:Occupation_Name', 'occupation');

    //ASELIS
    $this->_addRelation($v, GDirection::OUT, 'ea:auth:Participation_in_events', 'participations_civil_war');
    $this->_addRelation($v, GDirection::OUT, 'ea:auth:Relation_Civil_War', 'civil_war_relations');
    $this->_addRelation($v, GDirection::OUT, 'ea:auth:personalCourseLeftist', 'participations_leftist');
    $this->_addRelation($v, GDirection::OUT, 'ea:auth:participationNationalists', 'participations_nationalists');
    $this->_addRelation($v, GDirection::OUT, 'ea:auth:Activity_Field', 'authors_attributes');
    //

    $person_secondary_title = Config::get('arc.SOLR_LIST_FIELDS_PERSON_SECONDARY_TITLES');
    if (!empty($person_secondary_title)) {
      foreach ($person_secondary_title as $secondary_title) {
        $this->solrData->add("secondaryTitles", $v->getPropertyValue($secondary_title));
      }
    }

    $person_descriptions = Config::get('arc.SOLR_LIST_FIELDS_DESCRIPTIONS');
    if (!empty($person_descriptions)) {
      foreach ($person_descriptions as $description_field) {
        $fieldProperties = $v->getProperties($description_field);
        if (!empty($fieldProperties)) {
          foreach ($fieldProperties as $fprop) {
            $this->solrData->add("descriptions", $fprop->value());
          }
        }
      }
    }

    if ($project == 'humanities'){
    	$this->_addTermLang($v, 'ea:auth:Person_Entity_Language');
    }

    return true;
  }

  /**
   *
   * @param GVertex $v
   */
  private function processOrganization($v) {
  	$project = Config::get('arc.PROJECT_NAME');
    $this->_addBasics($v, 'organization');

    $organization_secondary_title = Config::get('arc.SOLR_LIST_FIELDS_ORGANIZATION_SECONDARY_TITLES');
    if (!empty($organization_secondary_title)) {
      foreach ($organization_secondary_title as $secondary_title_field) {
        $fieldProperties = $v->getProperties($secondary_title_field);
        if (!empty($fieldProperties)) {
          foreach ($fieldProperties as $fprop) {
            $this->solrData->add("secondaryTitles", $fprop->value());
          }
        }
      }
    }

    $organization_descriptions = Config::get('arc.SOLR_LIST_FIELDS_DESCRIPTIONS');
    if (!empty($organization_descriptions)) {
      foreach ($organization_descriptions as $description_field) {
        $fieldProperties = $v->getProperties($description_field);
        if (!empty($fieldProperties)) {
          foreach ($fieldProperties as $fprop) {
            $this->solrData->add("descriptions", $fprop->value());
          }
        }
      }
    }

    if ($project == 'humanities'){
    	$this->_addTermLang($v, 'ea:auth:Person_Entity_Language');
    }

    return true;
  }

  /**
   *
   * @param GVertex $v
   */
  private function processFamily($v) {
  	$project = Config::get('arc.PROJECT_NAME');
    // assign record type
    $this->_addBasics($v, 'family');

    $family_descriptions = Config::get('arc.SOLR_LIST_FIELDS_DESCRIPTIONS');
    if (!empty($family_descriptions)) {
      foreach ($family_descriptions as $description_field) {
        $fieldProperties = $v->getProperties($description_field);
        if (!empty($fieldProperties)) {
          foreach ($fieldProperties as $fprop) {
            $this->solrData->add("descriptions", $fprop->value());
          }
        }
      }
    }

    if ($project == 'humanities'){
    	$this->_addTermLang($v, 'ea:auth:Person_Entity_Language');
    }

    return true;
  }

  /**
   *
   * @param GVertex $v
   */
  private function processPlace($v) {
  	$project = Config::get('arc.PROJECT_NAME');
    $this->_addBasics($v, 'place');

   if ($project == 'humanities'){
    	$this->_addTermLang($v, 'ea:auth:Person_Entity_Language');
    }

    return true;
  }

  /**
   *
   * @param GVertex $v
   */
  private function processConcept($v) {
  	$project = Config::get('arc.PROJECT_NAME');
    $this->_addBasics($v, 'concept');

    if ($project == 'humanities'){
    	$this->_addTermLang($v, 'ea:auth:Person_Entity_Language');
    }

    return true;
  }

  /**
   *
   * @param GVertex $v
   */
  private function processObject($v) {
  	$project = Config::get('arc.PROJECT_NAME');
    $this->_addBasics($v, 'object');

    if ($project == 'humanities'){
    	$this->_addTermLang($v, 'ea:auth:Person_Entity_Language');
    }

    return true;
  }


  /**
   *
   * @param GVertex $v
   */
  private function processGeneral($v) {
  	$project = Config::get('arc.PROJECT_NAME');
    $this->_addBasics($v, 'general');

    if ($project == 'humanities'){
    	$this->_addTermLang($v, 'ea:auth:Person_Entity_Language');
    }

    return true;
  }


  /**
   *
   * @param GVertex $v
   */
  private function processEvent($v) {
  	$project = Config::get('arc.PROJECT_NAME');
    $this->_addBasics($v, 'event');

    if ($project == 'humanities'){
    	$this->_addTermLang($v, 'ea:auth:Person_Entity_Language');
    }

    return true;
  }

  /**
   *
   * @param GVertex $v
   */
  private function processGenre($v) {
  	$project = Config::get('arc.PROJECT_NAME');
    $this->_addBasics($v, 'genre');

    if ($project == 'humanities'){
    	$this->_addTermLang($v, 'ea:auth:Person_Entity_Language');
    }

    return true;
  }

  /**
   *
   * @param GVertex $v
   */
  private function processLemma($v) {
    $this->_addBasics($v, 'lemma');

    $this->solrData->set('title_in_english', $v->getPropertyValue('ea:lemma:title_in_english'));
    $this->_addRelation($v, GDirection::OUT, 'ea:lemma:author', 'l_recorders');
    $this->_addRelation($v, GDirection::OUT, 'ea:keyword:', 'l_keywords');
    //$this->_addRelation($v, GDirection::OUT, 'ea:lemma:category', 'l_categories');

    $this->_addCategories();

    $l_works = $v->getVertices(GDirection::OUT,'ea:lemma:work');
    if(!empty($l_works)){
      foreach ($l_works as $work){
         $w_authors = $work->getVertices(GDirection::OUT,'ea:work:authorWork');
         $this->_addSolrData($w_authors, 'l_authors');
       }
    }

    return true;
  }


  /**
   *
   * @param GVertex $v
   */
  private function processMedia($v) {
    $this->_addBasics($v, 'media');
    return true;
  }


  /**
   *
   * @param GVertex $v
   */
  private function processWebSiteInstance($v) {
    $this->_addBasics($v, 'web-site-instance');
    return true;
  }


  /**
   *
   * @param GVertex $v
   */
  private function processPeriodicPublication($v) {
    $this->_addBasics($v, 'periodic-publication');
    return true;
  }


  /**
   *
   * @param GVertex $v
   */
  private function processExpressionAsWork($v) {
    if (!(empty($v->getVertices(GDirection::OUT, 'ea:expressionOf:')))) {
      return;
    }
    // assign record type
    if ($v->getObjectType() == 'auth-expression') {
      $this->solrData->record_type = 'work';
      $this->solrData->form_type = 'work';
    }

    $this->solrData->label = GRuleUtil::getLabel($v); //$v->getAttribute('label');

    // MAIN VERTEX ATTRIBUTES
    $this->solrData->id = $v->getPropertyValue('ea:identifier:id');
    $this->solrData->object_type = $v->getPropertyValue('ea:obj-type:');
    $this->_addOpacData($v);
    $this->solrData->title = $v->getPropertyValue('dc:title:');

    // WORK_DESCRIPTIONS
    $work_descriptions = Config::get('arc.SOLR_LIST_FIELDS_DESCRIPTIONS');
    if (!empty($work_descriptions)) {
      foreach ($work_descriptions as $description_field) {
        $fieldProperties = $v->getProperties($description_field);
        if (!empty($fieldProperties)) {
          foreach ($fieldProperties as $fprop) {
            $this->solrData->add("descriptions", $fprop->value());
          }
        }
      }
    }

    $this->_addSubjects();
    //$this->_addSubjects($v, true, true);//EXPRESS SUbJECTS FROM EXPRESS
    //$this->_addSubjects($v, true, true, false, true);//EXPRESS SUbJECTS FROM EXPRESS

//		// SUBJECTS
//		$subjects = $v->getVertices(GDirection::OUT,'ea:subj:');
//		if (!empty($subjects)){
//			foreach ($subjects as $subject) {
//        $subject_text = GRuleUtil::getLabel($subject) . '‡' . $subject->id();
//				$this->solrData->add("subjects",$subject_text);
//			}
//		}

    // TODO = contributors[]

    // AUTHORS
    $this->_addRelation($v, GDirection::OUT, 'ea:work:authorWork', 'authors');//w_authors


    // MANIFESTATIONS
    $manifestationsRaw = $v->getVertices(GDirection::IN, 'ea:work:');
    $manifestationsInferred = $v->getVertices(GDirection::IN, 'inferred:ea:work:');
    $manifestations = array_merge($manifestationsRaw, $manifestationsInferred);
    if (!empty($manifestations)) {
      foreach ($manifestations as $manif) {
        $this->solrData->add("secondaryTitles", $manif->getPropertyValue('dc:title:'));
        $work_manif_secondary_title = Config::get('arc.SOLR_LIST_FIELDS_WORK_MANIF_SECONDARY_TITLES');
        if (!empty($work_manif_secondary_title)) {
          foreach ($work_manif_secondary_title as $secondary_title) {
            $this->solrData->add("secondaryTitles", $manif->getPropertyValue($secondary_title));
          }
        }

        $artifacts = $manif->getVertices(GDirection::IN, 'ea:artifact-of:');

        if (!empty($artifacts)) {
          foreach ($artifacts as $artf) {
            $this->solrData->add("digital_item_types", $artf->getPropertyValue('ea:item:type'));
          }
        }

        $this->solrData->num_of_digital_items = count($artifacts);
      }
    }

    $this->solrData->num_of_manifestations = count($manifestations);

    return true;
  }

  /**
   *
   * @param GVertex $v
   */
  private function processManifestation($v, $asWork) {

  	$project = Config::get('arc.PROJECT_NAME');
    $solrData = $this->solrData;
    // assign record type
    if ($asWork) {
      $this->solrData->set('record_type', 'work');
      $this->solrData->set('form_type', 'work');
    } else {
      $this->solrData->set('record_type', 'manifestation');
      $this->solrData->set('form_type', 'manifestation');
    }
    //$this->_addCategories($v,'ea:manif:subjectCategory');
    $this->_addCategories();

    // MAIN VERTEX ATTRIBUTES
    if ($asWork) {
      $this->solrData->id = $v->getPropertyValue('ea:identifier:id');
    } else {
      $this->solrData->id = 'MNF:' . $v->getPropertyValue('ea:identifier:id');
    }

    $this->solrData->object_type = $v->getPropertyValue('ea:obj-type:');
    $this->_addOpacData($v);
    $this->solrData->title = $v->getPropertyValue('dc:title:');

    $label = GRuleUtil::getLabel($v); //$v->getAttribute('label');
    $this->solrData->label = $label;

    // MANIF_DESCRIPTIONS
    $manif_descriptions = Config::get('arc.SOLR_LIST_FIELDS_DESCRIPTIONS');
    if (!empty($manif_descriptions)) {
      foreach ($manif_descriptions as $description_field) {
        $fieldProperties = $v->getProperties($description_field);
        if (!empty($fieldProperties)) {
          foreach ($fieldProperties as $fprop) {
            $this->solrData->add("descriptions", $fprop->value());
          }
        }
      }
    }

    $this->_addSubjects();
    //$this->_addSubjects($v, true, true); //MANIFESTATION SUBJECTS  FROM MANIF
    //$this->_addSubjects($v, false, false, true, true); //MANIFESTATION SUBJECTS  FROM MANIF

    //if (1==2) {//TODO: DEVEL
    //SUBJECTS FROM WORK
    $works = $this->getWorksFromManifestation();

    /* @var $work GVertex */
    if (!empty($works)) {
      foreach ($works as $work) {
        //$this->_addSubjects($work, false, false); //MANIFESTATION SUBJECTS  FROM WORKS
        //$this->_addSubjects($work, true, false, false, false); //MANIFESTATION SUBJECTS  FROM WORKS
        $this->_addRelation($work, GDirection::OUT, 'ea:work:authorWork', 'authors'); //m_
        //ASELIS
        if ($project == 'ghr' || $project == 'unioncatalog' || $project == 'humanities' ){
	        $this->_addRelation($work, GDirection::OUT, 'ea:work:chronological', 'chronological');
	        $this->_addRelation($work, GDirection::OUT, 'ea:work:TypologyWork', 'typology');
	        $this->_addRelation($work, GDirection::OUT, 'ea:work:Form', 'w_form');
	        $this->_addRelation($work, GDirection::OUT, 'ea:work:PoliticalSupport', 'political_support');
	        $this->_addRelation($work, GDirection::OUT, 'ea:work:subjectCategory', 'w_subject_category');
        }

        if ($project == 'humanities'){
        	$this->_addTermLang($work, 'ea:work:Language');
        }


      }
    }

    // TODO = contributors[]

    //TODO: xriazete author sto manif?  gia to issue (tefxos periodikou)?
    //$this->_addRelation($v, GDirection::OUT, '', 'm_authors');



    $ft = $v->getPropertyValue('ea:form-type:');
    if (!empty($ft)) {
      $this->solrData->set('form_type', $ft);
      //DRYLL
      if($project == 'dryl'){
     	 $this->solrData->add("pub_record_type",$ft);
      }
      //**
    }

    //DRYLL
    if($project == 'dryl'){

    	// LAWYER
    	$artifacts = $v->getVertices(GDirection::IN, 'ea:artifact-of:');
    	if (!empty($artifacts)) {
    		foreach ($artifacts as $artf) {
    			$lawyers = $artf->getVertices(GDirection::OUT, 'ea:item:ownerItem');
    			$this->_addSolrData($lawyers, 'lawyer');

    			$item_sublocation = $artf->getProperty('ea:item:sublocation');
    			if (!empty($item_sublocation)){
    				$tmp = $item_sublocation ->value();
    				$item_sublocation = ($tmp != 'undefined') ?  $item_sublocation->prps('selected_value') : null;
    			}
    			$this->solrData->add("item_sublocation", $item_sublocation);
    		}
    	}

    	$manif_content = $v->getProperties('ea:manif:content');
    	if (!empty($manif_content)) {
    		foreach ($manif_content as $fprop) {
    			$this->solrData->add("contents", $fprop->value());
    		}
    	}
    }
    //**


    //MANIF PUBLICATION PLACE - PUBLISHER NAME
    $this->_addPublicationInfo($v, '',true);


    //TEFXOS PERIODIKOU
    if ($ft == 'issue') {
      $journal = $v->getFirstVertex(GDirection::OUT, 'ea:issueOf:');
      if (!empty($journal)) {
        //$this->_addPublicationInfoDirect($jurnal, 'i_');
        $this->_addPublicationInfoDirect($journal, '',false);
      }
    }

    ## PERIODIC
//  $periodic = $v->getFirstVertex(GDirection::IN, 'ea:hasIssue:');
//  if (!empty($periodic)) {
//    PUtil::logRed("PERIODIC PUB INFO: " . $v->id());
//    $this->_addPublicationInfoDirect($v,'m_');
//  }


    //MANIF TYPE
    $manif_type = $v->getProperty('ea:manif:Type');
    if (!empty($manif_type)) {
      $tmp = $manif_type->value();
      $this->solrData->add("publication_types", ($tmp != 'undefined') ? $manif_type->prps('selected_value') : null);
    }


    //MANIF LANG
    $langs = $v->getTmpAttribute('Manif_lang');
    if (!empty($langs)) {
      foreach ($langs as $lang) {
        $this->solrData->add("languages", $lang);
      }
    }

    $artifacts = $v->getVertices(GDirection::IN, 'ea:artifact-of:');

    if (!empty($artifacts)) {
      foreach ($artifacts as $artf) {
        $this->solrData->add("digital_item_types", $artf->getPropertyValue('ea:item:type'));
      }
    }

    if ($asWork) {
      $this->solrData->num_of_manifestations = 1;
      $this->solrData->num_of_digital_items = count($artifacts);

      $ft = $v->getPropertyValue('ea:form-type:');
      if (!empty($ft)) {
        if ($ft == 'book') {
          $this->solrData->num_of_books = 1;
        } elseif ($ft == 'issue') {
          $this->solrData->num_of_issues = 1;
        }
      }
    } else {
      $this->solrData->num_of_manifestations = 0;
      $this->solrData->num_of_digital_items = 0;

      $ft = $v->getPropertyValue('ea:form-type:');
      if (!empty($ft)) {
        if ($ft == 'book') {
          $this->solrData->num_of_books = 1;
        } elseif ($ft == 'issue') {
          $this->solrData->num_of_issues = 1;
        }
      }
    }
    return true;
  }

  /**
   * @param GVertex $v
   */
  protected function processVertex($v) {

    $this->cache = array();
    $this->v = $v;
    //PUtil::logRed("SOLR RULE");
    $vid = $v->id();

    $object_status = $v->getPropertyValue("ea:status:");
    if ($object_status != 'finish') {
      //Log::info("SOLR: SKIPPED VERTEX " . $vid . " (NOT IN FINISH STATUS)");
      return;
    }

    $obj_type = $v->getObjectType();
    $this->solrData = isset($this->solrDataArray[$vid]) ? $this->solrDataArray[$vid] : new VertexSolrWorkData();

    $procRep = false;
    if ($obj_type == 'auth-work') {
      $procRep = $this->processWork($v);
    } elseif ($obj_type == 'periodic') {
      $procRep = $this->processPeriodic($v);
    } elseif ($obj_type == 'auth-person') {
      $procRep = $this->processPerson($v);
    } elseif ($obj_type == 'auth-organization') {
      $procRep = $this->processOrganization($v);
    } elseif ($obj_type == 'auth-family') {
      $procRep = $this->processFamily($v);
    } elseif ($obj_type == 'auth-place') {
      $procRep = $this->processPlace($v);
    } elseif ($obj_type == 'auth-concept') {
      $procRep = $this->processConcept($v);
    } elseif ($obj_type == 'auth-object') {
      $procRep = $this->processObject($v);
    } elseif ($obj_type == 'auth-general') {
      $procRep = $this->processGeneral($v);
    } elseif ($obj_type == 'auth-event') {
      $procRep = $this->processEvent($v);
    } elseif ($obj_type == 'auth-genre') {
      $procRep = $this->processGenre($v);
    } elseif ($obj_type == 'auth-expression') {
      $procRep = $this->processExpressionAsWork($v);
    } elseif ($obj_type == 'auth-manifestation') {
      $procRep = $this->processManifestation($v, false);
    } elseif ($obj_type == 'lemma') {
      $procRep = $this->processLemma($v);
    } elseif ($obj_type == 'media') {
      $procRep = $this->processMedia($v);
    } elseif ($obj_type == 'web-site-instance') {
      $procRep = $this->processWebSiteInstance($v);
    } elseif ($obj_type == 'periodic-publication') {
      $procRep = $this->processPeriodicPublication($v);
    } else {
      Log::info("SOLR UNKNOWN NODE: " . $v->getObjectType() . ' :: ' . $v->urnStr());
      return;
    }

    if (!$procRep) {
      return;
    }

    $this->solrDataArray[$vid] = $this->solrData;

    $date_avail = $v->getPropertyValue('dc:date:available');

    //$phpdate = strtotime( $date_issued );
    if (!empty($date_avail)) {
      $this->solrData->set('create_dt', new DateTime($date_avail));
    }

    //$v->setAttribute('solr_data',json_encode($this->solrData->arrayValue(),JSON_UNESCAPED_UNICODE));
    $solr_data = $this->solrData->getNodeInfoData();
    $v->setAttribute('solr_data', $solr_data);
    //Log::info(print_r($this->solrData,true));

    // an einai flaged os subject kane to is_subject TRUE
    if ($v->hasFlag('IS:subject')) {
      // 		if (!empty($v->getVertices(GDirection::IN, 'ea:subj:'))) {
      //	Log::info("SOLR: Found subject flagged record");
      $this->solrData->set('is_subject', true);
    }
  }

  public function postExecute() {
    $this->context->put('SOLR_VERTEX_DATA', $this->solrDataArray);
  }
}
