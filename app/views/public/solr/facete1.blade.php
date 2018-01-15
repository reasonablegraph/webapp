<?php

//Facetes access level
if (!empty($facete_params['access_level'])){
	if ($facete_params['access_level'] == 'login'){
		if(!user_access_login()){
			return;
		}
	}elseif ($facete_params['access_level'] == 'admin'){
		if(!user_access_admin()){
			return;
		}
	}elseif ($facete_params['access_level'] == 'mentainer'){
		if(!user_access_mentainer()){
			return;
		}
	}
}

$input_all = Input::all();
$faceteHasLines = false;
foreach ($facete_lines as $r) { if ($r > 0) { $faceteHasLines = true; break; }; }
$i=0;
unset($input_all['start']); //Deprecated pagination
unset($input_all['page']);

//Unset input
if(!empty($facete_params['unset_input'])){
	if(is_array($facete_params['unset_input'])){
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
	}else{
		unset($input_all[$facete_params['unset_input']]);
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
		<div class="list-group-item title {{$class_group_div}}" href="#side-{{$facete_name}}_facet" data-toggle="collapse" aria-expanded="true">@if (Config::get('arc.SOLR_DEV',0)) {{$facete_title}}	@else {{tr($facete_title)}}  @endif</div>
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
											{{trChoise($value_txt,$count)}}
										@else
											{{$value_txt}}
										@endif
										</a>
								</div>
								<div class="right-flist">
								@if( !empty($value_id) && empty($facete_params['hide_counter_link']) )
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
				<?php  unset($input_all[$facete_name]); ?>
				<div class="reset-facet reset-facet-li">
					<a href="?{{ http_build_query($input_all) }}">{{tr('Reset facet')}}</a>
				</div>
		@endif
	</div>
@endif