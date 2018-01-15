<?php
	$input_param = Input::all();
?>
		<div class="col-md-12 result-header" aria-hidden="true" >
			@if( $display_mode == 'normal' )
						{{trChoise('Found',$total_cnt)}} <strong>{{$total_cnt}}</strong> {{trChoise('result',$total_cnt)}}
						<span class="results_exp">
									@if ($numManifsFound>0 || $numIssuesFound>0) <!--  && $numManifsFound > $total_cnt -->
										({{trChoise('contain',$total_cnt)}} {{$numManifsFound}} {{trChoise('manifestations',$numManifsFound)}}
											@if ($numDigitalItemsFound > 0)
												{{tr('and')}} {{$numDigitalItemsFound}} {{trChoise('digital item',$numDigitalItemsFound)}}
											@endif
										)
									@endif
							{{-- &#9830; {{tr('total pages')}} {{$numPages}} --}}
						</span>

					<div class="col-sm-12 sb_label result-filter">
						@if($isFacetedQueryFlag)
							{{tr('Criteria of search')}}:
						@endif

						@foreach ($facetes_names as $name)
							@if (Input::has($name))
								@if($name =='form_type' || $name =='form_type_all' || $name =='language_of_term' || $name =='publication_type')
								&#9830; <strong>{{PUtil::explodeIdFacet(tr($input_param[$name]))}}</strong>
								@else
								&#9830; <strong>{{PUtil::explodeIdFacet($input_param[$name])}}</strong>
								@endif
							@endif
						@endforeach

					</div>
					<div class="result-filter" aria-hidden="true">
						<?php	$len = count($subjectsResultset);?>
						@if ($len>0)
							{{tr('Suggested topics')}}:
							@foreach ($subjectsResultset as $index => $document)
								@if ($index < $subjectsResultsetNum)
									<?php	 $r = json_decode($document->opac1, true); ?>
									<a href="{{UrlPrefixes::$item_opac}}{{$document->id}}">{{$r['title']}}</a>@if($index != $len - 1 && $index != $subjectsResultsetNum-1 ),
									@elseif ($index == $subjectsResultsetNum-1).<!-- cut flag -->
									@endif
								@endif
							@endforeach
						@endif
					</div>
			@elseif( $display_mode == 'list_id' )
					<div class="col-md-10 col-sm-12 sb_label" >
							{{$list_label}}
						<span class="results_exp">({{$total_cnt}} {{trChoise('result',$total_cnt)}})</span>
					</div>
					<div class="col-md-2 col-sm-12 sb_label">
						<a class="sb_link" href="{{UrlPrefixes::$item_opac}}{{$list_id}}"><span class="glyphicon glyphicon-arrow-left"></span> {{tr('Back to detail view')}}</a>
					</div>
			@endif
		</div>