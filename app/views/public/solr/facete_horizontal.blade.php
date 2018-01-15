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

	<style>
	.facetes_h{
 		padding-left:10px;
 		padding-right:10px;
 		padding-top:10px;
 		margin-bottom:10px;
 		border:1px solid #ccc;
 		background-image: linear-gradient(#ffffff, #eeeeee 50%, #e4e4e4);
 	}

 	a.facete_bnt{
	 	color: #fff;
	 	font-size: 0.9em;
		margin-right:2px;
	 	padding-bottom:2px;
	 	font-weight:bold;
 	}
 	</style>

@if( $faceteHasLines)
	<div class="facet_box">
		<div id="side-{{ $facete_name}}_facet" class="collapse in" aria-expanded="true">
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
									@if (!$is_selected)
											<a  class='btn btn-primary btn-xs facete_bnt'   href="?{{ http_build_query(array_merge($input_all, array( $facete_link => $value))) }}">
									@endif
											@if( !empty($facete_params['tr']) )
												{{trChoise($value_txt . 's',$count)}}
											@else
												{{$value_txt}}
											@endif
											<span class="badge" >{{$count}} </span>
										</a>
			@endforeach

			@if(isset($input_all[$facete_name]))
					<?php  unset($input_all[$facete_name]); ?>
						<a href="?{{ http_build_query($input_all) }}">{{tr('Reset facet')}}</a>
			@endif
			<div style="clear:both"> </div>
		</div>
	</div>
@endif