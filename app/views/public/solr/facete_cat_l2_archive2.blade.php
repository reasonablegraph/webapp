<?php
$input_all = Input::all();
$faceteHasLines = false;
foreach ($facete_lines as $r) { if ($r > 0) { $faceteHasLines = true; break; }; }
$i=0;
unset($input_all['start']); //Deprecated pagination
unset($input_all['page']);

if(!empty($facete_params['unset_input'])){
	foreach ($facete_params['unset_input'] as $k=>$v){
		if (Input::has($k) &&  Input::get($k) == $v){
			unset($input_all[$k]);
		}
	}
}
?>

@if( $faceteHasLines)
	<div class="facet_box">
		<div class="list-group-item title" href="#side-{{$facete_name}}_facet" data-toggle="collapse" aria-expanded="true">@if (Config::get('arc.SOLR_DEV',0)) {{$facete_title}}	@else {{tr($facete_title)}}  @endif</div>
		<div id="side-{{ $facete_name}}_facet" class="collapse in" aria-expanded="true">

					<?php
						$categories = array();
						foreach ($facete_lines as $value => $count){
							$tmp_arr = PUtil::explodeFacetValue($value); $value_txt = $tmp_arr[0]; $value_id = $tmp_arr[1];
							$cat_arr = PDao::getItemCategory($value_id);
							$categories[$cat_arr['label']][$value] = $count;
						}
// 						echo '<pre>'; print_r($categories); echo '</pre>';
					?>

				@foreach ($categories as $label => $val)
					<div class="list-group-item-facet">{{$label}}</div>
						@foreach ($val as $value => $count)
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
									<div style="float:left;">&nbsp&nbsp↳</div>
									<div class="left-flist" style="width:73%;">
							@if (!$is_selected)
													<a href="?{{ http_build_query(array_merge($input_all, array( $facete_link => $value))) }}">
							@endif
														<span>{{$value_txt}}</span>
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