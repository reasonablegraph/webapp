<?php
class GRuleOpacData extends AbstractGruleProcessVertice implements GRule {


	private $opac1 = array();
	private $opac2 = array();


	private $title = null;
	private $label = null;

	//private $opacDataArray = array();

	protected function init(){
		$saveInferenceFlag = (Config::get('arc.SAVE_INFERENCE_AS_JSON',1) > 0);
		$this->context->putCommand("OPAC_DATA_CMD", new GRuleOpacDataCmd($saveInferenceFlag));
		$this->skip_readonly = true;
	}


	/**
	 * @param GVertex $v
	 */
	private function processDigitalItem($v){
		$context = $this->context;

		$v->addFlag('IS:item');

#### Flag based on last update ####
####	$user = ArcApp::user();
####	if (!empty($user)){
####		$org_id = $user['org_id'];
####		$v->addFlag("ORG:$org_id");
####	}

		$organization_name = null;
		$organization = $v->getProperty('ea:item:sublocation');
		if (!empty($organization )){
			$organization_name = $organization->prps('selected_value');
			$org_id =$organization->value();
			$v->addFlag("ORG:$org_id");
		}else{
			$organization = $v->getFirstEdge(GDirection::OUT,'ea:item:sublocation');
			if(!empty($organization)){
				$org_data =$organization->data();
				$jdata = json_decode($org_data,true);
				$organization_name = $jdata['prps']['selected_value'];
				$org_id = $organization->label();
				$v->addFlag("ORG:$org_id");
			}
		}

		//$context->addDebugMessage("DIGITAL ITEM: " . $v);
		$description = $v->getPropertyValue('ea:item:description');
		$info = $v->getPropertyValue('ea:item:info');
		$size = $v->getPropertyValue('ea:item:size');
		$page = $v-> getPropertyValue('ea:item:page');
		$part = $v->getPropertyValue('ea:item:partNumber');
// 		$type = $v->getProperty('ea:item:type');

// 		if (!empty($type)){
// 			$tmp =$type->value();
// 			$type = ($tmp != 'undefined') ?  $type->prps('selected_value') : null;
// 		}

		$id = $v->persistenceId();

		$artifactOfPrimary = $v->getFirstEdge(GDirection::OUT,'ea:artifact-of:primary');
		if(!empty($artifactOfPrimary)){
			$element = $artifactOfPrimary->element();
			$elementVertex = $v->getFirstVertex(GDirection::OUT, $element);
			$element_title = GRuleUtil::getLabel($elementVertex);

			$var_templ = array(
					'manif_title' => $element_title,
			);

			$template = Config::get('arc_display_template.digital_item_connected_label');
			$label = ArcTemplateEngine::renderLine($template,$var_templ);
		}else{
			$label = $v->getPropertyValue('dc:title:');
		}

		if (empty($label) || trim($label) == ''){
			$var_templ = array(
					'id' => $id,
					'type' => $type,
					'part' => $part,
					'page' => $page,
			);
			// 		$label = tr('digital-item').': '.$id. ' ['.$type.'] ('.$part. ', '.$page.')';
			$template = Config::get('arc_display_template.digital_item_label');
			$label = ArcTemplateEngine::renderLine($template,$var_templ);
		}

		$this->opac1['id'] = $id;
		$this->label = $label;
		$this->title = $label;

// 		$artifactOf = $v->getFirstEdge(GDirection::OUT,'ea:artifact-of:');

// 		if(!empty($artifactOf)){
// 			$element = $artifactOf->element();
// 			$elementVertex = $v->getFirstVertex(GDirection::OUT,$element);
// 			$element_title = GRuleUtil::getLabel($elementVertex);

// 			$var_templ = array(
// 					'element_title' => $element_title,
// 					'label' => $label,
// 			);

// 			$template = Config::get('arc_display_template.digital_item_title');
// 			$title = ArcTemplateEngine::renderLine($template,$var_templ);
// 			$this->title = $title;
// 		}else{
// 			$title = $label;
// 		}

// 		$v->setTmpAttribute('title',$title);


// 		$v->updatePropertyValue('dc:title:', null, $label);
// 		$v->updatePropertyValue('ea:label:', null, $title);

		$v->updatePropertyValue('ea:label:', null, $label);

		$this->opac1['organization'] = $organization_name;
		$this->opac1['info'] = $info;
		$this->opac1['description'] = $description;
// 		$this->opac1['type'] = $type;
		$this->opac1['size'] = $size;
		$this->opac1['page'] = $page;
		$this->opac1['part'] = $part;

// 		$this->opac1['thumbs'] = PDao::getThumbs($id); ///THUMBNAILS//
		if (!empty(PDao::getThumbs2($id))){
			$this->opac1['thumbs'] = PDao::getThumbs2($id);
		}
	}



	/**
	 * @param GVertex $v
	 */
	private function processPhysicalItem($v){
		$context = $this->context;

		$v->addFlag('IS:item');

		//$context->addDebugMessage("DIGITAL ITEM: " . $v);
		$barcode = $v->getPropertyValue('ea:item:barcode');
		$part = $v->getPropertyValue('ea:item:partNumber');
		$copyNumber = $v->getPropertyValue('ea:item:copyNumber');
		$type = $v->getProperty('ea:item:type');
		$location = $v->getProperty('ea:item:location');
		$sublocation = $v->getProperty('ea:item:sublocation');
		$classification = $v->getProperty('ea:item:Classification');
		$office_code = $v->getPropertyValue('ea:item:office_code');
		$owner = $v->getPropertyValue('ea:item:ownerItem');

		if (!empty($classification)){
			$classification = $classification->pnctn();
		}
		if (!empty($type)){
			$tmp =$type->value();
			$type = ($tmp != 'undefined') ?  $type->prps('selected_value') : null;
		}
		if (!empty($location)){
			$tmp =$location->value();
			$location = ($tmp != 'undefined') ?  $location->prps('selected_value') : null;
		}
		if (!empty($sublocation )){
			$tmp =$sublocation ->value();
			$sublocation  = ($tmp != 'undefined') ?  $sublocation ->prps('selected_value') : null;
		}

		$id = $v->persistenceId();

		$var_templ = array(
				'id' => $id,
				'type' => $type,
				'barcode' => $barcode,
				'part' => $part,
				'copyNumber' => $copyNumber,
		);

		$template = Config::get('arc_display_template.physical_item_label');
		$label = ArcTemplateEngine::renderLine($template,$var_templ);
		// 		$label = tr('digital-item').': '.$id. ' ['.$type.'] ('.$part. ', '.$page.')';

		$this->opac1['id'] = $id;
		$this->opac1['part'] = $part;
		$this->opac1['type'] = $type;
		$this->opac1['barcode'] = $barcode;
		$this->opac1['copyNumber'] = $copyNumber;
		$this->opac1['location'] = $location;
		$this->opac1['sublocation'] = $sublocation;
		$this->opac1['classification'] = $classification;
		$this->opac1['office_code'] = $office_code;
		$this->opac1['owner'] = $owner;

		$this->label = $label;
		$this->title = $label;

		$artifactOf = $v->getFirstEdge(GDirection::OUT,'ea:artifact-of:');
		if(!empty($artifactOf)){
			$element = $artifactOf->element();
			$elementVertex = $v->getFirstVertex(GDirection::OUT,$element);
			$element_title = GRuleUtil::getLabel($elementVertex);

			$var_templ = array(
					'element_title' => $element_title,
					'label' => $label,
			);

			$template = Config::get('arc_display_template.physical_item_title');
			$title = ArcTemplateEngine::renderLine($template,$var_templ);
			$this->title = $title;
		}else{
			$title = $label;
		}

// 		$v->setTmpAttribute('title',$title);
		$v->updatePropertyValue('dc:title:', null, $label);
		$v->updatePropertyValue('ea:label:', null, $title);

// 		$this->opac1['thumbs'] = PDao::getThumbs($id); ///THUMBNAILS//
		if (!empty(PDao::getThumbs2($id))){
			$this->opac1['thumbs'] = PDao::getThumbs2($id);
		}

	}


	/**
	 * @param GVertex $v
	 */
	private function processSubjectEntities($v){
		$context = $this->context;
		$id = $v->persistenceId();
		$this->opac1['id'] = $id;
// 		$this->opac1['thumbs'] = PDao::getThumbs($id); ///THUMBNAILS//
		$thumb = PDao::getThumbs2($id);
		if (!empty($thumb)){
			$this->opac1['thumbs'] = $thumb;
		}



		$as_subj_manif = 0;

		$subjects = $v->getVertices(GDirection::IN,'ea:subj:');
		if (!empty($subjects)){
			$direct_manif = 0;
			$work_manif = 0;
			$expres_manif = 0;
			foreach ($subjects as $entity) {
				if ($entity->getObjectType() == 'auth-manifestation') {
					$direct_manif =  ++$direct_manif;
				}
				$workOf = $entity->getVertices(GDirection::OUT,'ea:workOf:');
				if( !empty($workOf) ){
					$work_manif = $work_manif + count($workOf);
				}

				$inferred_work = $entity->getVertices(GDirection::IN,'inferred:ea:work:');
				if( !empty($inferred_work) ){
					$expres_manif = $expres_manif + count($inferred_work);
				}
			}
			$as_subj_manif = $direct_manif + $work_manif + $expres_manif;
		}


		$chain_link = $v->getVertices(GDirection::IN,'ea:inferred-chain-link:');
		if (!empty($chain_link)){
			foreach ($chain_link as $cl){
				$entities = $cl->getVertices(GDirection::IN,'ea:subj:');
				if (!empty($entities)){
					$entity_as_subj_manif = 0;
					foreach ($entities as $entity) {
						$direct_manif = 0;
						$work_manif = 0;
						$expres_manif = 0;
						if ($entity->getObjectType() == 'auth-manifestation') {
							$direct_manif = ++$direct_manif;
						}
						$work_manifs = $entity->getVertices(GDirection::OUT,'ea:workOf:');
						if( !empty($work_manifs) ){
							$work_manif =  count($work_manifs);
						}
						$expres_manifs = $entity->getVertices(GDirection::IN,'inferred:ea:work:');
						if( !empty($expres_manifs) ){
							$expres_manif = count($expres_manifs);
						};
						$entity_as_subj_manif = $entity_as_subj_manif + $direct_manif + $work_manif + $expres_manif;
					}
					$as_subj_manif = $as_subj_manif + $entity_as_subj_manif;
				}
			}
		}

		if (!empty($as_subj_manif)){
			$this->opac1['as_subj_manif'] = $as_subj_manif;
		}

		### Conference ###
		$form_type = $v->getPropertyValue('ea:form-type:');
		if ($form_type=='conference'){
			$v->addFlag('IS:conference');
		}

		### Category/Levels ###
		$form_type = $v->getPropertyValue('ea:form-type:');
		if ($form_type=='category'){
			$v->addFlag('IS:category');

			$parent  = $v->getFirstVertex(GDirection::OUT,'ea:concept:category_parent');
			$child = $v->getFirstVertex(GDirection::IN,'ea:concept:category_parent');

			if (!empty($parent)){
				$v->addFlag('LEVEL:2');
				$v->removeFlag('LEVEL:1');
				$v->removeFlag('HAS:subcategory');
				$parent_label = GRuleUtil::getLabel($parent);
				$dc_title = $v->getPropertyValue('dc:title:');
				$var_templ = array(
						'parent_label' => $parent_label,
						'dc_title' => $dc_title,
				);
				$template = Config::get('arc_display_template.subcategory_label');
				$label = ArcTemplateEngine::renderLine($template,$var_templ);
				$this->label =  $label;
			}elseif(!empty($child)){
				$v->addFlag('HAS:subcategory');
				$v->addFlag('LEVEL:1');
				$v->removeFlag('LEVEL:2');
			}else{
				$v->addFlag('LEVEL:1');
				$v->removeFlag('LEVEL:2');
				$v->removeFlag('HAS:subcategory');
			}

		}
		############

	}



	/**
	 * @param GVertex $v
	 */
	private function processPeriodic( $v){

		$id = $v->persistenceId();
		$this->opac1['id'] = $id;
		if (!empty(PDao::getThumbs2($id))){
			$this->opac1['thumbs'] = PDao::getThumbs2($id);
		}

		$title = $v->getPropertyValue('dc:title:');
		$title_remainder = $v->getPropertyValue('ea:periodic:title_remainder');

		$var_periodic_templ = array(
				'title' => $title,
				'title_remainder' => $title_remainder,
		);

		$template = Config::get('arc_display_template.periodic_title');
		$label = ArcTemplateEngine::renderLine($template,$var_periodic_templ);

		$this->label = $label;
		$this->title = $label;


		$ppublishers = $v->getVertices(GDirection::OUT, 'ea:periodic:publisher_name');
		$publishers = array();

		if (!empty($ppublishers)){
			foreach ($ppublishers as $pub){
				//$value_array['name'] = = GRuleUtil::getLabel($publisher);
				$value_array['name'] = $pub->getPropertyValue('dc:title:');
				$value_array['id'] = $pub->persistenceId();
				$publishers[]= $value_array;
			}
		}
		$this->opac1['publishers'] = $publishers;

		$this->opac1['time_range'] = $v->getPropertyValue('ea:periodic:issues_time_range');

		$ditem_loc = ARRAY();
		//$ditem_loc[] = $v-> getPropertyValue('ea:periodic:location_in_library');
		$ditem_loc_presc = $v->getProperties('ea:periodic:location_in_library');
		if(!empty($ditem_loc_presc)){
			foreach ($ditem_loc_presc  as $ditem_loc_p){
				$ditem_loc[] =  $ditem_loc_p->value();
			}
		}

		//Issues number
		$issues= $v->getVertices(GDirection::OUT,'ea:hasIssue:');
		if (!empty($issues)){
			$this->opac1['issues_num'] = count($issues);
			foreach ($issues as $issue){
				$ditems =$issue->getVertices(GDirection::BOTH,'ea:artifact-of:');
				foreach ($ditems as $ditem){
					$item_obj_type = $ditem->getPropertyValue('ea:obj-type:');
					if ($item_obj_type == 'physical-item'){
						$ditem_sublocation = $ditem->getProperty('ea:item:sublocation');
						if (!empty($ditem_sublocation)){
							$tmp = $ditem_sublocation->value();
							$ditem_sublocation = ($tmp != 'undefined') ?  $ditem_sublocation->prps('selected_value') : null;
							$ditem_loc[] = $ditem_sublocation;
						}
					}
				}
			}
		}

		$this->opac1['location'] = array_unique($ditem_loc);


		//Dryll - Flags is old/new/corrected record , lawyer emails
		$completion_level = $v->getPropertyValue('ea:control:completion_level');
		if (isset($completion_level)){
			$this->opac1['completion_level'] = $completion_level;
			if($completion_level == 0){
				$v->addFlag('IS:new');
				$v->removeFlag('IS:old');
				$v->removeFlag('IS:corrected');
			}elseif($completion_level == 1){
				$v->addFlag('IS:old');
				$v->removeFlag('IS:new');
				$v->removeFlag('IS:corrected');
			}elseif($completion_level == 2){
				$v->addFlag('IS:corrected');
				$v->removeFlag('IS:old');
				$v->removeFlag('IS:new');
			}else{
				$v->removeFlag('IS:old');
				$v->removeFlag('IS:new');
				$v->removeFlag('IS:corrected');
			}
		}

		$related_lawyers = $v->getVertices(GDirection::OUT, 'ea:periodic:related_lawyers');
		if (!empty($related_lawyers)){
			foreach ($related_lawyers as  $lawyer){
				$email = $lawyer->getPropertyValue('ea:auth:Address_Mail');
				if(!empty($email)){
					$this->opac2['lawyers_emails'][] = $email;
				}
			}
		}
		//**

	}


	/**
	 * @param GVertex $v
	 */
	private function processLemma( $v){

		$title_in_english = $v->getPropertyValue('ea:lemma:title_in_english');
		if(!empty($title_in_english)){
			$this->title = $title_in_english;
			$this->label = $title_in_english;
		}else{
			$this->title = $v->getPropertyValue('dc:title:');
		}

		$id = $v->persistenceId();
		$this->opac1['id'] = $id;
// 		$this->opac1['thumbs'] = PDao::getThumbs($id); ///THUMBNAILS//
		if (!empty(PDao::getThumbs2($id))){
			$this->opac1['thumbs'] = PDao::getThumbs2($id);
		}

		//Citations
		$lemma_manif = $v-> getVertices(GDirection::OUT,'ea:lemma:manifestation');
		if (!empty($lemma_manif)){
			$manif_citations = array();
			foreach ($lemma_manif as $manif){
				$manif_attributes = $manif->getAttributes();
				if(!empty($manif_attributes['opac1']['citation'])){
					$manif_citations[] = $manif_attributes['opac1']['citation'];
				}
			}
			$this->opac1['citations'] = $manif_citations;
// 			echo '<pre>'; print_r($manif_citations); echo '</pre>';
		}

	}


	/**
	 * @param GVertex $v
	 */
	private function processWebSiteInstance( $v){

		$id = $v->persistenceId();
		$this->opac1['id'] = $id;

		//Citation///////////////////////////////////////////////////////////////////
		// 		Author, A. A., & Author, B. B. (Date of publication). Title of article.
		// 		Title of Online Periodical, volume number(issue number if available). Retrieved from
		// 		http://www.someaddress.com/full/url/

		$title = $v->getPropertyValue('dc:title:');
		$title_remainder = $v->getPropertyValue('ea:manif:Title_Remainder');
		$website_url = $v->getPropertyValue('ea:website:url');
		$author = $v->getPropertyValue('ea:web_site_instance:author');
		$publication_date = $v->getPropertyValue('ea:manif:Publication_Date');

		$var_citation_templ = array(
				'authors' => $author,
				'publication_date' => $publication_date,
				'title' => $title,
				'title_remainder' => $title_remainder,
				'website_url' => $website_url,
		);

		$template = Config::get('arc_display_template.web_site_instance_citation');
		$citation = ArcTemplateEngine::renderLine($template,$var_citation_templ);
		$this->opac1['citation'] = $citation;
		///////////////////////////////////////////////////////////////////////////////
	}


	/**
	 * @param GVertex $v
	 */
	private function processMedia( $v){

		$id = $v->persistenceId();
		$this->opac1['id'] = $id;

		//TODO Citation
		$title = $v->getPropertyValue('dc:title:');
		$title_remainder = $v->getPropertyValue('ea:manif:Title_Remainder');

		$var_citation_templ = array(
				'title' => $title,
				'title_remainder' => $title_remainder,
		);

		$template = Config::get('arc_display_template.media_citation');
		$citation = ArcTemplateEngine::renderLine($template,$var_citation_templ);
		$this->opac1['citation'] = $citation;
		///////////////////////////////////////////////////////////////////////////////
	}


	/**
	 * @param GVertex $v
	 */
	private function processPeriodicPublication( $v){

		$id = $v->persistenceId();
		$this->opac1['id'] = $id;

		//Citation///////////////////////////////////////////////////////////////////
		$title = $v->getPropertyValue('dc:title:');
		$title_remainder = $v->getPropertyValue('ea:manif:Title_Remainder');
		$publisher_name = $v->getPropertyValue('ea:manif:Publisher_Name');
		$publication_date = $v->getPropertyValue('ea:manif:Publication_Date');
		$issue = $v->getPropertyValue('ea:issue:');

		$var_citation_templ = array(
				'publisher_name' => $publisher_name,
				'publication_date' => $publication_date,
				'title' => $title,
				'title_remainder' => $title_remainder,
				'issue' => $issue,
		);

		$template = Config::get('arc_display_template.periodic_publication_citation');
		$citation = ArcTemplateEngine::renderLine($template,$var_citation_templ);
		$this->opac1['citation'] = $citation;
		///////////////////////////////////////////////////////////////////////////////
	}


	/**
	 * @param GVertex $v
	 */
	private function processLemmaCategory( $v){

		$id = $v->persistenceId();
		$this->opac1['id'] = $id;
		$parent_category = $v->getFirstVertex(GDirection::OUT,'ea:category:parent');

		$v->addFlag('IS:category');

		if (!empty($parent_category)){
			$v->addFlag('LEVEL:2');
			$v->removeFlag('LEVEL:1');

			$parent_cat_label = GRuleUtil::getLabel($parent_category);
			$dc_title = $v->getPropertyValue('dc:title:');

			$var_templ = array(
					'parent_label' => $parent_cat_label,
					'dc_title' => $dc_title,
			);

			$template = Config::get('arc_display_template.lemma_category_label');
			$label = ArcTemplateEngine::renderLine($template,$var_templ);
			$this->label =  $label;

		}else{
			$v->addFlag('LEVEL:1');
			$v->removeFlag('LEVEL:2');
		}
	}


	/**
	 * @param GVertex $v
	 */
	private function processSubjectChain( $v){
				$jdata =  (!empty($v->getTmpAttribute('jdata'))) ? $v->getTmpAttribute('jdata') : null ;
				$chain_subjects =  (!empty($jdata['chain_subjects'])) ? $jdata['chain_subjects'] : null ;
				$v->setAttribute('chain_subjects', $chain_subjects);
	}


	/**
	 * @param GVertex $v
	 */
	private function processPlace($v){
		//Putil::logGreen('PROC PLACE: '. $v->urnStr());
		$context = $this->context;
// 		$type = $v->getProperty('ea:auth:Place_Type');

// 		if (!empty($type)){
// 			$tmp =$type->value();
// 			$type = ($tmp != 'undefined') ?  $type->prps('selected_value') : null;
// 		}
// 		$type = null;
// 		$vtype = $v->getFirstVertex(GDirection::OUT, 'ea:auth:Place_Type');
// 		if (!empty($vtype)){
// 			$type = GRuleUtil::getLabel($vtype);
// 		}

		$var_templ = array(
				'title' => $this->title,
// 				'type' => $type,
		);

		$template = Config::get('arc_display_template.place_label');
		$label = ArcTemplateEngine::renderLine($template,$var_templ);

		$id = $v->persistenceId();

		$this->opac1['id'] = $id;
		$this->label = $label;
// 		$this->title = $label;

// 		$this->opac1['thumbs'] = PDao::getThumbs($id); ///THUMBNAILS//
		$thumbs = PDao::getThumbs2($id);
		if (!empty($thumbs)){
			$this->opac1['thumbs'] = $thumbs;
		}

// 		$publication_places = $v->getVertices(GDirection::IN,'ea:manif:Publication_Place');
// 		if (!empty($publication_places)){
// 			$this->opac1['as_pplace'] = count($publication_places);
// 		}
		$manif_pubplaces = 0;
		$per_pubplaces = 0;
		$as_pubplaces = $v->getVertices(GDirection::IN,'ea:manif:Publication_Place');
		if (!empty($as_pubplaces)){
			$manif_pubplaces = count($as_pubplaces);
		}
		##################### DRYL ################################
		$as_per_pubplaces = $v->getVertices(GDirection::IN,'ea:periodic:publication_place');
		if (!empty($as_per_pubplaces)){
				$per_pubplaces = count($as_per_pubplaces);
		}
		###########################################################
		$this->opac1['as_pplace'] = $manif_pubplaces + $per_pubplaces;



		$subjects = $v->getVertices(GDirection::IN,'ea:subj:');
		$chain_link = $v->getVertices(GDirection::IN,'ea:inferred-chain-link:');
		if (!empty($subjects)){
				$this->opac1['as_subj'] = count($subjects);
				if (!empty($chain_link )){
					foreach ($chain_link as $cl){
						$as_subjects = $cl->getVertices(GDirection::IN,'ea:subj:');
						if (!empty($as_subjects)){
							$this->opac1['as_subj'] += count($as_subjects);
						}
					}
				}
			}elseif (!empty($chain_link)){
				foreach ($chain_link as $icl){
					$as_subjects = $icl->getVertices(GDirection::IN,'ea:subj:');
					if (!empty($as_subjects)){
						$this->opac1['as_subj'] = count($as_subjects);
					}
				}
			}

		$v->updatePropertyValue('ea:label:', null, $label);


	}



	/**
	 * @param GVertex $v
	 */
	private function processWork( $v){
		$context = $this->context;

		$id = $v->persistenceId();

		$w_part_number = $v->getPropertyValue('ea:work:Title_PartNumber');
		$w_title_part_name = $v->getPropertyValue('ea:work:Title_PartName');

		$element_title =null;
		$element_dc_title =null;
		$contributor_label =null;

		//First contributor concat to title
		$edgesOut = $v->getEdges(GDirection::OUT);
		$setting_json = Setting::get('contributor_work_type_map');
		foreach ($edgesOut as $e){
			$element = $e->element();
			if (array_key_exists($element, $setting_json)) {
				$contributor_label = $setting_json [$element];
				$elementVertex = $v->getFirstVertex(GDirection::OUT,$element);
				$element_title = GRuleUtil::getLabel($elementVertex);
				$element_dc_title = $elementVertex->getPropertyValue("dc:title:");
				break;
			}
		}

		//contributor name full-stop if not exist
		$dot = false;
		if (!empty($element_dc_title)){
			if(substr($element_dc_title, -1) != '.'){
				$dot = true ;
			}
		}

		$var_templ = array(
				'title' => $this->title,
				'part_number' => $w_part_number,
				'title_part_name' => $w_title_part_name,
				'element_title'=>$element_title,
				'element_dc_title'=>$element_dc_title,
				'full_stop'=>$dot,
				'contributor_label'=>$contributor_label,
		);

		$template = Config::get('arc_display_template.work_title');
		$title_punct = ArcTemplateEngine::renderLine($template,$var_templ);

		$template = Config::get('arc_display_template.work_title_opac');
		$dc_label = ArcTemplateEngine::renderLine($template,$var_templ);

		$label = $title_punct;

		$this->opac2['Title_punc'] =  $label;
		$this->label = $label;

// 		$v->setTmpAttribute('Title_punc', $label);
// 		$v->setTmpAttribute('label',$label);


		$this->opac1['id'] = $v->persistenceId();

		$mafs = ARRAY();
		$ditem_all = ARRAY();

		$etree = ARRAY();
		$expressions = $v->getVertices(GDirection::OUT,'ea:expression:');
		foreach ($expressions as $expr){
			//$context->addDebugMessage("EXPRESSION: " . $expr);
			//$expr_title = $expr->getPropertyValue('dc:title:');
// 			$expr_title = $expr->getTmpAttribute("Title_punc");

			$expr_title = GRuleUtil::getLabel($expr);

			##MUSIC
			$express_type = $expr->getPropertyValue('ea:form-type:');
			if (!empty($express_type)){
				if($express_type == 'auth-expression-music'){
					$form = $expr->getPropertyValue('ea:expres:Form_music');
					if (!empty($form)){
						$expr_title =  '['.$form.'] '.$expr_title;
					}
				}
			}
			#######

			//$manifs = $expr->getVertices(GDirection::OUT,'reverse:ea:work:');
			$manifs = $expr->getVertices(GDirection::OUT,'ea:workOf:');
			$emtree = ARRAY();
			foreach ($manifs as $manif){
				$mafs[] = $manif;

				$maif_id = $manif->persistenceId();
			//	$context->addDebugMessage("MANIFESTATION: " . $manif);
			//$manif_title = $manif->getPropertyValue('dc:title:');

			/**********************************************************************/
				//With Express
				$ditems =$manif->getVertices(GDirection::BOTH,'ea:artifact-of:');
				$dtree = ARRAY();
				foreach ($ditems as $ditem){

					$item_obj_type = $ditem->getPropertyValue('ea:obj-type:');
					$ditem_sublocation =null;
					$ditem_classification = null;

					if ($item_obj_type == 'digital-item'){

							$ditem_type = $ditem->getPropertyValue('ea:item:type');
							if(empty($ditem_type)){
								$type = $ditem->getFirstEdge(GDirection::OUT,'ea:item:type');
								if(!empty($type)){
									$ditem_type = $type->label();
								}
							}

							$ditem_partNumber = $ditem->getPropertyValue('ea:item:partNumber');
							$ditem_page = $ditem->getPropertyValue('ea:item:page');
							$var_templ3 = array(
									'type' => $ditem_type,
									'part_number' => $ditem_partNumber,
									'page' => $ditem_page,
							);
							$template3 = Config::get('arc_display_template.digital_item_catalogue_title');

					}else{
							$ditem_type = $ditem->getProperty('ea:item:type');
							$ditem_barcode = $ditem->getPropertyValue('ea:item:barcode');
							$ditem_partNumber = $ditem->getPropertyValue('ea:item:partNumber');
							$ditem_copyNumber = $ditem->getPropertyValue('ea:item:copyNumber');
							$ditem_sublocation = $ditem->getProperty('ea:item:sublocation');
							$ditem_classification = $ditem->getPropertyValue('ea:item:Classification');

							if (!empty($ditem_type)){
								$tmp =$ditem_type->value();
								$ditem_type = ($tmp != 'undefined') ?  $ditem_type->prps('selected_value') : null;
							}

							if (!empty($ditem_sublocation)){
								$tmp =$ditem_sublocation->value();
								$ditem_sublocation = ($tmp != 'undefined') ?  $ditem_sublocation->prps('selected_value') : null;
							}

							$var_templ3 = array(
									'type' => $ditem_type,
									'barcode' => $ditem_barcode,
									'part_number' => $ditem_partNumber,
									'copyNumber' => $ditem_copyNumber,
							);
							$template3 = Config::get('arc_display_template.physical_item_catalogue_title');
					}

					$ditem_label = ArcTemplateEngine::renderLine($template3,$var_templ3);
					$ditem_all[$maif_id]['type'][] = $ditem_label;
					$ditem_all[$maif_id]['id'][] = $ditem->persistenceId();
					$ditem_all[$maif_id]['object-type'][]= $item_obj_type;
					$ditem_all[$maif_id]['sublocation'][]= $ditem_sublocation;
					$ditem_all[$maif_id]['classification'][]= $ditem_classification;

					$dtree[] = ARRAY('title'=>$item_obj_type,'id'=>$ditem->persistenceId(),'label'=>$ditem_label, 'sublocation'=>$ditem_sublocation, 'classification'=>$ditem_classification);
				}

			/**********************************************************************/

				$manif_title = $manif->getTmpAttribute("Title_punc");

				//MUSIC
				$manif_type = $manif->getPropertyValue('ea:form-type:');
				if($manif_type == 'auth-manifestation-music' && !empty($manif_type )){
					$form_type = $manif->getProperty('ea:manif:Type');
					if (!empty($form_type)){
						$tmp = $form_type->value();
						$form_manif = ($tmp != 'undefined') ?  $form_type->prps('selected_value') : null;
						$manif_title = '['.$form_manif.'] '.$manif_title;
					}
				}

				$emtree[] = ARRAY('title'=>$manif_title,'id'=>$manif->persistenceId(), 'items'=>$dtree);
			}
			$etree[] = ARRAY('title'=>$expr_title, 'id'=>$expr->persistenceId(), 'manifestations'=>$emtree);

		}
		$this->opac1['expressions'] =$etree ;

		//$manifs = $v->getVertices(GDirection::OUT,'reverse:ea:work:');
		$manifs = $v->getVertices(GDirection::OUT,'ea:workOf:');
		$mtree = ARRAY();
		foreach ($manifs as $manif){
			$mafs[] = $manif;

			$maif_id = $manif->persistenceId();
			//$context->addDebugMessage("MANIFESTATION: " . $manif);
			//$manif_title = $manif->getPropertyValue('dc:title:');
			$manif_title = $manif->getTmpAttribute("Title_punc");

			/**********************************************************************/
			//Prototype Express
			$ditems =$manif->getVertices(GDirection::BOTH,'ea:artifact-of:');
			$dtree = ARRAY();
			foreach ($ditems as $ditem){
				$item_obj_type = $ditem->getPropertyValue('ea:obj-type:');
				$ditem_sublocation = null;
				$ditem_classification = null;

				if ($item_obj_type=='digital-item'){

					$ditem_type = $ditem->getPropertyValue('ea:item:type');
					if(empty($ditem_type)){
						$type = $ditem->getFirstEdge(GDirection::OUT,'ea:item:type');
						if(!empty($type)){
							$ditem_type = $type->label();
						}
					}

					$ditem_partNumber = $ditem->getPropertyValue('ea:item:partNumber');
					$ditem_page = $ditem->getPropertyValue('ea:item:page');
					$var_templ2 = array(
							'type' => $ditem_type,
							'part_number' => $ditem_partNumber,
							'page' => $ditem_page,
					);
					$template2 = Config::get('arc_display_template.digital_item_catalogue_title');

				}else{
					$ditem_type = $ditem->getProperty('ea:item:type');
					$ditem_barcode = $ditem->getPropertyValue('ea:item:barcode');
					$ditem_partNumber = $ditem->getPropertyValue('ea:item:partNumber');
					$ditem_copyNumber = $ditem->getPropertyValue('ea:item:copyNumber');
					$ditem_sublocation = $ditem->getProperty('ea:item:sublocation');
					$ditem_barcode = $ditem->getPropertyValue('ea:item:barcode');
					$ditem_classification = $ditem->getPropertyValue('ea:item:Classification');

					if (!empty($ditem_type)){
						$tmp =$ditem_type->value();
						$ditem_type = ($tmp != 'undefined') ?  $ditem_type->prps('selected_value') : null;
					}

					if (!empty($ditem_sublocation)){
						$tmp =$ditem_sublocation->value();
						$ditem_sublocation = ($tmp != 'undefined' ) ?  $ditem_sublocation->prps('selected_value') : null;
					}

					$var_templ2 = array(
							'type' => $ditem_type,
							'barcode' => $ditem_barcode,
							'part_number' => $ditem_partNumber,
							'copyNumber' => $ditem_copyNumber,
					);
					$template2 = Config::get('arc_display_template.physical_item_catalogue_title');
				}

				$ditem_label = ArcTemplateEngine::renderLine($template2,$var_templ2);
				$ditem_all[$maif_id]['type'][] = $ditem_label;
				$ditem_all[$maif_id]['id'][]= $ditem->persistenceId();
				$ditem_all[$maif_id]['object-type'][]= $item_obj_type;
				$ditem_all[$maif_id]['sublocation'][]= $ditem_sublocation;
				$ditem_all[$maif_id]['classification'][]= $ditem_classification;


				$dtree[] = ARRAY('title'=>$item_obj_type,'id'=>$ditem->persistenceId(),'label'=>$ditem_label, 'sublocation'=>$ditem_sublocation, 'classification'=>$ditem_classification);
			}
// 			$this->opac1['items'] = $dtree;
			/**********************************************************************/

			$mtree[] = ARRAY('title'=>$manif_title,'id'=>$manif->persistenceId(),'items'=>$dtree);
		}
		$this->opac1['manifestations'] = $mtree;


		$mc = count($mafs);
		$a = $v->getFirstVertex(GDirection::OUT, 'ea:work:authorWork');
		$author = null;
		if (!empty($a)){
			$author = array('title'=>GRuleUtil::getLabel($a), 'id'=>$a->persistenceId());
		}
		//if ($mc>1 || $mc == 0){
			$this->opac1['public_title'] =ARRAY('title'=>$dc_label/*$label*/,'id'=>$v->persistenceId(),'author'=>$author);

			$public_lines = array();
			foreach ($mafs as $m){
				$digital_items = array();
				$manif_title = $m->getTmpAttribute("Title_punc");

				//DRYLL
				$m_completion_level = null;
				$manif_completion_level = $m->getPropertyValue('ea:control:completion_level');
				if (isset($manif_completion_level)){
					$m_completion_level = $manif_completion_level;
				}
				//**

				$maif_id = $m->persistenceId();
				foreach($ditem_all as $dkey => $dvalue)
				{
						if($dkey == $maif_id){
							$digital_items = $dvalue;
						}
				}

				//DRYLL -->'completion_level'=>$m_completion_level
				$public_lines[]  = array('title'=>$manif_title, 'id'=>$m->persistenceId(),'items'=>$digital_items, 'completion_level'=>$m_completion_level);
			}

			$this->opac1['public_lines'] = $public_lines;


			//Flags is musical
			$work_type = $v->getPropertyValue('ea:form-type:');
			if (!empty($work_type)){
				if($work_type == 'auth-work-music'){
					$v->addFlag('IS:musical');
				}else{
					$v->removeFlag('IS:musical');
				}
			}
			//**

			//Dryll - Flags is old/new/corrected record
			$completion_level = $v->getPropertyValue('ea:control:completion_level');
			if (isset($completion_level)){
				$this->opac1['completion_level'] = $completion_level;
				if($completion_level == 0){
					$v->addFlag('IS:new');
					$v->removeFlag('IS:old');
					$v->removeFlag('IS:corrected');
				}elseif($completion_level == 1){
					$v->addFlag('IS:old');
					$v->removeFlag('IS:new');
					$v->removeFlag('IS:corrected');
				}elseif($completion_level == 2){
					$v->addFlag('IS:corrected');
					$v->removeFlag('IS:old');
					$v->removeFlag('IS:new');
				}else{
					$v->removeFlag('IS:old');
					$v->removeFlag('IS:new');
					$v->removeFlag('IS:corrected');
			}
			}
			//**

			///THUMBNAILS//
			$thumbs = PDao::getThumbs2($id);
			if (!empty($thumbs)){
				$this->opac1['thumbs'] = $thumbs;
			}else{
				$m = $v->getVertices(GDirection::IN, 'ea:work:'); //(direct) Manif of Work
				if (!empty($m)){
					foreach ($m as $manif){
						$manif_id = $manif->persistenceId();
						$manif_thumbs = PDao::getThumbs2($manif_id);
						if (!empty($manif_thumbs)){
							$this->opac1['thumbs'] = $manif_thumbs;
							break;
						}
					}
				}
				if (empty($this->opac1['thumbs'])){
					$e = $v->getVertices(GDirection::OUT, 'ea:expression:');//Express of Work
					if (!empty($e)){
						foreach ($e as $expres){
							$express_id = $expres->persistenceId();
							$express_thumbs =PDao::getThumbs2($express_id);
							if (!empty($express_thumbs)){
								$this->opac1['thumbs'] = $express_thumbs;
								break;
							}else{
								$m = $expres->getVertices(GDirection::IN, 'ea:work:'); //Manif of Express
								if (!empty($m)){
									foreach ($m as $manif){
										$manif_id = $manif->persistenceId();
										$manif_thumbs =PDao::getThumbs2($manif_id);
										if (!empty($manif_thumbs)){
											$this->opac1['thumbs'] = $manif_thumbs;
											break;
										}
									}
								}
							}
						}
					}
				}
			}


			//**PART RELATION
			//WORKS (individual,independent)
			foreach ($v->getFlags() as $fl){
				if($fl == "INDIVIDUAL_WORK"){
					$work_ind_container = $v->getVertices(GDirection::OUT,'ea:relation:containerOfIndependent');
					$work_independent = ARRAY();
					if (!empty($work_ind_container)){
						foreach ($work_ind_container as $work_i){
							$work_independent[] = ARRAY('id'=> $work_i->persistenceId(),'label'=> GRuleUtil::getLabel($work_i));
							$this->opac1['independent_works'] = $work_independent;
						}
					}
				}elseif($fl == "INDEPENDENT_WORK"){
					$work_ind_contained =	$v->getVertices(GDirection::OUT,'ea:relation:containedInIndividual');
					$work_individual = ARRAY();
					if (!empty($work_ind_contained)){
						foreach ($work_ind_contained as $work_i){
							$work_individual[] = ARRAY('id'=> $work_i->persistenceId(),'label'=> GRuleUtil::getLabel($work_i));
							$this->opac1['individual_works'] = $work_individual;
						}
					}
				}
			}
			//WORKS (contributions)
			$work_contributions_contained = $v->getVertices(GDirection::OUT,'ea:relation:containedInContributions');
			$work_contr = ARRAY();
			if (!empty($work_contributions_contained)){
				foreach ($work_contributions_contained as $work_c){
					$work_contr[] = ARRAY('id'=> $work_c->persistenceId(),'label'=> GRuleUtil::getLabel($work_c));
					$this->opac1['contained_in_contribution'] = $work_contr;
				}
			}

			$work_contributions_container = $v->getVertices(GDirection::OUT,'ea:relation:containerOfContributions');
			$work_contr = ARRAY();
			if (!empty($work_contributions_container)){
				foreach ($work_contributions_container as $work_c){
					$work_contr[] = ARRAY('id'=> $work_c->persistenceId(),'label'=> GRuleUtil::getLabel($work_c));
					$this->opac1['contained_contributions'] = $work_contr;
				}
			}
			//WORKS (documents)
			$work_documents_contained= $v->getVertices(GDirection::OUT,'ea:relation:containedInDocuments');
			$work_doc = ARRAY();
			if (!empty($work_documents_contained)){
				foreach ($work_documents_contained as $work_d){
					$work_doc[] = ARRAY('id'=> $work_d->persistenceId(),'label'=> GRuleUtil::getLabel($work_d));
					$this->opac1['contained_in_document'] = $work_doc;
				}
			}

			$work_documents_container = $v->getVertices(GDirection::OUT,'ea:relation:containerOfDocuments');
			$work_doc = ARRAY();
			if (!empty($work_documents_container)){
				foreach ($work_documents_container as $work_d){
					$work_doc[] = ARRAY('id'=> $work_d->persistenceId(),'label'=> GRuleUtil::getLabel($work_d));
					$this->opac1['contained_documents'] = $work_doc;
				}
			}
			//**

// 		} elseif ( $mc == 1){
// 			$m = $mafs[0];
// 			$manif_title = $m->getTmpAttribute("Title_punc");
// 			//$resp = $m->getPropertyValue('ea:manif:Title_Responsibility');
// 			//$this->opac1['public_title'] = array('title'=>$manif_title, 'id'=>$m->persistenceId(), 'author'=>$author, 'responsibility'=>$resp);
// 			$this->opac1['public_title'] = array('title'=>$manif_title, 'id'=>$m->persistenceId(), 'author'=>$author);
// 		}

	}


	/**
	 * @param GVertex $v
	 */
	private function processManifestation( $v){
		$context = $this->context;

		$id = $v->persistenceId();
		$this->opac1['id'] = $id;

		$title_punct = $v->getTmpAttribute('Title_punc');

		$this->opac2['Title_punc'] =  $title_punct;

		$this->label = $title_punct;

		$this->opac1['publication'] = (!empty($v->getTmpAttribute('publication')))?$v->getTmpAttribute('publication'):'';

		//*ITEMS
		//$ditems = $v->getVertices(GDirection::BOTH,'ea:artifact-of:');
		$ditems = $v->getVertices(GDirection::OUT,'reverse:ea:artifact-of:');
		$dtree = ARRAY();
		foreach ($ditems as $ditem){
			$item_obj_type = $ditem->getPropertyValue('ea:obj-type:');

			if ($item_obj_type=='digital-item'){

				$ditem_type = $ditem->getPropertyValue('ea:item:type');
				if(empty($ditem_type)){
					$type = $ditem->getFirstEdge(GDirection::OUT,'ea:item:type');
					if(!empty($type)){
						$ditem_type = $type->label();
					}
				}

				$ditem_partNumber = $ditem->getPropertyValue('ea:item:partNumber');
				$ditem_page = $ditem->getPropertyValue('ea:item:page');
				$var_templ = array(
						'type' => $ditem_type,
						'part_number' => $ditem_partNumber,
						'page' => $ditem_page,
				);
				$template = Config::get('arc_display_template.digital_item_catalogue_title');

			}else{
				//PHYSICAL-ITEM
				$ditem_type = $ditem->getProperty('ea:item:type');
				$ditem_location = $ditem->getProperty('ea:item:location');
				$ditem_sublocation = $ditem->getProperty('ea:item:sublocation');
				$ditem_office_code = $ditem->getPropertyValue('ea:item:office_code');
				$ditem_owner = $ditem->getPropertyValue('ea:item:ownerItem');
				$ditem_barcode = $ditem->getPropertyValue('ea:item:barcode');
				$ditem_partNumber = $ditem->getPropertyValue('ea:item:partNumber');
				$ditem_copyNumber = $ditem->getPropertyValue('ea:item:copyNumber');
				$ditem_classification = $ditem->getProperty('ea:item:Classification');

				if (!empty($ditem_type)){
					$tmp =$ditem_type->value();
					$ditem_type = ($tmp != 'undefined') ?  $ditem_type->prps('selected_value') : null;
				}

				if (!empty($ditem_location)){
					$tmp =$ditem_location->value();
					$ditem_location = ($tmp != 'undefined') ?  $ditem_location->prps('selected_value') : null;
				}

				if (!empty($ditem_sublocation )){
					$tmp =$ditem_sublocation ->value();
					$ditem_sublocation  = ($tmp != 'undefined') ?  $ditem_sublocation ->prps('selected_value') : null;
				}

				if (!empty($ditem_classification)){
					$ditem_classification = $ditem_classification->pnctn();
				}


				$var_templ = array(
						'type' => $ditem_type,
						'barcode' => $ditem_barcode,
						'part_number' => $ditem_partNumber,
						'copyNumber' => $ditem_copyNumber,
				);
				$template = Config::get('arc_display_template.physical_item_catalogue_title');
				$ditem_label = ArcTemplateEngine::renderLine($template,$var_templ);

				$ptree[] = ARRAY(
													'title'=>$item_obj_type,
													'id'=>$ditem->persistenceId(),
													'label'=>$ditem_label,
													'type'=>$ditem_type,
													'location'=>$ditem_location,
													'sublocation'=>$ditem_sublocation,
													'office_code'=>$ditem_office_code,
													'owner'=>$ditem_owner,
													'barcode'=>$ditem_barcode,
													'partNumber'=>$ditem_partNumber,
													'copyNumber'=>$ditem_copyNumber,
													'classification'=>$ditem_classification
												);
				$this->opac1['physical-items'] = $ptree;
			}
			///////////////////


			$ditem_label = ArcTemplateEngine::renderLine($template,$var_templ);
			$dtree[] = ARRAY('title'=>$item_obj_type,'id'=>$ditem->persistenceId(),'label'=>$ditem_label);
		}
		$this->opac1['items'] = $dtree;



		//*EXRESS-WORK LANG
		$manif_lang = ARRAY();
		$linked_work= $v->getFirstVertex(GDirection::OUT,'ea:work:');
		if (!empty($linked_work)){
			$expres_langs = $linked_work->getProperties('ea:expres:Language');
			if (!empty($expres_langs)){
				foreach ($expres_langs as $epl){
					$tmp =$epl->value();
					if($tmp != 'undefined'){
						$manif_lang[] = $epl->prps('selected_value');
					}
				}
				if(empty($manif_lang)){
					$linked_work= $v->getFirstVertex(GDirection::OUT,'inferred:ea:work:');
					if(!empty($linked_work)){
						$work_langs = $linked_work->getProperties('ea:work:Language');
						if (!empty($work_langs)){
							foreach ($work_langs as $wl){
								$tmp =$wl->value();
								if ($tmp != 'undefined'){
									$manif_lang[] = $wl->prps('selected_value');
								}
							}
						}
					}
				}
			}else{
				$work_langs = $linked_work-> getProperties('ea:work:Language');
				if (!empty($work_langs)){
					foreach ($work_langs as $wl){
						$tmp =$wl->value();
						if ($tmp != 'undefined'){
						$manif_lang[] = $wl->prps('selected_value');
						}
					}
				}
			}
		}

		##################### DRYL ################################
		$manif_langs = $v->getProperties('ea:expres:Language');
		if (!empty($manif_langs)){
			foreach ($manif_langs as $ml){
				$tmp =$ml->value();
				if ($tmp != 'undefined'){
					$manif_lang[] = $ml->prps('selected_value');
				}
			}
		}

		$periodic = $v->getFirstVertex(GDirection::IN,'ea:hasIssue:');
		if (!empty($periodic )){
			$periodic_langs = $periodic->getProperties('ea:expres:Language');
			if (!empty($periodic_langs)){
				foreach ($periodic_langs as $pl){
					$tmp =$pl->value();
					if ($tmp != 'undefined'){
						$manif_lang[] = $pl->prps('selected_value');
					}
				}
			}
		}

		$completion_level = $v->getPropertyValue('ea:control:completion_level');
		if (isset($completion_level)){
			$this->opac1['completion_level'] = $completion_level;
			if($completion_level == 0){
				$v->addFlag('IS:new');
				$v->removeFlag('IS:old');
				$v->removeFlag('IS:corrected');
			}elseif($completion_level == 1){
				$v->addFlag('IS:old');
				$v->removeFlag('IS:new');
				$v->removeFlag('IS:corrected');
			}elseif($completion_level == 2){
				$v->addFlag('IS:corrected');
				$v->removeFlag('IS:old');
				$v->removeFlag('IS:new');
			}else{
				$v->removeFlag('IS:old');
				$v->removeFlag('IS:new');
				$v->removeFlag('IS:corrected');
			}
		}
		###########################################################


		//ISSUE PUBLISHER
		$manif_type = $v->getPropertyValue('ea:form-type:');
		if (!empty($manif_type)){

			if($manif_type == 'issue'){
				$periodic = $v->getFirstVertex(GDirection::IN, 'ea:hasIssue:');
				if (!empty($periodic)){
					$ppublishers = $periodic->getVertices(GDirection::OUT, 'ea:periodic:publisher_name');
					$publishers = array();
					if (!empty($ppublishers)){
						foreach ($ppublishers as $pub){
							//$value_array['name'] = = GRuleUtil::getLabel($publisher);
							$value_array['name'] = $pub->getPropertyValue('dc:title:');
							$value_array['id'] = $pub->persistenceId();
							$publishers[]= $value_array;
						}
					}

					$this->opac1['publishers'] = $publishers;
				}
			}
		}


		//Authors
		$work_indirect = $v->getFirstVertex(GDirection::OUT,'inferred:ea:work:');
		$work_direct_out = $v->getFirstVertex(GDirection::OUT,'ea:work:');
		$work_direct_in = $v->getFirstVertex(GDirection::IN,'ea:workOf:');

		if(!empty($work_direct_out)){
			$authors_work = $work_direct_out->getVertices(GDirection::OUT,'ea:work:authorWork');
		}else if(!empty($work_direct_in)){
			$authors_work = $work_direct_in->getVertices(GDirection::OUT,'ea:work:authorWork');
		}else if(!empty($work_indirect)){
			$authors_work = $work_indirect->getVertices(GDirection::OUT,'ea:work:authorWork');
		}

		$i=0;
		$auth_w_citation = array();
		$auth_w_opac = array();
		if (!empty($authors_work)){
			foreach ($authors_work as $author){
				// 				$auth_w[]['name'] = GRuleUtil::getLabel($author);
// 				$auth_w[]['name'] = $author->getPropertyValue('dc:title:');
				$value_array['name'] = $author->getPropertyValue('dc:title:');
				$value_array['id'] = $author->persistenceId();
				$auth_w_opac[]= $value_array;
				$i++;
				if($i>1){
					$value_array['delimiter'] ='true';
				}
				$auth_w_citation[]= $value_array;
			}
		}

		$this->opac1['authors'] = $auth_w_opac;//$authors;


		//Work-Periodic/ Categories/ Conferences////////////////////////////////////////////////////////////////////////////////////////
		$w_categories = array();
		$w_category = array();
		$periodic_direct = $v->getFirstVertex(GDirection::IN,'ea:hasIssue:');
		if(!empty($work_direct_in)){
			$work_categories = $work_direct_in->getVertices(GDirection::OUT,'ea:work:subjectCategory');
			foreach ($work_categories as $category){
				$w_category['label'] = $category->getAttribute('label');
				$w_category['id'] = $category->persistenceId();
				$w_categories[] = $w_category;
			}
			if (!empty($w_categories)){
				$this->opac1['work_categories'] = $w_categories;
			}

			### Dryll Conference #####################
			if(!empty($work_direct_in)){
				$conference = $work_direct_in->getFirstVertex(GDirection::OUT,'ea:work:conference_event');
				if (!empty($conference)){
					$conf = array();
					$c_coordinator = array();
					$c_coordinators = array();

					$conf['label'] = $conference->getPropertyValue('dc:title:');
					$conf['id'] = $conference->persistenceId();
					$this->opac1['conference'] = $conf;

					$conf_coordinators= $conference->getVertices(GDirection::OUT,'ea:event:coordinator_of_conference');
					foreach ($conf_coordinators as $coordinator){
						$c_coordinator['label'] = $coordinator->getAttribute('label');
						$c_coordinator['id'] = $coordinator->persistenceId();
						$c_coordinators[] = $c_coordinator;
					}
					if (!empty($c_coordinators)){
							$this->opac1['conf_coordinators'] = $c_coordinators;
					}
				}
			}
			############################################

		}elseif ($periodic_direct){
			$periodic_categories = $periodic_direct->getVertices(GDirection::OUT,'ea:periodic:category');
			foreach ($periodic_categories as $category){
				$w_category['label'] = $category->getAttribute('label');
				$w_category['id'] = $category->persistenceId();
				$w_categories[] = $w_category;
			}
			if (!empty($w_categories)){
				$this->opac1['periodic_categories'] = $w_categories;
			}
		}

		//Citation///////////////////////////////////////////////////////////////////
		//Author, A. A. (Year of publication). Title of work: Capital letter also for subtitle. Location: Publisher.

		$title_remainder = $v->getPropertyValue('ea:manif:Title_Remainder');

		$publication_name = null;
		$publication_place = null;
		$publication_date = null;

		$ps = $v->getProperties('ea:manif:Publication');
		if (!empty($ps)){
			foreach ($ps as $psid => $p){
				$tid = $p->treeId();
				if (!empty($tid)){
					$tps = $v->getChildProperties($tid);
					foreach ($tps as $tp){
						$prps = $tp->prps();
						if ($tp->element() == 'ea:manif:Publication_Place'){
							$publication_place = $tp->value();
						}elseif ($tp->element() == 'ea:manif:Publisher_Name'){
							$publication_name = $tp->value();
						}elseif ($tp->element() == 'ea:manif:Publication_Date'){
							$publication_date =  $tp->valueJson();
							if(!empty($publication_date['t'])){
								$publication_date = $publication_date['t'];
							}else{
								$publication_date = $publication_date['y'];
							}
						}
					}
				}
			}
		}


		$var_citation_templ = array(
				'authors' => $auth_w_citation,
				'publication_date' => $publication_date,
				'title' => $v->getPropertyValue('dc:title:'),
				'title_remainder' => $title_remainder,
				'publication_place' => $publication_place,
				'publication_name' => $publication_name,
		);

		$template = Config::get('arc_display_template.manifestation_citation');
		$citation = ArcTemplateEngine::renderLine($template,$var_citation_templ);
		$this->opac1['citation'] = $citation;
		///////////////////////////////////////////////////////////////////////////////


		$this->opac1['lang'] = 	$manif_lang;
		$v->setTmpAttribute('Manif_lang', $manif_lang); //Solr tmp var

		//Flags is issue/book/musical && Form-type
		$manif_type = $v->getPropertyValue('ea:form-type:');
		if (!empty($manif_type)){
			($manif_type == 'issue') ? $v->addFlag('IS:issue') : $v->removeFlag('IS:issue') ;
			($manif_type == 'book') ? $v->addFlag('IS:book') : $v->removeFlag('IS:book') ;

			if($manif_type == 'auth-manifestation-music'){
				$v->addFlag('IS:musical');
				$form_type = $v->getProperty('ea:manif:Type');
				if (!empty($form_type)){
					$tmp = $form_type->value();
					$this->opac1['form'] = ($tmp != 'undefined') ?  $form_type->prps('selected_value') : null;
				}
			}else{
				$v->removeFlag('IS:musical') ;
			}
		}
		//**

		///THUMBNAILS//
		$thumbs = PDao::getThumbs2($id);
		if (!empty($thumbs)){
			$this->opac1['thumbs'] = $thumbs;
		}elseif($manif_type == 'issue'){
			$periodic = $v->getFirstVertex(GDirection::IN, 'ea:hasIssue:');//Periodic of issue
			if (!empty($periodic)){
				$periodic_id = $periodic->persistenceId();
				$periodic_thumbs =PDao::getThumbs2($periodic_id);
				if (!empty($periodic_thumbs)){
					$this->opac1['thumbs'] = $periodic_thumbs;
				}
			}
		}else{
			$we = $v->getFirstVertex(GDirection::OUT, 'ea:work:');//Express of Manif or Work of Manif
			if (!empty($we)){
				$we_id = $we->persistenceId();
				$we_thumbs =PDao::getThumbs2($we_id);
				if (!empty($we_thumbs)){
					$this->opac1['thumbs'] = $we_thumbs;
				}else{
					$work_inf = $we->getFirstVertex(GDirection::OUT, 'inferred:ea:work:'); //Work of Manif (direct)
					if (!empty($work_inf)){
						$work_inf_id = $work_inf->persistenceId();
						$work_inf_thumbs =PDao::getThumbs2($work_inf_id);
						if (!empty($work_inf_thumbs)){
							$this->opac1['thumbs'] = $work_inf_thumbs;
						}
					}
				}
			}
		}


		//*WORKS (individual,independent)
		$workIndependent = $v->getVertices(GDirection::OUT,'ea:work:independent');
		foreach ($workIndependent  as $work_i){
			$work_ind[] = ARRAY('id'=> $work_i->persistenceId(),'label'=> GRuleUtil::getLabel($work_i));
			$this->opac1['independent_works'] = $work_ind;
		}
		//**

		//*WORKS (contributions)
		$work_contributions_out = $v->getVertices(GDirection::OUT,'ea:work:contribution');
		if (!empty($work_contributions_out)){
			foreach ($work_contributions_out as $work_c){
				$work_contr[] = ARRAY('id'=> $work_c->persistenceId(),'label'=> GRuleUtil::getLabel($work_c));
				$this->opac1['contained_contributions'] = $work_contr;
			}
		}
		//**

		//*WORKS (documents)
		$work_documents_out = $v->getVertices(GDirection::OUT,'ea:work:documents');
		if (!empty($work_documents_out)){
			foreach ($work_documents_out as $work_d){
				$work_doc[] = ARRAY('id'=> $work_d->persistenceId(),'label'=> GRuleUtil::getLabel($work_d));
				$this->opac1['contained_documents'] = $work_doc;
			}
		}
		//**

	}


	/**
	 * @param GVertex $v
	 */
	private function processExpression( $v){
		$context = $this->context;

		$id = $v->persistenceId();
		$this->opac1['id'] = $id;

		$title_punct = $v->getTmpAttribute('Title_punc');
		$this->opac2['Title_punc'] =  $title_punct;
		$this->label = $title_punct;

		//WORKS
		$works = $v->getVertices(GDirection::OUT,'ea:expressionOf:');
		$wtree= ARRAY();
		foreach ($works as $work){
			$work_id = $work->persistenceId();
			//$manif_title = $manif->getPropertyValue('dc:title:');
			$work_title =  GRuleUtil::getLabel($work);
			$wtree[] = ARRAY('title'=>$work_title,'id'=>$work_id,/*'items'=>$dtree*/);
		}
		$this->opac1['works'] = $wtree;

		//MANIFS
		$manifs = $v->getVertices(GDirection::OUT,'ea:workOf:');
		$mtree = ARRAY();
		foreach ($manifs as $manif){
			$mafs[] = $manif;
			$maif_id = $manif->persistenceId();
			//$manif_title = $manif->getPropertyValue('dc:title:');
			$manif_title = $manif->getTmpAttribute("Title_punc");

			##MUSIC
			$manif_type = $manif->getPropertyValue('ea:form-type:');
			if (!empty($manif_type)){
				if($manif_type == 'auth-manifestation-music'){
					$form_type = $manif->getProperty('ea:manif:Type');
					if (!empty($form_type)){
						$tmp = $form_type->value();
						$form = ($tmp != 'undefined') ?  $form_type->prps('selected_value') : null;
						$manif_title =  '['.$form.'] '.$manif_title;
					}
				}
			}
			#######

			$mtree[] = ARRAY('title'=>$manif_title,'id'=>$manif->persistenceId(),/*'items'=>$dtree*/);
		}
		$this->opac1['manifestations'] = $mtree;


		//Flags is musical & Express_Form
		$express_type = $v->getPropertyValue('ea:form-type:');
		if (!empty($express_type)){
			if($express_type == 'auth-expression-music'){
				$this->opac1['form'] = $v->getPropertyValue('ea:expres:Form_music');
				$v->addFlag('IS:musical');
				$form = $v->getPropertyValue('ea:expres:Form_music');
				if (!empty($form)){
					$this->opac2['Title_punc'] =  '['.$form.'] '.$title_punct;
				}
			}else{
				$v->removeFlag('IS:musical');
			}
		}
		//**

// 		$this->opac1['thumbs'] = PDao::getThumbs($id); ///THUMBNAILS//
		$thumbs = PDao::getThumbs2($id);
		if (!empty($thumbs)){
			$this->opac1['thumbs'] = $thumbs;
		}

	}

	/**
	 * @param GVertex $v
	 */
	private function processActor($v){
// // 		$context = $this->context;
// // 		$title_punct = $v->getTmpAttribute('Title_punc');
// // 		$this->opac2['Title_punc'] =  $title_punct;
// // 		$this->label = $title_punct;
// 		$context = $this->context;
// 		$title_punct = $v->getTmpAttribute('Title_punc');
// 		$this->label = $title_punct;

// 		$p_numeration = $v->getPropertyValue('ea:auth:Person_Numeration');
// 		$p_fuller_name = $v->getPropertyValue('ea:auth:Person_FullerName');
// 		$p_dates_associated  = $v->getPropertyValue('ea:auth:Person_DatesAssociated');
// // 		$p_titles_associated  = $v->getProperty('ea:auth:Person_TitlesAssociated');
// 		$p_titles_associated  = $v->getProperties('ea:auth:Person_TitlesAssociated');

// // 		if(!empty($p_titles_associated)){
// // 			$tmp = $p_titles_associated->value();
// // 			$p_titles_associated = ($tmp != 'undefined') ?  $tmp : null;
// // 		}

// 		$value_array = array();
// 		$list_array = array();

// 		if(!empty($p_titles_associated)){
// 			$i=0;
// 			foreach ($p_titles_associated as $vi) {
// 				$val1 = $vi-> value();
// 				$i++;
// 				if($i>1){
// 					$value_array['delimiter'] ='true';
// 				}

// 				if(!empty($val1)){
// 					$value_array['value'] = $val1;
// 					$list_array['list'][] = $value_array;
// 				}
// 			}
// 		}

// 		$var_templ = array(
// 		          'title' => $this->title,
// 		          'person_dates_associated' => $p_dates_associated,
// 		          'person_numeration' => $p_numeration,
// 		          'person_fuller_name' => $p_fuller_name,
// 		          'titles_associated' => $list_array,
// 		      );

// 		$template = Config::get('arc_display_template.person_title');
// 		$label = ArcTemplateEngine::renderLine($template,$var_templ);

		$id = $v->persistenceId();

		$label = $v->getTmpAttribute('label');

		$this->opac2['Title_punc'] =  $label;
		$this->opac1['public_title'] =ARRAY('title'=>$label,'id'=>$id);

		$v->updatePropertyValue('ea:label:', null, $label);

		if (empty($this->label)){
			$this->label =  $label;
		}


		$manif_publishers = 0;
		$per_publishers = 0;
		$as_publishers = $v->getVertices(GDirection::IN,'ea:manif:Publisher_Name');
		if (!empty($as_publishers)){
			$manif_publishers = count($as_publishers);
		}
		##################### DRYL ################################
		$as_per_publishers = $v->getVertices(GDirection::IN,'ea:periodic:publisher_name');
		if (!empty($as_per_publishers)){
			$per_publishers = count($as_per_publishers);
		}
		###########################################################
		$this->opac1['as_publishers'] = $manif_publishers + $per_publishers;


		$as_subj = $v->getVertices(GDirection::IN,'ea:subj:');
		$chain_link = $v->getVertices(GDirection::IN,'ea:inferred-chain-link:');
		if (!empty($as_subj)){
				$this->opac1['as_subj'] = count($as_subj);
				if (!empty($chain_link)){
					foreach ($chain_link as $cl){
						$as_subj  = $cl->getVertices(GDirection::IN,'ea:subj:');
						if (!empty($as_subj)){
							$this->opac1['as_subj'] += count($as_subj);
						}
					}
				}
			}elseif (!empty($chain_link)){
				foreach ($chain_link as $cl){
					$as_subj = $cl->getVertices(GDirection::IN,'ea:subj:');
					if (!empty($as_subj)){
						$this->opac1['as_subj'] = count($as_subj);
					}
				}
			}

		//$this->opac1['thumbs'] = PDao::getThumbs($id); ///THUMBNAILS//
		$thumbs = PDao::getThumbs2($id);
		if (!empty($thumbs)){
			$this->opac1['thumbs'] = $thumbs;
		}

	}


	/**
	 * @param GVertex $v
	 */
	protected function processVertex( $v){
		//Putil::logGreen('OPACDATA: '. $v->urnStr());
		$this->title = null;
		$this->label = null;
		$this->opac1 = array();
		$this->opac2 = array();

		$v->addFlag('OT:'. $v->getObjectType());

		$context = $this->context;
		//$context->addDebugMessage("OpacData: " . $v);


		$id = $v->persistenceId();
		//GGraphUtil::dumpVertex($v);
		$g = $v->graph();

		$title = $v->getPropertyValue('dc:title:');
		$this->title  = $title;

		$obj_type = $v->getPropertyValue('ea:obj-type:');

		if ($obj_type == 'auth-person'|| $obj_type == 'auth-family'|| $obj_type == 'auth-organization'){
			$this->processActor( $v);
		}elseif  ($obj_type == 'auth-manifestation'){
			$this->processManifestation( $v);
		}elseif  ($obj_type == 'auth-expression'){
			$this->processExpression( $v);
		}elseif ($obj_type == 'auth-work'){
			$this->processWork($v);
		}elseif  ($obj_type == 'periodic'){
			$this->processPeriodic($v);
		}elseif($obj_type == 'auth-place'){
			$this->processPlace($v);
		}elseif ($obj_type == 'digital-item'){
			$this->processDigitalItem( $v);
		}elseif  ($obj_type == 'physical-item'){
				$this->processPhysicalItem( $v);
		}elseif  ($obj_type == 'lemma'){
				$this->processLemma($v);
		}elseif  ($obj_type == 'lemma-category'){
				$this->processLemmaCategory($v);
		}elseif  ($obj_type == 'web-site-instance'){
				$this->processWebSiteInstance($v);
		}elseif  ($obj_type == 'media'){
				$this->processMedia($v);
		}elseif  ($obj_type == 'periodic-publication'){
				$this->processPeriodicPublication($v);
		}elseif  ($obj_type == 'subject-chain'){
				$this->processSubjectChain($v);
		}else{
			$this->processSubjectEntities( $v);
		}

		if (empty($this->label)){
			$this->label = GRuleUtil::getLabel($v);
		}


// 		$this->context->addDebugMessage("OPAC id: " . $id . " title: " . $title);
// 		$this->context->addDebugMessage("OPAC id: " . $id . " label: " . $this->label);

		$this->opac1['title'] = $this->title;
		//$this->opac1['label'] = $label;
		$this->opac1['obj_type'] = $obj_type;

// 		$this->opac1['flags'] = $v->getFlags(); //FLAGS//
// 		$this->opac1['thumbs'] = PDao::getThumbs($id); ///THUMBNAILS//


		$entity_lang = $v->getProperty('ea:auth:Person_Entity_Language');
		if (!empty($entity_lang)){
			$tmp = $entity_lang->value();
			if( $tmp != 'undefined' ){
				$this->opac1['entity_lang'] = $entity_lang->prps('selected_value');
			}
		}

		if( (!empty($v->getFirstEdge(GDirection::IN,'ea:inferred-chain-link:')) || !empty($v->getFirstEdge(GDirection::IN,'ea:subj:'))) && ($obj_type !='subject-chain') ){
			$v->addFlag('IS:subject');
		}

		$data = $v->getTmpAttribute('jdata');
		if (empty($data)){
			$data = array();
		}

		$data['opac1'] = $this->opac1;
		$data['opac2'] = $this->opac2;
		$data['label'] = $this->label;

		//$this->context->addDebugMessage("OPAC id: " . $id . " data: " . print_r($data,true));

		//$cmd = new GRuleOpacDataCmd($id, $data,$this->label,$this->title);
		//$context->putCommand('V_OPAC_DATA_' . $id, $cmd);
// 		$l =$this->label;
// 		$t = $this->title;

		$v->setTmpAttribute('label', $this->label);

		$v->setAttribute('opac1', $this->opac1);
		$v->setAttribute('opac2', $this->opac2);
		$v->setAttribute('label', $this->label);
		$v->setAttribute('title', $this->title);
		//$this->opacDataArray[$id] = array($data,$l,$t);

	}

	public function postExecute() {
	//	$this->context->put('OPAC_DATA_ARRAY', $this->opacDataArray);
	}


}
