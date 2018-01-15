<!-- ----------------------------------------------------------------- SR-ONLY -------------------------------------------------------------------- -->
<?php
$input_all = Input::all();
$faceteHasLines = false;
foreach ($facete_lines as $r) { if ($r > 0) { $faceteHasLines = true; break; }; }


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


?>


@if( $faceteHasLines && !empty($facete_params['sr'])  && !(Input::has($facete_name)) )

		<li>
		{{tr($facete_title.'_sr-only')}}
			@foreach ($facete_lines as $value => $count)
					<?php
						unset($input_all['start']);
						unset($input_all['page']);
						//$i++;
						$tmp_arr = PUtil::explodeFacetValue($value);
						$value_txt = $tmp_arr[0];
						$value_id = $tmp_arr[1];
					?>
					<a href="?{{ http_build_query(array_merge($input_all, array( $facete_name => $value,'fr' => 0))) }}">
					@if( !empty($facete_params['tr']) )
						{{trChoise($value_txt.'s',$count)}} {{$count}}
					@else
						{{$value_txt}} {{$count}}
					@endif
					</a>
			@endforeach
		</li>

@endif
