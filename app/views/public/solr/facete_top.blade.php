<?php
$input_all = Input::all();
$faceteHasLines = false;
$class_sel = null;
foreach ($facete_lines as $r) { if ($r > 0) { $faceteHasLines = true; break; }; }
$i=0;
unset($input_all['start']); //Deprecated pagination
unset($input_all['page']);

if(!empty($facete_params['unset_input'])){
	foreach ($facete_params['unset_input'] as $k=>$v){
		if(is_array($v)){
			if (Input::has($k)){
				foreach ($v as $val){
					if (Input::get($k) == $val){
						unset($input_all[$k]);
					}
				}
			}
		}else{
			if (Input::has($k) &&  Input::get($k) == $v){
						unset($input_all[$k]);
			}
		}
	}
}

//Collapse facetes div
$class_group_div = null;
$class_side_div = 'in';
if( !empty($facete_params['collapse']) && !isset($input_all[$facete_name])){
	//----------- check collapse depending access level -------------------------
	if(is_array($facete_params['collapse'])){
		if(user_access_admin()){
			if(!empty($facete_params['collapse']['admin'])){
				$class_group_div = 'collapsed';
				$class_side_div = null;
			}
		}else if(user_access_mentainer()){
			if(!empty($facete_params['collapse']['mentainer'])){
				$class_group_div = 'collapsed';
				$class_side_div = null;
			}
		}else if(user_access_login()){
			if(!empty($facete_params['collapse']['login'])){
				$class_group_div = 'collapsed';
				$class_side_div = null;
			}
		}else if(user_is_anonymous()){
			if(!empty($facete_params['collapse']['anonymous'])){
				$class_group_div = 'collapsed';
				$class_side_div = null;
			}
		}
	}else{
		$class_group_div = 'collapsed';
		$class_side_div = null;
	}
	//-----------------------------------------------------------------------------
}


?>


@if( $faceteHasLines)
	<div class="facet_box">
			@if(isset($input_all[$facete_name]))
					<?php
					$reset_facete = $input_all;
					unset($reset_facete[$facete_name]);
					$class_sel = 'selected';
					 ?>
					<div class="facet-header reset_top_facet"><a class="reset_top_facet_icon glyphicon glyphicon-remove" href="?{{ http_build_query($reset_facete) }}"></a></div>
			@endif

		<div class="facet-header {{$class_sel}} top {{$class_group_div}}" href="#side-{{$facete_name}}_facet" data-toggle="collapse" aria-expanded="true">
			<span class="glyphicon glyphicon-tag facet-icon" aria-hidden="true"></span>
				@if (Config::get('arc.SOLR_DEV',0)) {{$facete_title}}	@else {{tr($facete_title)}}  @endif
		</div>

		<div id="side-{{ $facete_name}}_facet" class="collapse {{$class_side_div}}" aria-expanded="true">
				@foreach ($facete_lines as $value => $count)
						<?php
							$i++;
							$tmp_arr = PUtil::explodeFacetValue($value); $value_txt = $tmp_arr[0]; $value_id = $tmp_arr[1];
							$is_selected = false;
							$selected_class = '';
							if (Input::has($facete_name) && PUtil::explodeIdFacet($input_all[$facete_name]) ==  $value_txt){
								$is_selected = true;
								$selected_class = 'selvalue';
							}
						?>
						@if($i <= $moreFacetsNum || (count($facete_lines) == $i && $i-1 == $moreFacetsNum) )
							<div class="list-group-item-facet {{$selected_class}}">
						@else
							<div class="list-group-item-facet {{$selected_class}} narrow{{$facete_name}} hidden">
						@endif
								<div class="left-flist">
								@if (!$is_selected)
										<a href="?{{ http_build_query(array_merge($input_all, array( $facete_link => $value))) }}">
								@endif
										@if( !empty($facete_params['tr']) )
											{{trChoise($value_txt . 's',$count)}}
										@else
											{{$value_txt}}
										@endif
										</a>
								</div>
								<div class="right-flist">
								@if( !empty($value_id) )
									<a href="/archive/item/{{$value_id}}"><span class="badge" >{{$count}} </span></a>
								@else
									<span class="badge" >{{$count}} </span>
								@endif
								</div>
							</div>
				@endforeach
		<div style="clear:both"> </div>
		@if($i > $moreFacetsNum && !(count($facete_lines) == $i && $i-1 == $moreFacetsNum) )
				<a id="more-narrow{{$facete_name}}" class="list-group-item extend" href="javascript:moreFacets('narrow{{$facete_name}}')">{{tr('more')}} ...</a>
				<a class="list-group-item extend narrow{{$facete_name}} hidden" href="javascript:lessFacets('narrow{{$facete_name}}')">{{tr('less')}} ...</a>
		@endif
		</div>

		@if(isset($input_all[$facete_name]))
				<?php  //unset($input_all[$facete_name]); ?>
<!-- 				<div class="reset-facet reset-facet-li"> -->
<!-- 					<a href="?{{ http_build_query($input_all) }}">{{tr('Reset facet')}}</a> -->
<!-- 				</div> -->
		@endif
	</div>
@endif