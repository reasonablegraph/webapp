<?php
  //$lang = get_lang(); //?lang={{$lang}}
  //$input_vars = Input::all(); //?{{http_build_query($input_vars)}}
  $relation_work_wholepart_map = Setting::get('relation_work_wholepart_map');
  $id = $document->id;
  $label = $document->label;
  $r = json_decode($document->opac1, true);
  $ft = $document->form_type;
  //echo '<pre>'; print_r($document); echo '</pre>';
?>
        <li class="resitem">
            @if (isset($r['thumbs']['small']) && ! empty($r['thumbs']['small']))
             {{--<!-- <span aria-hidden="true" class="thumb_bg_img" style="background-image:url({{{UrlPrefixes::$media}}}/{{$r['thumbs']['small'][0]}});"></span>  -->--}}
             <span aria-hidden="true" class="sch_thumb_img" >
               <a class="group colorbox-load cboxElement" rel="gal" href="{{{UrlPrefixes::$media}}}/{{$r['thumbs']['big'][0]}}" title="{{$r['thumbs']['description']}}" >
                 <img src="{{{UrlPrefixes::$media}}}/{{$r['thumbs']['icon_small'][0]}}" alt="{{$r['thumbs']['description']}}">
              </a>
             </span>
            @endif

            @if($r['obj_type'] != 'auth-work' && $r['obj_type'] != 'auth-manifestation' && $r['obj_type'] != 'lemma' )
              {{tr($r['obj_type'])}}:
            @endif

            @if (isset($r['public_title']['title']))
              <?php $lnk_class = $r['obj_type'] == 'auth-work' ? 'class="bold_slr"' : null; ?>
              <a href="{{UrlPrefixes::$item_opac}}{{$r['public_title']['id']}}" {{$lnk_class}}> {{$r['public_title']['title']}}
            @else
              <a href="{{UrlPrefixes::$item_opac}}{{$r['id']}}">
              {{--<!--  {{$r['title']}} -->--}}
              {{$document->label}}
            @endif
              </a>

            @if(!empty($r['authors']))
              <br> <span class="label_solr">{{tr('Author')}}:
              @foreach ($r['authors'] as $index=>$author)
                  <a href="{{UrlPrefixes::$item_opac}}{{$author['id']}}">{{$author['name']}}</a>@if($index < count($r['authors'])-1),@endif
              @endforeach
              </span>
            @endif

            @if(isset($r['items']) && ! empty($r['items']))
              @if(user_access_login())
                 <br>
{{--<!--                  <span class="label label-info" aria-hidden="true">{{tr('Available files')}}:</span> -->--}}
                 <span class="label_solr" aria-hidden="true">{{tr('Available files')}}:</span>
                 <span class="sr-only">{{tr('Available files for instant download')}}: </span>
                 @foreach ($r['items'] as $item )
                   @if(isset($item['id']))
                      @if( isset($item['label']) && !empty($item['label']) )
                          @if( $item['title']== 'digital-item' )
                           <span class="sdonwload label label-success"><a href="{{UrlPrefixes::$item_opac}}{{$item['id']}}/download/0">{{$item['label']}}</a></span>
                          @else
                           <span class="sdonwload label label-success"><a href="{{UrlPrefixes::$item_opac}}{{$item['id']}}">{{$item['label']}}</a></span>
                          @endif

                      @endif
                    @endif
                 @endforeach
                @endif
              @endif

            @if (isset($r['public_lines']))

           <?php //echo '<pre>'; print_r($r['public_lines']); echo '</pre>'; ?>
              @if (count($r['public_lines']) > 0)
                  @if (count($r['public_lines']) == 1)
                  <span class="res_l">{{tr('Manifestation of work')}}: </span>
                   {{-- tetrimeno foreach plcnt == 1 --}}
                    @foreach ($r['public_lines'] as $m)
                    <span class="res_l"><a href="{{UrlPrefixes::$item_opac}}{{$m['id']}}">{{$m['title']}}</a>
                       @if(isset($m['items']) && ! empty($m['items']))
                        @foreach ($m['items']['id'] as $index => $item_id )
                         @if ($m['items']['object-type'][$index] == 'physical-item')
                          @if(isset($m['items']['sublocation'][$index]) && ! empty($m['items']['sublocation'][$index]))
		                        ({{$m['items']['sublocation'][$index]}}@if(isset($m['items']['classification'][$index]) && ! empty($m['items']['classification'][$index])) - {{$m['items']['classification'][$index]}}@endif)
		                      @elseif(isset($m['items']['classification'][$index]) && ! empty($m['items']['classification'][$index]))
		                        [{{$m['items']['classification'][$index]}}]
		                      @endif
                         @endif
                         @if ($m['items']['object-type'][$index] == 'digital-item')
                          @if(isset($m['items']['type'][$index]) && ! empty($m['items']['type'][$index]))
                           @if(user_access_login())
                             <span class="sr-only">{{tr('Available files for instant download')}}: </span>
                             [<a href="{{UrlPrefixes::$item_opac}}{{$m['items']['id'][$index]}}/download/0">{{$m['items']['type'][$index]}}</a>]
                           @endif
                          @endif
                         @endif
                        @endforeach
                       @endif
                    </span>
                    @endforeach
                  @else
                  <span class="res_l">{{tr('Manifestations of work')}}: </span>
                    <ol type="1">
                      @foreach ($r['public_lines'] as $m)
                        <li><a href="{{UrlPrefixes::$item_opac}}{{$m['id']}}">{{$m['title']}}</a>
                        @if(isset($m['items']) && ! empty($m['items']))
                         @foreach ($m['items']['id'] as $index => $item_id )
                          @if ($m['items']['object-type'][$index] == 'physical-item')
                           @if(isset($m['items']['sublocation'][$index]) && ! empty($m['items']['sublocation'][$index]))
		                         ({{$m['items']['sublocation'][$index]}}@if(isset($m['items']['classification'][$index]) && ! empty($m['items']['classification'][$index])) - {{$m['items']['classification'][$index]}}@endif)
		                       @elseif(isset($m['items']['classification'][$index]) && ! empty($m['items']['classification'][$index]))
		                         [{{$m['items']['classification'][$index]}}]
		                       @endif
                          @endif
                          @if ($m['items']['object-type'][$index] == 'digital-item')
                           @if(isset($m['items']['type'][$index]) && ! empty($m['items']['type'][$index]))
                            @if(user_access_login())
                              <span class="sr-only">{{tr('Available files for instant download')}}: </span>
                              [<a href="{{UrlPrefixes::$item_opac}}{{$m['items']['id'][$index]}}/download/0">{{$m['items']['type'][$index]}}</a>]
                            @endif
                           @endif
                          @endif
                         @endforeach
                        @endif
                      </li>
                      @endforeach
                    </ol>
                  @endif
              @endif
            @endif


{{--<!--         <div style ="font-size:11px;"> -->--}}
{{--<!--             @if($highlighting->getResult($document->id)) -->--}}
{{--<!--               @foreach ($highlighting->getResult($document->id) as $field => $highlight) -->--}}
                <?php  // echo tr($field) . ': <span style ="font-size:11px;font-style: italic;">"' . implode(' (...) ', $highlight) . '..."</span><br/>'; ?>
{{--<!--               @endforeach -->--}}
{{--<!--             @endif -->--}}
{{--<!--           </div> -->--}}

            @if (isset($r['individual_works']))
              @foreach ($r['individual_works'] as $iw)
                @if(isset($iw['id']) && ! empty($iw['id']))
                  <span class="res_l">{{tr($relation_work_wholepart_map['ea:relation:containedInIndividual'])}}:</span>
                  <span class="res_l"><a href="{{UrlPrefixes::$item_opac}}{{$iw['id']}}">{{$iw['label']}}</a></span>
                @endif
              @endforeach
            @endif

            @if (isset($r['contained_in_contribution']))
              @foreach ($r['contained_in_contribution'] as $cw)
                @if(isset($cw['id']) && !empty($cw['id']))
                  <span class="res_l">{{tr($relation_work_wholepart_map['ea:relation:containedInContributions'])}}:</span>
                  <span class="res_l"><a href="{{UrlPrefixes::$item_opac}}{{$cw['id']}}">{{$cw['label']}}</a></span>
                @endif
              @endforeach
            @endif

           @if (Config::get('arc.SOLR_DEV',0))
              <div class="otype" style="float: right"  >{{$ft}}   {{$id}}</div>
           @endif





            <div class="clearfix"></div>
      </li>
