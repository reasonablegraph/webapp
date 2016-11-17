<?php



class DisplayCommands {



/**
 * @param DisplayContext $context
 */
  public static function init($context,$options){
    //$context->dump();
    $idata = $context->getItemMetadata();
    //$idata->dump();
    $ib = $context->getItemBasics();
    //print_r($ib);
    $itemId = $ib['item_id'];
    $context->set('_itemId', $itemId);
    $lang = get_lang();
    $context->set('_lang', $lang);

    $title = $idata->getFirstItemValue('dc:title:');
    $title_txt =  empty($title) ? null : htmlspecialchars($title->textValue());
    $context->set('_title', $title_txt);

    $status= $idata->getFirstItemValue('ea:status:');
    $status_txt = empty($status) ? 'internal':$status->textValue();
    $context->set('_status', $status_txt);

    $thumbs_s = $context->getThumbsSmall();
    $thumbs_b = $context->getThumbsBig();

    $has_thumbs_s = (!empty($thumbs_s));
    $has_thumbs_b = (!empty($thumbs_b));
    $has_page_thumbs_s = ($has_thumbs_s && count($thumbs_s) > 0);
    $has_page_thumbs_b = ($has_thumbs_b && count($thumbs_b) > 0);
    $context->set('_has_thumbs_s',$has_thumbs_s);
    $context->set('_has_thumbs_b',$has_thumbs_b);
    $context->set('_has_page_thumbs_s',$has_page_thumbs_s);
    $context->set('_has_page_thumbs_b',$has_page_thumbs_b);


    $obj_type_names = get_object_type_names();
    $context->set('_obj_type_names',$obj_type_names);

   // $repo_maintainer_flag = user_access(Config::get('arc.PERMISSION_REPO_MENTAINER'));
    $repo_maintainer_flag = ArcApp::has_permission(Permissions::$REPO_MAINTAINER );
    $context->set('_userIsRepoMaintainer',$repo_maintainer_flag);

    $repo_login_flag = ArcApp::has_permission(Permissions::$REPO_LOGIN );
    $context->set('_userIsRepoLogin',$repo_login_flag);

    $label_width = Putil::safeArrGet($options, 'label_width',null);
    $context->set('_label_width',$label_width);

// 		$grid_class_prefix = Putil::safeArrGet($options, 'grid_class_prefix','col-md-');
// 		$grid_col_1_size  = Putil::safeArrGet($options, 'grid_col_1_size',3);
// 		$grid_col_1_class = $grid_class_prefix . $grid_col_1_size;
// 		$grid_col_2_class = $grid_class_prefix .  (12 - $grid_col_1_size);
// 		$context->set('_grid_col_1_class',$grid_col_1_class);
// 		$context->set('_grid_col_2_class',$grid_col_2_class);

$grid_class_prefix = Putil::safeArrGet($options, 'grid_class_prefix','col-md-');
$context->set('_grid_class_prefix',$grid_class_prefix);
// 		$context->set('_grid_col_1_size',$grid_col_1_size);

  }





  /**
   * @param DisplayContext $context
   */
  public static function setGridColClass($context,$options){
    $grid_class_prefix = Putil::safeArrGet($options, 'grid_class_prefix','col-md-');
    $grid_col_1_size  = Putil::safeArrGet($options, 'grid_col_1_size',4);
    $grid_col_1_class = $grid_class_prefix . $grid_col_1_size;
    $grid_col_2_class = $grid_class_prefix . (12 - $grid_col_1_size) ;
    $context->set('_grid_col_1_class',$grid_col_1_class);
    $context->set('_grid_col_2_class',$grid_col_2_class);
  }


/**
 * DEPENDS INIT
 * @param DisplayContext $context
 */
  public static function hasFoto($context,$options){
    $response = $options['response'];
    $context->set($response,$context->get('_has_thumbs_s'));
  }


 /**
   * DEPENDS INIT
   * @param DisplayContext $context
   */
    public static function isDetailView($context,$options){
      $response = $options['response'];
      if (isset($_GET['dv'])) {
        $detail_view = true;
      }else {
        $detail_view = false;
      }
      $context->set($response,$detail_view);
    }


/**
   * DEPENDS INIT
   * @param DisplayContext $context
   */
    public static function isScreenReader($context,$options){
      $response = $options['response'];
      $context->set($response,true);
    }


  /**
   * DEPENDS TO context->itemId (init)
   * @param DisplayContext $context
   */
  public static function  detail_link($context,$options){
    $item_id = $context->get('_itemId');
    if($context->get('isDetailView') == false){
      $text = tr('Detail view');
      printf('<div class="row"><div class="col-md-3 col-md-offset-9"><a href="/archive/item/%s?dv=1">%s</a></div></div>',$item_id,$text);
    }else{
      $text = tr('Simple view');
      printf('<div class="row"><div class="col-md-3 col-md-offset-9"><a href="/archive/item/%s">%s</a></div></div>',$item_id,$text);
    }
  }


  /**
   * DEPENDS INIT
   * @param DisplayContext $context
   */
  public static function isUserRepoMaintainer($context,$options){
    $response = $options['response'];
    $context->set($response,$context->get('_userIsRepoMaintainer'));
  }

  /**
   * DEPENDS INIT
   * @param DisplayContext $context
   */
  public static function isUserRepoLogin($context,$options){
    $response = $options['response'];
    $context->set($response,$context->get('_userIsRepoLogin'));
  }




  /**
   * @param DisplayContext $context
   */
  public static function title_europeana($context,$options){

  	$idata = $context->getItemMetadata();
  	$val = $idata->getFirstItemValue('ea:europeana:key1');
		$rdf_link = empty($val) ? null : '/archive/edm/' . $val->textValue();

  	$title = $context->get('_title');
  	if (!empty($title)){
  		if($context->get('isScreenReader') == true){
  			printf('<h1>%s</h1>', $title);
  		}else{
  			$class="item-title";
				if (empty($rdf_link)){
  				printf('<div class="row"><h1 class="item-title">%s</h1></div>', $title);
				} else {
  				printf('<div class="row title-person"><div class="item-title-person"><h1 class="%s">%s</h1></div><div class="rda_link"><a href="%s" target="_blank"><img alt="rdf_link" src="/_assets/img/rdf.png"></a></div></div>',$class,htmlspecialchars($title),$rdf_link);
				}
  		}
  	}
  }


  public static function MarcMnem($context,$options){

  	//$format = Putil::safeArrGet($options, 'format', 'marc-xml');
  	$output_format = Putil::safeArrGet($options, 'output_format', 'mnem');
  	$idata = $context->getItemMetadata();
  	/* @var  $idata ItemMetadata */
  	$val = $idata->getFirstItemValue('ea:marc:record:xml');
		if (empty($val)){
			return;
		}
		$xml = $val->textValue();



		if ($output_format == 'xml') {
			echo '<div style="white-space: nowrap; unicode-bidi: embed; font-family: monospace; white-space: pre;">';
			print_r(htmlentities($xml));
			echo '</div>';
			return;
		}

		if ($output_format == 'mnem') {
			$journals = new File_MARCXML($xml, File_MARC::SOURCE_STRING);
			$record = $journals->next();

			if ($record) {
				echo '<div style="white-space: nowrap; unicode-bidi: embed; font-family: monospace; white-space: pre; border-left: 1px solid black; padding-left: 5px; margin-top: 15px;">';
				echo FileMarcUtil::record2HtmlString($record);
				echo '</div>';
				return;
			}
		}

  }


  /**
   * @param DisplayContext $context
   */
  public static function openDiv($context,$options){

    $col_size = Putil::safeArrGet($options, 'grid_col_size',null);
    $class = '';
    $role = '';
    $sep = '';
    $aria_label = '';
    if(! empty($col_size)){
      $class =  $context->get('_grid_class_prefix') . $col_size;
      $sep = ' ';
    }
    if (isset($options['class'])){
      $class .= ($sep . $options['class']);
    }
    $options['class'] = $class;

    if (isset($options['role'])){
      $role .= ($sep . $options['role']);
    }
    $options['role'] = $role;

    if (isset($options['aria-label'])){
      $aria_label .= ($sep . $options['aria-label']);
    }
    $options['aria-label'] = $aria_label;


    $attrs = PUtil::getMapWithKeys($options, array('id','class','aria-hidden','role','aria-label'));
    echo(PSnipets::createElementString('div',$attrs));
  }

  /**
   * @param DisplayContext $context
   */
  public static function closeDiv($context,$options){
      printf('</div>');
  }

  /**
   * @param DisplayContext $context
   */
  public static function openUl($context,$options){
    $attrs = PUtil::getMapWithKeys($options, array('id','class','aria-hidden'));
    echo(PSnipets::createElementString('ol',$attrs));
  }

  /**
   * @param DisplayContext $context
   */
  public static function closeUl($context,$options){
    printf('</ol>');
  }

  /**
   * DEPENDS TO INIT
   * @param DisplayContext $context
   */
  public static function foto($context,$options){
    //$context->dump();

//   	echo '<pre>'; print_r($vals); echo '</pre>';

    $thumbs_s = $context->getThumbsSmall();
    $thumbs_b = $context->getThumbsBig();

    $has_thumbs_s = $context->get('_has_thumbs_s');
    $has_thumbs_b = $context->get('_has_thumbs_b');

    $has_page_thumbs_s = $context->get('_has_page_thumbs_s');
    $has_page_thumbs_b = $context->get('_has_page_thumbs_b');

    $thumb_s = null;
    if ($has_thumbs_s && isset($thumbs_s[0])){
      $thumb_s = $thumbs_s[0];
    }
    $thumb_b = null;
    if ($has_thumbs_b && isset($thumbs_b[0])){
      $thumb_b = $thumbs_b[0];
    }

//     $title = $context->get('_title');

//     $idata = $context->getItemMetadata();
//     $thumb_description = $idata->getTextValue('ea:auth:thumb_description');

    $thumb_description = $context->getThumbsDescription();
    //     if (!empty($thumb_description)){
    //     	echo '<br>'. $thumb_description;
    //     }

    if(!empty($thumb_description)){
      $title = tr($thumb_description);
      $aria_hidden = "false";
    }else{
      $title = tr('Photo of item');
      $aria_hidden = "true";
    }

    //echo('<div class="itemthumb">');
    if (empty($thumb_s)){
      printf('<img src="/_assets/img/na.png" alt="%s" aria-hidden = "true" />',$title);

// 			/** Mimetype Thumb**/
//       $ib = $context->getItemBasics();
//       $obj_type = $ib['obj_type'];
//       $bitstreams = $context->getBitstreams();
//       if ($obj_type == 'digital-item' && !empty($bitstreams)){
//       	$first_bitstream = reset($bitstreams);
//       	$mimetype = $first_bitstream['mimetype'];
//       	echo "<pre>"; print_r($mimetype);  echo "</pre>";
//       }

    } else{
      if ($has_page_thumbs_b){
        printf('<a class="group colorbox-load" rel="gal" href="/media/%s" title="%s" aria-hidden = "%s" ><img src="/media/%s" alt="%s" /></a>',
        $thumb_b, $title, $aria_hidden, $thumb_s, $title);
    } else {
        printf('<img src="/media/%s" alt="%s" aria-hidden = "%s" />',  $thumb_s, $title, $aria_hidden);
      }
    }
    //echo('</div>');

  }


  /**
   * @param DisplayContext $context
   */
  public static function foto_thumbs($context,$options){
    $class = Putil::safeArrGet($options, 'class', null);
    $thumbs_s = $context->getThumbsSmall();
    $thumbs_b = $context->getThumbsBig();

    $has_page_thumbs_s = $context->get('_has_page_thumbs_s');
    $has_page_thumbs_b = $context->get('_has_page_thumbs_b');


    if ($has_page_thumbs_s &&  $has_page_thumbs_b  && count($thumbs_s) > 1) {
      if ( isset($thumbs_b['l']) && isset($thumbs_s['l']) ) {
        $msg = tr("Ενδεικτικές Πρώτες και τελευταία σελίδα");
      } else {
        $msg = tr("Ενδεικτικές σελίδες");
      }

    printf('<div class="front_%s" aria-hidden="true"><h3 class="pthumbstitle">%s</h3>', $class,$msg);
    $idata = $context->getItemMetadata();

    $val = $idata->getFirstItemValue('ea:edoc:Pages');
    $pages = !empty($val)? $val->textValue() : '';

    DisplayCommandSnipets::item_pages_preview($pages, $thumbs_s, $thumbs_b);

    printf('<p class="mynote">%s</p></div>',tr('Κάντε κλίκ στις μικρογραφίες για μεγένθυση. Τα μεγέθη των φωτογραφιών είναι ενδεικτικά για προεπισκόπηση.'));
    }
  }

  /**
   * DEPENDS TO context->title (init)
   * @param DisplayContext $context
   */
  public static function title($context,$options){
    $title = $context->get('_title');
    if (!empty($title)){
      if($context->get('isScreenReader') == true){
        printf('<h1>%s</h1>', $title);
      }else{
        printf('<div class="row"><h1 class="item-title">%s</h1></div>', $title);
      }
    }
  }


  /**
   * DEPENDS TO context->title (init)
   * @param DisplayContext $context
   */
  public static function manifestation_title($context,$options){

    $title = $context->get('_title');
    $class = isset($options['class']) ? $options['class']: '';
    $template = Putil::safeArrGet($options, 'template', 'empty');
    $idata = $context->getItemMetadata();
    $i=0; $j=0;
    $value_array = array();
    $list_part = array();
    $value_array_num = array();
    $list_number= array();

    $m_title_medium = $idata->getTextValue('ea:manif:Title_Medium');
    $m_title_remainder = $idata->getTextValue('ea:manif:Title_Remainder');
    $m_title_responsibility = $idata->getTextValue('ea:manif:Title_Responsibility');
    $m_title_partNumber = $idata->getItemValues('ea:manif:Title_PartNumber');
    $m_title_partName = $idata->getItemValues('ea:manif:Title_PartName');

    foreach ($m_title_partName as $v) {
      $i++;
      if($i>1){
        $value_array['delimiter'] ='true';
      }
      $m_title_partName = $v->textValue();
      $value_array['value'] = $m_title_partName;
      $list_part['list'][] = $value_array;
    }

    foreach ($m_title_partNumber as $v) {
      $j++;
      if($j>1){
        $value_array_num['delimiter'] ='true';
      }
      $m_title_partNumber = $v->textValue();
      $value_array_num['value'] = $m_title_partNumber;
      $list_number['list'][] = $value_array_num;
    }

    $var_templ = array(
              'title' => $title,
              'title_medium' => $m_title_medium,
              'title_remainder' => $m_title_remainder,
              'title_responsibility' => $m_title_responsibility,
              'list_part' => $list_part,
              'list_number' => $list_number,
          );

          $m = new Mustache_Engine;
          if($context->get('isScreenReader') == true){
            $template = Config::get('arc_display_template_sr-only.'.$template);
            $content = $m->render($template,$var_templ);
            printf('<h1>%s</h1>',$content);
          }else{
            $template = Config::get('arc_display_template.'.$template);
            $content = $m->render($template,$var_templ);
            printf('<div class="row"><h1 class="%s">%s</h1></div>',$class,$content);
          }

  }

  public static function date($context,$options){

    $class = Putil::safeArrGet($options, 'class', null);
    $template = Putil::safeArrGet($options, 'template', 'empty');
    $idata = $context->getItemMetadata();
    $date =$idata->getArrayValue($options['key']);

    if(!empty($date)){
        DisplayCommands::setGridColClass($context,$options);
        $labelE = DisplayCommandSnipets::createLabel($context,$options);
        $labelSR = isset($options['label']) ? htmlspecialchars(tr($options['label'])): '&nbsp;';
        $grid_col_class = $context->get('_grid_col_2_class');

        $year = $date[5]['json']['y'];
        $month  = $date[5]['json']['m'];
        $day = $date[5]['json']['d'];
        $month_tr =null;

        if( !empty($day) && !empty($month)){
        	$month_tr = tr('nom_'.$month);
        }else if(!empty($month)){
        	$month_tr = tr('acc_'.$month);
        }

        $comment = $date[5]['json']['t'];

        $var_templ = array(
            'year' => $year,
            'month' => $month_tr,
            'day' => $day,
            'comment' => $comment,
            'label' => $labelE,
            'label_sr' =>$labelSR,
            'grid_col_class' => $grid_col_class,
        );

        $m = new Mustache_Engine;
        if($context->get('isScreenReader') == true){
          $template = Config::get('arc_display_template_sr-only.'.$template);
          $content = $m->render($template,$var_templ);
          printf('<li>%s</li>',$content);
        }else{
          $template = Config::get('arc_display_template.'.$template);
          $content = $m->render($template,$var_templ);
          printf('<div class="row %s">%s</div>',$class,$content);
        }


    }else{
        return;
    }
  }


  /**
   * DEPENDS TO context->title (init)
   * @param DisplayContext $context
   */
  public static function title_person($context,$options){

    $class = Putil::safeArrGet($options, 'class', null);
    $template = Putil::safeArrGet($options, 'template', 'empty');
    $idata2 = $context->getItemBasics();

    if (!empty($idata2['jdata'])){
      $json = json_decode($idata2['jdata'], true);
      if (!empty($json['opac2'])){
        $title_punc = $json['opac2'];
        if (!empty($title_punc['Title_punc'])){
          $title = $title_punc['Title_punc'];
//           if($context->get('isScreenReader') == true){
//             printf('<h1>%s</h1>',htmlspecialchars($title));
//           }else{
//           	$item_id = $context->get('_itemId');
//           	$rdf_link = '/archive/itemxml/'.$item_id;
//           	printf('<div class="row title-person"><div class="item-title-person"><h1 class="%s">%s</h1></div><div class="rda_link"><a href="%s" target="_blank"><img alt="rdf_link" src="/_assets/img/rdf.png"></a></div></div>',$class,htmlspecialchars($title),$rdf_link);
//           }

          $class_item = 'item-title-person';
          $item_id = $context->get('_itemId');
          $rdf_link = '/archive/itemxml/'.$item_id;
          $class_rda_link ='rda_link';
          $src_rda_icon = '/_assets/img/rdf.png';

          $var_templ = array(
          		'title' => $title,
          		'class_item' => $class_item,
          		'class' => $class,
          		'rdf_link' =>$rdf_link,
          		'class_rda_link' => $class_rda_link,
          		'src_rda_icon' => $src_rda_icon,
          );

          $m = new Mustache_Engine;
          if($context->get('isScreenReader') == true){
          	$template = Config::get('arc_display_template_sr-only.'.$template);
          	$content = $m->render($template,$var_templ);
          	printf('<h1>%s</h1>',$content);
          }else{
          	if(user_access_mentainer()){
	          	$template = Config::get('arc_display_template.'.$template.'_rdf');
	          	$content = $m->render($template,$var_templ);
	          	printf('<div class="row title-person">%s</div>',$content);
          	}else{
          		$template = Config::get('arc_display_template.'.$template);
          		$content = $m->render($template,$var_templ);
          		printf('<div class="row">%s</div>',$content);
          	}
          }
        }else{
          return;
             }
      }else{
        return;
      }
    }
  }

  /**
   * DEPENDS TO context->title (init)
   * @param DisplayContext $context
   */
  public static function basic_title($context,$options){

      $class = Putil::safeArrGet($options, 'class', null);
      $template = Putil::safeArrGet($options, 'template', 'empty');
      $idata2 = $context->getItemBasics();

      if (!empty($idata2['jdata'])){
            $json = json_decode($idata2['jdata'], true);
            if (!empty($json['label'])){
                $title = $json['label'];
//                 if($context->get('isScreenReader') == true){
//                   printf('<h1>%s</h1>',htmlspecialchars_decode($title));
//                 }else{
//                   printf('<div class="row"><h1 class="%s">%s</h1></div>',$class,htmlspecialchars_decode($title));
//                 }
                $var_templ = array(
                		'title' => $title,
                		'class' => $class,
                );

                $m = new Mustache_Engine;
                if($context->get('isScreenReader') == true){
                	$template = Config::get('arc_display_template_sr-only.'.$template);
                	$content = $m->render($template,$var_templ);
                	printf('<h1>%s</h1>',$content);
                }else{
                	$template = Config::get('arc_display_template.'.$template);
                	$content = $m->render($template,$var_templ);
                	printf('<div class="row">%s</div>',$content);
                }
              }
      }else{
            return;
      }

//   		if ($opac->hasOpac2('Title_punc')){
//   			$title = $opac->opac2('Title_punc');
//   			printf('<div class="row"><h2 class="%s">%s</h2></div>',$class,$title);
//   		}

/////////////////////////////////////////////////////
// METHOD 1 (OPAC-jdata)
////////////////////////////////////////////////////
//   		$idata2 = $context->getItemBasics();
//   		if (!empty($idata2['jdata'])){
//   				$json = json_decode($idata2['jdata'], true);
//   				$title = PUtil::opac2($json, 'Title_punc');
//   				printf('<h2 class="%s">M1: %s</h2>',$class,htmlspecialchars($title));
//   		}
////////////////////////////////////////////////////

//     $title = $context->get('_title');
//     $class = Putil::safeArrGet($options, 'class', null);
//     $template = Putil::safeArrGet($options, 'template', 'empty');

//     $idata = $context->getItemMetadata();
//     $date_birth_year = $idata->getFirstItemValue('ea:authPersonCoded:Dates_Birth');
//     $birth_year =  PUtil::get_year_from_date($date_birth_year);
//     $date_death_year= $idata->getFirstItemValue('ea:authPersonCoded:Dates_Death');
//     $death_year =  PUtil::get_year_from_date($date_death_year);

//     $p_numeration = $idata->getTextValue('ea:auth:Person_Numeration');
//     $p_fuller_name = $idata->getTextValue('ea:auth:Person_FullerName');
//     $p_dates_associated  = $idata->getTextValue('ea:auth:Person_DatesAssociated');

//     $p_titles_associated  = $idata->getItemValues('ea:auth:Person_TitlesAssociated');
//     $value_array = array();
//     $list_array = array();

//     if(!empty($p_titles_associated)){
//     	$i=0;
//     	foreach ($p_titles_associated as $v) {
//     		$val1 = $v->textValue();
//     		$i++;
//     		if($i>1){
//     			$value_array['delimiter'] ='true';
//     		}

//      		if(!empty($val1)){
//     					$value_array['value'] = htmlspecialchars($val1);
//     					$list_array['list'][] = $value_array;
//     		}
//     	}
//     }

//     $var_templ = array(
//           'title' => $title,
//           'birth_year' => $birth_year,
//           'death_year' => $death_year,
//           'person_dates_associated' => $p_dates_associated,
//           'person_numeration' => $p_numeration,
//           'person_fuller_name' => $p_fuller_name,
//           'titles_associated' => $list_array,
//       );

//     $template = Config::get('arc_display_template.'.$template);
//     $m = new Mustache_Engine;
//     echo '<h2 class="'.$class.'">'.$m->render($template,$var_templ).'</h2>';
  }




  /**
   * DEPENDS TO context->title (init)
   * @param DisplayContext $context
   */
  public static function lemma_title($context,$options){

  	$class = Putil::safeArrGet($options, 'class', null);
  	$template = Putil::safeArrGet($options, 'template', 'empty');
  	$idata2 = $context->getItemBasics();

  	if (!empty($idata2['jdata'])){
	  		$json = json_decode($idata2['jdata'], true);
	  		if (!empty($json['opac1']['title'])){
		  			$title = $json['opac1']['title'];
		  			$var_templ = array(
		  					'title' => $title,
		  					'class' => $class,
		  			);

		  			$m = new Mustache_Engine;
		  			if($context->get('isScreenReader') == true){
		  			$template = Config::get('arc_display_template_sr-only.'.$template);
		  				$content = $m->render($template,$var_templ);
		                	printf('<h1>%s</h1>',$content);
		  			}else{
		  			$template = Config::get('arc_display_template.'.$template);
		  					$content = $m->render($template,$var_templ);
		  					printf('<div class="row">%s</div>',$content);
		  			}
	  		}elseif (!empty($json['label'])){
           $title = $json['label'];
           $var_templ = array(
           		'title' => $title,
           		'class' => $class,
           );

           $m = new Mustache_Engine;
           if($context->get('isScreenReader') == true){
           	$template = Config::get('arc_display_template_sr-only.'.$template);
           	$content = $m->render($template,$var_templ);
           	printf('<h1>%s</h1>',$content);
           }else{
           	$template = Config::get('arc_display_template.'.$template);
           	$content = $m->render($template,$var_templ);
           	printf('<div class="row">%s</div>',$content);
           }
  			}else{
  				 return;
  			}
  		}else{
  			return;
  		}
    }


  /**
   * DEPENDS TO context->title (init)
   * @param DisplayContext $context
   */
  public static function title_work($context,$options){

  	$class = Putil::safeArrGet($options, 'class', null);
  	$template = Putil::safeArrGet($options, 'template', 'empty');
  	$idata2 = $context->getItemBasics();

  	if (!empty($idata2['jdata'])){
  		$json = json_decode($idata2['jdata'], true);
  		if (!empty($json['opac1']['public_title']['title'])){
  			$title = $json['opac1']['public_title']['title'];
//   			if($context->get('isScreenReader') == true){
//   				printf('<h1>%s</h1>',htmlspecialchars_decode($title));
//   			}else{
//   				printf('<div class="row"><h1 class="%s">%s</h1></div>',$class,htmlspecialchars_decode($title));
//   			}
  			$var_templ = array(
  					'title' => $title,
  					'class' => $class,
  			);

  			$m = new Mustache_Engine;
  			if($context->get('isScreenReader') == true){
  				$template = Config::get('arc_display_template_sr-only.'.$template);
  				$content = $m->render($template,$var_templ);
  				printf('<h1>%s</h1>',$content);
  			}else{
  				$template = Config::get('arc_display_template.'.$template);
  				$content = $m->render($template,$var_templ);
  				printf('<div class="row">%s</div>',$content);
  			}
  		}
  	}else{
  		return;
  	}
  }


	/**
	 * @param DisplayContext $context
	 */
	public static function citations($context, $options)
	{
		$keys = isset($options['key']) ? $options['key'] : null;
		if (empty($keys)) {
			return;
		}

		$class = isset($options['class']) ? $options['class'] : '';
		$template = Putil::safeArrGet($options, 'template', 'empty');
		$idata = $context->getItemMetadata();
		DisplayCommands::setGridColClass($context, $options);
		$labelE = DisplayCommandSnipets::createLabel($context, $options);
		$labelSR = isset($options['label']) ? htmlspecialchars(tr($options['label'])) : '&nbsp;';
		$grid_col_class = $context->get('_grid_col_2_class');
		$citation_array = array();
		$i = 0;
		foreach ($keys as $key) {
			$vals = $idata->getItemValues($key);
			if (!empty($vals)) {
				foreach ($vals as $v) {
					$list_array = array();
					$lnk = $v->recordId();
					$vals1 = $idata->getArrayValues('ea:citation:', $lnk);
					if (!empty($vals1)) {
						foreach ($vals1 as $v2) {
							$jdata = $v2[5];
							$i++;
							$my_title = PUtil::getItemValueArrLabel($v2);
							$value_array = array();

							if (isset($jdata['data']['edit_type'] )){
								$edit_type = $jdata['data']['edit_type'];
								if ($edit_type == 'html'){
									$my_title = html_entity_decode($my_title);
								}else if ($edit_type == 'text'){
									$my_title = htmlentities($my_title);
								}else if ($edit_type == 'text_br'){
									$my_title =nl2br(htmlentities($my_title));
								}
							}else{
								$my_title = htmlentities($my_title);
							}

							$value_array['value'] = $my_title;
							if ($i > 1) {
								$value_array['delimiter'] = 'true';
							}
							$citation_array[] = $value_array;
						}
					}
				}
			}
		}

		$var_templ = array(
			'citation_list' => $citation_array,
			'label' => $labelE,
			'label_sr' => $labelSR,
			'grid_col_class' => $grid_col_class,
		);


		if (!empty($citation_array)){
			$m = new Mustache_Engine;
			if ($context->get('isScreenReader') == true) {
				$template = Config::get('arc_display_template_sr-only.' . $template);
				$content = $m->render($template, $var_templ);
				printf('<li>%s</li>', $content);
			} else {
				$template = Config::get('arc_display_template.' . $template);
				$content = $m->render($template, $var_templ);
				printf('<div class="row %s">%s</div>', $class, $content);
			}
		} else {
				return;
		}

		//**** RULE-ENGINE CITATION****//
		//   	$class = Putil::safeArrGet($options, 'class', null);
		//   	$template = Putil::safeArrGet($options, 'template', 'empty');
		//   	$idata = $context->getItemBasics();

		//   	if (!empty($idata['jdata'])){
		//   		$json = json_decode($idata['jdata'], true);
		//   		$citations = PUtil::opac1($json, 'citations');
		//   		$citation = PUtil::opac1($json, 'citation');

		//   		if (!empty($citations) || !empty($citation)){
		//   			DisplayCommands::setGridColClass($context,$options);
		//   			$labelE = DisplayCommandSnipets::createLabel($context,$options);
		//   			$labelSR = isset($options['label']) ? htmlspecialchars(tr($options['label'])): '&nbsp;';
		//   			$grid_col_class = $context->get('_grid_col_2_class');

		//   			if (!empty($citations)){
		//   				$item_citation = $citations;
		//   				$is_manif_citation = false;
		//   			}else {
		//   				$item_citation = $citation;
		//   				$is_manif_citation = true;
		//   			}

		//   			$var_templ = array(
		//   					'citations' => $item_citation,
		//   					'is_manif_citation' => $is_manif_citation,
		//   					'label' => $labelE,
		//   					'label_sr' =>$labelSR,
		//   					'grid_col_class' => $grid_col_class,
		//   			);

		//   			$m = new Mustache_Engine;
		//   			if($context->get('isScreenReader') == true){
		//   				$template = Config::get('arc_display_template_sr-only.'.$template);
		//   				$content = $m->render($template,$var_templ);
		//   				printf('<li>%s</li>',$content);
		//   			}else{
		//   				$template = Config::get('arc_display_template.'.$template);
		//   				$content = $m->render($template,$var_templ);
		//   				printf('<div class="row %s">%s</div>',$class,$content);
		//   			}

		//   		}
		//   	}
	}


  /**
   * @deprecated
   * @param DisplayContext $context
   */
  public static function publication($context,$options){

    $label = Putil::safeArrGet($options, 'label', 'publication');
    $class = Putil::safeArrGet($options, 'class', null);
    $idata = $context->getItemMetadata();
    $vals = $idata->getItemValues('ea:publication:statement');

    foreach ($vals as $v) {

      printf('<div class="front_%s"><span >%s:</span> ', $class,htmlspecialchars($label));
      $lnk = $v->recordId();
      $sep ='';

      $vals1 =$idata->getArrayValues('ea:publication:place', $lnk);
      foreach ($vals1 as $v) {
        echo($sep);
        printf('<a href="/archive/search?m=a&p=%s" class="authlink">%s <img src="/_assets/img/find.png" alt="search" /></a> ',urlencode($v[0]),htmlspecialchars($v[0]));
        $sep = '&#160;&#160; | &#160;&#160; ';
      }

      $vals2 =$idata->getArrayValues('ea:date:orgissued', $lnk);
      foreach ($vals2 as $v) {
        echo($sep);
         printf('<a href="/archive/search?m=a&y=%s" class="authlink">%s <img src="/_assets/img/find.png" alt="search" /></a> ',urlencode($v[0]),htmlspecialchars($v[0]));
         $sep = '&#160;&#160; | &#160;&#160; ';
      }

      $vals3 =$idata->getArrayValues('dc:publisher:', $lnk);
      foreach ($vals3 as $v) {
          echo($sep);
          printf('%s',htmlspecialchars($v[0]));
          $sep = '; ';
      }

      $vals4 =$idata->getArrayValues('ea:publication:printing-place', $lnk);
      $vals5 =$idata->getArrayValues('ea:publication:printer-name', $lnk);
      if (count($vals4) > 0 or count($vals5) > 0 ){
        echo(" (");
        $sep = '';
        foreach ($vals4 as $v) {
          echo($sep);
          printf(' <span class="sp_prn_place">%s</span>',htmlspecialchars($v[0]));
          $sep = '; ';
        }
        foreach ($vals5 as $v) {
          echo($sep);
          printf(' <span class="sp_prn_name">%s</span>',htmlspecialchars($v[0]));
          $sep = '; ';
        }
        echo(") ");
      }
      printf('</div>' );
    }
  }

  /**
   * @param DisplayContext $context
   */
  public static function oneline($context,$options){

    $class = isset($options['class']) ? $options['class']: '';
    $template = Putil::safeArrGet($options, 'template', 'empty');
    $idata = $context->getItemMetadata();
    $vals = $idata->getItemValues($options['key']);

    if(!empty($vals)){
	    	$array_val = array();
	    	DisplayCommands::setGridColClass($context,$options);
	    	$labelE = DisplayCommandSnipets::createLabel($context,$options);
	    	$labelSR = isset($options['label']) ? htmlspecialchars(tr($options['label'])): '&nbsp;';
	    	$grid_col_class = $context->get('_grid_col_2_class');

	    	foreach ($vals as $v) {
		    		$val1 = $v->textValue();
		    		$ri = $v->refItem();
		    		if (!empty($ri)){
		    			$val2 = sprintf('%s',htmlspecialchars($val1));
		    			$array_val['value'] = $val2;
		    			$array_val['url'] = '/archive/item/'.$ri;
		    		}else{
		    			$val2 = sprintf('%s',htmlspecialchars($val1));
		    			$array_val['value'] = $val2;
		    		}
	    	}

	    	$var_templ = array(
	    			'array_val' => $array_val,
	    			'label' => $labelE,
	    			'label_sr' =>$labelSR,
	    			'grid_col_class' => $grid_col_class,
	    	);

	    	$m = new Mustache_Engine;
	    	if($context->get('isScreenReader') == true){
	    		$template = Config::get('arc_display_template_sr-only.'.$template);
	    		$content = $m->render($template,$var_templ);
	    		printf('<li>%s</li>',$content);
	    	}else{
	    		$template = Config::get('arc_display_template.'.$template);
	    		$content = $m->render($template,$var_templ);
	    		printf('<div class="row %s">%s</div>',$class,$content);
	    	}
     }else{
        return;
     }
  }


  /**
   * @param DisplayContext $context
   */
  public static function oneline_key_child($context,$options){

  	$class = isset($options['class']) ? $options['class']: '';
  	$template = Putil::safeArrGet($options, 'template', 'empty');
  	$idata = $context->getItemMetadata();
  	$vals = $idata->getItemValues($options['key']);

  	if(!empty($vals)){
  		$array_val = array();
  		DisplayCommands::setGridColClass($context,$options);
  		$labelE = DisplayCommandSnipets::createLabel($context,$options);
  		$labelSR = isset($options['label']) ? htmlspecialchars(tr($options['label'])): '&nbsp;';
  		$grid_col_class = $context->get('_grid_col_2_class');

  		foreach ($vals as $v) {
  			$lnk = $v->recordId();
  			$vals_child =$idata->getArrayValues($options['key_child'], $lnk);
  			if(!empty($vals_child)){
		  			foreach ($vals_child as $vc) {
		  				$val = sprintf('%s',htmlspecialchars($vc[0]));
		  				$array_val['value'] = $val;
		  			}

		  			$var_templ = array(
		  					'array_val' => $array_val,
		  					'label' => $labelE,
		  					'label_sr' =>$labelSR,
		  					'grid_col_class' => $grid_col_class,
		  			);

		  			$m = new Mustache_Engine;
		  			if($context->get('isScreenReader') == true){
		  				$template = Config::get('arc_display_template_sr-only.'.$template);
		  				$content = $m->render($template,$var_templ);
		  				printf('<li>%s</li>',$content);
		  			}else{
		  				$template = Config::get('arc_display_template.'.$template);
		  				$content = $m->render($template,$var_templ);
		  				printf('<div class="row %s">%s</div>',$class,$content);
		  			}
  			}
  		}
  	}else{
  		return;
  	}
  }



  /**
   * @param DisplayContext $context
   */
  public static function one_label_combination($context,$options){

    $class = isset($options['class']) ? $options['class']: '';
    $template = Putil::safeArrGet($options, 'template', 'empty');
    $idata = $context->getItemMetadata();

    $labelE ='';
    $grid_col_class='';
    $vals = $idata->getItemValues($options['key']);




    if(!empty($vals)){

            $all_list_array = array();
            DisplayCommands::setGridColClass($context,$options);
            $labelE = DisplayCommandSnipets::createLabel($context,$options);
            $labelSR = isset($options['label']) ? htmlspecialchars(tr($options['label'])): '&nbsp;';
            $grid_col_class = $context->get('_grid_col_2_class');
            $str = 0;

              foreach ($vals as $v) {
                $value_array = array();
                $list_array = array();
                $value_array_url = array();
                $lnk = $v->recordId();
                $jdata= $v->data();

                if(!empty($v)){
                    $val11 = $v->textValue();
                    $ri = $v->refItem();
                    if(!empty($val11)){

                    if (isset($jdata['data']['edit_type'] )){
                    	$edit_type = $jdata['data']['edit_type'];
                    	if ($edit_type == 'html'){
                    		$value_array['value'] = html_entity_decode($val11);
                    	}else if ($edit_type == 'text'){
                    		$value_array['value'] = htmlentities($val11);
                    	}else if ($edit_type == 'text_br'){
                    		$value_array['value'] =nl2br(htmlentities($val11));
                    	}
                    }else{
                    	$value_array['value'] = htmlentities($val11);
                    }

//                     $value_array['value'] = $val11;

                      //Mode: 1=same-row(default), 2=break-line, 3=auto
                      $mode_view = isset($options['mode_view']) ? $options['mode_view']: 1;
                      if ($mode_view == 2){
                      	$grid_col_class = "br_row_wh col-md-12";
                      	$labelE = null;
                      }else if ($mode_view == 3){
                      	$str += strlen(utf8_decode($val11));
                      	if ( $str > 160){
                      		$grid_col_class = "br_row_wh col-md-12";
                      		$labelE = null;
                      	}
                      }

                      if(!empty($ri) ){
                      	$value_array['url'] = "/archive/item/$ri";
                      }
                      $list_array['list_text'][] = $value_array;
                    }
                }

                //------------------ link ----------------------//
              if( isset($options['key_child']) ){
                $i=0;
                $vals1 =$idata->getArrayValues($options['key_child'], $lnk);
                foreach ($vals1 as $v2) {
                  $i++;
                  if($i>1){
                    $value_array_url['delimiter'] ='true';
                  }
                  $sel_val = $v2[5];
                  if(!empty($sel_val)){
                    if(!empty($sel_val['json']['u'])){
                      $val1 = $sel_val['json']['u'];
                      $value_array_url['value'] = tr('Click Here');
                      if (!preg_match("~^https?://~i", $val1)) {
                        $val1 = "http://" . $val1;
                      }
                      $value_array_url['url'] = $val1;
                      $value_array_url['description'] ='';
                      if(!empty($sel_val['json']['d'])){
                        $val2 = $sel_val['json']['d'];
                        $value_array_url['description'] = $val2;
                      }
                      $list_array['list_url'][] = $value_array_url;
                    }
                   }
                 }
               }
                //----------------------------------------//
                $all_list_array['pos'][] = $list_array;
              }

              $var_templ = array(
                  'all_array' => $all_list_array,
                  'label' => $labelE,
                  'label_sr' =>$labelSR,
                  'grid_col_class' => $grid_col_class,
              );

              $m = new Mustache_Engine;
              if($context->get('isScreenReader') == true){
                $template = Config::get('arc_display_template_sr-only.'.$template);
                $content = $m->render($template,$var_templ);
                printf('<li>%s</li>',$content);
              }else{
                $template = Config::get('arc_display_template.'.$template);
                $content = $m->render($template,$var_templ);
                printf('<div class="row %s">%s</div>',$class,$content);
              }
      }else{
              return;
      }

  }


  /**
   * @param DisplayContext $context
   */
  public static function publication_child($context,$options){
  	$class = isset($options['class']) ? $options['class']: '';
  	$template = Putil::safeArrGet($options, 'template', 'empty');
  	$idata = $context->getItemMetadata();

  	$labelE = '';
  	$labelSR = '';
  	$grid_col_class='';
  	$vals = $idata->getItemValues($options['key']);

  	if(!empty($vals)){
  		$all_list_array = array();
  		DisplayCommands::setGridColClass($context,$options);
  		$labelE = DisplayCommandSnipets::createLabel($context,$options);
  		$labelSR = isset($options['label']) ? htmlspecialchars(tr($options['label'])): '&nbsp;';
  		$grid_col_class = $context->get('_grid_col_2_class');

  		$list_array = array();
  		$i=0;
  		foreach ($vals as $v) {
  			$value_array = array();
  			$lnk = $v->recordId();
  			$vals1 =$idata->getArrayValues($options['key_child'], $lnk);
  			if(!empty($vals1)){
  				foreach ($vals1 as $v2) {
  					$i++;
  					$ri = $v2[4];
  					$my_title = PUtil::getItemValueArrLabel($v2);
  					$value_array = array();
  					if(!empty($ri)){
  						$value_array['value'] = $my_title;
  						$value_array['url'] = '/archive/item/' . $ri;
  						$value_array['class'] = 'assetlink';
  						if($i>1){
  							$value_array['delimiter'] ='true';
  						}
  						$list_array['link_list'][] = $value_array;
  					} else{
  						$value_array['value'] = $my_title;
  						if($i>1){
  							$value_array['delimiter'] ='true';
  						}
  						$list_array['link_list'][] = $value_array;
  					}
	  			}
  			}else{
  				return;
  			}
  		}

  		$var_templ = array(
  				'line_list' => $list_array,
  				'label' => $labelE,
  				'label_sr' =>$labelSR,
  				'grid_col_class' => $grid_col_class,
  		);

  		$m = new Mustache_Engine;
  		if($context->get('isScreenReader') == true){
  			$template = Config::get('arc_display_template_sr-only.'.$template);
  			$content = $m->render($template,$var_templ);
  			printf('<li>%s</li>',$content);
  		}else{
  			$template = Config::get('arc_display_template.'.$template);
  			$content = $m->render($template,$var_templ);
  			printf('<div class="row %s">%s</div>',$class,$content);
  		}

  	}else{
  		return;
  	}

  }



  /**
   * @param DisplayContext $context
   */
  public static function one_label_line($context,$options){

    $class = isset($options['class']) ? $options['class']: '';
    $lang = Putil::safeArrGet($options, 'lang', null);
    $template = Putil::safeArrGet($options, 'template', 'empty');
    $idata = $context->getItemMetadata();
    $vals = $idata->getItemValues($options['key']);

    if(!empty($vals)){
        $value_array = array();
        $list_array = array();
        $labelE ='';
        $grid_col_class='';
//           echo '<pre>'; print_r($vals); echo '</pre>';
        DisplayCommands::setGridColClass($context,$options);
        $labelE = DisplayCommandSnipets::createLabel($context,$options);
        $labelSR = isset($options['label']) ? htmlspecialchars(tr($options['label'])): '&nbsp;';
        $grid_col_class = $context->get('_grid_col_2_class');
        $i=0;

        foreach ($vals as $v) {
          $val1 = $v->textValue();
          $ri = $v->refItem();
          $sel_val = $v->data();
          $i++;
          if($i>1){
            $value_array['delimiter'] ='true';
          }
          if(!empty($ri) ){
            $value_array['url'] = "/archive/item/$ri";
            $value_array['class'] = 'assetlink';

            #TO SEE
//             if( !empty($sel_val) && !empty($sel_val['prps']['pnctn']) /*&& empty($sel_val['data']['remote_type'])*/ ){
//             	$val1 = $sel_val['prps']['pnctn'];
//             	$value_array['value'] = $val1;
//             }else{
            	$value_array['value'] = $val1;
//             }



//             $value_array['value'] = $val1;
            $list_array['link_list'][] = $value_array;
          }else if(!empty($sel_val) && !empty($sel_val['prps']['pnctn'])){
            $val1 = $sel_val['prps']['pnctn'];
            $value_array['value'] = $val1;
            $list_array['list'][] = $value_array;
          }else if(!empty($sel_val) && !empty($sel_val['prps']['selected_value'])){
            if ($val1!='undefined'){
               $val1 = $sel_val['prps']['selected_value'];
               $value_array['value'] = $val1;
               $list_array['list'][] = $value_array;
            }else{
              return;
            }
          }else if(!empty($sel_val) && !empty($sel_val['json']['u'])){
            $val1 = $sel_val['json']['u'];
            $value_array['value'] = tr('Click Here');
            if (!preg_match("~^https?://~i", $val1)) {
              $val1 = "http://" . $val1;
            }
            $value_array['url'] = $val1;
            $value_array['description'] ='';
             if(!empty($sel_val['json']['d'])){
                $val2 = $sel_val['json']['d'];
                $value_array['description'] = $val2;
            }
            $list_array['link_list'][] = $value_array;
          }else if(!empty($sel_val) && !empty($sel_val['json']['z'])){
          	if(($sel_val['json']['z']) == 'date'){
	          		if(!empty($sel_val['json']['d']) && !empty($sel_val['json']['m']) && !empty($sel_val['json']['y'])){
	          				$val1 = $sel_val['json']['d'].'-'.$sel_val['json']['m'].'-'.$sel_val['json']['y'];
	          		}else if(!empty($sel_val['json']['m']) && !empty($sel_val['json']['y'])){
	          				$val1 = $sel_val['json']['m'].'-'.$sel_val['json']['y'];
	          		}else if(!empty($sel_val['json']['m']) && !empty($sel_val['json']['d'])){
	          				$val1 = $sel_val['json']['d'].'-'.$sel_val['json']['m'].'-';
	          		}else if(!empty($sel_val['json']['d']) && !empty($sel_val['json']['y'])){
	          					$val1 = $sel_val['json']['d'].'- -'.$sel_val['json']['y'];
	          		}else if(!empty($sel_val['json']['y'])){
	          				$val1 = $sel_val['json']['y'];
	          		}else if(!empty($sel_val['json']['m'])){
	          				$val1 = '- '.$sel_val['json']['m'].' -';
	          		}else if(!empty($sel_val['json']['d'])){
	          				$val1 = $sel_val['json']['d'].'- '.'-';
	          		}
          	}
            $value_array['value'] = $val1;
            $list_array['list'][] = $value_array;
          }else if(!empty($val1)){

          	if(!empty($lang)){
          		$value_lang = $v->lang();
          		if ($value_lang != '_'){
          			$val1 = $val1 .'('. $value_lang . ')';
          		}
          	}

            $value_array['value'] = $val1;
            $list_array['list'][] = $value_array;
          }

        }

        $var_templ = array(
            'line_list' => $list_array,
            'label' => $labelE,
            'label_sr' =>$labelSR,
            'grid_col_class' => $grid_col_class,
        );

        $m = new Mustache_Engine;
        if($context->get('isScreenReader') == true){
          $template = Config::get('arc_display_template_sr-only.'.$template);
          $content = $m->render($template,$var_templ);
          printf('<li>%s</li>',$content);
        }else{
          $template = Config::get('arc_display_template.'.$template);
          $content = $m->render($template,$var_templ);
          printf('<div class="row %s">%s</div>',$class,$content);
        }

    }else{
        return;
    }
  }



  /**
   * @param DisplayContext $context
   */
  public static function lemma_original_title($context,$options){

  	$idata2 = $context->getItemBasics();
  	if (!empty($idata2['jdata'])){
  		$json = json_decode($idata2['jdata'], true);
  		if (empty($json['opac1']['title'])){
  			return;
  		}
  	}

  	$class = isset($options['class']) ? $options['class']: '';
  	$lang = Putil::safeArrGet($options, 'lang', null);
  	$template = Putil::safeArrGet($options, 'template', 'empty');
  	$idata = $context->getItemMetadata();
  	$vals = $idata-> getFirstItemValue($options['key']);

  	if(!empty($vals)){
  		$labelE ='';
  		$grid_col_class='';
  		DisplayCommands::setGridColClass($context,$options);
  		$labelE = DisplayCommandSnipets::createLabel($context,$options);
  		$labelSR = isset($options['label']) ? htmlspecialchars(tr($options['label'])): '&nbsp;';
  		$grid_col_class = $context->get('_grid_col_2_class');

  		$text_value = $vals->textValue();

  		$var_templ = array(
  				'label' => $labelE,
  				'label_sr' =>$labelSR,
  				'grid_col_class' => $grid_col_class,
  				'text_value' => $text_value,
  		);

  			$m = new Mustache_Engine;
  			if($context->get('isScreenReader') == true){
  				$template = Config::get('arc_display_template_sr-only.'.$template);
  				$content = $m->render($template,$var_templ);
  				printf('<li>%s</li>',$content);
  			}else{
  				$template = Config::get('arc_display_template.'.$template);
  				$content = $m->render($template,$var_templ);
  				printf('<div class="row %s">%s</div>',$class,$content);
  			}

  		}else{
  			return;
  		}
  	}



  /**
   * @param DisplayContext $context
   */
  public static function one_label_line_reverse($context,$options){

  	$class = isset($options['class']) ? $options['class']: '';
  	$template = Putil::safeArrGet($options, 'template', 'empty');
  	$idata = $context->getItemMetadata();
  	$vals = $idata->getItemValues($options['key']);

  	if(!empty($vals)){
  		$value_array = array();
  		$list_array = array();
  		$labelE ='';
  		$grid_col_class='';
  		DisplayCommands::setGridColClass($context,$options);
  		$labelE = DisplayCommandSnipets::createLabel($context,$options);
  		$labelSR = isset($options['label']) ? htmlspecialchars(tr($options['label'])): '&nbsp;';
  		$grid_col_class = $context->get('_grid_col_2_class');
  		$i=0;

  		foreach ($vals as $v) {
  			$val1 = $v->textValue();
  			$ri = $v->refItem();
  			$sel_val = $v->data();
  			$i++;
  			if($i>1){
  				$value_array['delimiter'] ='true';
  			}
  			if(!empty($ri)){
  				$value_array['url'] = "/archive/item/$ri";
  				$value_array['class'] = 'assetlink';
  				$value_array['value'] = $val1;
  				$list_array['link_list'][] = $value_array;
  			}else if(!empty($val1)){
  				$value_array['value'] = $val1;
  				$list_array['list'][] = $value_array;
  			}
  		}

  		$var_templ = array(
  				'line_list' => $list_array,
  				'label' => $labelE,
  				'label_sr' =>$labelSR,
  				'grid_col_class' => $grid_col_class,
  		);

  		$m = new Mustache_Engine;
  		if($context->get('isScreenReader') == true){
  			$template = Config::get('arc_display_template_sr-only.'.$template);
  			$content = $m->render($template,$var_templ);
  			printf('<li>%s</li>',$content);
  		}else{
  			$template = Config::get('arc_display_template.'.$template);
  			$content = $m->render($template,$var_templ);
  			printf('<div class="row %s">%s</div>',$class,$content);
  		}
  	}else{
  		return;
  	}
  }


  /**
   * @param DisplayContext $context
   */
  public static function object_type($context,$options){

    $class = isset($options['class']) ? $options['class']: '';
    $template = Putil::safeArrGet($options, 'template', 'empty');
    $idata = $context->getItemMetadata();
    $vals = $idata-> getTextValue('ea:obj-type:');
    $work_type= $idata-> getTextValue('ea:work:Type');

    if(!empty($vals)){
      $labelE ='';
      $grid_col_class='';
      DisplayCommands::setGridColClass($context,$options);
      $labelE = DisplayCommandSnipets::createLabel($context,$options);
      $labelSR = isset($options['label']) ? htmlspecialchars(tr($options['label'])): '&nbsp;';
      $grid_col_class = $context->get('_grid_col_2_class');
      $vals = empty($work_type) ? tr($vals) : tr($vals).' ('.tr($work_type).')';
//       $vals = tr($vals);

      $var_templ = array(
          'object_type' => $vals,
          'label' => $labelE,
          'label_sr' =>$labelSR,
          'grid_col_class' => $grid_col_class,
      );

      $m = new Mustache_Engine;
      if($context->get('isScreenReader') == true){
        $template = Config::get('arc_display_template_sr-only.object_type');
        $content = $m->render($template,$var_templ);
        printf('<li>%s</li>',$content);
      }else{
        $template = Config::get('arc_display_template.object_type');
        $content = $m->render($template,$var_templ);
        printf('<div class="row %s">%s</div>',$class,$content);
      }

    }else{
      return;
    }
  }

  /**
   * @param DisplayContext $context
   */
  public static function entityRelations($context,$options){

    $template = Putil::safeArrGet($options, 'template', 'empty');
    $class = isset($options['class']) ? $options['class']: '';
    $grid_col_class='';
    DisplayCommands::setGridColClass($context,$options);
    $grid_col_class_label = $context->get('_grid_col_1_class');
    $grid_col_class = $context->get('_grid_col_2_class');
    $setting_json = Setting::get($options['setting_variable']);

    if(!empty($setting_json)){
            $ex_array = array();
            $idata = $context->getItemMetadata();

            foreach ($setting_json as $k => $l) {
            	if($k != 'ea:relation:containerOfIndependent' && $k != 'ea:relation:containerOfContributions' && $k != 'ea:relation:containerOfDocuments'){
	              $vals = $idata->getItemValues($k);
	              if(!empty($vals)){
	                    $value_array = array();
	                    $list_array = array();
	                    $i=0;
	                    foreach ($vals as $v) {
	                      $i++;
	                      if($i>1){
	                        $value_array['delimiter'] ='true';
	                      }
	                      $val1 = $v->textValue();
	                      $ri = $v->refItem();

	//                       $sel_val = $v->data();
	//                       if(!empty($ri)){
	//                       	if($sel_val != null){
	//                       		if(isset($sel_val['data']['ref_label'])){
	//                       			$value_array['value'] = $sel_val['data']['ref_label'];
	//                       		}
	//                       	}
	                      	$list_array['label'] = tr($l);
	                        $value_array['value'] = $val1;
	                        if(!empty($ri)){
	                        	$value_array['url'] = "/archive/item/$ri";
	                        }else{
	                        	$value_array['url'] = null;
	                        }
	                        $list_array['link_list'][] = $value_array;
	//                       }

	                    }
	                    $ex_array['ex_list'][] = $list_array;
	              }
            	}
            }

            $var_templ = array(
                'ex_array' => $ex_array,
                'class' => $class,
                'grid_col_class' => $grid_col_class,
            		'grid_col_class_label' => $grid_col_class_label,
            );

            $m = new Mustache_Engine;
            if($context->get('isScreenReader') == true){
              $template = Config::get('arc_display_template_sr-only.'.$template);
              echo $content = $m->render($template, $var_templ);
            }else{
              $template = Config::get('arc_display_template.'.$template);
              echo $content = $m->render($template, $var_templ);
            }
    }else{
        return;
    }
  }




  /**
   * @param DisplayContext $context
   */
  public static function entityOtherLang($context,$options){

  	$template = Putil::safeArrGet($options, 'template', 'empty');
  	$class = isset($options['class']) ? $options['class']: '';
  	$grid_col_class='';
  	DisplayCommands::setGridColClass($context,$options);
  	$grid_col_class_label = $context->get('_grid_col_1_class');
  	$grid_col_class = $context->get('_grid_col_2_class');
  	$setting_json = Setting::get($options['setting_variable']);

  	if(!empty($setting_json)){
  		$ex_array = array();

  		foreach ($setting_json as $k => $l) {
  			$relation_data  = DisplayCommands::_getMembers($context, $options, $k);
  			if(!empty($relation_data)){

  				$value_array = array();
  				$list_array = array();
  				$i=0;
  				foreach($relation_data as $row){
  					if (!empty($row['jdata'])){
  						$i++;
  						if($i>1){
  							$value_array['delimiter'] ='true';
  						}

  						$json = json_decode($row['jdata'], true);
  						$id = PUtil::opac1($json, 'id');
  						$entity_lang = PUtil::opac1($json, 'entity_lang');
  						if ($json && isset($json['label']) ){
  							$title = $json['label'];
  						}else{
  							$title = PUtil::opac1($json, 'title');
  						}

  						$list_array['label'] = tr($l);
  						$value_array['value'] = $title;
  						$value_array['entity_lang'] = $entity_lang;

  						if(!empty($id)){
  							$value_array['url'] = "/archive/item/$id";
  						}
  						$list_array['link_list'][] = $value_array;

  					}
  				}
  					$ex_array['ex_list'][] = $list_array;
  				}
  			}

  			$var_templ = array(
  					'ex_array' => $ex_array,
  					'class' => $class,
  					'grid_col_class' => $grid_col_class,
  					'grid_col_class_label' => $grid_col_class_label,
  			);

  			$m = new Mustache_Engine;
  			if($context->get('isScreenReader') == true){
  				$template = Config::get('arc_display_template_sr-only.'.$template);
  				echo $content = $m->render($template, $var_templ);
  			}else{
  				$template = Config::get('arc_display_template.'.$template);
  				echo $content = $m->render($template, $var_templ);
  			}
  		}else{
  			return;
  		}
  	}

//   public static function subject_chain_entities($context,$options){

//   	$template = Putil::safeArrGet($options, 'template', 'empty');
//   	$class = isset($options['class']) ? $options['class']: '';
//   	$grid_col_class='';
//   	DisplayCommands::setGridColClass($context,$options);
//   	$grid_col_class = $context->get('_grid_col_2_class');

//   	$labelE ='';
//   	$labelE = DisplayCommandSnipets::createLabel($context,$options);

//   	$primary_subject_type_map = Setting::get('primary_subject_type_map');
//   	$subject_type_map = Setting::get('subject_type_map');
//   	$setting_json = array_merge($primary_subject_type_map, $subject_type_map);

//   	if(!empty($setting_json)){
//   		$ex_array = array();
//   		$idata = $context->getItemMetadata();
// //   		echo "<pre>";  print_r($idata); echo "</pre>";

//   		foreach ($setting_json as $k => $l) {
//   			$vals = $idata->getItemValues($k);
//   			if(!empty($vals)){
//   				$list_array = array();
//   				foreach ($vals as $v) {
//   					$val1 = $v->textValue();
//   					$ri = $v->refItem();
// //   					$sel_val = $v->data();
//   					if(!empty($ri)){
// //   						if($sel_val != null){
// //   							if(isset($sel_val['data']['ref_label'])){
// //   								$list_array['value'] = $sel_val['data']['ref_label'];
// //   							}
// //   						}
//   						$list_array['obj_type'] = tr($l);
//   						$list_array['url'] = "/archive/item/$ri";
//   						$list_array['value'] = $val1;
//   					}
//   					$ex_array['ex_list'][] = $list_array;
//   				}
//   			}
//   		}

//   		$var_templ = array(
//   				'ex_array' => $ex_array,
//   				'label' => $labelE,
//   				'grid_col_class' => $grid_col_class,
//   		);

//   		$m = new Mustache_Engine;
//   		if($context->get('isScreenReader') == true){
//   			$template = Config::get('arc_display_template_sr-only.'.$template);
//   			$content = $m->render($template,$var_templ);
//   			printf('<li>%s</li>',$content);
//   		}else{
//   			$template = Config::get('arc_display_template.'.$template);
//   			$content = $m->render($template,$var_templ);
//   			printf('<div class="row %s">%s</div>',$class,$content);
//   		}

//   	}else{
//   		return;
//   	}
//   }



  public static function subject_chain_entities($context,$options){

  	$template = Putil::safeArrGet($options, 'template', 'empty');
  	$class = isset($options['class']) ? $options['class']: '';
  	$grid_col_class='';
  	DisplayCommands::setGridColClass($context,$options);
  	$grid_col_class = $context->get('_grid_col_2_class');

  	$labelE ='';
  	$labelE = DisplayCommandSnipets::createLabel($context,$options);

  	$idata2 = $context->getItemBasics();

  	if (!empty($idata2['jdata'])){
  		$json = json_decode($idata2['jdata'], true);

  		if (!empty($json['chain_subjects'])){
  			$chain_subjects = $json['chain_subjects'];
  			$list_array = array();
  			foreach ($chain_subjects as $subjects) {
  				if (!empty($subjects['label'])){
  					$list_array['value']= $subjects['label'];
  					if(!empty($subjects['ot'])){
  						$list_array['obj_type'] = tr($subjects['ot']);
  					}
  					if(!empty($subjects['id'])){
  						$ri = $subjects['id'];
  						$list_array['url'] = "/archive/item/$ri";
  					}
  					$ex_array['ex_list'][] = $list_array;
  				}else{
  					return;
  				}
  			}

  			$var_templ = array(
  					'ex_array' => $ex_array,
  					'label' => $labelE,
  					'grid_col_class' => $grid_col_class,
  			);

  			$m = new Mustache_Engine;
  			if($context->get('isScreenReader') == true){
  				$template = Config::get('arc_display_template_sr-only.'.$template);
  				$content = $m->render($template,$var_templ);
  				printf('<li>%s</li>',$content);
  			}else{
  				$template = Config::get('arc_display_template.'.$template);
  				$content = $m->render($template,$var_templ);
  				printf('<div class="row %s">%s</div>',$class,$content);
  			}

  		}else{
  			return;
  		}
  	}else{
  			return;
  		}
  }



  /**
   * @param DisplayContext $context
   */
  public static function tags($context,$options){

    $class = Putil::safeArrGet($options, 'class', null);
    $idata = $context->getItemMetadata();
    $vals = $idata->getItemValues('dc:subject:');


    if (empty($vals)){
        return;
    }

    $options['label_class'] = 'col-md-12';
    $options['label_align'] = 'left';
    $labelE = DisplayCommandSnipets::createLabel($context,$options);
    printf('<div class="row"> %s  <div class="col-md-12">',$labelE);

    $sep = '';
    foreach ($vals  as $k => $v) {
      $val1 = $v->textValue();
      printf('%s <a class="assetlink" href="/archive/term?t=%s" target="_blank"> %s</a>',$sep,urlencode($val1),htmlspecialchars($val1));
      $sep = ', ';
    }

    echo('</div>');
    echo('</div>');

  }



  /**
   * @param DisplayContext $context
   */
  public static function url($context,$options){
    $label = Putil::safeArrGet($options, 'label', null);
    $class = Putil::safeArrGet($options, 'class', null);
    $idata = $context->getItemMetadata();

    $vals = $idata->getItemValues($options['key']);
    foreach ($vals as $v) {
      $url_text = $v->textValue();
      if (!empty($url_text)){
          $val1 = null; $val2=null;
          $arr = explode('|',$url_text,2);
          if (count($arr) >= 1){
            $val1 = $arr[0];
          }
          if (count($arr) >= 2){
            $val2 = $arr[1];
          }
          if (! empty($val1) && ! empty($val2) ){
            $labelE = DisplayCommandSnipets::createLabel($context, $options);
            $grid_col_2_class = $context->get('_grid_col_2_class');
            printf('<div class="row %s"> %s <div class="%s"> <a class="assetlink" href="%s" target="_blank"> %s</a> </div></div>',$class, $labelE, $grid_col_2_class,$val1,htmlspecialchars($val2));

          }
      }

    }

  }



  /**
   * @param DisplayContext $context
   */
  public static function lineList($context,$options){
    $key = Putil::safeArrGet($options, 'key', null);

    $idata = $context->getItemMetadata();
    $vals =$idata->getItemValues($key);

    DisplayCommandSnipets::item_property_line($context,$options, $vals);

  }

  /**
   * @param DisplayContext $context
   */
  public static function oneLine2($context,$options){
    $key = Putil::safeArrGet($options, 'key', null);

    $idata = $context->getItemMetadata();
    $vals =$idata->getItemValues($key);


    $class = Putil::safeArrGet($options, 'class', null);
    $grid_col_2_class = $context->get('_grid_col_2_class');


    $cnt = count($vals);
    if ($cnt>0){

      printf('<div class="row %s">',$class);
      //echo('<div class="clear">&#160;</div>');
      echo(DisplayCommandSnipets::createLabel($context, $options));
      $tmp = $cnt -1;
      $k=0;
      printf('<ul class="%s">',$grid_col_2_class);

      foreach ($vals as $v){
        $vv = $v->textValue();
        $ref_item = $v->refItem();

        echo('<li>');

        if (empty($ref_item)){
          $url = sprintf('/archive/search?m=a&a=%s',urlencode($vv));
          printf('<a href="%s" class="authlink">%s <img src="/_assets/img/find.png" alt="%s" /></a>',$url,html_data_view($vv),tr('Αναζήτηση'));
        } else {
          $url = "/archive/item/" . urlencode($ref_item);
          printf('<a href="%s" class="authlink">%s <img src="/_assets/img/find.png" alt="%s" /></a>',$url,html_data_view($vv),tr('Αναζήτηση'));
        }
        if ($k < $tmp){
          //echo("&#160; &#160; | &#160;&#160;");
        }
        $k+=1;
        echo('</li>');
      }
      echo('</ul>');
      echo('</div>');

    }


  }


  /**
   * @param DisplayContext $context
   */
  public static function bitstreams($context,$options){
    //$context->dump();
    $bitstreams = $context->getBitstreams();


//     echo('<pre>');print_r($bitstreams);echo('</pre>');

    $articles = $context->getArticles();
    $item_id = $context->get('_itemId');

    $ib = $context->getItemBasics();
    $obj_type = $ib['obj_type'];

    //public static function bitstream_downlads($item_id, $bitstreams, $articles, $options = array()){

      if (empty($articles)){
        $articles = array();
      }

      if (empty($bitstreams)){
        $bitstreams = array();
      }

      if (count($bitstreams ) > 0  || count($articles) > 0) {
        $i=0;
        $list_num = count($bitstreams);

        echo('<div class="sfilters header">');
        echo('<h2>');
          printf('%s',tr('Αvailable documents'));
        echo('</h2>');
        echo('</div>');


        echo('<ol class="itemlist">');

        foreach ($bitstreams as $seq_id => $v){
          $i++;
          $class = ($i==$list_num) ? 'resitem last' : 'resitem';
          printf('<li class="%s">', $class);

          //$bitstream_id = $v['bitstream_id'];
          $bitstream_id = $v['bitstream_id'];
          $mimetype = $v['mimetype'];
          $fname = $v['name'];
          $fbytes = $v['size_bytes'];
          $fsize = PUtil::formatSizeBytes($fbytes);
          $desc = $v['description'];
          $info = $v['info'];
          $artifact_id = $v['artifact_id'];
          $src_url = $v['src_url'];
          $furl = $v['furl'];
          $file_ext = $v['file_ext'];
          $download_fname = $v['download_fname'];
          $item_ref = $v['item'];
          $jdata = $v['jdata'];

          if (!empty($jdata)){
            $json = json_decode($jdata, true);
            $item_id = PUtil::opac1($json,'id');
            $item_title = PUtil::opac1($json,'title');
            $item_info = PUtil::opac1($json,'info');
            $item_description = PUtil::opac1($json,'description');
            //           $item_type = PUtil::opac1($json,'type');
            $item_size = PUtil::opac1($json,'size');
            $item_page = PUtil::opac1($json,'page');
            $item_part = PUtil::opac1($json,'part');
          }

          #$url1 = "/archive/download?i=".urlencode($item_id) . "&d=" . urlencode($bitstream);
          #$url2 = $url1 . "&m=dt";
          $ddoc_flag =false;
          if ($mimetype == 'application/pdf' || $mimetype == 'application/x-cbr'  || $mimetype == 'image/vnd.djvu'){
            $ddoc_flag =true;
        }
        $p2 = empty($furl) ? $artifact_id  : $furl;
        $image_flag  = false;
        if ($mimetype == 'image/jpeg' || $mimetype == 'image/png'){
        $image_flag  = true;
        }
        if ($image_flag){
        $pp = $furl;
            if (empty($pp)){
              if ($image_flag){
              $ext = PUtil::image_extension_from_mimetype($mimetype);
              $pp = $artifact_id . '.' . $ext;
              } else {
              $pp = $artifact_id;
              }
              }
              if (empty($download_fname)){
              $download_fname = $pp;
              }
              $url1 = sprintf('/archive/item/%s/download/%s',$item_id,$pp);
              $url2 = sprintf('/archive/items/%s/%s',$item_id,$item_ref);
              $url3 = sprintf('/archive/item/%s/%s',$item_id,$pp);
              } else {
              $url1 = sprintf('/archive/item/%s/download/%s',$item_id,$p2);
              $url2 = sprintf('/archive/item/%s/%s',$item_id,$p2);
              }
              $direct_download_flag = false;

          if ($mimetype  == 'image/png' || $mimetype == 'image/jpeg' || strpos($mimetype, 'application/rdf+xml') === 0){
                  $direct_download_flag = true;
          }
                      $tfile = $v['thumb_file'];
                      $url = ($direct_download_flag) ? $url2 : $url1;

                      if (strpos($mimetype, 'application/epub+zip') === 0){
                        $tfile = 'epub.jpg';
                        printf('<span class="bithumb_bg_img" style="background-image:url(/_assets/img/%s); "></span>  ', $tfile);

                      }else if (strpos($mimetype, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') === 0 || strpos($mimetype, 'application/msword') === 0){
                      	$tfile = 'doc.jpg';
                      	printf('<span class="bithumb_bg_img" style="background-image:url(/_assets/img/%s); "></span>  ', $tfile);

                      }else if (strpos($mimetype, 'application/pdf') === 0){
                      	$tfile = 'pdf.jpg';
                      	printf('<span class="bithumb_bg_img" style="background-image:url(/_assets/img/%s); "></span>  ', $tfile);

                      }else if (strpos($mimetype, 'audio/mpeg') === 0){
                      	$tfile = 'mp3.jpg';
                      	printf('<span class="bithumb_bg_img" style="background-image:url(/_assets/img/%s); "></span>  ', $tfile);

                      }else if (strpos($mimetype, 'audio/x-ms-wma') === 0){
                      		$tfile = 'wma.jpg';
                      		printf('<span class="bithumb_bg_img" style="background-image:url(/_assets/img/%s); "></span>  ', $tfile);

                      }else if (strpos($mimetype, 'audio/x-wav') === 0){
                      			$tfile = 'wav.jpg';
                      			printf('<span class="bithumb_bg_img" style="background-image:url(/_assets/img/%s); "></span>  ', $tfile);

                      }else if (strpos($mimetype, 'application/x-rar-compressed') === 0){
                      			$tfile = 'daisy.jpg';
                      			printf('<span class="bithumb_bg_img" style="background-image:url(/_assets/img/%s); "></span>  ', $tfile);

                      }else if (empty($tfile)){
                      	$tfile = 'general_file.jpg';
                      	printf('<span class="bithumb_bg_img" style="background-image:url(/_assets/img/%s); "></span>  ', $tfile);

                      }else{
                        printf('<span class="bithumb_bg_img" style="background-image:url(/media/%s); "></span>  ', $tfile);
                      }

                          if(!empty($item_description)){
                          $desc = $item_description;
        }

        printf('<span style="display:inline-block">');

         if(!empty($desc)){
          $desc = sprintf('%s: %s',tr('Description'),$desc);
        }

        $msg = empty($desc) ? $fname : $desc;
          if ($direct_download_flag){
        //printf('%s',$msg);
        if ($image_flag){
          printf(' <a href="%s">%s</a> ', $url3,$msg);
        } else {
        printf(' <a href="%s">%s</a> ', $url2,$msg);
        }
        } else {
        printf('%s',$msg);
        }
        if (!empty($desc)){
          echo " ($file_ext)";
        }
        if ($image_flag){
        printf(' <a href="%s">[Image Details]</a> ', $url2);
        }

            if(!empty($item_info)){
        $info = $item_info;
        }

          if (!empty($info) && $obj_type!="digital-item"){
              echo("<br/>$info");
          }
          if (!empty($src_url)){
            $src_url_desc = $src_url;
            if (strlen($src_url) > 70){
            $src_url_desc = substr($src_url,0,70);
            $src_url_desc = $src_url_desc . "...";
        }
            printf('<br/>source:&nbsp;<a href="%s">%s</a>',$src_url,$src_url_desc);
            }


            if ($obj_type!="digital-item"){
		            if (user_access_admin() ){
		            if($v['symlink']){
		            printf(' <a href="/prepo/edit_bitstream_symlink?sid=%s">[edit_sym]</a> ',$v['symlink_id']);
		            } else {
		            if(!empty($item_id)){
		            printf(' <a href="%s%s" target="_blank">[%s]</a> ',UrlPrefixes::$item_edit, $item_id,tr('edit'));
		            printf(' <a href="%s%s" target="_blank">[%s]</a> ',UrlPrefixes::$item_admin, $item_id,tr('admin'));
		            printf(' <a href="%s%s" target="_blank">[%s]</a> ',UrlPrefixes::$item_opac, $item_id,tr('opac'));
		            }
		            //                 printf(' <a href="/prepo/edit_bitstream?bid=%s">[edit_bit]</a> ',$bitstream_id);
		            }
		            }else{
		            	if(!empty($item_id)){
		            		printf(' <a href="%s%s">[%s]</a> ',UrlPrefixes::$item_opac, $item_id,tr('More information'));
		            	}
		            }
       			}

       			if ($obj_type!="digital-item"){
		            if(!empty($item_part)){
		            	printf('<br/>%s: %s',tr('Part number'),$item_part);
		            }

		            if(!empty($item_page)){
		            printf('<br/>%s: %s',tr('Page'),$item_page);
		            }
       			}

            if(!empty($item_size)){
                  $fsize = $item_size;
            }

            printf('<br/>%s: %s',tr('Size'),$fsize);
            printf('</span>');


            if ($context->get('_userIsRepoLogin')){
              echo('<span class="r_icons">');
              printf('<a href="%s" class="img_dnwload" title="%s" >%s</a>', $url1, tr('Download'), tr('Download'));

              if (strpos($mimetype, 'application/epub+zip') === 0){
              	//$epub_dir = PUtil::frepo_finame_to_full_path($v['internal_id'],);
                //$url4 = sprintf('%s%s',Config::get('readers.EPUB_READER_URL_REFIX'),$v['internal_id']);
              	//$url4 = PUtil::frepo_finame_to_full_path($v['internal_id'],Config::get('readers.EPUB_READER_URL_REFIX'));
              	$url4 = '/archive/epub-viewer?e=' . $v['internal_id'];
              	printf('<a href="%s" class="img_onreader" title="%s">%s</a>', $url4, tr('Online Viewer'),tr('Online Viewer'));
              }
              echo('</span>');
            }

            echo('<div class="clearfix"></div>');
            echo('</li>');
        }

        echo('</ol>');
      }

  }

  /**
   * @param DisplayContext $context
   */
  public static function bitstreams_table($context,$options){

        //$context->dump();
        $bitstreams = $context->getBitstreams();
        $articles = $context->getArticles();
        $item_id = $context->get('_itemId');

        //public static function bitstream_downlads($item_id, $bitstreams, $articles, $options = array()){

        if (empty($articles)){
          $articles = array();
        }

        if (empty($bitstreams)){
          $bitstreams = array();
        }

        if (count($bitstreams ) > 0  || count($articles) > 0) {
        echo('<table id="downloads" class="table table-striped table-bordered">');
        printf('<thead><tr><th class="inf1" colspan="2">%s</th><th class="inf2">size</th><th class="inf2">download</th></tr></thead>',tr('Αvailable documents'));

        //$item_id = $rep['id'];

        foreach ($articles as $article){
          //print_r($article);
          $p = null;
          if (empty($article['node_path'])){
            $p = "/node/" . $article['drupal_node'];
          } else {
            $p = "/" . $article['node_path'];
          }
          // 					$np = $article['node_path'];
          // 					//if ($article['content_type'] == DataFields::DB_content_ctype_article){
          // 					$p = "/content/" . $np;
          // 				}

          //				$p = "/node/" . $article['drupal_node'];

          $url1 = "$p";
          $url2 = sprintf("/archive/download_article/%s",$article['content_id']);
          echo("<tr>");
          printf('<td  class="bthumb"><a href="%s"><img src="/_assets/img/items/text.png"/></a></td>',$url2);
          printf('<td style="width:90%%"><a href="%s">%s</a><br/>%s</td>',$url2, $article['title'],$article['bitstream_desc']);
          printf('<td>%s</td>', PUtil::formatSizeBytes($article['size_bytes']));
          echo('</td><td class="inf2">');
          printf('<a href="%s"><img src="/_assets/img/down32.png" alt="Download" title="Download" /></a>',$url2);
          echo("</td>");
          echo("</tr>");
        }

        foreach ($bitstreams as $seq_id => $v){
          echo('<tr>');
          //$bitstream_id = $v['bitstream_id'];
          $bitstream_id = $v['bitstream_id'];
          $mimetype = $v['mimetype'];
          $fname = $v['name'];
          $fbytes = $v['size_bytes'];
          $fsize = PUtil::formatSizeBytes($fbytes);
          $desc = $v['description'];
          $info = $v['info'];
          $artifact_id = $v['artifact_id'];
          $src_url = $v['src_url'];
          $furl = $v['furl'];
          $file_ext = $v['file_ext'];
          $download_fname = $v['download_fname'];
          $item_ref = $v['item'];
          $jdata = $v['jdata'];

          if (!empty($jdata)){
          $json = json_decode($jdata, true);
          $item_id = PUtil::opac1($json,'id');
          $item_title = PUtil::opac1($json,'title');
          $item_info = PUtil::opac1($json,'info');
          $item_description = PUtil::opac1($json,'description');
//           $item_type = PUtil::opac1($json,'type');
          $item_size = PUtil::opac1($json,'size');
          $item_page = PUtil::opac1($json,'page');
          }

          #$url1 = "/archive/download?i=".urlencode($item_id) . "&d=" . urlencode($bitstream);
          #$url2 = $url1 . "&m=dt";
          $ddoc_flag =false;
          if ($mimetype == 'application/pdf' || $mimetype == 'application/x-cbr'  || $mimetype == 'image/vnd.djvu'){
            $ddoc_flag =true;
          }
          $p2 = empty($furl) ? $artifact_id  : $furl;
          $image_flag  = false;
          if ($mimetype == 'image/jpeg' || $mimetype == 'image/png'){
            $image_flag  = true;
          }
          if ($image_flag){
            $pp = $furl;
            if (empty($pp)){
              if ($image_flag){
                $ext = PUtil::image_extension_from_mimetype($mimetype);
                $pp = $artifact_id . '.' . $ext;
              } else {
                $pp = $artifact_id;
              }
            }
            if (empty($download_fname)){
              $download_fname = $pp;
            }
            $url1 = sprintf('/archive/item/%s/download/%s',$item_id,$pp);
            $url2 = sprintf('/archive/items/%s/%s',$item_id,$item_ref);
            $url3 = sprintf('/archive/item/%s/%s',$item_id,$pp);
          } else {
            $url1 = sprintf('/archive/item/%s/download/%s',$item_id,$p2);
            $url2 = sprintf('/archive/item/%s/%s',$item_id,$p2);
          }
          $direct_download_flag = false;
          if ($mimetype  == 'image/png' || $mimetype == 'image/jpeg' || strpos($mimetype, 'application/rdf+xml') === 0){
            $direct_download_flag = true;
          }
          $tfile = $v['thumb_file'];
          $url = ($direct_download_flag) ? $url2 : $url1;

          echo('<td class="bthumb">');
          printf('<span class="thumb_bg_img" style="background-image:url(/media/%s); "></span>  ', $tfile);
          //Sreen reader
//           if (!empty($tfile)){
//             if ($image_flag){
//               printf('<a class="colorbox-load" rel="images" href="%s"><img src="/media/%s"/></a>',$url3,$tfile);
//             }
//             // 					elseif ($ddoc_flag){
//             // 						printf('<a href="%s"><img src="/media/%s"/></a>',$url2,$tfile);
//             // 					}
//             else {
//               printf('<a href="%s"><img src="/media/%s"/></a>',$url,$tfile);
//             }
//           } else {
//             printf('<a href="%s"><img src="/_assets/img/items/document.png"/></a>',$url);
//           }
          echo('</td>');
          echo('<td class="inf1" style="vertical-align:top;">');

          if(!empty($item_description)){
            $desc = $item_description;
          }


          $msg = empty($desc) ? $fname : $desc;
          if ($direct_download_flag){
            //printf('%s',$msg);
            if ($image_flag){
              printf(' <a href="%s">%s</a> ', $url3,$msg);
            } else {
              printf(' <a href="%s">%s</a> ', $url2,$msg);
            }
          } else {
            printf('%s',$msg);
          }
          if (!empty($desc)){
            echo " ($file_ext)";
          }
          if ($image_flag){
          printf(' <a href="%s">[Image Details]</a> ', $url2);
          }

          if(!empty($item_info)){
            $info = $item_info;
          }

          if (!empty($info)){
          echo("<br/>$info");
          }
          if (!empty($src_url)){
          $src_url_desc = $src_url;
          if (strlen($src_url) > 70){
          $src_url_desc = substr($src_url,0,70);
          $src_url_desc = $src_url_desc . "...";
          }
          printf('<br/>source:&nbsp;<a href="%s">%s</>',$src_url,$src_url_desc);
          }
          if (user_access_admin()){
          if($v['symlink']){
            printf(' <a href="/prepo/edit_bitstream_symlink?sid=%s">[edit_sym]</a> ',$v['symlink_id']);
            } else {
                if(!empty($item_id)){
                printf(' <a href="/prepo/edit_step1?i=%s">[%s]</a> ',$item_id,tr('edit_item'));
                }
//                 printf(' <a href="/prepo/edit_bitstream?bid=%s">[edit_bit]</a> ',$bitstream_id);
          }
                }
          if(!empty($item_page)){
            printf('<br/>%s: %s',tr('Pages'),$item_page);
           }
                echo('</td><td class="inf2" style="text-align:right;">');

                if(!empty($item_size)){
                  $fsize = $item_size;
                }
                printf('%s', $fsize);
              echo('</td>');
              echo('</td><td class="inf2">');

             //Sreen reader
             //printf('<a href="%s"><img src="/_assets/img/down32.png" alt="Download" title="Download" /></a>',$url1);
              printf('<a href="%s" class="img_dwload" >%s</a>', $url1, tr('Download'));

          echo("</td>");

              echo("</tr>");
        }
              echo('</table>');
        }
  }


  /**
   * @param DisplayContext $context
   */
  public static function adminBar($context,$options){
    $ok = $context->get('_userIsRepoMaintainer');
    if (!$ok){
      return;
    }

    $item_id = $context->get('_itemId');
    $status = $context->get('_status');
    $idata = $context->getItemMetadata();
    //$idata->dump();
    $tmp =$idata->getFirstItemValue('ea:bitstream:id');
    $ref_bitstream =empty($tmp) ? null : $tmp->textValue();
    $tmp =$idata->getFirstItemValue('ea:content:id');
    $rec_content = empty($tmp) ? null : $tmp->textValue();

		$basics = $context->getItemBasics();
    $user_create = $basics['user_create'];

    $item['id']=$item_id;
    $item['ref_bitstream']=$ref_bitstream;
    $item['ref_content']=$rec_content;
    $item['status']=$status;
    $item['user_create']=$user_create;

    PSnipets::item_admin_bar($item);

  }



  /**
   * @param DisplayContext $context
   */
  public static function solrLink($context,$options){

  	$template = Putil::safeArrGet($options, 'template', 'empty');
  	$item_id = $context->get('_itemId');
  	$label = $context->get('_title');
  	$idata = $context->getItemBasics();

  	if (!empty($idata['jdata'])){
  		$json = json_decode($idata['jdata'], true);
  		if ( !empty($json['opac1']['as_publishers']) || !empty($json['opac1']['as_pplace']) || !empty($json['opac1']['as_subj']) ){
	  		echo '<div class="row tbr">';

				//as Publisher
	  		if (!empty($json['opac1']['as_publishers'])){

	  			$solr_field = 'publishers_ids';
	  			$title = 'Manifestations with publisher';

	  			$url = UrlPrefixes::$search_solr."?$solr_field=$item_id";
	  			$var_templ = array(
	  					'title' => tr($title),
	  					'label' => $label,
	  					'url' => $url,
	  			);
	  			$m = new Mustache_Engine;
	  			$template_lnk = Config::get('arc_display_template.'.$template);
	  			$content = $m->render($template_lnk,$var_templ);
	  			echo $content;
	  		}

	  		//as Publication Place
	  		if (!empty($json['opac1']['as_pplace'])){

	  			$solr_field = 'publication_places_ids';
	  			$title = 'Manifestations with publication place';

	  			$url = UrlPrefixes::$search_solr."?$solr_field=$item_id";
	  			$var_templ = array(
	  					'title' => tr($title),
	  					'label' => $label,
	  					'url' => $url,
	  			);
	  			$m = new Mustache_Engine;
	  			$template_lnk = Config::get('arc_display_template.'.$template);
	  			$content = $m->render($template_lnk,$var_templ);
	  			echo $content;
	  		}

	  		//as Subject
	  		if (!empty($json['opac1']['as_subj'])){

	  			$solr_field = 'subjects_ids';
	  			$title = 'Manifestations with subject';

	  			$url = UrlPrefixes::$search_solr."?$solr_field=$item_id";
	  			$var_templ = array(
	  					'title' => tr($title),
	  					'label' => $label,
	  					'url' => $url,
	  			);
	  			$m = new Mustache_Engine;
	  			$template_lnk = Config::get('arc_display_template.'.$template);
	  			$content = $m->render($template_lnk,$var_templ);
	  			echo $content;
	  		}

	  		echo '</div>';
  		}
  	}

  }


  /**
   * @param DisplayContext $context
   */
  public static function itemRelations($context,$options){

    $compare_field = Putil::safeArrGet($options, 'compare_field', null);
    $relation_id = Putil::safeArrGet($options, 'relation_id', null);
    $direction = Putil::safeArrGet($options, 'direction', 'both');

    $label = Putil::safeArrGet($options, 'label',null);
    $members  = DisplayCommands::_getMembers($context, $options);

    foreach($members as $row){
    	$obj_type = $row['obj_type'];
    	if ($obj_type == Config::get('arc.DB_OBJ_TYPE_AUTH-EXPRESSION') ){
    		return;
    	}
    }

    if (empty($members)){
      return;
    }

    $item_id = $context->get('_itemId');
    $lang = $context->get('_lang');

    $obj_type_names=$context->get('_obj_type_names');
    $title = $context->get('_title');

    $list_edit_flag  = false;

// 	echo('<div id="tresults">');
// 	echo('<div class="rescnt row res-infobar">');
// 	if (!empty($label)){
// 		printf('<span style="float:left">%s</span>',tr($label));
// 	}elseif (! empty($title)){
// 		printf('<span style="float:left">Related with&nbsp; %s :</span>',tr($title));
// 	}
// 	echo('</div>');

	  echo('<div class="sfilters header">');
	  echo('<h2>');
	  if (!empty($label)){
	  	$count_m = count($members);
	    printf('%s',trChoise($label,$count_m));
	  }elseif (! empty($title)){
	    printf('Related with&nbsp; %s :',tr($title));
	  }
	  echo('</h2>');
	  echo('</div>');

	  echo('<ol class="itemlist">');
	  DisplayCommandSnipets::item_list($members, $obj_type_names,false,$list_edit_flag,true);
	  echo('</ol>');

  }


  /**
   * @param DisplayContext $context
   */
  public static function personRelations($context,$options){
  	$class = isset($options['class']) ? $options['class']: '';
  	$grid_col_class = $context->get('_grid_col_2_class');
  	$template = Putil::safeArrGet($options, 'template', 'empty');
  	$key = Putil::safeArrGet($options, 'key', null);


  	if (!empty($options['setting_variable'])){
	  	$setting_variable = $options['setting_variable'];
	  	if (is_array($setting_variable)){
	  		$setting_json= array();
	  		foreach ($setting_variable as $sub_arr){
	  			$tmp = Setting::get($sub_arr);
	  			if(!empty($tmp)){
	  				$setting_json =  array_merge($setting_json,$tmp);
	  			}
	  		}
	  	}else{
	  		$setting_json = Setting::get($setting_variable);
	  	}

	//    $contributor_work_type_map = Setting::get('contributor_work_type_map');
	//    $contributor_manif_type_map = Setting::get('contributor_manif_type_map');
	//    $inferred_contributor_express_type_map = Setting::get('contributor_express_type_map');
	//    $setting_json = array_merge($contributor_work_type_map, $contributor_manif_type_map, $inferred_contributor_express_type_map);

	  	if(!empty($setting_json)){

	  		foreach ($setting_json as $relation_id => $label) {

	  				$compare_field = Putil::safeArrGet($options, 'compare_field', null);
	  				$direction = Putil::safeArrGet($options, 'direction', 'both');

	  				$members  = DisplayCommands::_getMembers($context, $options, $relation_id);

	  				$d=1; //display method

	  				$item_id = $context->get('_itemId');
	  				$lang = $context->get('_lang');
	  				$obj_type_names=$context->get('_obj_type_names');
	  				$title = $context->get('_title');

	  				$list_edit_flag  = false;

	 					if (!empty($members)){

	  						if ($d == 1){
	  								$colspan="4";
	  								if ($list_edit_flag){
	  								$colspan="5";
	  						}

	  						echo('<div class="sfilters header">');
	    					echo('<h2>');
	  						if (!empty($label)){
	  							printf('%s %s %s',tr("Συμμετέχει ως"),tr($label),tr("σε:"));
	  						}elseif (! empty($title)){
	  							printf('Related with&nbsp; %s :',tr($title));
	 						 }
	  						echo('</h2>');
	  				 		echo('</div>');

	  			 			echo('<ol class="itemlist">');
	  						DisplayCommandSnipets::item_list($members, $obj_type_names,false,$list_edit_flag,true);
	  						echo('</ol>');

	  				} elseif ($d > 1){
	  					#####################################################################################
	  					### TABLE HEADER THUMBS
	  					#####################################################################################

	  					echo('<table id="tresults">');
	  					echo("\n");
	  					echo('<thead>');
	  					echo('<tr><th>');
	  					DisplayCommandSnipets::size_buttons($item_id,$lang);
	  					echo('</th></tr>');
	  					echo('</thead>');
	  					echo('</table>');

	  					DisplayCommandSnipets::item_list_thumbs($members,$d,$lang);

	  					#####################################################################################
	  					### TABLE FOOTER THUMBS
	  					#####################################################################################

	  					echo('<br/>');
	  					echo('<table><tr><td style="text-align: center;">');
	  					echo('</td></tr></table>');
	  				}
	 				}
	  		}
	 	 }else{
	  		return;
	 	 }
	 }else{
	  	return;
	 }

  }



  /**
   * @param DisplayContext $context
   */
  public static function itemRelations_table($context,$options){

    $compare_field = Putil::safeArrGet($options, 'compare_field', null);
    $relation_id = Putil::safeArrGet($options, 'relation_id', null);
    $direction = Putil::safeArrGet($options, 'direction', 'both');
    $object_type = Putil::safeArrGet($options, 'object_type', null);

    $label = Putil::safeArrGet($options, 'label',null);
    $members  = DisplayCommands::_getMembers($context, $options);

//         echo('<pre>');print_r($members);echo('</pre>');

    if (empty($members)){
      return;
    }

    $d=1; //display method

    $item_id = $context->get('_itemId');
    $lang = $context->get('_lang');

    $obj_type_names=$context->get('_obj_type_names');
    $title = $context->get('_title');

    $list_edit_flag  = false;
      if ($d == 1){
        $colspan="4";
        if ($list_edit_flag){
          $colspan="5";
        }


        echo('<table class="table table-striped table-bordered members">');
        echo('<thead>');
        printf('<tr><th colspan="%s">',$colspan);
        if (!empty($label)){
          printf('<span style="float:left">%s</span>',tr($label));
        }elseif (! empty($title)){
          printf('<span style="float:left">Related with&nbsp; %s :</span>',tr($title));
        }
        //Sreen Reader
        //DisplayCommandSnipets::size_buttons($item_id,$lang);
        echo("</th></tr>");
        echo('</thead>');
        echo('<tbody valign="top">');
        echo("\n");

        DisplayCommandSnipets::item_list_table($members, $obj_type_names,false,$list_edit_flag,true);

        echo('</tbody>');
        echo('</table>');
      } elseif ($d > 1){
        #####################################################################################
        ### TABLE HEADER THUMBS
        #####################################################################################

        echo('<table id="tresults">');
        echo("\n");
        echo('<thead>');
        echo('<tr><th>');
        DisplayCommandSnipets::size_buttons($item_id,$lang);
        echo('</th></tr>');
        echo('</thead>');
        echo('</table>');

        DisplayCommandSnipets::item_list_thumbs($members,$d,$lang);

        #####################################################################################
        ### TABLE FOOTER THUMBS
        #####################################################################################

        echo('<br/>');
        echo('<table><tr><td style="text-align: center;">');
        echo('</td></tr></table>');
      }

  }



  /**
   * @param DisplayContext $context
   */
  public static function ItemPhysical_table($context,$options){

  	$relation_id = Putil::safeArrGet($options, 'relation_id', null);
  	$direction = Putil::safeArrGet($options, 'direction', 'both');
  	$label = Putil::safeArrGet($options, 'label',null);
  	$members  = DisplayCommands::_getMembers($context, $options);

  	if (empty($members)){
  		return;
  	}

  	$has_pitem = false;
  	foreach($members as $m){
	  	if (in_array('physical-item',$m)) {
	  		$has_pitem = true;
	  	}
  	}
  	if (!$has_pitem){
  		return;
  	}

  	$lang = $context->get('_lang');
  	$title = $context->get('_title');

  	echo('<table id="physical-items" class="table table-striped table-bordered members">');
	  		echo('<caption class="sfilters header">');
	  		if (!empty($label)){
	  			printf('<h2>%s</h2>',tr($label));
	  		}elseif (! empty($title)){
	  			printf('<h2>Related with&nbsp; %s :</h2>',tr($title));
	  		}
	  		echo('</caption>');

	  		echo('<thead>');
	  			echo("<tr>\n");
			  		printf('<th>%s</th>',tr('Type'));
			  		printf('<th>%s</th>',tr('Sublocation'));
			  		printf('<th>%s</th>',tr('Location'));
						printf('<th>%s</th>',tr('Barcode'));
						printf('<th>%s</th>',tr('Classification'));
						printf('<th>%s</th>',tr('Part number'));
						printf('<th>%s</th>',tr('Copy number'));
						printf('<th>%s</th>',tr('Link'));
	  			echo("</tr>\n");
	  		echo('</thead>');
	  		echo('<tbody valign="top">');
	  		echo("\n");
		  		foreach($members as $row){
						if ($row['obj_type'] == 'physical-item'){
							if (!empty($row['jdata'])){
								$opac = new OpacHelper($row['jdata']);
			  				$pitem_id = $opac->opac1('id');
			  				echo("<tr>\n");
			  						echo("<td>");
			  								echo $opac->opac1('type');
			  						echo("</td>");
			      				echo('<td>');
			  								echo $opac->opac1('sublocation');
			  		     		echo('</td>');
			  		     		echo('<td>');
			  		     				echo $opac->opac1('location');
			  		     		echo('</td>');
			  		     		echo('<td>');
			  		     				echo $opac->opac1('barcode');
			  		     		echo('</td>');
			  		     		echo('<td>');
			  		     				echo $opac->opac1('classification');
			  		     		echo('</td>');
			  		     		echo('<td>');
			  		     				echo $opac->opac1('part');
			  		     		echo('</td>');
										echo('<td>');
												echo  $opac->opac1('copyNumber');
			  						echo('</td>');
			  						echo('<td>');
					  						if (user_access_admin() ){
					  							printf(' <a href="%s%s" target="_blank">[%s]</a> ',UrlPrefixes::$item_edit, $pitem_id,tr('edit'));
					  							printf(' <a href="%s%s" target="_blank">[%s]</a> ',UrlPrefixes::$item_admin, $pitem_id,tr('admin'));
					  							printf(' <a href="%s%s" target="_blank">[%s]</a> ',UrlPrefixes::$item_opac, $pitem_id,tr('opac'));
					  						}else{
					  							printf(' <a href="%s%s" target="_blank">[%s]</a> ',UrlPrefixes::$item_opac, $pitem_id,tr('More information'));
					  						}
			  						echo('</td>');

			  				echo("</tr>\n");
							}
						}
		  		}
  		echo('</tbody>');
  	echo('</table>');

  }


  /**
   * @param DisplayContext $context
   */
  public static function contextDump($context,$options){
    $context->dump();
    $context->getItemMetadata()->dump();



  }


// 	/**
// 	 * @param DisplayContext $context
// 	 */
// 	public static function note($context,$options){
// 		$class = isset($options['class']) ? "front ".$options['class']: "front";
// 		$label = isset($options['label']) ? tr($options['label']).':': null;
// 		$labelWidth = Putil::safeArrGet($options, 'label_width',$context->get('_label_width'));

// 		$idata = $context->getItemMetadata();
// 		$vals = $idata->getItemValues($options['key']);

// 		foreach ($vals as $v) {
// 			$val1 = $v->textValue();
// 			$lnk = $v->recordId();
// 			$val2 = $idata->getFirstItemValue('ea:text-format:', $lnk);
// 			$val1 = (!empty($val2) && $val2->textValue()=='html') ? $val1 : nl2br(htmlspecialchars($val1));
// 			$options['label_float'] = 'left';
// 			$labelE = DisplayCommandSnipets::createLabel($context, $options);
// 			printf('
// 					<div class="%s">
// 					%s
// 					<div style=float:left;">%s</div>
// 					<div class="spacer">&nbsp;</div>
// 					</div>
// 					',$class,$labelE,$val1);
// 		}

// 	}


  /**
   * @param DisplayContext $context
   */
  public static function note($context,$options){

    $class = isset($options['class']) ? $options['class']: '';
    $label = isset($options['label']) ? tr($options['label']).':': null;

    $idata = $context->getItemMetadata();
    $vals = $idata->getItemValues($options['key']);

    foreach ($vals as $v) {
      $val1 = $v->textValue();

      $lnk = $v->recordId();
      $val2 = $idata->getFirstItemValue('ea:text-format:', $lnk);
      $val1 = (!empty($val2) && $val2->textValue()=='html') ? $val1 : nl2br(htmlspecialchars($val1));

      $grid_col_2_class = $context->get('_grid_col_2_class');
      $labelE = DisplayCommandSnipets::createLabel($context,$options);
      printf('<div class="row %s">%s  <div class="%s"> %s</div></div>',$class,$labelE,$grid_col_2_class, $val1);
    }

  }



  /**
   * @param DisplayContext $context
   */
  public static function dimensions($context,$options){

    $idata = $context->getItemMetadata();
    $pages = $idata->getFirstItemValue('ea:size:pages');
    $sheets = $idata->getFirstItemValue('ea:size:sheets');
    $dims = $idata->getFirstItemValue('ea:dimensions:extent');

    $options['label'] = tr('Σελίδες');
    $labelE = DisplayCommandSnipets::createLabel($context,$options);
    if (empty($pages) && empty($sheets) && empty($dims)) return;
    echo('<div class="row dimensions">');
    echo($labelE);
    $grid_col_2_class = $context->get('_grid_col_2_class');
    printf('<div class="%s">',$grid_col_2_class);
    if (!empty($pages)){
      printf('<span>%s:</span> <span class="value pages">%s</span> ',tr('Σελίδες'),$pages->textValue());
    };
    if (!empty($sheets)){
      printf('<span>%s:</span> <span class="value sheets">%s</span> ',tr('Φύλλα'),$sheets->textValue());
    };
    if (!empty($dims)){
      printf('<span>%s:</span> <span class="value dims">%s</span> ',tr('Διαστάσεις'),$dims->textValue());
    };
    echo('</div>');
    echo('</div>');
    // 		$vals = $idata->getItemValues($options['key']);

    // 		foreach ($vals as $v) {
    // 			$val1 = $v->textValue();

    // 			if (!empty($v->refItem())){
    // 				$val2 = sprintf('<a class="assetlink" href="%s" target="_blank">%s</a>',$v->refItem(),htmlspecialchars($val1));
    // 			}else{
    // 				$val2 = sprintf('%s',htmlspecialchars($val1));
    // 			}

    // 			printf('<div class="%s"> <span class="front_sc_label">%s</span> %s</div>',$class,htmlspecialchars($label),$val2);
    // 		}

  }



  private static function _getMembers($context,$options,$relation_id=null){
    $compare_field = Putil::safeArrGet($options, 'compare_field', null);
    $direction = Putil::safeArrGet($options, 'direction', 'both');
    $inferred = Putil::safeArrGet($options, 'inferred', null);
    if (empty($relation_id) || is_null($relation_id)){
    	$relation_id = Putil::safeArrGet($options, 'relation_id', null);
    }

    $itemRelations =$context->getItemRelations();

    $cmp = null;
    if ( !empty($compare_field)){
      $cmp = function($r1, $r2) use($compare_field){
        $a = $r1[$compare_field];
        $b = $r2[$compare_field];
        if ($a == $b) {return 0;}
        return ($a < $b) ? -1 : 1;
      };
    }

    if ($direction == 'from'){
      $members = $itemRelations->getRelationsFrom($relation_id,$cmp,$inferred);
    } else if($direction == 'to') {
      $members = $itemRelations->getRelationsTo($relation_id,$cmp,$inferred);
    } else {
      $members = $itemRelations->getRelationsBoth($relation_id,$cmp,$inferred);
    }
    return $members;
  }

  /**
   * @param DisplayContext $context
   */
  public static function itemRelationLines($context,$options){

    $label = Putil::safeArrGet($options, 'label',null);
    $labelWidth = Putil::safeArrGet($options, 'label_width',$context->get('_label_width'));

    $members  = DisplayCommands::_getMembers($context, $options);
    echo('<div>');
    $c = 0;
    foreach ($members as $k=>$v){
      echo('<div class="relation_line_wrap">');
      if ($c == 0 && !empty($label)){
        printf('<label class="front_sc_label" style="width:%s">%s:</label>',$labelWidth, tr($label));
      } else {
        printf('<label class="front_sc_label" style="width:%s">&nbsp;</label>',$labelWidth);
      }
      printf('<span class="relation_line"><a href="/archive/item/%s">%s</a></span>',$v['item_id'],$v['title']);
      $c+=1;
      echo('</div>');
      echo("\n");
    };

// 					echo('<pre>');
// 					print_r($members);
// 					echo('</pre>');

    echo('</div>');
  }



  public static function itemRelationLines2($context,$options){
    $key = Putil::safeArrGet($options, 'key', null);

    $idata = $context->getItemMetadata();
    $vals =$idata->getItemValues($key);


    $class = Putil::safeArrGet($options, 'class', null);
    $grid_col_2_class = $context->get('_grid_col_2_class');

    $members  = DisplayCommands::_getMembers($context, $options);
    $cnt = count($members);
    if ($cnt>0){

      printf('<div class="row %s">',$class);
      //echo('<div class="clear">&#160;</div>');
      echo(DisplayCommandSnipets::createLabel($context, $options));
      $tmp = $cnt -1;
      $k=0;
      printf('<ul class="%s">',$grid_col_2_class);

      foreach ($members as $k=>$v){
        //echo('<pre>');print_r($v);echo('</pre>');
        $vv = $v['title'];
        $ref_item = $v['item_id'];
        echo('<li>');
        $url = "/archive/item/" . urlencode($ref_item);
        printf('<a href="%s" class="authlink">%s <img src="/_assets/img/find.png" alt="%s" /></a>',$url,html_data_view($vv),tr('Αναζήτηση'));
        if ($k < $tmp){
          //echo("&#160; &#160; | &#160;&#160;");
        }
        $k+=1;
        echo('</li>');
      }
      echo('</ul>');
      echo('</div>');

    }


  }





}





























class DisplayCommandSnipets {


  /**
   * @param DisplayContext $context
   */

  public static function createLabel($context,$options){

    $label = isset($options['label']) ? htmlspecialchars(tr($options['label'])): '&nbsp;';
    //if (empty($label)) return '';

    $labelWidth = Putil::safeArrGet($options, 'label_width',$context->get('_label_width',null));
    $labelAlign = Putil::safeArrGet($options, 'label_align','left');
    $labelFloat = Putil::safeArrGet($options, 'label_float',null);
    $labelClass = Putil::safeArrGet($options, 'label_class', $context->get('_grid_col_1_class'));

    $style = '';
    if (!empty($labelWidth)){ $style .= sprintf('width:%s;',$labelWidth); }
//     if (!empty($labelAlign)){ $style .= sprintf('text-align:%s;',$labelAlign); }
    if (!empty($labelFloat)){ $style .= sprintf('float:%s;',$labelFloat); }

    return sprintf('<label class="%s" style="%s">%s</label>',$labelClass, $style, $label);

  }


  public static function item_property_line($context,$options, $vals){

    $class = Putil::safeArrGet($options, 'class', null);


    $cnt = count($vals);
    if ($cnt>0){

      printf('<div class="front_%s">',$class);
      echo('<div class="clear">&#160;</div>');
      echo(DisplayCommandSnipets::createLabel($context, $options));
      $tmp = $cnt -1;
      $k=0;
      foreach ($vals as $v){
        $vv = $v->textValue();
        $ref_item = $v->refItem();
        if (empty($ref_item)){
          $url = sprintf('/archive/search?m=a&a=%s',urlencode($vv));
          printf('<a href="%s" class="authlink">%s <img src="/_assets/img/find.png" alt="%s" /></a>',$url,html_data_view($vv),tr('Αναζήτηση'));
        } else {
          $url = "/archive/item/" . urlencode($ref_item);
          printf('<a href="%s" class="authlink">%s <img src="/_assets/img/find.png" alt="%s" /></a>',$url,html_data_view($vv),tr('Αναζήτηση'));
        }
        if ($k < $tmp){
          echo("&#160; &#160; | &#160;&#160;");
        }
        $k+=1;
      }
      echo('</div>');
    }


  }




  public static function size_buttons($i,$lang) {
    echo('<div id="sizebtns" >');
    #echo(tr('Αλλαγή εμφάνισης') . ': &nbsp;');
    printf('<a href="/archive/item/%s&lang=%s&d=4"><img class="sizebtn" src="/_assets/img/vthumbs2.png"/></a>',$i,$lang);
    printf('<a href="/archive/item/%s&lang=%s&d=3"><img class="sizebtn" src="/_assets/img/vthumbs.png"/></a>',$i,$lang);
    printf('<a href="/archive/item/%s&lang=%s&d=1"><img class="sizebtn" src="/_assets/img/vlist.png"/></a>',$i,$lang);
    echo('</div>');
  }





  public static function item_list($result, $obj_type_names, $edit_flag = false, $list_edit_flag = false, $small_img_flag = false){

    #####################################################################################
    ### TABLE BODY LIST
    #####################################################################################
    $lang = get_lang();

    $img_class1="resimg";
    $img_class2="";
    $i=0;
    if ($small_img_flag){
      $img_class1 = "smallimg1";
      $img_class2 = "smallimg2";
    }

    $no_download = '<img title="not available for download" alt="not available for download" src="/_assets/img/no-download.png"/>';
    $download = '<span class="glyphicon glyphicon-download-alt" title="Available for download" aria-hidden="true"></span>';

    $list_num = count($result);

    foreach($result as $row){
      $i++;
      $obj_type = $row['obj_type'];
      $folder_flag = $row['folder'];
      $folders = $row['folders'];

      if (!PUtil::isEmpty($folders)){
        $folders = sprintf('(%s)',$folders);
      }

      if (empty($row['bibref'])){
      $download_img = $download;
      } else {
      $download_img = $no_download;
    }

    $class = ($i== $list_num) ? 'resitem last' : 'resitem';
    printf('<li class="%s">', $class);

        $thumb = $row['thumb'];
        $pages = $row['pages'];
        if (!empty($pages)){
        $pagesStr = sprintf('<br/> %s: %s', tr('σελιδες'), $pages);
        } else {
        $pagesStr = "";
        }
            if ($folder_flag){
          $txt = ($obj_type == Config::get('arc.DB_OBJ_TYPE_WEBSITE'))? tr('σελίδες') : ($obj_type == Config::get('arc.DB_OBJ_TYPE_SILOGI')) ? tr('τεκμήρια'): tr('τεύχη') ;
              $tefxiStr = sprintf('<br/>%s:%s',$txt , coalesce($row['issue_cnt'],'1'));
          } else {
          $tefxiStr= "";
      }
              if ($edit_flag){
                $tefxiStr .= sprintf('<br>id: %s 	&#160; 	&#160; status: <a href="/archive/recent?s=%s">%s</a>  ',$row['item_id'],$row['status'],$row['status']);
                if (! empty($row['user_create'])){
                $tefxiStr .= sprintf('&#160; &#160;  create: %s',$row['user_create']);
        }
        if (! empty($row['user_update'])){
                $tefxiStr .= sprintf('&#160; &#160;  update: %s',$row['user_update']);
        }

        $dt = coalesce($row['dt_update'], $row['dt_create']);

          $phpdate = strtotime( $dt );
              $tefxiStr .= sprintf('&#160; &#160; %s',date('d/m/Y',strtotime( $dt )));
                }

              if (! empty($thumb)){
                    printf('<span aria-hidden="true" class="thumb_bg_s_img" style="background-image:url(/media/%s);"></span>', $thumb);
              }else{
                    if ($obj_type == 'silogi'){
                        printf('<a href="/archive/item/%s?lang=%s" title="%s" aria-hidden="true"><img class="%s" src="/_assets/img/books4_64.png" alt="%s"/></a>',$row['item_id'],$lang,htmlspecialchars($row['title']), $img_class1, htmlspecialchars($row['title']));
                    }
              }

              if (!empty($row['jdata'])){
                $json = json_decode($row['jdata'], true);
                if (!empty($json['opac1']['public_title']['title'])){
                	$title = $json['opac1']['public_title']['title'];
                }else if (! empty($json['label'])){
                	$title = $json['label'];
	            	}
              }
              if (empty($title)){
              	$title = $row['title'];
              }
              if (empty($title)){
              	$title = $row['item_id'];
              }

              printf('<a href="/archive/item/%s?lang=%s">%s</a> %s %s %s %s %s',$row['item_id'], $lang, $title ,$row['place'],$row['year'], $folders, $pagesStr, $tefxiStr);


              //LIST-WORK
              //WORKS (individual,independent)
              if (!empty($json['opac1']['independent_works'])){
              	$relation_work_wholepart_map = Setting::get('relation_work_wholepart_map');
              	$independent_works = $json['opac1']['independent_works'];
              	$delimiter = true;
              	$works_array  = array();
              	foreach ($independent_works as $indw){
              		if (($indw === end($independent_works))){
              			$delimiter = false;
              		}
              		if(empty($indw['label'])){
              			$indw['label']=$indw['id'];
              		}

              		$works_array[]  = array('label' => $indw['label'], 'id' => $indw['id'], 'delimiter' => $delimiter);
              	}

              	$var_templ_contained = array(
              			'list_title' => $relation_work_wholepart_map['ea:relation:containerOfIndependent'],
              			'works_array' => $works_array,
              	);
              	$m = new Mustache_Engine;

              	$template_contained = Config::get('arc_display_template.work_contained');
              	$content = $m->render($template_contained,$var_templ_contained);
              	echo $content;
              	//SR-ONLY
              	$template_contained2 = Config::get('arc_display_template_sr-only.work_contained');
              	$content2 = $m->render($template_contained2,$var_templ_contained);
              	echo $content2;
              }

              //WORKS (contributions)
              if (!empty($json['opac1']['contained_contributions'])){
              	$relation_work_wholepart_map = Setting::get('relation_work_wholepart_map');
              	$contributions_works = $json['opac1']['contained_contributions'];
              	$delimiter = true;
              	$works_array  = array();
              	foreach ($contributions_works as $contrw){
              		if (($contrw === end($contributions_works))){
              			$delimiter = false;
              		}
              		if(empty($contrw['label'])){
              			$contrw['label']=$contrw['id'];
              		}

              		$works_array[]  = array('label' => $contrw['label'], 'id' => $contrw['id'], 'delimiter' => $delimiter);
              	}

              	$var_templ_contained = array(
              			'list_title' => $relation_work_wholepart_map['ea:relation:containerOfContributions'],
              			'works_array' => $works_array,
              	);
              	$m = new Mustache_Engine;

              	$template_contained = Config::get('arc_display_template.work_contained');
              	$content = $m->render($template_contained,$var_templ_contained);
              	echo $content;
              		//SR-ONLY
              	$template_contained2 = Config::get('arc_display_template_sr-only.work_contained');
              	$content2 = $m->render($template_contained2,$var_templ_contained);
              	echo $content2;
              }

              //WORKS (documents)
              if (!empty($json['opac1']['contained_documents'])){
              	$relation_work_wholepart_map = Setting::get('relation_work_wholepart_map');
              	$documents_works = $json['opac1']['contained_documents'];
              	$delimiter = true;
              	$works_array  = array();
              	foreach ($documents_works as $documw){
              		if (($documw === end($documents_works))){
              			$delimiter = false;
              		}
              		if(empty($documw['label'])){
              			$documw['label']=$documw['id'];
              		}

              		$works_array[]  = array('label' => $documw['label'], 'id' => $documw['id'], 'delimiter' => $delimiter);
              	}

              	$var_templ_contained = array(
              			'list_title' => $relation_work_wholepart_map['ea:relation:containerOfDocuments'],
              			'works_array' => $works_array,
              	);
              	$m = new Mustache_Engine;

              	$template_contained = Config::get('arc_display_template.work_contained');
              	$content = $m->render($template_contained,$var_templ_contained);
              	echo $content;
              	//SR-ONLY
              	$template_contained2 = Config::get('arc_display_template_sr-only.work_contained');
              	$content2 = $m->render($template_contained2,$var_templ_contained);
              	echo $content2;
              }
              //*


//               //LIST-MANIF
//               //WORKS (individual,independent)
//               if (!empty($json['opac1']['contained_independent_works'])){
//               	$relation_work_wholepart_map = Setting::get('relation_work_wholepart_map');
//               	$contained_independent_works = $json['opac1']['contained_independent_works'];
//               	$delimiter = true;
//               	$works_array  = array();
//               	$works_array2  = array();
//               	foreach ($contained_independent_works as $ciw){
//               		if (($ciw === end($contained_independent_works))){
//               			$delimiter = false;
//               		}
//               		$works_array[]  = array('label' => $ciw['label'], 'id' => $ciw['id'], 'delimiter' => $delimiter);
//               	}
// //               	 if (!empty($json['opac1']['container_individual_works'])){
// //               	 		$container_individual_works = $json['opac1']['container_individual_works'];
// //               	 		$delimiter2 = true;
// //               	 		foreach ($container_individual_works as $ciw2){
// //               	 			if (($ciw2 === end($container_individual_works))){
// //               	 				$delimiter2 = false;
// //               	 			}
// //               	 			$works_array2[]  = array('label' => $ciw2['label'], 'id' => $ciw2['id'], 'delimiter' => $delimiter2);
// //               	 		}
// //               	 }
//               	$var_templ_contained = array(
//               			'list_title' => $relation_work_wholepart_map['ea:relation:containerOfIndependent'],
//               			'works_array' => $works_array,
// //               			'list_title2' => $relation_work_wholepart_map['ea:relation:containedInIndividual'],
// //               			'works_array2' => $works_array2,
//               	);
//               	$m = new Mustache_Engine;

//               	$template_contained = Config::get('arc_display_template.manifestation_contained');
//               	$content = $m->render($template_contained,$var_templ_contained);
//               	echo $content;
//               		//SR-ONLY
//               	$template_contained2 = Config::get('arc_display_template_sr-only.manifestation_contained');
//               	$content2 = $m->render($template_contained2,$var_templ_contained);
//               	echo $content2;
//               }
//               //*


              echo('<div class="clearfix"></div>');
              echo('</li>');
              }
  }



  public static function item_list_table($result, $obj_type_names, $edit_flag = false, $list_edit_flag = false, $small_img_flag = false){

    #####################################################################################
    ### TABLE BODY LIST
    #####################################################################################
    $lang = get_lang();

    $img_class1="resimg";
    $img_class2="";
    if ($small_img_flag){
      $img_class1 = "smallimg1";
      $img_class2 = "smallimg2";
    }


    $no_download = '<img title="not available for download" alt="not available for download" src="/_assets/img/no-download.png"/>';
// 		$download = '<img  title="available for download" alt="available for download" src="/_assets/img/download.png"/>';
    $download = '<span class="glyphicon glyphicon-download-alt" title="Available for download" aria-hidden="true"></span>';

    foreach($result as $row){
      // echo("<pre>");
      // print_r($row);
      // echo("<pre>");
      $obj_type = $row['obj_type'];
      $folder_flag = $row['folder'];
      $folders = $row['folders'];

      if (!PUtil::isEmpty($folders)){
        $folders = sprintf('(%s)',$folders);
      }

      #$folder_flag = false;
      #if ($obj_type == DB_OBJ_TYPE_EFIMERIDA  || $obj_type == DB_OBJ_TYPE_PERIODIKO ||$obj_type == DB_OBJ_TYPE_WEBSITE  ){
      #		$folder_flag = true;
      #}

      if (empty($row['bibref'])){
        $download_img = $download;
      } else {
        $download_img = $no_download;
      }

      echo("<tr>\n");
      if ($list_edit_flag && user_access_admin()){
        echo("<td>");
        printf('<input class="listedit" type="checkbox" name="%s" value="%s" />',$row['item_id'],$row['item_id']);
        echo("</td>");
      }

      echo('<td class="std1">');

      if ($folder_flag){
        printf('<a href="/archive/item/%s?lang=%s"><img class="mimeico %s" src="/_assets/img/items/folder.png"/></a>',$row['item_id'],$lang, $img_class2);
        $bg_img= 'subject.png';
      } else if ($obj_type == Config::get('arc.DB_OBJ_TYPE_WEBSITE_INSTANCE')){
        printf('<a href="/archive/item/%s?lang=%s"><img class="mimeico %s" src="/_assets/img/items/text-html.png"/></a>',$row['item_id'],$lang, $img_class2);
        $bg_img= 'subject.png';
      } else if ($obj_type == Config::get('arc.DB_OBJ_TYPE_PERSON')){
        printf('<a href="/archive/item/%s?lang=%s"><img class="mimeico %s" src="/_assets/img/items/user.png"/></a>',$row['item_id'],$lang, $img_class2);
        $bg_img= 'subject.png';
      } elseif ($obj_type == Config::get('arc.DB_OBJ_TYPE_AUTH-WORK')){
        $bg_img= 'expression.png';
      }elseif($obj_type == Config::get('arc.DB_OBJ_TYPE_AUTH-EXPRESSION')){
        $bg_img= 'expression.png';
      }elseif($obj_type == Config::get('arc.DB_OBJ_TYPE_AUTH-MANIFESTATION')){
        $bg_img= 'manif.png';
      }elseif($obj_type == Config::get('arc.DB_OBJ_TYPE_AUTH-PERSON')){
        $bg_img= 'person.png';
      }elseif($obj_type == Config::get('arc.DB_OBJ_TYPE_AUTH-FAMILY')){
        $bg_img= 'family.png';
      }elseif($obj_type == Config::get('arc.DB_OBJ_TYPE_AUTH-ORGANIZATION')){
        $bg_img= 'organ.png';
      }elseif($obj_type == Config::get('arc.DB_OBJ_TYPE_AUTH-PLACE')){
        $bg_img= 'map.png';
      }else{
        // 	$bg_img= 'document.png';
        $bg_img= 'subject.png';
      }


      printf('<span class="obj_type_bg_img" style="background-image:url(/_assets/img/items/%s);"></span><span class="element-invisible">%s</span>  ', $bg_img, tr($obj_type_names[$obj_type]));
      //Sreen Reader
      //printf('<span class="obj_type" style="background-image:url(/_assets/img/items/%s);">%s</span>',$bg_img,tr($obj_type_names[$obj_type]));
//       else {
//         printf('<a href="/archive/item/%s?lang=%s"><img class="mimeico %s" src="/_assets/img/items/document.png"/></a>',$row['item_id'],$lang, $img_class2);
//       }
//       echo('<br/>');
//       echo(tr($obj_type_names[$obj_type]));
      if ($folder_flag){
        $txt = ($obj_type == Config::get('arc.DB_OBJ_TYPE_WEBSITE'))? tr('σελίδες') : ($obj_type == Config::get('arc.DB_OBJ_TYPE_SILOGI')) ? tr('τεκμήρια'): tr('τεύχη') ;
        printf('<br/>%s:&nbsp;%s',$txt , coalesce($row['issue_cnt'],'1'));
      }

      echo('</td>');

      $thumb = $row['thumb'];
      $pages = $row['pages'];
      if (!empty($pages)){
        $pagesStr = sprintf('<br/> %s: %s', tr('σελιδες'), $pages);
      } else {
        $pagesStr = "";
      }
      if ($folder_flag){
        $txt = ($obj_type == Config::get('arc.DB_OBJ_TYPE_WEBSITE'))? tr('σελίδες') : ($obj_type == Config::get('arc.DB_OBJ_TYPE_SILOGI')) ? tr('τεκμήρια'): tr('τεύχη') ;
        $tefxiStr = sprintf('<br/>%s:%s',$txt , coalesce($row['issue_cnt'],'1'));
      } else {
        $tefxiStr= "";
      }
      if ($edit_flag){
        $tefxiStr .= sprintf('<br>id: %s 	&#160; 	&#160; status: <a href="/archive/recent?s=%s">%s</a>  ',$row['item_id'],$row['status'],$row['status']);
        if (! empty($row['user_create'])){
          $tefxiStr .= sprintf('&#160; &#160;  create: %s',$row['user_create']);
        }
        if (! empty($row['user_update'])){
          $tefxiStr .= sprintf('&#160; &#160;  update: %s',$row['user_update']);
        }

        $dt = coalesce($row['dt_update'], $row['dt_create']);

        $phpdate = strtotime( $dt );
        $tefxiStr .= sprintf('&#160; &#160; %s',date('d/m/Y',strtotime( $dt )));


      }

      echo('<td>');
      if (! empty($thumb)){
        printf('<span class="thumb_s_bg_img" style="background-image:url(/media/%s); "></span>  ', $thumb);
        //Sreen Reader
//       	printf(' <a href="/archive/item/%s?lang=%s" title="%s"><img class="%s" src="/media/%s" alt="%s"/></a>',$row['item_id'],$lang,htmlspecialchars($row['title']), $img_class1, $thumb, htmlspecialchars($row['title']));
      } else {
        if ($obj_type == 'silogi'){
          printf('<a href="/archive/item/%s?lang=%s" title="%s"><img class="%s" src="/_assets/img/books4_64.png" alt="%s"/></a>',$row['item_id'],$lang,htmlspecialchars($row['title']), $img_class1, htmlspecialchars($row['title']));
        } else {
//           printf('<img class="resimg" src="/_assets/img/pixel.gif"/>');
          echo ('<span class="empty_td"></span>');
          //printf('<a href="/archive/item/%s?lang=%s" title="%s"><img class="resimg" src="/_assets/img/pixel.gif" alt="%s"/></a>',$row[5],$lang,$row[1], $row[1]);
        }
      }
      echo('</td>');
      printf('<td style="width:100%%">');

//       if (!empty($row['jdata'])){
//       		$opac = new OpacHelper($row['jdata']);
//       		if ($opac->hasOpac2('Title_punc')){
//       			$title = $opac->opac2('Title_punc');
//       		}else{
// 						$title = htmlspecialchars($row['title']);
// 					}
//       }
      if (!empty($row['jdata'])){
        $json = json_decode($row['jdata'], true);
        if (!empty($json['label'])){
          $title = htmlspecialchars($json['label']);
        }
      }else{
          $title = htmlspecialchars($row['title']);
      }

      printf('<a href="/archive/item/%s?lang=%s">%s</a><br/> %s %s %s %s %s',$row['item_id'], $lang, $title ,$row['place'],$row['year'], $folders, $pagesStr, $tefxiStr);
      #		if ($folder_flag){
      #			printf('<div class="tefxi">%s:&nbsp;%s</div>',($obj_type == DB_OBJ_TYPE_WEBSITE)? 'σελίδες' : 'τεύχη' , coalesce($row['issue_cnt'],'1'));
      #		}
      echo('</td>');

      if ($edit_flag){
      echo('<td>');
//       Sreen Reader
//       if ($folder_flag){
//         echo('&nbsp;');
//       } else {
//         printf('<a class="dwlink" href="/archive/item/%s?lang=%s">%s</a>',  $row['item_id'], $lang, $download_img);
//       }
      if ($edit_flag){
        printf('<br/><A href="/prepo/edit_step2?i=%s">[E]</a>',$row['item_id']);
      }
      echo('</td>');
       }

      echo("</tr>\n");
    }


  }






  public static function item_list_thumbs($results,$d,$lang){



    printf('<div id="thl_%s">',$d);


    #####################################################################################
    ### TABLE BODY THUMBS
    #####################################################################################

    foreach($results as $row){
      $title =$row['title'];
      $item_id = $row['item_id'];
      $thumb1 = $row['thumb'];
      $thumb2 = $row['thumb1'];
      $thumb3 = $row['thumb2'];
      $obj_type = $row['obj_type'];


      $folder_flag = $row['folder'];


      $aggr_flag = $row['issue_aggr'];
      if ($d == 2){
        $thumb = $thumb1;
      } else if($d == 3){
        $thumb = $thumb2;
      } else {
        $thumb = $thumb3;
      }
      if (! empty($thumb)){
        $src = sprintf("/media/%s",$thumb);
      } else{
        if ($obj_type == "silogi"){
          if ($d == 4){
            $src = "/_assets/img/books4_200.png";
          } else {
            $src = "/_assets/img/books4_110.png";
          }
        } else{
          $src = "/_assets/img/pixel.gif";
        }
      }
      printf('<div class="item_thl">');
      printf('<a href="/archive/item/%s&lang=%s" title="%s"><img class="resimg" src="%s" alt="%s"/></a>',$item_id,$lang,$title, $src,$title);
      if ($folder_flag){
        if ($d == 4){
          printf('<img class="folderico" src="/_assets/img/items/folder48.png"/>');
        } else {
          printf('<img class="folderico" src="/_assets/img/items/folder24.png"/>');
        }
      }
      echo('</div>');
    }

    echo('<div class="spacer">&nbsp;</div>');
    echo('</div>');



  }



  public static function item_pages_preview($pages,$thumbs_s,$thumbs_b, $options = ARRAY()){

    printf('<div id="pagesthumbs" class="thumbsl1">');
    if (empty($pages)){
      $cc = 0;
      foreach ($thumbs_s as $k => $v){
        if($k > 0 && $cc <5){
          $cc ++;
          if (isset($thumbs_b[$k])){
            printf('<a class="group colorbox-load" rel="gal" href="/media/%s"><img src="/media/%s" /><br /></a>',$thumbs_b[$k], $thumbs_s[$k]);
          } else {
            printf('<a class="group colorbox-load" rel="gal" href="/media/%s"><img src="/media/%s" /><br /></a>',$thumbs_s[$k], $thumbs_s[$k]);
          }
        }
      }
    } else {
      if ( isset($thumbs_s[1])) {
        $bt = isset($thumbs_b[1]) ? $thumbs_b[1] : $thumbs_s[1];
        printf('<a class="group colorbox-load" rel="gal" href="/media/%s" title="%s %s"><img src="/media/%s" /><br />%s %s</a>',
        $bt,tr('Σελίδα 2 από'),$pages, $thumbs_s[1],tr('Σελίδα 2 από'),$pages);
      }
      if ( isset($thumbs_s[2])) {
        $bt = isset($thumbs_b[2]) ? $thumbs_b[2] : $thumbs_s[2];
        printf('<a class="group colorbox-load" rel="gal" href="/media/%s" title="%s %s"><img src="/media/%s" /><br />%s %s</a>',
        $bt,tr('Σελίδα 3 από'),$pages, $thumbs_s[2],tr('Σελίδα 3 από'), $pages);
      }
      if ( isset($thumbs_s[3]) ) {
        $bt = isset($thumbs_b[3]) ? $thumbs_b[3] : $thumbs_s[3];
        printf('<a class="group colorbox-load" rel="gal" href="/media/%s" title="%s %s"><img src="/media/%s" /><br />%s %s</a>',
        $bt,tr('Σελίδα 4 από'), $pages, $thumbs_s[3],tr('Σελίδα 4 από'), $pages);
      }
      if ( isset($thumbs_s[4])  ) {
        $bt = isset($thumbs_b[4]) ? $thumbs_b[4] : $thumbs_s[4];
        printf('<a class="group colorbox-load" rel="gal" href="/media/%s" title="%s %s"><img src="/media/%s" /><br />%s %s</a>',
        $bt,tr('Σελίδα 5 από'),$pages, $thumbs_s[4],tr('Σελίδα 5 από'),$pages);
      }
      if ( isset($thumbs_s['l']) ) {
        $bt = isset($thumbs_b['l']) ? $thumbs_b['l'] : $thumbs_s['l'];
        printf('<a class="group colorbox-load" rel="gal" href="/media/%s" title="%s"><img src="/media/%s" /><br /> %s</a>',
        $bt,tr('Τελευταία Σελίδα'),$thumbs_s['l'],tr('Τελευταία Σελίδα'));
      }
    }
    printf('<div class="clear">&nbsp;</div>');
    printf('</div>');

  }






}



?>



