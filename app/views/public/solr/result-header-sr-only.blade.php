<!-- ----------------------------------------------------------------- SR-ONLY -------------------------------------------------------------------- -->
	<?php $input_param = Input::all();  ?>

		@if ($total_cnt>0 && !empty($input_param['fr']))
			<a id="filter-content"></a>
			<div class="sr-only" >
			@if (empty($input_param['record_type']) || empty($input_param['digital_item_types']))
				<h2>{{tr('Narrow Search sr-only')}}</h2>
			@endif
			<?php
				foreach ($facetes_sr as $facete){
					echo $$facete;
				}
			?>
		 <a href="?{{ http_build_query(array_merge($input_param, array('fr' => 0))) }}">{{tr('Hide filter sr-only')}}</a>
    </div>
   @endif

			<div class="result-header sr-only">
				@if( $display_mode == 'normal' )
						{{trChoise('Found',$total_cnt)}} <strong>{{$total_cnt}}</strong> {{trChoise('result',$total_cnt)}}
						@if ($numManifsFound>0 )
							({{trChoise('contain',$total_cnt)}} {{$numManifsFound}} {{trChoise('manifestation',$numManifsFound)}}
							@if ($numDigitalItemsFound > 0)
								{{tr('and')}} {{$numDigitalItemsFound}} {{trChoise('digital item',$numDigitalItemsFound)}}
							@endif
							)
						@endif
						, {{tr('total pages')}} {{$numPages}}.

						<div class="result-filter">
						<?php
// 									foreach ($facetes_names as $name){
// 										if ( Input::has($name) ){
// 											echo tr('Criteria of search sr-only');
// 											break;
// 										}
// 									}
									if ( $isFacetedQueryFlag ){
										echo tr('Criteria of search sr-only');
									}

						?>
						<ol>
								<?php
									$fcnt=0;
									foreach ($facetes_names as $name){
										$reset_param = Input::all();
										if ( Input::has($name) ){
											?>
											<li>
											<?php
											$fcnt++;
											echo ($name == 'record_type' || $name == 'form_type') ? tr(PUtil::explodeIdFacet($reset_param[$name])): PUtil::explodeIdFacet($reset_param[$name]); //TODO GENERIC EXCEPTION
											unset($reset_param[$name]);
											unset($reset_param['fr']);
											unset($reset_param['start']);
											unset($reset_param['page']);
											?>

												<a href="?{{ http_build_query($reset_param) }}">{{tr('Reset facetes sr-only')}}</a>
											</li>
											<?php
										}
									}
								?>
						</ol>
					</div>

					@if ($total_cnt>0 && empty($input_param['fr']) && $fcnt>0 )
						<a href="?{{ http_build_query(array_merge($input_param, array('fr' => '1'))) }}#filter-content">{{tr('Display filter more sr-only')}}</a>
					@elseif ($total_cnt>0 && empty($input_param['fr']))
						<a href="?{{ http_build_query(array_merge($input_param, array('fr' => '1'))) }}#filter-content">{{tr('Display filter sr-only')}}</a>
					@endif

				@elseif( $display_mode == 'list_id' )
					{{$list_label}}
					  ({{$total_cnt}} {{trChoise('result',$total_cnt)}}) <a href="{{UrlPrefixes::$item_opac}}{{$list_id}}">{{tr('Back to detail view')}}</a>
				@endif

			</div>
			<div class="sr-only">{{tr('List of search results')}}:</div>
