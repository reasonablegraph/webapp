<?php


class PSnipets {


	#########################################################################
	###  PAGING SOLR
	#########################################################################

	public static function solr_paging_number( $numPages, $resultsPerPage ) {

		echo('<div class="pager">');
			for ($i = 0; $i < $numPages; $i++) {
				$start = $i * $resultsPerPage;
				$input_copy = Input::all();

				if(!empty($input_copy['start'])){
					$currentStart = $input_copy['start'];
					unset($input_copy['start']);
				}else{
					$currentStart = 0;
				}

				$url = http_build_query(array_merge($input_copy, array('start' => $start)));
				$num = $i + 1;

				if ($start != $currentStart) {
					printf('<a href="?%s">%s</a> | ',$url,$num);
				} else {
					printf('%s | ',$num);
				}
			}
		echo('</div>');

	}


	public static function solr_paging_text( $numPages, $resultsPerPage ) {

		if ($numPages == 1 || $numPages == 0){
			return;
		}

		$input_copy = Input::all();
		if(!empty($input_copy['start'])){
			$currentStart = $input_copy['start'];
			unset($input_copy['start']);
		}else{
			$currentStart = 0;
		}

		$pageNo = floor($currentStart/$resultsPerPage) + 1;
		$next_page_start = $currentStart + $resultsPerPage;
		$url_next_page = http_build_query(array_merge($input_copy, array('start' => $next_page_start)));
		$previous_page_start = $currentStart - $resultsPerPage;
		$url_previous_page = http_build_query(array_merge($input_copy, array('start' => $previous_page_start)));

		echo('<div class="pager">');
			if ($pageNo > 1){
				printf('<span><a href="?%s">&larr; %s</a></span>',$url_previous_page,tr('Previous page'));
			}else{
				echo(' <span class="disabled" aria-hidden="true" ><a href="#">&larr; ' .tr('Previous page') .'</a></span>');
			}

			if ($numPages > $pageNo){
				printf('<span class="currpage pager_l">%s %s %s %s</span><span class="pager_l" >
				<a href="?%s">%s &rarr;</a></span>',tr('page'),$pageNo,tr('from'),$numPages,$url_next_page,tr('Next page'));
			}else{
				printf(' <span class="currpage pager_l">%s %s %s %s</span> <span class="pager_l disabled" aria-hidden="true">
				<a href="#">%s &rarr;</a></span>',tr('page'),$pageNo,tr('from'),$numPages,tr('Next page'));
			}
		echo('</div>');

	}




	#####################################################################################
	###  PAGING BLOCK
	#####################################################################################

	//$pagingBlock = function() use($result,$m,$d,$paging_data,$sl) {
	public static function block_pagingBlock($results,$m,$paging_data) {

		$rc = count($results);
		$total_cnt = $paging_data['total_cnt'];
		$limit = $paging_data['limit'];
		$offset = $paging_data['offset'];
		$pageNo = floor($offset/Config::get('arc.PAGING_LIMIT')) + 1;
		$total_pages = ceil($total_cnt / Config::get('arc.PAGING_LIMIT'));


				if ($total_pages == 1 || $total_pages == 0){
		 			return;
		 		}


		echo('<div class="pager">');
// 		echo('<ul class="pager">');

		if ($m == 'a') {
			//ADVANCE SEARCH


			if ($rc > 0 &&  $paging_data['offset'] > 0){
				$u_offset = urlencode($paging_data['prev_offset']);
				// 			printf('<li><a href="/archive/search?tt=%s&m=a&c=%s&y=%s&p=%s&o=%s&l=%s&a=%s&d=%s&y1=%s&y2=%s&sl=%s">&larr; %s</a></li>'
				// 			,$u_term,$u_col,$u_year,$u_place,$u_offset,$u_title,$u_author,$d,$u_y1,$u_y2,$sl,tr('Προηγούμενη'));
				$new_url = Putil::replaceRelativeUrlGetParams(array('m'=>'a','o'=>$u_offset));
// 				printf('<li><a href="%s">&larr; %s</a></li>',$new_url,tr('Previous page'));
				printf('<span><a href="%s">&larr; %s</a></span>',$new_url,tr('Previous page'));

			}
			else {
				//echo('<span stype="font-color:black;">[' .tr('Προηγούμενη') .']</span>');
// 				echo(' <li class="disabled"><a href="#">&larr; ' .tr('Previous page') .'</a></li>');
				echo(' <span class="disabled"><a href="#">&larr; ' .tr('Previous page') .'</a></span>');
			}

			if ($rc  == $paging_data['limit']){
				$u_offset = urlencode($paging_data['next_offset']);
				//printf('<li class="currpage">%s/%s</li> <a href="/archive/search?tt=%s&m=a&c=%s&y=%s&p=%s&o=%s&l=%s&a=%s&d=%s&y1=%s&y2=%s&sl=%s">%s</a>',
				//$pageNo, $total_pages, $u_term,$u_col,$u_year,$u_place,$u_offset,$u_title,$u_author,$d,$u_y1,$u_y2,$sl,tr('Επόμενη'));
				$new_url = Putil::replaceRelativeUrlGetParams(array('m'=>'a','o'=>$u_offset));
// 				printf('<li class="currpage">%s/%s</li><li><a href="%s">%s &rarr;</a></li>',$pageNo, $total_pages,$new_url,tr('Next page'));
				printf('<span class="currpage pager_l">%s/%s</span><span><a href="%s">%s &rarr;</a></span>',$pageNo, $total_pages,$new_url,tr('Next page'));
			}
			else {
				//printf(' &#160;&#160;&#160; (%s/%s) &#160;&#160;&#160;<span style="color:black;">[%s]</span>',$pageNo, $total_pages,tr('Επόμενη'));
// 				printf('<li class="currpage">%s/%s</li> <li class="disabled"><a href="#">%s &rarr;</a></li>',$pageNo, $total_pages,tr('Next page'));
				printf('<span class="currpage pager_l">%s/%s</span> <span class="pager_l disabled"><a href="#">%s &rarr;</a></span>',$pageNo, $total_pages,tr('Next page'));

			}
		} else {
			//SIMPLE SEARCG
			if ($pageNo > 1){
				$u_offset = urlencode($paging_data['prev_offset']);
				//printf('<li><a href="/archive/search?m=s&t=%s&o=%s&d=%s&y1=%s&y2=%s&c=%s&sl=%s">&larr; %s</a></li> ',$u_term,$u_offset,$d,$u_y1,$u_y2,$u_col,$sl,tr('Προηγούμενη'));
				$new_url = Putil::replaceRelativeUrlGetParams(array('m'=>'s','o'=>$u_offset));
// 				printf('<li><a href="%s">&larr; %s</a></li>',$new_url,tr('Previous page'));
				printf('<span><a href="%s">&larr; %s</a></span>',$new_url,tr('Previous page'));
			}
			else {
				//echo('<span style="color:black;">[' .tr('Προηγούμενη') .']</span>');
// 				echo(' <li class="disabled"><a href="#">&larr; ' .tr('Previous page') .'</a></li>');
				echo(' <span class="disabled" aria-hidden="true" ><a href="#">&larr; ' .tr('Previous page') .'</a></span>');

			}

			#echo(" (total records $total_cnt) " );
			if ($pageNo<$total_pages){
				$u_offset = urlencode($paging_data['next_offset']);
				// 			printf('<li class="currpage">%s/%s</li> <li><a href="/archive/search?m=s&t=%s&o=%s&d=%s&y1=%s&y2=%s&c=%s&sl=%s">%s &rarr;</a></li>',
				// 			$pageNo, $total_pages,$u_term,$u_offset,$d,$u_y1,$u_y2,$u_col,$sl,tr('Επόμενη'));
				$new_url = Putil::replaceRelativeUrlGetParams(array('m'=>'s','o'=>$u_offset));
// 				printf('<li class="currpage">%s %s %s %s</li><li><a href="%s">%s &rarr;</a></li>',tr('page'),$pageNo,tr('from'),$total_pages,$new_url,tr('Next page'));
				printf('<span class="currpage pager_l">%s %s %s %s</span><span class="pager_l" ><a href="%s">%s &rarr;</a></span>',tr('page'),$pageNo,tr('from'),$total_pages,$new_url,tr('Next page'));

			}
			else {
				//printf(' &#160;&#160;&#160; (%s/%s) &#160;&#160;&#160;<span style="color:black;">[%s]</span>',$pageNo, $total_pages,tr('Επόμενη'));
// 				printf(' <li class="currpage">%s %s %s %s</li> <li class="disabled"><a href="#">%s &rarr;</a></li>',tr('page'),$pageNo,tr('from'),$total_pages,tr('Next page'));
				printf(' <span class="currpage pager_l">%s %s %s %s</span> <span class="pager_l disabled" aria-hidden="true"><a href="#">%s &rarr;</a></span>',tr('page'),$pageNo,tr('from'),$total_pages,tr('Next page'));
			}
		}

// 		echo("</ul>");
		echo('</div>');

	}




	#####################################################################################
	### COUNT APOTELESMATA KRITIRIA
	#####################################################################################
	public static function block_kritiria($total_cnt,$c,$m,$sss,$ss,$y,$p,$o,$l,$a,$d,$r,$y1,$y2,$sl,$lang,$counters,$display_lang_select_flag,$ot) {


		//  echo('<div class="row res-infobar">');
		//echo('<p class="rescnt">');

		if ($total_cnt == 0){
		printf("%s.",tr('No entries found'));
		//return;
		} elseif ($total_cnt == 1){
		printf('%s <strong>%s</strong> %s: ',trChoise('Found',$total_cnt),$total_cnt, tr('entry'));
		} else {
		printf('%s <strong>%s</strong> %s: ',trChoise('Found',$total_cnt),$total_cnt, tr('entries'));
			if (! empty($counters)){
				$sep = '';
			foreach ($counters as $count_obj_type => $count){
			echo ($sep); $sep = ', ';
			printf(' %s <a href="%s">%s</a>',$count, Putil::replaceRelativeUrlGetParams(array('ot'=>$count_obj_type)),  trChoise($count_obj_type . 's',$count));
					///$count_obj_type
			}


			}
			}


			if ($display_lang_select_flag){
			if (!empty($lang)){
			echo("&nbsp; ($lang)");
			}
	}

			#echo(" &nbsp; &nbsp; ");
			#if ($r == 1){
			#	echo(" <b>Ταξινόμηση:</b> ");
			#	echo("Ημερομ. Αρχειοθ.");
			#} elseif ($r == 2){
			#	echo(" <b>Ταξινόμηση:</b> ");
			#		echo("Τίτλος");
			#} elseif ($r == 3){
			#	echo(" <b>Ταξινόμηση:</b> ");
			#		echo("Έτος desc");
			#} elseif ($r == 4){
			#	echo(" <b>Ταξινόμηση:</b> ");
			#		echo("Έτος asc");
			#}


			echo("<span aria-hidden=\"true\">");
			echo(" &nbsp; &nbsp;");
			$criteria = "<span class=\"glyphicon glyphicon-filter\" aria-hidden=\"true\"></span> <strong> " . tr('Κριτήρια') .": </strong> ";
					$coma = '';

					if (!empty($ss)){
			echo($criteria); $criteria = '';
				echo ($coma);
				//echo(" $ss");


			printf('<a href="%s">%s<span class="glyphicon glyphicon-remove" aria-hidden="true" style="position:relative;top:3px; left: 1px;"></span> </a>',PUtil::replaceRelativeUrlGetParams(array('t'=>null)),tr($ss));
			// 		printf('<a href="%s">%s<img src="/_assets/img/remove-icon.png"/></a>',PUtil::replaceRelativeUrlGetParams(array('t'=>null)),tr($ss));
			//printf( ' Όρος: %s ' ,$ss);
			$coma = ' ';
			}
			if (!empty($sss)){
			echo($criteria); $criteria = '';
			echo ($coma);
			echo(" $sss");
			//printf(' Όρος: %s ' ,$sss);
			$coma = ',';
			}

			if (!empty($c)){
			$cn = get_menu_name($c);
			if (!empty($cn)){
				echo($criteria); $criteria = '';
				echo ($coma);
					echo(" " . tr($cn));
					$coma = ',';
				}
				}

				if (!empty($ot)){
				echo($criteria); $criteria = '';
				echo ($coma);
			printf('<a href="%s">%s<span class="glyphicon glyphicon-remove" aria-hidden="true" style="position:relative;top:3px; left: 1px;"></a>',PUtil::replaceRelativeUrlGetParams(array('ot'=>null)),tr($ot));
			$coma = ' ';
				}

				if (!empty($l)){
				echo($criteria); $criteria = '';
				echo ($coma);
				echo(" $l");
				//printf(' Τίτλος: %s ' ,$l);
				$coma = ',';
			}

			if (!empty($p)){
			echo($criteria); $criteria = '';
			echo ($coma);
			echo(" $p");
			//	printf(' Τόπος: %s ' ,$p);
			$coma = ',';
			}

			if (!empty($y)){
			echo($criteria); $criteria = '';
			echo ($coma);
			echo(" $y");
			//printf(' Έτος: %s ' ,$y);
			$coma = ',';
			}
			if (!empty($y1) || ! empty($y2)){
			echo($criteria); $criteria = '';
			if($y1 == -11){
			echo ($coma);
			echo(tr(' Άγνωστη Ημ/νία'));
			} else {
			if (empty($y1)){ $y1='...';};
			if (empty($y2)){ $y2='...';};
			echo ($coma);
			echo(" $y1-$y2");
			}
			$coma = ',';
			}


				if (!empty($a)){
				echo($criteria); $criteria = '';
				echo ($coma);
				echo(" $a ");
				//printf(' Συγγραφέας: %s ' ,$a);
				$coma = ',';
				}


				//echo('</p>');
				//  echo('</div>');
				echo("</span>");
	}














    public static function createAtributesString($attributesMap){
        $attributes = '';
        foreach ($attributesMap as $k=>$v){
            if (!PUtil::isEmpty($v)){
                $attributes .= sprintf('%s="%s" ',$k,$v);
            }
        }
        return $attributes;
    }


    public static function createElementString($elementName, $attributesMap){
        $attributes = PSnipets::createAtributesString($attributesMap);
        return sprintf('<%s %s>',$elementName,$attributes);
    }

	/**
	 *
	 * @param unknown $item_id
	 * @param ItemMetadata $idata
	 */
	public static function print_admin_item_metadata($item_id, $idata, $edit_link = true) {
		$item_load_flag = !empty($item_id);

		$functor = function ($kk, $vv) {
			return $vv;
		};

		// $functor = function($k,$v) {

		// if ($k == DataFields::ea_description_abstract
		// || $k == DataFields::dc_descrption
		// || $k == DataFields::ea_status_comment
		// || $k == DataFields::ea_oring_comment
		// ){
		// $v = str_replace("\n",'<br/>',$v);
		// }
		// return $v;
		// };

		// * 0: text value
		// * 1: lang
		// * 2: database id: dsd.metadatavalue2(metadata_value_id)
		// * 3: relation
		// * 4: ref_item
		// * 5: json_data
		// * 6: record_id
		// * 7: weight
		// * 8: link (pointer) diktis st parent record_id
		// * 9: inferred
		// * 10: level

		//echo ("<pre>");


// 		$graph = new GGraphO();
// 		GGraphIO::addItemToGraph($graph, $item_id);
// 		$v =  $graph->getVertex(GURN::createOLDWithId($vertexId));
// 		//$v->getTreeProperty($treeId);
// 		$v->getAllProperties();

// 		$link_names = array ();
// 		$links = array ();
		$roots = array();
		$parentship =  array();
		$values = array ();
		$empty_values = array();
		$rid_c = 0;
		foreach ( $idata->values as $key => $value ) {
			// echo("$k\n");
			// print_r($v);
			if (! empty ( $value )) {
				foreach ( $value as $k => $v ) {
					$rid = $v [6];
					if (empty($rid)){
						$rid =  '#'.$rid_c;
						$rid_c += 1;
						$v ['k'] = $key;
						$empty_values[$rid] = $v;
						$values [$rid] = $v;
					} else {
					$l = $v [8];
					$v ['k'] = $key;
					$values [$rid] = $v;
					if (empty($l)) {
						$roots[$rid] = $v;
					} else {
						if (!isset($parentship[$l])){
							$parentship[$l] = array();
						}
						$parentship[$l][] =$rid;
					}
				}
				}
			}
		}

		//echo ("</pre>");

		//ksort ( $values );
		ksort ( $roots );

		$c=0;

		$print_line = function($item_id,$k,$v,$functor,$item_load_flag,$level,$edit_link) use (&$c) {
			$c+=1;
			$cid = $v [6];
			$grp = $v [8];
			// $val = empty($v[6]) ? $v[0] : $v[6];
			$val = $v [0];
// 			$val = htmlspecialchars ( $val );
			$data = $v[5];
			$srlzn = 'text';
			if (!empty($data)){
				$dataArray =$data;
				if (!empty($dataArray) && !empty($dataArray['data']) && !empty($dataArray['data']['srlzn'])){
					$srlzn = $dataArray['data']['srlzn'];
				}
			}
			if ($srlzn == 'json'){
					$val = htmlspecialchars(json_encode(json_decode($val),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
			}
			$key = htmlspecialchars ( $v ['k'] );
			$pad = '';
			if ($level > 1){
				for($i=0;$i<$level-1;$i++){
					$pad .= '&nbsp;&nbsp;';
				}
				$pad .= '↳' .$cid;
			} else {
				$pad = $cid;
			}
			echo ("<tr>");
			//echo ("<td>$pad $k</td><td>$cid</td><td>$grp</td><td>$key</td>");
			printf("<td>%s</td><td>%s</td><td>%s</td>",$c, $pad,$key);
			//printf("<td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td>",$c,$cid, $pad,$grp,$key);
			if (empty ( $v [4] )) {
				printf ( "<td class='item_metadata'>%s</td>", $functor ( $key, $val ) );
			} else {
				printf ( '<td class="item_metadata"><a href="/archive/item/%s">%s</a>', $v [4], $functor ( $key, $val ) );
				if (! empty ( $v [3] )) {
					printf ( " &nbsp; (rel: %s)", $v [3] );
				}
			}
			if ($item_load_flag){
				echo ("<td>");
				echo($v[9] ? '⊦':'');
				echo ("</td>");
				echo ("<td>");
				echo ($v [2]);
				echo ("</td>");
				if ($edit_link){
					printf ( '<td><a href="/prepo/edit_metadata?itid=%s&id=%s"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span><span class="sr-only">' . tr ( 'Edit' ) . '</span></a></td>', $item_id, $v [2] );
				}
			}
			echo ("</tr>");
		};

		$fnt = function($level,$k,$f) use ($parentship,$values,$print_line,$functor,$item_load_flag,$item_id,$edit_link){
			$level = $level + 1;
			$v = $values[$k];
			$print_line($item_id,$k,$v,$functor,$item_load_flag,$level,$edit_link);
			while (isset($parentship[$k])){
				$keys = $parentship[$k];
				unset($parentship[$k]);
				foreach ($keys as $nk){
					$f($level,$nk,$f);
				}
			}
		};


		echo ('<table class="table table-bordered table-condensed table-striped">');
		echo ('<thead class="a_thead"><tr><th colspan="7"><span class="a_shead">Metadata</span></th></tr></thead>');

		foreach ( $roots as $k=>$v ) {
			$level = 0;
			$fnt($level,$k,$fnt);
		}
		foreach ( $empty_values as $k=>$v ) {
			$print_line($item_id,$k,$v,$functor,$item_load_flag,0,$edit_link);
		}

		echo ("</table>\n");
	}




    /**
     *
     * @param boolean $item_load_flag
     * @param ItemMetadataIterator $it
     */
    public static function print_item_metadata_iterator($item_id, $it, $functor=null){
        if (empty($functor)){
            $functor = function($kk,$vv){
                return $vv;
            };
        }

        $item_load_flag = false;
        if (!empty($item_id)){
            $item_load_flag = true;
        }
        echo('<table class="table table-bordered table-condensed table-striped">');
        $c = 0;
        foreach($it as $key => $value) {
            $cc = 0;
            if (! empty($value)){
                foreach ($value as $k => $v){
                    //echo("<pre>");print_r($v);echo("</pre>");
                    $cid  = $v[6];
                    $grp = $v[8];
                    $c++; $cc++;
                    if ($cc > 1){
                        $okc = $cc;
                    } else {
                        $okc = "";
                    }
                    //$val = empty($v[6]) ? $v[0] : $v[6];
                    $val = $v[0];
                    if (! empty($val)){
                        $val = htmlspecialchars($val);
                        $key = htmlspecialchars($key);
                        echo("<tr>");
                        echo("<td>$c</td><td>$okc</td><td>$cid</td><td>$grp</td><td>$key</td>");
                        if (empty($v[4])){
                            printf("<td>%s</td>",$functor($key,$val));
                        } else {
                            printf('<td><a href="/archive/item/%s">%s</a>',$v[4],$functor($key,$val));
                            if (! empty($v[3])){
                                printf(" &nbsp; (rel: %s)",$v[3]);
                            }

                        }

                        if($item_load_flag){
                            echo ("<td>");
                            echo ($v[2]);
                            echo("</td>");
                            printf('<td><a href="/prepo/edit_metadata?itid=%s&id=%s"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span><span class="sr-only">'. tr('Edit'). '</span></a></td>',$item_id,$v[2]);
                        }
                        echo("</tr>");
                        if($key == DataFields::ea_status){
                            $collspan= $item_load_flag ? 6 : 4;
                            printf('<tr><td colspan="%s"> &nbsp; </td></tr>',$collspan);
                        }
                    }


                }
            }

        }
        echo("</table>\n");

    }

    public static function item_admin_bar($item){

        $item_id = $item['id'];
        $ref_bitstream = $item['ref_bitstream'];
        $ref_content = $item['ref_content'];

        //lock edit form submitter
        $user_create = $item['user_create'];
        $user = ArcApp::username();
        $is_admin = ArcApp::user_access_admin();
        $edit_lock_owner = Config::get('arc.owner_edit_form_lock',0);
        $edit_link = true;
        if ( $edit_lock_owner && $user_create!= $user && !$is_admin){
        	$edit_link = false;
        }

        echo('<hr />');
        echo('<div class="row">');
        echo('<ul id="admin_area" class="nav nav-pills">');
            printf('<li><a href="/prepo/edit_step3?i=%s"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> '. tr('Admin'). '</a></li>',$item_id);

            if ($edit_link){
            printf('<li><a href="/prepo/edit_step1?i=%s"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span> '. tr('Edit'). '</a></li>',$item_id);
            }
            if ($edit_link && !empty($ref_bitstream)){
                printf('<li><a href="/prepo/edit_bitstream?bid=%s"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span> '. tr('Edit Bitstream'). '</a></li>',$ref_bitstream);
            }
            $print_flag =  variable_get('arc_display_artifacts', 0);
            if ($print_flag):
                printf('<li><a href="/prepo/artifacts?i=%s"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span> '. tr('Artifacts'). '</a></li>',$item_id);
            endif;
            if (! empty($ref_content)){
                printf('<li><a href="/prepo/edit_content?cid=%s"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span> '. tr('Edit Content'). '</a></li>',$ref_content);
            }
            $print_flag =  variable_get('arc_display_notes', 0);
            if ($print_flag):
                printf('<li><a id="new_note" href="#"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> '. tr('Create note'). '</a></li>');
            endif;

            $print_flag =  variable_get('arc_display_export', 0);
            if ($print_flag):
            printf('<li><a href="/prepo/export_item?i=%s"><span class="glyphicon glyphicon-export" aria-hidden="true"></span> '. tr('Export'). '</a></li>',$item_id);
            endif;


            printf('<li class="pull-right disabled"><a href="#">status: %s</a></li>',tr($item['status']));
           echo('</ul>');
        echo("</div>");


        $javascript= "
        <script>
        jQuery('#new_note').click(function(e){
        var form = document.createElement('form');
        form.setAttribute('method', 'post');
        form.setAttribute('action', '/prepo/contents');

        var hiddenField1 = document.createElement('input');
        hiddenField1.setAttribute('type', 'idden');
        hiddenField1.setAttribute('name', 'ADD_note');
        hiddenField1.setAttribute('value','ADD_note');
        form.appendChild(hiddenField1);

        var hiddenField2 = document.createElement('input');
        hiddenField2.setAttribute('type', 'hidden');
        hiddenField2.setAttribute('name', 'item_id');
        hiddenField2.setAttribute('value',$item_id);
        form.appendChild(hiddenField2);

        document.body.appendChild(form);
        form.submit();
        });
        </script>
        ";
        echo($javascript);

    }
    /**
     *
     * @param ItemMetadata $idata
     * @return multitype:number
     */
    public static function print_mesages($idata,$extra_errors = null){
        $errors = $idata->getErrors();
        if (!empty($extra_errors)){
            $errors = array_merge($errors, $extra_errors);
        }
        $warnings = $idata->getWarnings();
        $infos = $idata->getInfos();
        if (isset($_SESSION['info_messages'])){
            $session_infos = $_SESSION['info_messages'];
            $infos = array_merge($infos,$session_infos);
            unset($_SESSION['info_messages']);
        }

        if (isset($_SESSION['warn_messages'])){
            $session_warns = $_SESSION['warn_messages'];
            $warnings = array_merge($warnings,$session_warns);
            unset($_SESSION['warn_messages']);
        }

                $ses_warnings = $warnings;
        if (isset($_SESSION['printed_warning_messages'])){
            $session_printed_warnings = $_SESSION['printed_warning_messages'];
            foreach ($session_printed_warnings as $del_val) {
                if(($key = array_search($del_val, $warnings)) !== false) {
                unset($warnings[$key]);
                }
            }
            $ses_warnings = array_merge($warnings,$session_printed_warnings);
        }
        $_SESSION['printed_warning_messages'] = $ses_warnings;

        $err_counter = count($errors);
        $warn_counter = count($warnings);
        $infos_counter = count($infos) ;
        $msg_counter = $err_counter + $warn_counter + $infos_counter;
        if ($msg_counter > 0) {
            echo('<div class="fmessages">');
            if ($err_counter  > 0){
                echo('<ul style="color:red">');
                    echo("<pre>");
                    print_r($errors);
                    echo("</pre>");
//              foreach ($errors as $e){
//                  echo ('<li><span>Error: </span>' . htmlspecialchars($e) . "</li>\n");
//              }
                echo("</ul>");
            }
            if ($warn_counter  > 0){
                echo('<ul style="color:#FF00FF">');
                foreach ($warnings as $e){
                    echo ("<li><span>Warning: </span>" . htmlspecialchars($e) . "</li>\n");
                }
                echo("</ul>");
            }
            if ($infos_counter  > 0){
                echo('<ul>');
                foreach ($infos as $e){
                    echo ("<li><span>Info: </span>" . htmlspecialchars($e) . "</li>\n");
                }
                echo("</ul>");
            }
            echo('</div>');
        }
        $rep =ARRAY($err_counter,$warn_counter,$infos_counter, $msg_counter);
        return $rep;
    }


public static function item_property_line($idata, $key,$label){
    $vals =$idata->getItemValues($key);
    $cnt = count($vals);
    if ($cnt>0){
        echo('<div class="clear">&#160;</div>');
        printf('<span class="contribs_label">%s</span>: &#160; ',$label);
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
    }


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


    public static function artifacts($item_id, $artifacts){

        if (count($artifacts ) == 0 ){
            return;
        }

        echo('<table id="artifacts">');
        printf('<thead><tr><th class="inf1">%s</th></tr></thead>',tr('Library items')); #tr('Διαθέσιμα τεκμήρια στην Βιβλιοθήκη')
        foreach($artifacts as $artifact){
            printf('<tr><td>call number: %s</td></tr>',$artifact['call_number']);
        }
        echo('</table>');
 }

    public static function bitstream_downlads($item_id, $bitstreams, $articles, $options = array()){

        if (empty($articles)){
            $articles = array();
        }

        if (empty($bitstreams)){
            $bitstreams = array();
        }

        if (count($bitstreams ) > 0  || count($articles) > 0) {
            echo('<table class="table table-bordered table-striped" id="downloads">');
            printf('<thead><tr><th class="inf1" colspan="2">%s</th><th class="inf2">Size</th><th class="inf2">Download</th></tr></thead>',tr('Διαθέσιμα τεκμήρια'));

            //$item_id = $rep['id'];

            foreach ($articles as $article){
                //print_r($article);
                $p = null;
                if (empty($article['node_path'])){
                    $p = "/node/" . $article['drupal_node'];
                } else {
                    $p = "/" . $article['node_path'];
                }
//                  $np = $article['node_path'];
//                  //if ($article['content_type'] == DataFields::DB_content_ctype_article){
//                  $p = "/content/" . $np;
//              }

//              $p = "/node/" . $article['drupal_node'];

                $url1 = "$p";
                $url2 = sprintf("/archive/download_article/%s",$article['content_id']);
                echo("<tr>");
                printf('<td  class="bthumb"><a href="%s"><img src="/_assets/img/items/text.png"/></a></td>',$url2);
                printf('<td style="width:90%%"><a href="%s">%s</a><br/>%s</td>',$url2, $article['title'],$article['bitstream_desc']);
                printf('<td>%s</td>', PUtil::formatSizeBytes($article['size_bytes']));
                echo('</td><td class="inf2">');
                printf('<a href="%s"><span class="glyphicon glyphicon-download" aria-hidden="true"></span><span class="sr-only">'. tr('Download Item'). '</span></a>',$url2);
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
                if (!empty($tfile)){
                    if ($image_flag){
                        printf('<a class="colorbox-load" rel="images" href="%s"><img src="/media/%s"/></a>',$url3,$tfile);
                    }
//                  elseif ($ddoc_flag){
//                      printf('<a href="%s"><img src="/media/%s"/></a>',$url2,$tfile);
//                  }
                    else {
                        printf('<a href="%s"><img src="/media/%s"/></a>',$url,$tfile);
                    }
                } else {
                    printf('<a href="%s"><img src="/_assets/img/items/document.png"/></a>',$url);
                }
                echo('</td>');
                echo('<td class="inf1" style="vertical-align:top;">');
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
                    //  if (user_access_admin()){
                        if($v['symlink']){
                        printf(' <a href="/prepo/edit_bitstream_symlink?sid=%s"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span><span class="sr-only">'. tr('Edit Sym'). '</span></a> ',$v['symlink_id']);
                    } else {
                            printf(' <a href="/prepo/edit_bitstream?bid=%s"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span><span class="sr-only">'. tr('Edit bit'). '</span></a> ',$bitstream_id);
                    }
                //}
                                        echo('</td><td class="inf2" style="text-align:right;">');
                    printf('%s', $fsize);
                                                echo('</td>');
                                                echo('</td><td class="inf2">');
                    printf('<a href="%s" class="item-dwlink"><span class="glyphicon glyphicon-download" aria-hidden="true"></span><span class="sr-only">'. tr('Download Item'). '</span></a>',$url1);
                    echo("</td>");

                            echo("</tr>");
            }
            echo('</table>');
            }

    }




    public static function artifacts_table($item_id, $table_flag = true){

    //  $STATUS_MAP = Lookup::get_artifact_status_values();

        $dbh = dbconnect();
        $SQL=
        "SELECT
        a.id,
        a.uuid,
        a.item_id,
        a.sn,
        a.sn_pref,
        a.sn_suff,
        a.call_number,
        a.call_number_pref,
        a.call_number_sn,
        a.call_number_suff,
        a.status,
        a.data,
        a.create_dt,
        a.item_impl
    FROM dsd.item2 i
    JOIN dsd.artifacts a ON a.item_id = i.item_id
    WHERE i.item_id = ? ORDER BY  a.sn_pref,a.sn_suff";

        $stmt = $dbh->prepare($SQL);
        $stmt->bindParam(1, $item_id);
        $stmt->execute();
        $r = $stmt->fetchAll();
        if (count($r) > 0){
            if ($table_flag){
                echo('<table>');
                echo('<tr>');
                echo('<th>id</th>');
                echo('<th>call_number</th>');
                echo('<th>serial number</th>');
                echo('<th>status</th>');
                echo('<th>create</th>');
                echo('<th>actions</th>');
                echo('</tr>');
            }
            foreach($r as $k => $v){
                $id = $v['id'];
                $sn = $v['sn'];
                $call_number = $v['call_number'];
                $ref_item = $v['item_impl'];
                echo('<tr style="vertical-align:top;">');
                printf('<td>%s</td>',$id);
                printf('<td>%s</td>',$call_number);
                printf('<td>%s</td>',$sn);
                //printf('<td>%s</td>',$STATUS_MAP[$v['status']]);
                printf('<td>%s</td>',$v['status']);
                printf('<td>%s</td>',$v['create_dt']);
                echo("<td>");
                //printf('<a href="/prepo/edit_artifact?id=%s">[edit]</a> ',$id,$id);

                printf('<a href="/prepo/edit_step1?i=%s">[edit]</a>',$ref_item);
                echo(PConstants::NBSP);
                echo(PConstants::NBSP);
                echo(PConstants::NBSP);
                echo(PConstants::NBSP);
                printf('<a onClick="return confirm(\'Are you sure you want to delete this?\')" href="/prepo/delete_item?i=%s">[delete]</a>',$ref_item);

                                //printf('<a href="/prepo/view_artifact?id=%s">[view]</a>',$id,$id);
            //  printf('<a href="/prepo/edit_step2?i=%s">[view]</a>',$ref_item);

                echo("</td>");
                echo("</tr>\n");
            }

            if ($table_flag){
                echo("<table>");
            }

        }


    }





    public static function contents_table($item_id){

        $CONTENT_TYPE_MAP = Lookup::get_content_type_values();
        $VISIBILITY_MAP = Lookup::get_visibility_values();
        //$BUNDLE_MAP = Lookup::get_content_bundles();

        $dbh = dbconnect();
        $SQL=sprintf(
        "SELECT item_id, content_id, weight, title, description, publish_dt,create_dt, visibility, bb_create_dt, bb_weight, bundle_name, content_type, item, symlink, bb_id, fweight
        FROM dsd.item_content_all
        WHERE item_id = ?  AND visibility <> %s order by fweight", DataFields::DB_visibility_deleted);
        $stmt = $dbh->prepare($SQL);
        $stmt->bindParam(1, $item_id);
        $stmt->execute();
        $r = $stmt->fetchAll();
        if (count($r) > 0){
            echo('<table>');
            printf('<tr><th colspan="9">content</th></tr>');
            printf("<tr><th>id</th> <th>weight</th> <th>desc</th> <th>date</th> <th>visibility</th> <th>bundle</th> <th>ctype</th><th>sym</th><th></th></tr>");
            foreach($r as $k => $v){
                $symlink_flag = $v['symlink'];
                echo('<tr style="vertical-align:top;">');
                printf('<td>%s</td>',$v['content_id']);
                //printf('<td><a href="/prepo/edit_step2?i=%s">%s</a></td>',$v['item'],$v['item']);
                printf('<td>%s</td>', $v['fweight']);
                printf('<td>%s</td>', $v['description']);
                printf('<td>%s</td>', $v['publish_dt']);
                printf('<td>%s</td>', $VISIBILITY_MAP[$v['visibility']]);
                printf('<td>%s</td>', $v['bundle_name']);
                printf('<td>%s</td>', $CONTENT_TYPE_MAP[$v['content_type']]);
                printf('<td>%s</td>', $symlink_flag ? 'Y':'');

                echo('<td style="text-align:right;">');
                $url = null;
                if ($symlink_flag){
                    $url = sprintf("/prepo/edit_content_symlink?sid=%s",$v['bb_id']);
                } else {
                    $url = sprintf("/prepo/edit_content?cid=%s",$v['content_id']);
                }
                printf('<a href="%s">[edit]</a>',$url);
                echo('</td>');
                echo("</tr>\n");
            }
            echo("<table>");
        }
    }





    public static function display_twitts($s,$tweet_offset,$limit = 80){
        if (PUtil::strBeginsWith($s, '#')){
            $ht = substr($s,1);
        } else {
            $ht = $s;
            $s = '#' . $s;
        }
        $dbh = dbconnect();
        $lang = get_lang();

        // /*img.ut { float: left; padding-left: 1px; padding-bottom 1px; padding-top: 2px; padding-right: 4px;}*/
        echo '
           <style>
             table.tweets tr td { border:0px; border-bottom:1px solid gray; padding:4px;  vertical-align: middle; }
             span.dt {font-size: 10px; float:right;}
           </style>
        ';

        echo('<table class="tweets">');
        echo('<tr><th colspan="2">tweets</th></tr>');
        $SQL="SELECT t.user_thumb_url, t.user_name, t.user_id, t.text, t.retweets, t.favorites, t.lang,
                    t.tweet_dt, t.avatar, b.artifact_id as bitstream_id, b.file_ext, th.file, t.retweet
          FROM dsd.tweet t
          JOIN dsd.tweet_hashtag h ON (h.tweet = t.t_id)
                    LEFT JOIN public.tweet2bitstream  d ON (d.tweet = t.t_id)
                    LEFT JOIN public.bitstream b ON (b.bitstream_id = d.bitstream_id)
                    LEFT JOIN dsd.thumbs th ON (th.item_id = b.item and th.ttype=4)
          WHERE t.status = 'finish'  AND h.hashtag = ?  ORDER  BY t.tweet_dt desc limit " . $limit  . " offset  ?";

        $stmt = $dbh->prepare($SQL);
        $stmt->bindParam(1, $ht);
        $stmt->bindParam(2, $tweet_offset);

        $stmt->execute();
        $tc = 0;
        while ($t = $stmt->fetch()){
            $tc+=1;
            echo("<tr>");
            echo("<td>");

            $aurl =  '/avatar/' . $t['avatar'];


            printf('<img class="ut" src="%s"/></td><td>',$aurl);
            $retweet_msg = '';
            if ($t['retweet'] == 1){
                $retweet_msg = '(retweeted)';
                printf("%s<br/>", $retweet_msg);
            }
            //printf("%s %s<br/>", $t['user_name'],$retweet_msg);
            printf("%s <br/>",$t['text']);
            if (!empty($t['file'])){
                printf('<a href="/archive/item/%s/%s.%s"><img src="/media/%s"/></a> ',Config::get('arc.TWEETS_ITEM'), $t['bitstream_id'],$t['file_ext'],$t['file']);

            } elseif  (!empty($t['bitstream_id'])){
                printf('<a href="/archive/item/%s/%s.%s">[image]</a> ',Config::get('arc.TWEETS_ITEM'), $t['bitstream_id'],$t['file_ext']);
            }
            printf('<span class="dt">%s</span>',$t['tweet_dt']);

            echo("</td>");
            echo("</tr>\n");

            //          echo("<pre>");
            //          print_r($t);
            //          echo("</pre>");

        }

        echo('</table>');

        return $tc;
    }


    public static function item_list($result, $obj_type_names, $edit_flag = false, $list_edit_flag = false, $small_img_flag = false){

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
        $download =  '<img  title="available for download" alt="available for download" src="/_assets/img/download.png"/>';

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
            #       $folder_flag = true;
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
            printf('<a href="/archive/item/%s?lang=%s"><img class="mimeico %s" src="/_assets/img/items/folder.png"/></a>',$row[5],$lang, $img_class2);
        } else if ($obj_type == Config::get('arc.DB_OBJ_TYPE_WEBSITE_INSTANCE')){
            printf('<a href="/archive/item/%s?lang=%s"><img class="mimeico %s" src="/_assets/img/items/text-html.png"/></a>',$row[5],$lang, $img_class2);
        } else if ($obj_type == Config::get('DB_OBJ_TYPE_PERSON')){
            printf('<a href="/archive/item/%s?lang=%s"><img class="mimeico %s" src="/_assets/img/items/user.png"/></a>',$row[5],$lang, $img_class2);
        } else {
            printf('<a href="/archive/item/%s?lang=%s"><img class="mimeico %s" src="/_assets/img/items/document.png"/></a>',$row[5],$lang, $img_class2);
        }
        echo('<br/>');

        echo(tr($obj_type_names[$obj_type]));
        if ($folder_flag){
            $txt = ($obj_type == Config::get('DB_OBJ_TYPE_WEBSITE'))? tr('σελίδες') : ($obj_type == Config::get('DB_OBJ_TYPE_SILOGI')) ? tr('τεκμήρια'): tr('τεύχη') ;
            printf('<br/>%s:&nbsp;%s',$txt , PUtil::coalesce($row['issue_cnt'],'1'));
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
            $txt = ($obj_type == Config::get('DB_OBJ_TYPE_WEBSITE'))? tr('σελίδες') : ($obj_type == Config::get('DB_OBJ_TYPE_SILOGI')) ? tr('τεκμήρια'): tr('τεύχη') ;
            $tefxiStr = sprintf('<br/>%s:%s',$txt , PUtil::coalesce($row['issue_cnt'],'1'));
        } else {
            $tefxiStr= "";
        }
        if ($edit_flag){
            $tefxiStr .= sprintf('<br>id: %s    &#160;  &#160; status: <a href="/archive/recent?s=%s">%s</a>  ',$row['item_id'],$row['status'],$row['status']);
            if (! empty($row['user_create'])){
                $tefxiStr .= sprintf('&#160; &#160;  create: %s',$row['user_create']);
            }
            if (! empty($row['user_update'])){
                $tefxiStr .= sprintf('&#160; &#160;  update: %s',$row['user_update']);
            }

            $dt = PUtil::coalesce($row['dt_update'], $row['dt_create']);

            $phpdate = strtotime( $dt );
            $tefxiStr .= sprintf('&#160; &#160; %s',date('d/m/Y',strtotime( $dt )));


        }

        echo('<td>');
        if (! empty($thumb)){
            printf(' <a href="/archive/item/%s?lang=%s" title="%s"><img class="%s" src="/media/%s" alt="%s"/></a>',$row[5],$lang,htmlspecialchars($row[1]), $img_class1, $thumb, htmlspecialchars($row[1]));
        } else {
            if ($obj_type == 'silogi'){
                printf('<a href="/archive/item/%s?lang=%s" title="%s"><img class="%s" src="/_assets/img/books4_64.png" alt="%s"/></a>',$row[5],$lang,htmlspecialchars($row[1]), $img_class1, htmlspecialchars($row[1]));
            } else {
                printf('<img class="resimg" src="/_assets/img/pixel.gif"/>');
                //printf('<a href="/archive/item/%s?lang=%s" title="%s"><img class="resimg" src="/_assets/img/pixel.gif" alt="%s"/></a>',$row[5],$lang,$row[1], $row[1]);
            }
        }
        echo('</td>');
        printf('<td style="width:100%%">');
        printf('<a href="/archive/item/%s?lang=%s">%s</a><br/> %s %s %s %s %s',$row[5], $lang, htmlspecialchars($row['title']),$row[3],$row[2], $folders, $pagesStr, $tefxiStr);
        #       if ($folder_flag){
        #           printf('<div class="tefxi">%s:&nbsp;%s</div>',($obj_type == DB_OBJ_TYPE_WEBSITE)? 'σελίδες' : 'τεύχη' , PUtil::coalesce($row['issue_cnt'],'1'));
        #       }
        echo('</td>');
        echo('<td>');
        if ($folder_flag){
            echo('&nbsp;');
        } else {
            printf('<a href="/archive/item/%s?lang=%s">%s</a>',  $row[5], $lang, $download_img);
        }
        if ($edit_flag){
            printf('<br/><A href="/prepo/edit_step2?i=%s">[E]</a>',$row['item_id']);
        }
        echo('</td>');
        echo("</tr>\n");
        }


    }








}


class FormSnipets {
    /**
     *
     *
     *
     * @param unknown $key
     * @param ItemMetadata $idata
     * @param unknown $options
     * @return number
     *
     * options:
     *   div_id
     *   label :    ex:  title:uniform:
     *   show_help: boolean   (default: false)
     *   add_button: boolean  (default: false)
     *   add_button_id:    ex: add_title_uniform  (OPTIONAL)
     *   add_button_label: ex: add_title  (default: add)
     *   add_button_first : topo8eti prota to koumpi default false
     *   size: ex: 80  (default:80)
     *   autocompete_url: url for autocomplete (OPTIONAL);
     *   skip_on_empty:  default false
     *   select_key_map:  map me klidia px authors (OPTIONAL);
     *   print_label: default true
     *   input_br: default true
     *   autocomplete_fn: javscript function pou 8a energopiisi to autocomlete (OPTIONAL)
     *   append_group: append @group_no (optional)
     *
     */
    public static function displayField($key, $idata, $options){

            //list($pk,$pg) = ItemMetadata::splitKey($key);
            //echo("<pre>KEY: $key PK $pk ## PG: $pg #</pre>");

            $get_uid = function($prefix=null){
                $prev = isset($_REQUEST['auto_uid'])? $_REQUEST['auto_uid'] : 1;
                $next = $prev + 1;
                $_REQUEST['auto_uid'] =$next;
                $rep = empty($prefix)? 'rid_' : $prefix . '_';
                $rep .= $next;
                return $rep;
            };


            $get_label = function($label,$group){
                if (!empty($group)){
                    $label = $label . " ($group)";
                }
                return $label;
            };

            $get_group = function(){
                $pgroup = 0;
                if (isset($_REQUEST['group'])){
                    $pgroup = $_REQUEST['group'];
                }
                $group = $pgroup + 1;
                $_REQUEST['group'] = $group;
                return $group;
            };

            $jprint_js_select_on_change = function($js_select_element,$print_script_tag = false){
                if ($print_script_tag){
                    echo("\n<script>\n");
                }
                printf('%s.each(function(index,element) { ',$js_select_element);
                printf('var author_el=jQuery(element); author_el.change(function(ob){ ');
                printf('author_el.next().attr("name",author_el.val()); });');
                printf("});");
                if ($print_script_tag){
                    echo("\n</script>\n");
                }
            };


            $print_label = function($key,$label,$show_help,$group=0){
                if (!empty($group)){
                    $label = $label . " ($group)";
                }
                echo('<span class="input_label">');
                if ($show_help){
                    printf('<a href="#" onclick="showUrlInDialog(\'/prepo/field_help?key=%s\',\'%s\'); return false;">%s:</a>',$key,$key,$label);
                }else{
                    echo($label);echo(':');
                }
                echo('</span>');
            };

            $div_id = isset($options['div_id'])?$options['div_id']:$get_uid();
          $label = isset($options['label'])?tr($options['label']):null;
            $add_button = isset($options['add_button'])? $options['add_button'] :false;
            $add_button_label = isset($options['add_button_label'])? $options['add_button_label'] :'add';
            $add_button_first = isset($options['add_button_first'])? $options['add_button_first'] :false;
            $show_help = isset($options['show_help'])? $options['show_help'] :false;
            $size= isset($options['size'])? $options['size']: 80;
            $add_button_id = isset($options['add_button_id'])? $options['add_button_id'] : $get_uid('btn');
            $autocomplete_url =  isset($options['autocomplete_url'])? $options['autocomplete_url'] :null;
            $add_autocomplete = ! empty($autocomplete_url);
            $select_key_map =  isset($options['select_key_map'])? $options['select_key_map'] :null;
            $add_select_key = ! empty($select_key_map);
            $skip_on_empty = isset($options['skip_on_empty'])? $options['skip_on_empty'] :false;
            $print_label_flag = isset($options['print_label'])? $options['print_label'] :true;
            $input_br = isset($options['input_br'])? $options['input_br'] :true;
            $autocomplete_fn = isset($options['autocomplete_fn'])? $options['autocomplete_fn'] :null;
            $append_group = isset($options['append_group'])? $options['append_group'] :false;
            printf('<div id="%s">',$div_id);

            $value = $idata->getStaffValueArraySK($key);
            $tmp = isset($value[0]) ? $value[0]: null;
            $val1 = empty($tmp)?null: $tmp[0];
            $g1 = empty($tmp)?null: $tmp[1];
            if ($add_button && $add_button_first){
                $print_label($key,$label,$show_help,0);
                printf('<button id="%s" type="button">%s</button><br/>',$add_button_id,$add_button_label);
            }

            $group = 0;
            $fk=  $key;
            if ($append_group){
                if (empty($val1)){
                    $group = $get_group();
                } else {
                    $group = $g1;
                }
                $fk = $key . '@' . $group;
            }
            if (!($skip_on_empty && empty($val1)) ){

                if ($print_label_flag){
                    $print_label($key,$label,$show_help,$group);
                }
                if($add_select_key){
                    $sid = $get_uid('sel');
                    print_select(null,$sid,$select_key_map, $fk . "[]",true,true);
                    $jprint_js_select_on_change("jQuery('$sid')",true);
                }
                $input_id = $get_uid('inp');
                printf('<input id="%s" type="text" size="%s" name="%s[]" value="%s"/>',$input_id, $size,$fk, htmlspecialchars($val1));
                if ($add_button && ! $add_button_first) {
                    printf('<button id="%s" type="button">%s</button><br/>',$add_button_id,$add_button_label);
                } else if ($input_br){
                    echo("<br/>");
                }
                if (!empty($value)){
                    $c = 0;
                    foreach($value as $k=>$va){
                        $v = $va[0];
                        $mg = empty($va[1])?$group:$va[1];
                        $c ++;
                        if ($c > 1){
                            if ($print_label_flag){
                                $label = $get_label($label,$mg);
                                printf('<span class="input_label">%s:</span>',$label);
                            }
                            if($add_select_key){
                                $sid = $get_uid('sel');
                                print_select(null,$sid,$select_key_map, $fk . "[]",true,true);
                                $jprint_js_select_on_change("jQuery('$sid')",true);
                            }
                            $input_id = $get_uid('inp');
                            printf(' <input id="%s" type="text" size="%s" name="%s[]" value="%s"/> ',$input_id,$size, $fk,htmlspecialchars($v));
                            if ($input_br){
                                echo('<br/>');
                            }
                        }
                    }
                }
            }

            echo('</div>');
            if ($add_button){
                echo("<script>\n");
                echo("(function(){ \n");
                printf(' var div_un = jQuery("#%s"); ',$div_id);
                printf(' jQuery("#%s").click(function(ev) {' ."\n",$add_button_id);
                if ($add_select_key){
                    printf('var selecte = jQuery("<select>");'."\n");
                    foreach ($select_key_map as $k => $v){
                        $v = tr($v);
                        $val = $v == null ? '' : htmlspecialchars($v);
                        printf('jQuery(\'<option value="%s">%s</option>\').appendTo(selecte)' ."\n", $k,$val);
                    }
                }
                if ($print_label_flag){
                    printf(' var tmp1 = jQuery("<span>%s: </span>");'."\n",$label);
                }
                printf('  var tmp2 = jQuery(\'<input type="text" name="%s[]" size="%s" />\');'."\n",$fk,$fk,$size);
                if ($input_br){
                    printf(' var tmp3 = jQuery("<br/>");');
                }else {
                    printf(' var tmp3 = jQuery("<span>&nbsp;</span>");');
                }
                if ($add_autocomplete){
                    if (!empty($autocomplete_fn)){
                        printf(' %s(tmp2,"%s",2);',$autocomplete_fn, $autocomplete_url);
                    }else{
                        printf('tmp2.autocomplete({ source: "%s", minLength: 2}); ',$autocomplete_url);
                    }
                }
                if ($print_label_flag){
                    printf(' div_un.append(tmp1);');
                }
                if ($add_select_key){
                    printf(' div_un.append(selecte);');
                }
                printf(' div_un.append(tmp2);');
                printf(' div_un.append(tmp3);');

                if ($add_select_key){
                    $jprint_js_select_on_change('selecte',false);
                }
                printf(' });');
                echo("\n })()\n");
                echo('</script>');
            }





            if ($add_autocomplete){
                echo("<script>\n");
                echo("(function(){ \n");
                printf('jQuery(\'input[type="text"][name*="%s[]"]\').each(function(index,element) { ',$fk);
                if (!empty($autocomplete_fn)){
                    printf(' %s(element,"%s",2);',$autocomplete_fn, $autocomplete_url);
                }else{
                    printf(' jQuery(element).autocomplete({ source: "%s", minLength: 2}); ;',$autocomplete_url);
                }
                echo("});");
                echo("\n })()\n");
                echo('</script>');
            }


            }


}





?>