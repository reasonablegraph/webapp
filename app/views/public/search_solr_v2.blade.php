@section('content')


<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="//code.jquery.com/ui/1.10.4/jquery-ui.min.js"></script>
<link rel="stylesheet" type="text/css" href="//code.jquery.com/ui/1.10.4/themes/redmond/jquery-ui.css">

<script type="text/javascript">
$(function () {
    $('input[name="term"]').autocomplete({
        source: '/prepo/solr_suggest',
        minLength: 2
    });

    $(".clear_form").click(function() {
        $(this).closest('form').find("input[type=text], textarea").val("");
    });

});

// function clearForms(){
//	    $(':input').not(':button, :submit, :reset, :hidden, :checkbox, :radio').val('');
//	    $(':checkbox, :radio').prop('checked', false);
// }

</script>


<!-- <h3>SOLR SEARCH</h3> -->

<div class="row"  id="searchfconteiner"  <?php echo $hide_search_form ?  'aria-hidden="true"' : 'aria-hidden="false"'; ?>>
  <div class="panel panel-default" role="search" aria-label="{{tr('search form')}}" >
    <div class="panel-heading" ><h1>
      @if( $stype!='a')
        {{tr('Search in Repository')}}
      @else
        {{tr('Advance search in Repository')}}
      @endif
       {{tr(Config::get('arc.INSTALLATION_LEGEND'))}}
     </h1></div>

    <div class="panel-body">
      <form method="get" class="arch-sform  form-horizontal" >

      @if( $stype != 'a')
            <!-- Simple specific -->
            <input type="hidden" name="m" value="s"/>

            <div class="form-group">
                <div class="col-md-10">
                <label for="terms" class="col-md-2 control-label">{{tr('Term field')}}:</label>
                <input class="col-md-10 form-search" name="term" type="text"  value="{{$term}}" placeholder="{{tr('Import term')}}"  />

              </div>
                <div class="col-md-2">

                <?php /*
                  <?php if ($display_lang_select_flag): ?>
                    <?php  if ($m != 'a'): ?>
                      <label class="element-invisible" for="select_lang"><?=tr('Γλωσσα')?>: </label>
                      <select name="sl" class="title form-control" id="select_lang">
                        <option value="0" <?php  if ($sl == '0'){echo 'selected="selected"'; } ?> ><?=tr('Επιλογή Γλώσσας')?></option>
                        <option value="el" <?php if ($sl == 'el'){echo 'selected="selected"'; } ?> ><?=tr('Ελληνικά')?></option>
                        <option value="en" <?php if ($sl == 'en'){echo 'selected="selected"'; } ?> ><?=tr('English')?></option>
                      </select>

                    <?php endif; ?>
                    <?php endif; ?>
                  */ ?>
              </div>
            </div>
      @else
          <!-- Advance specific -->
            <input type="hidden" name="m" value="a"/>

            <div class="form-group">
              <div class="col-md-10">
                <label for="terms" class="col-md-2 control-label">{{tr('Search all')}}: </label>

                <input class="col-md-10 text form-search" type="text" name="term" value="{{$term}}" placeholder="{{tr('Import term')}}"/>
              </div>
               <div class="col-md-2"> </div>
            </div>

            <div class="form-group">
                <div class="col-md-10">
                <label for="title" class="col-md-2 control-label">{{tr('Title')}}: </label>
                <input class="col-md-4 text form-search" type="text" name="l" value="{{$l}}" placeholder="{{tr('Import title')}}"/>
                <label for="y" class="col-md-2 control-label">{{tr('Year')}}: </label>
                <input type="text" class="col-md-4 text form-search" name="y" value="{{$y}}" placeholder="{{tr('Import year')}}" />
              </div>
               <div class="col-md-2"> </div>
            </div>

            <div class="form-group">
               <div class="col-md-10">
                <label for="author" class="col-md-2 control-label">{{tr('Author')}}: </label>
                <input class="col-md-4 text form-search" type="text" name="a" value="{{$a}}" placeholder="{{tr('Import author')}}" />
                 <label for="isbn" class="col-md-2 control-label">{{tr('ISBN')}}: </label>
                <input class="col-md-4 text form-search" type="text" name="p" value="{{$p}}" placeholder="{{tr('Import isbn')}}" />
              </div>
              <div class="col-md-2"> </div>
            </div>

            <div class="form-group">
               <div class="col-md-10">
                <label for="subject" class="col-md-2 control-label">{{tr('Subject')}}: </label>
                <input class="col-md-4 text form-search" type="text" name="subj" value="{{$subj}}" placeholder="{{tr('Import subject')}}" />
                 <label for="digital_type" class="col-md-2 control-label">{{tr('Item type')}}: </label>
                <select  id="digital_type" class="col-md-4 text form-search">
                    <option value="undefined">{{tr('All types')}}</option>
                    <option value="pdf">PDF</option>
                    <option value="daisy">{{tr('DAISY text')}}</option>
                    <option value="epub">EPUB</option>
                    <option value="docx">DOCX</option>
                    <option value="wma">WMA</option>
                    <option value="mp3">MP3</option>
                </select>
              </div>
              <div class="col-md-2"> </div>
            </div>
      @endif

      <div class="form-group">
        <div class="col-md-10 "> <!-- <div class="col-md-7 col-md-offset-2">-->
           <div class="col-md-10  col-md-offset-2 search-buttons">
            <button name="submit" type="submit" value="search" class="btn btn-default" >{{tr('Search')}}</button>
            <button name="clear" value="clear" class="clear_form btn btn-default"  >{{tr('Clear')}}</button>
          </div>
        </div>
        <div class="col-md-2">
        @if ($stype == 's')
          @if (variable_get('arc_search_display_advance_link'))
        <!--    <a class="m_search_link"  href="{{{UrlPrefixes::$search_solr}}}?m=a&term={{$term}}">{{tr('Advance search')}}</a> -->
          @endif
        @else
            <a class="m_search_link" href="{{{UrlPrefixes::$search_solr}}}?term={{$term}}">{{tr('Simple search')}}</a>
        @endif
        </div>
      </div>

      </form>
    </div>
  </div>
</div>


@if (get_get('submit') !== null || ($total_cnt>0))
  <div class="row" role="main" aria-label="{{tr('search results')}}"  >


   <!-- ------------------------------- SR-ONLY ----------------------------------- -->
    <?php $input_param = Input::all();  ?>
     @if ($total_cnt>0 && empty($input_param['fr']))
<!-- 	     <div class="sr-only" > -->  <div>
		     <a href="?{{ http_build_query(array_merge($input_param, array('fr' => '1'))) }}#filter-content">{{tr('Display filter sr-only')}}</a>
	     </div>
     @endif


     @if ($total_cnt>0 && !empty($input_param['fr']))
      <a id="filter-content"></a>
<!--       <div class="sr-only" > -->  <div>
       @if (empty($input_param['record_type']) && empty($input_param['digital_item_types']))
       	<h2>{{tr('Narrow Search sr-only')}}</h2>
       @endif

       <!-- Object Type Filter -->
  		  @if (empty($input_param['record_type']))
	       <div>{{tr('Object Type sr-only')}}</div>
	        <ol>
	          @foreach ($record_type_facet as $value => $count)
	            @if($count)
	            <?php
	             unset($input_param['start']); //link unset start
	             ?>
	             <li><a href="?{{ http_build_query(array_merge($input_param, array('record_type' => $value))) }}">{{trChoise($value.'s',$count)}} {{$count}}</a></li>
	            @endif
	          @endforeach
	        </ol>
	      @else
          {{tr('Apply filter sr-only')}} {{tr('Object Type')}} {{tr($input_param['record_type'])}} {{tr('in results sr-only')}}
	      @endif
				<!-- -->


				<!-- Digital Items Filter -->
  		  @if (empty($input_param['digital_item_types']))
	       <div>{{tr('Digital item sr-only')}}</div>
	        <ol>
	          @foreach ($digital_item_types_facet as $value => $count)
	            @if($count)
	            <?php
	             unset($input_param['start']); //link unset start
	             ?>
	             <li><a href="?{{ http_build_query(array_merge($input_param, array('digital_item_types' => $value))) }}">{{tr($value)}} {{$count}}</a></li>
	            @endif
	          @endforeach
	        </ol>
	      @else
          {{tr('Apply filter sr-only')}} {{tr('Digital Items')}} {{tr($input_param['digital_item_types'])}} {{tr('in results sr-only')}}
	      @endif
				<!-- -->






			 @if (!empty($input_param['record_type']))
	      <div>
		      <?php
					$input_copy = Input::all();
		      unset($input_copy['record_type']);
		      unset($input_copy['start']); //reset facede unset start
		      ?>
	      	<a href="?{{ http_build_query($input_copy) }}">{{tr('Reset facetes sr-only')}}</a>
     		</div>
       @endif

       @if (!empty($input_param['fr']) && empty($input_param['object_type']))
				 <?php  unset($input_param['fr']);  ?>
      	 <a href="?{{ http_build_query($input_param)}}">{{tr('Hide filter sr-only')}}</a>
       @endif

    </div>
   @endif
  <!-- ------------------------------------------------------------------------------- -->

    <div class="col-md-9 side-left">
      <div id="tresults" >

        <div class="result-header"  >
					<!-- {{tr('search results')}} -->
					{{trChoise('Found',$total_cnt)}} <strong>{{$total_cnt}}</strong> {{trChoise('result',$total_cnt)}}
					@if ($numManifsFound>0 ) <!--  && $numManifsFound > $total_cnt -->
						<span class="results_exp">({{trChoise('contain',$total_cnt)}} {{$numManifsFound}} {{trChoise('manifestation',$numManifsFound)}}
						@if ($numDigitalItemsFound > 0)
							{{tr('and')}} {{$numDigitalItemsFound}} {{trChoise('digital item',$numDigitalItemsFound)}}
						@endif
						)</span>
					@endif

          <div class="result-filter" aria-hidden="true">
          @if (
								Input::has('record_type') ||
								Input::has('authors') ||
								Input::has('subjects') ||
								Input::has('publication_places') ||
								Input::has('publication_types') ||
								Input::has('publishers') ||
								Input::has('digital_item_types') ||
								Input::has('languages')
					)
						{{tr('Criteria of search')}}:
					@endif

           @if (Input::has('record_type'))
          	&#9830; {{tr($input_param['record_type'])}}
           @endif
           @if (Input::has('authors'))
          	&#9830; {{tr($input_param['authors'])}}
           @endif
           @if (Input::has('subjects'))
          	&#9830; {{tr($input_param['subjects'])}}
           @endif
           @if (Input::has('publication_places'))
          	&#9830; {{tr($input_param['publication_places'])}}
           @endif
           @if (Input::has('publication_types'))
          	&#9830; {{tr($input_param['publication_types'])}}
           @endif
           @if (Input::has('publishers'))
          	&#9830; {{tr($input_param['publishers'])}}
           @endif
           @if (Input::has('digital_item_types'))
          	&#9830; {{tr($input_param['digital_item_types'])}}
           @endif
           @if (Input::has('languages'))
            &#9830; {{tr($input_param['languages'])}}
           @endif
         </div>

					<div class="result-filter" aria-hidden="true">
						<?php	$len = count($subjectsResultset);?>
						@if ($len>0)
							{{tr('Suggested topics')}}:
							@foreach ($subjectsResultset as $index => $document)
								<?php	 $r = json_decode($document->opac1, true); ?>
								<a href="{{UrlPrefixes::$item_opac}}{{$document->id}}">{{$r['title']}}</a>@if($index != $len - 1),@endif
							@endforeach
						@endif
					</div>

        </div>

<!-- 				<div class="rescnt row res-infobar"> -->
<!-- 					{{trChoise('Found',$total_cnt)}} <strong>{{$total_cnt}}</strong> {{trChoise('result',$total_cnt)}} -->
<!-- 			@if ($numManifsFound>0 ) <!--  && $numManifsFound > $total_cnt -->
<!-- 						({{trChoise('contain',$total_cnt)}} {{$numManifsFound}} {{trChoise('manifestation',$numManifsFound)}} -->
<!-- 						@if ($numDigitalItemsFound > 0) -->
<!-- 							{{tr('and')}} {{$numDigitalItemsFound}} {{trChoise('digital item',$numDigitalItemsFound)}} -->
<!-- 						@endif -->
<!-- 						) -->
<!-- 					@endif -->
<!-- 				</div> -->

        <ol class="reslist itemlist">
          @foreach ($resultset as $document)
          <?php	 $r = json_decode($document->opac1, true); ?>
          <?php	 //echo '<pre>'; print_r($r); echo '</pre>'; ?>
            <li class="resitem">

            @if (isset($r['thumbs']['thumb']) && ! empty($r['thumbs']['thumb']))
              <span aria-hidden="true" class="thumb_bg_img" style="background-image:url(/media/{{$r['thumbs']['thumb']}});"></span>
            @endif


            @if($r['obj_type'] != 'auth-work' && $r['obj_type'] != 'auth-manifestation' && $r['obj_type'] != 'lemma' )
              {{tr($r['obj_type'])}}:
            @endif

            @if (isset($r['public_title']['title']))
              <a href="{{UrlPrefixes::$item_opac}}{{$r['public_title']['id']}}"><strong>
                  {{$r['public_title']['title']}}
            @else
              <a href="{{UrlPrefixes::$item_opac}}{{$document->id}}"><strong>
                  {{$r['title']}}
            @endif
             </strong></a><br/>

            @if (isset($r['public_lines']))
              @if (count($r['public_lines']) > 0)
                  @if (count($r['public_lines']) == 1)
                  <span class="res_l">{{tr('Manifestation of work')}}: </span>
                   {{-- tetrimeno foreach plcnt == 1 --}}
                    @foreach ($r['public_lines'] as $m)
                    <span class="res_l"><a href="{{UrlPrefixes::$item_opac}}{{$m['id']}}">{{$m['title']}}</a>
                    @if(isset($m['items']) && ! empty($m['items']))
                    @if(user_access_login())
                    <span class="sr-only">{{tr('Available files for instant download')}}: </span>
                      @foreach ($m['items']['type'] as $index => $type)
                        @if (isset($m['items']['object-type']))
                            @if ($m['items']['object-type'][$index] == 'digital-item')
                              @if(isset($type) && ! empty($type))
                                [<a href="{{UrlPrefixes::$item_opac}}{{$m['items']['id'][$index]}}/download/0">{{$type}}</a>]
                              @endif
                            @endif
                        @endif
                      @endforeach
                    @endif
                    @endif
                    </span>
                    @endforeach
                  @else
                  <span class="res_l">{{tr('Manifestations of work')}}: </span>
                    <ol type="1">
                      @foreach ($r['public_lines'] as $m)
                        <li><a href="{{UrlPrefixes::$item_opac}}{{$m['id']}}">{{$m['title']}}</a>
                        @if(isset($m['items']) && ! empty($m['items']))
                        @if(user_access_login())
                        <span class="sr-only">{{tr('Available files for instant download')}}: </span>
                          @foreach ($m['items']['type'] as $index => $type)
                            @if (isset($m['items']['object-type']))
                              @if ($m['items']['object-type'][$index] == 'digital-item')
                                @if(isset($type) && ! empty($type))
                                  [<a href="{{UrlPrefixes::$item_opac}}{{$m['items']['id'][$index]}}/download/0">{{$type}}</a>]
                                @endif
                              @endif
                            @endif
                          @endforeach
                        @endif
                        @endif
                      </li>
                      @endforeach
                    </ol>
                  @endif
              @endif
            @endif

<!--         <div style ="font-size:11px;"> -->
<!--             @if($highlighting->getResult($document->id)) -->
<!--               @foreach ($highlighting->getResult($document->id) as $field => $highlight) -->
                <?php  // echo tr($field) . ': <span style ="font-size:11px;font-style: italic;">"' . implode(' (...) ', $highlight) . '..."</span><br/>'; ?>
<!--               @endforeach -->
<!--             @endif -->
<!--           </div> -->

            @if (isset($r['individual_works']))
              @foreach ($r['individual_works'] as $iw)
                @if(isset($iw['id']) && ! empty($iw['id']))
                  <span class="res_l">{{$relation_work_wholepart_map['ea:relation:containedInIndividual']}}:</span>
                  <span class="res_l"><a href="{{UrlPrefixes::$item_opac}}{{$iw['id']}}">{{$iw['label']}}</a></span>
                @endif
              @endforeach
            @endif

            @if (isset($r['contained_in_contribution']))
              @foreach ($r['contained_in_contribution'] as $cw)
                @if(isset($cw['id']) && !empty($cw['id']))
                  <span class="res_l">{{$relation_work_wholepart_map['ea:relation:containedInContributions']}}:</span>
                  <span class="res_l"><a href="{{UrlPrefixes::$item_opac}}{{$cw['id']}}">{{$cw['label']}}</a></span>
                @endif
              @endforeach
            @endif

            <div class="clearfix"></div>

            </li>
          @endforeach
          </ol>
      </div>
    </div>

    <div class="col-md-3  side-right" aria-hidden="true">
       <div class="facet-header">
        <span class="glyphicon glyphicon-filter facet-icon" aria-hidden="true"></span>{{tr('Narrow Search')}}
      </div>
      <div id="side-panel-institution" class="list-group facet">


      <!-- Record Type Filter -->
      <div class="facet_box">

        <?php
	         $input_all = Input::all();
	       ?>
       	@if(isset($input_all['record_type']))
	          <?php  unset($input_all['record_type']); ?>
			       <div class="reset_facet_in"><a href="?{{ http_build_query($input_all) }}">[x]</a></div>
			  @endif
				<div class="list-group-item title resf" href="#side-record_type_facet" data-toggle="collapse" aria-expanded="true">{{tr('Object Type')}}</div>

	      <div id="side-record_type_facet" class="collapse in" aria-expanded="true">
	          @foreach ($record_type_facet as $value => $count)
	            @if($count)
	            <?php
	             unset($input_all['start']); //link unset start
	             ?>
	              <a class="list-group-item"  href="?{{ http_build_query(array_merge($input_all, array('record_type' => $value))) }}">{{trChoise($value.'s',$count)}} <span class="badge">{{$count}} </span></a>
	            @endif
	          @endforeach
<!-- 	          @if(isset($input_all['record_type'])) -->
	          	<?php  //unset($input_all['record_type']); ?>
<!-- 	      	    <div class="reset-facet reset-facet-li"> -->
<!--      	  	<a href="?{{ http_build_query($input_all) }}">{{tr('Reset facet')}}</a> --> <!-- Reset record type -->
<!-- 	      	    </div> -->
<!--  						@endif -->
	        </div>
        </div>
      <!-- -->


      <!-- Authors Filter -->
       <div class="facet_box">
        <div class="list-group-item title" href="#side-authors_facet" data-toggle="collapse" aria-expanded="true">
        {{tr('Authors')}}
        </div>
        <div id="side-authors_facet" class="collapse in" aria-expanded="true">
          @foreach ($authors_facet as $value => $count)
            @if($count)
            <?php
             $input_all = Input::all();
             unset($input_all['start']); //link unset start
             ?>
              <a class="list-group-item "  href="?{{ http_build_query(array_merge($input_all, array('authors' => $value))) }}">{{tr($value)}} <span class="badge">{{$count}} </span></a>
            @endif
          @endforeach
          @if(isset($input_all['authors']))
	          <?php  unset($input_all['authors']); ?>
	          <div class="reset-facet reset-facet-li">
	        	 <a href="?{{ http_build_query($input_all) }}">{{tr('Reset facet')}}</a> <!-- Reset authors-->
	          </div>
 				@endif
        </div>
       </div>
      <!-- -->


      <!-- Subjects Filter -->
       <div class="facet_box">
        <div class="list-group-item title" href="#side-subjects_facet" data-toggle="collapse" aria-expanded="true">
        {{tr('Subjects')}}
        </div>
        <div id="side-subjects_facet" class="collapse in" aria-expanded="true">
          @foreach ($subjects_facet as $value => $count)
            @if($count)
            <?php
             $input_all = Input::all();
             unset($input_all['start']); //link unset start
             ?>
              <a class="list-group-item "  href="?{{ http_build_query(array_merge($input_all, array('subjects' => $value))) }}">{{tr($value)}} <span class="badge">{{$count}} </span></a>
            @endif
          @endforeach
         @if(isset($input_all['subjects']))
          <?php  unset($input_all['subjects']); ?>
          <div class="reset-facet reset-facet-li">
          	<a href="?{{ http_build_query($input_all) }}">{{tr('Reset facet')}}</a> <!-- Reset subjects-->
          </div>
 				@endif
        </div>
       </div>
      <!-- -->


          <!-- Language Filter -->
     <div class="facet_box">
        <div class="list-group-item title" href="#side-languages_facet" data-toggle="collapse" aria-expanded="true">
        {{tr('Language')}}
        </div>
        <div id="side-languages_facet" class="collapse in" aria-expanded="true">
          @foreach ($languages_facet as $value => $count)
            @if($count)
            <?php
             $input_all = Input::all();
             unset($input_all['start']); //link unset start
             ?>
              <a class="list-group-item "  href="?{{ http_build_query(array_merge($input_all, array('languages' => $value))) }}">{{tr($value)}} <span class="badge">{{$count}} </span></a>
            @endif
          @endforeach
         @if(isset($input_all['languages']))
          <?php  unset($input_all['languages']); ?>
          <div class="reset-facet reset-facet-li">
          	<a href="?{{ http_build_query($input_all) }}">{{tr('Reset facet')}}</a> <!-- Reset language -->
          </div>
 				@endif
        </div>
       </div>
      <!-- -->

			<div class="line-separator"></div>

      <!-- Publication places Filter -->
       <div class="facet_box">
        <div class="list-group-item title" href="#side-publication_places_facet" data-toggle="collapse" aria-expanded="true">
        {{tr('Publication places')}}
        </div>
        <div id="side-publication_places_facet" class="collapse in" aria-expanded="true">
          @foreach ($publication_places_facet as $value => $count)
            @if($count)
            <?php
             $input_all = Input::all();
             unset($input_all['start']); //link unset start
             ?>
              <a class="list-group-item "  href="?{{ http_build_query(array_merge($input_all, array('publication_places' => $value))) }}">{{tr($value)}} <span class="badge">{{$count}} </span></a>
            @endif
          @endforeach
         @if(isset($input_all['publication_places']))
          <?php  unset($input_all['publication_places']); ?>
          <div class="reset-facet reset-facet-li">
          	<a href="?{{ http_build_query($input_all) }}">{{tr('Reset facet')}}</a> <!-- Reset publication places-->
        	</div>
 				@endif
        </div>
       </div>
      <!-- -->


      <!-- Publication Type Filter -->
       <div class="facet_box">
        <div class="list-group-item title" href="#side-publication_types_facet" data-toggle="collapse" aria-expanded="true">
        {{tr('Publication types')}}
        </div>
        <div id="side-publication_types_facet" class="collapse in" aria-expanded="true">
          @foreach ($publication_types_facet as $value => $count)
            @if($count)
            <?php
             $input_all = Input::all();
             unset($input_all['start']); //link unset start
             ?>
              <a class="list-group-item "  href="?{{ http_build_query(array_merge($input_all, array('publication_types' => $value))) }}"> {{tr($value)}} <span class="badge">{{$count}} </span></a>
            @endif
          @endforeach
         @if(isset($input_all['publication_types']))
          <?php  unset($input_all['publication_types']); ?>
          <div class="reset-facet reset-facet-li">
          	<a href="?{{ http_build_query($input_all) }}">{{tr('Reset facet')}}</a> <!-- Reset publication types-->
        	</div>
 				@endif
        </div>
       </div>
      <!-- -->


      <!-- Publishers Filter -->
       <div class="facet_box">
        <div class="list-group-item title" href="#side-publishers_facet" data-toggle="collapse" aria-expanded="true">
        {{tr('Publishers')}}
        </div>
        <div id="side-publishers_facet" class="collapse in" aria-expanded="true">
          @foreach ($publishers_facet as $value => $count)
            @if($count)
            <?php
             $input_all = Input::all();
             unset($input_all['start']); //link unset start
             ?>
              <a class="list-group-item "  href="?{{ http_build_query(array_merge($input_all, array('publishers' => $value))) }}">{{tr($value)}} <span class="badge">{{$count}} </span></a>
            @endif
          @endforeach
         @if(isset($input_all['publishers']))
          <?php  unset($input_all['publishers']); ?>
          	<div class="reset-facet reset-facet-li">
          <a href="?{{ http_build_query($input_all) }}">{{tr('Reset facet')}}</a> <!-- Reset publishers-->
       	</div>
 				@endif
        </div>
       </div>
      <!-- -->

     <div class="line-separator"></div>

     <!-- Digital Items Filter -->
      <div class="facet_box">
        <div class="list-group-item title" href="#side-digital_item_types_facet" data-toggle="collapse" aria-expanded="true">
        {{tr('Digital Items')}}
        </div>
        <div id="side-digital_item_types_facet" class="collapse in" aria-expanded="true">
          @foreach ($digital_item_types_facet as $value => $count)
            @if($count)
            <?php
             $input_all = Input::all();
             unset($input_all['start']); //link unset start
             ?>
              <a class="list-group-item "  href="?{{ http_build_query(array_merge($input_all, array('digital_item_types' => $value))) }}">{{tr($value)}} <span class="badge">{{$count}} </span></a>
            @endif
          @endforeach
         @if(isset($input_all['digital_item_types']))
          <?php  unset($input_all['digital_item_types']); ?>
          <div class="reset-facet reset-facet-li">
          	<a href="?{{ http_build_query($input_all) }}">{{tr('Reset facet')}}</a> <!-- Reset digital Items-->
        	</div>
 				@endif
        </div>
       </div>
      <!-- -->

      </div>

      <div class="reset-facet">
      <?php
			$input_copy = Input::all();
      unset($input_copy['record_type']);
      unset($input_copy['authors']);
      unset($input_copy['subjects']);
      unset($input_copy['publication_places']);
      unset($input_copy['publication_types']);
      unset($input_copy['publishers']);
      unset($input_copy['digital_item_types']);
      unset($input_copy['start']); //reset facede unset start
      ?>
      <a href="?{{ http_build_query($input_copy) }}">{{tr('Reset all facetes')}}</a>
      </div>
    </div>

  </div>

	<?php PSnipets::solr_paging_text($numPages,$resultsPerPage);  ?>

  @if($hide_search_form )
      <div class="pager">{{tr('End of search')}}, <a href="{{{UrlPrefixes::$search_solr}}}?m={{$stype}}">{{tr('return to search page')}}</a></div>
  @endif


@endif

@stop
