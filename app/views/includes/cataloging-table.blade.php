@foreach ($results as $r)

	<?php
			$edit_link = true;
			if ( $edit_lock_owner && $r['user_create']!= $user && !$is_admin){
				$edit_link = false;
			}
	?>

<tr>
<td class="std1"  style="vertical-align:middle;">
 <!-- {{{tr($r['obj_type_display'])}}} -->
<?php
$obj_type_name = tr('cat_'.$r['obj_type_display']) ;

if ($r['obj_type'] == Config::get('arc.DB_OBJ_TYPE_AUTH-WORK')){
	$bg_img= 'work.jpg';
}elseif($r['obj_type'] == Config::get('arc.DB_OBJ_TYPE_AUTH-EXPRESSION')){
		$bg_img= 'expression.jpg';
}elseif($r['obj_type'] == Config::get('arc.DB_OBJ_TYPE_AUTH-MANIFESTATION')){
	$flags = json_decode($r['flags_json'],true);
	if(!empty($flags) && in_array('IS:issue',$flags)){
		$obj_type_name = tr('Issue of periodic');
	}
	$bg_img= 'manif.jpg';
}elseif($r['obj_type'] == Config::get('arc.DB_OBJ_TYPE_AUTH-PERSON')){
	$bg_img= 'person.jpg';
}elseif($r['obj_type'] == Config::get('arc.DB_OBJ_TYPE_AUTH-FAMILY')){
	$bg_img= 'family.jpg';
}elseif($r['obj_type'] == Config::get('arc.DB_OBJ_TYPE_AUTH-ORGANIZATION')){
		$bg_img= 'organ.jpg';
}elseif($r['obj_type'] == Config::get('arc.DB_OBJ_TYPE_AUTH-PLACE')){
			$bg_img= 'map.jpg';
}elseif($r['obj_type'] == Config::get('arc.DB_OBJ_TYPE_AUTH-CONCEPT')){
	$flags = json_decode($r['flags_json'],true);
	if(!empty($flags) && in_array('IS:category',$flags)){
		$obj_type_name = tr('Category');
	}
	$bg_img= 'subject.jpg';
}elseif($r['obj_type'] == Config::get('arc.DB_OBJ_TYPE_AUTH-EVENT')){
	$flags = json_decode($r['flags_json'],true);
	if(!empty($flags) && in_array('IS:conference',$flags)){
		$obj_type_name = tr('Conference');
	}
	$bg_img= 'subject.jpg';
}else{
// 	$bg_img= 'document.png';
	$bg_img= 'subject.jpg';
}
		printf('<span class="obj_type" style="background-image:url(/_assets/img/items/%s);">%s</span>',$bg_img,$obj_type_name);
	?>
</td>
<td style="margin:auto; vertical-align:middle; text-align:center;">
@if (! empty($r['thumb']))
	<img src="{{{UrlPrefixes::$media}}}/{{{$r['thumb']}}}"/>
@else
	<img style="width:65px;" src="{{{UrlPrefixes::$assets_img}}}/no-image.jpg"/>
	 @endif
</td>

<td style="width:100%">

 <?php  //echo '<pre>'; print_r($r); echo '</pre>';
$json = json_decode($r['jdata'], true);
if(!empty($json['label'])){
	$label= $json['label'];
}else{
	$label = $r['title'];
}
?>

@if ($r['obj_type'] == 'auth-work')
		<b><a href="{{{UrlPrefixes::$item_opac}}}{{{$r['id']}}}" class='work_link'>{{{$label}}}</a></b>
		<!--({{{$r['id']}}})-->
		@if ($edit_link)
		<a href="{{{UrlPrefixes::$item_edit}}}{{{$r['id']}}}" class='work_edit'>[edit]</a>
		@endif
		<a href="{{{UrlPrefixes::$item_admin}}}{{{$r['id']}}}" class='work_edit'>[admin]</a>
		<a href="{{{UrlPrefixes::$item_opac}}}{{{$r['id']}}}" class='work_edit'>[opac]</a>
		<br/>

		@if (isset($r['manifestations']) && count($r['manifestations']) > 0)
		<?php
			$dispaly_default_expresion = Config::get('arc.dispaly_default_expresion',1);
		?>
		@if ($dispaly_default_expresion)
			<b> &nbsp;&nbsp; ↳ &nbsp; <span class="expres_link"><b><i><u>expression:</u></i></b></span> {{{tr('default expresion')}}}</b><br/>
		@endif

			@foreach ($r['manifestations'] as $m)
					<b>
					@if ($dispaly_default_expresion)
					&nbsp;&nbsp;
					@endif
					&nbsp;&nbsp; ↳ &nbsp; <span class='manif_link'><b><i><u>manifestation:</u></i></b></span> <a href="{{{UrlPrefixes::$item_opac}}}{{{$m['id']}}}" class='manif_edit' >{{{$m['title']}}}</a></b>
					<!--({{{$m['id']}}})-->
					@if ($edit_link)
					<a href="{{{UrlPrefixes::$item_edit}}}{{{$m['id']}}}" class='manif_edit'>[edit]</a>
					@endif
					<a href="{{{UrlPrefixes::$item_admin}}}{{{$m['id']}}}" class='manif_edit'>[admin]</a>
					<a href="{{{UrlPrefixes::$item_opac}}}{{{$m['id']}}}" class='manif_edit'>[opac]</a>
					<br/>
					@if (isset($m['items']))
					@foreach ($m['items'] as $i)  <?php // echo '<pre>'; print_r($i); echo '</pre>'; ?>
						<b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ↳ &nbsp; <span class="digital_item_link"><b><i><u>item:</u></i></b></span> <a href="{{{UrlPrefixes::$item_opac}}}{{{$i['id']}}}" class='digital_item_edit'>@if (isset($i['label'])) {{{$i['label']}}} @endif</a></b>
							<!--({{{$i['id']}}})-->
							@if ($edit_link)
							<a href="{{{UrlPrefixes::$item_edit}}}{{{$i['id']}}}" class='digital_item_edit'>[edit]</a>
							@endif
							<a href="{{{UrlPrefixes::$item_admin}}}{{{$i['id']}}}" class='digital_item_edit'>[admin]</a>
							<a href="{{{UrlPrefixes::$item_opac}}}{{{$i['id']}}}" class='digital_item_edit'>[opac]</a>
							<br/>
					@endforeach
					@endif

			@endforeach
		@endif

		@if (isset($r['expressions']) && count($r['expressions']) > 0)
		@foreach ($r['expressions'] as $e)
		<b> &nbsp;&nbsp; ↳ &nbsp; <span class="expres_link"><b><i><u>expression:</u></i></b></span> <a href="{{{UrlPrefixes::$item_opac}}}{{{$e['id']}}}" class='expres_edit'>{{{$e['title']}}}</a></b>
		<!--({{{$e['id']}}})-->
		@if ($edit_link)
		<a href="{{{UrlPrefixes::$item_edit}}}{{{$e['id']}}}" class='expres_edit'>[edit]</a>
		@endif
 		<a href="{{{UrlPrefixes::$item_admin}}}{{{$e['id']}}}" class='expres_edit'>[admin]</a>
 		<a href="{{{UrlPrefixes::$item_opac}}}{{{$e['id']}}}" class='expres_edit'>[opac]</a>
 		<br/>

				@foreach ($e['manifestations'] as $m)
				<b> &nbsp;&nbsp;&nbsp;&nbsp; ↳ &nbsp; <span class='manif_link'><b><i><u>manifestation:</u></i></b></span> <a href="{{{UrlPrefixes::$item_opac}}}{{{$m['id']}}}" class='manif_edit'>{{{$m['title']}}}</a></b>
				<!--({{{$m['id']}}})-->
				@if ($edit_link)
				<a href="{{{UrlPrefixes::$item_edit}}}{{{$m['id']}}}" class='manif_edit'>[edit]</a>
				@endif
				<a href="{{{UrlPrefixes::$item_admin}}}{{{$m['id']}}}" class='manif_edit'>[admin]</a>
				<a href="{{{UrlPrefixes::$item_opac}}}{{{$m['id']}}}" class='manif_edit'>[opac]</a>
				<br/>

							@if (isset($m['items']))
							@foreach ($m['items'] as $i)
							<b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ↳ &nbsp; <span class="digital_item_link"><b><i><u>item:</u></i></b></span> <a href="{{{UrlPrefixes::$item_opac}}}{{{$i['id']}}}" class='digital_item_edit'> {{{$i['label']}}}</a></b>
							<!--({{{$i['id']}}})-->
							@if ($edit_link)
							<a href="{{{UrlPrefixes::$item_edit}}}{{{$i['id']}}}" class='digital_item_edit'>[edit]</a>
							@endif
							<a href="{{{UrlPrefixes::$item_admin}}}{{{$i['id']}}}" class='digital_item_edit'>[admin]</a>
							<a href="{{{UrlPrefixes::$item_opac}}}{{{$i['id']}}}" class='digital_item_edit'>[opac]</a>
							<br/>
							@endforeach
							@endif

				@endforeach

		@endforeach
		@endif

		@if (isset($r['individual_works']))
				 @foreach ($r['individual_works'] as $iw)
						@if(isset($iw['id']) && ! empty($iw['id']))
						<span class="res_l">{{$relation_work_wholepart_map['ea:relation:containedInIndividual']}}:
						<a class="work_link" href="{{UrlPrefixes::$item_opac}}{{$iw['id']}}"><b>{{$iw['label']}}</b></a></span>
						@endif
				 @endforeach
			@endif

		@if (isset($r['contained_in_contribution']))
			@foreach ($r['contained_in_contribution'] as $cw)
				@if(isset($cw['id']) && !empty($cw['id']))
					<span class="res_l">{{$relation_work_wholepart_map['ea:relation:containedInContributions']}}:
					<a class="work_link" href="{{UrlPrefixes::$item_opac}}{{$cw['id']}}"><b>{{$cw['label']}}</b></a></span>
				@endif
			@endforeach
		@endif

		@if (isset($r['contained_in_document']))
			@foreach ($r['contained_in_document'] as $dw)
				@if(isset($dw['id']) && !empty($dw['id']))
					<span class="res_l">{{$relation_work_wholepart_map['ea:relation:containedInDocuments']}}:
					<a class="work_link" href="{{UrlPrefixes::$item_opac}}{{$dw['id']}}"><b>{{$dw['label']}}</b></a></span>
				@endif
			@endforeach
		@endif

@elseif ($r['obj_type'] == 'auth-manifestation')
	<b><a href="{{{UrlPrefixes::$item_opac}}}{{{$r['id']}}}" class='manif_link'>{{{ $r['title'] }}}</a></b>
	<!--({{{$r['id']}}})-->
	@if ($edit_link)
	<a href="{{{UrlPrefixes::$item_edit}}}{{{$r['id']}}}" class='manif_edit'>[edit]</a>
	@endif
	<a href="{{{UrlPrefixes::$item_admin}}}{{{$r['id']}}}" class='manif_edit'>[admin]</a>
	<a href="{{{UrlPrefixes::$item_opac}}}{{{$r['id']}}}" class='manif_edit'>[opac]</a>
	<br/>


		@if (isset($r['items']))
		@foreach ($r['items'] as $i)
							<b> &nbsp;&nbsp;↳ &nbsp; <span class="digital_item_link"><b><i><u>item:</u></i></b></span> <a href="{{{UrlPrefixes::$item_opac}}}{{{$i['id']}}}" class='digital_item_edit'>{{{$i['label']}}}</a></b>
							<!--({{{$i['id']}}})-->
							@if ($edit_link)
							<a href="{{{UrlPrefixes::$item_edit}}}{{{$i['id']}}}" class='digital_item_edit'>[edit]</a>
							@endif
							<a href="{{{UrlPrefixes::$item_admin}}}{{{$i['id']}}}" class='digital_item_edit'>[admin]</a>
							<a href="{{{UrlPrefixes::$item_opac}}}{{{$i['id']}}}" class='digital_item_edit'>[opac]</a>
							<br/>
		@endforeach
		@endif



@elseif ($r['obj_type'] == 'auth-expression')

	<b><a href="{{{UrlPrefixes::$item_opac}}}{{{$r['id']}}}" class="expres_link" >{{{ $r['title'] }}}</a></b>
	<!--({{{$r['id']}}})-->
	@if ($edit_link)
	<a href="{{{UrlPrefixes::$item_edit}}}{{{$r['id']}}}" class='expres_edit'>[edit]</a>
	@endif
	<a href="{{{UrlPrefixes::$item_admin}}}{{{$r['id']}}}" class='expres_edit'>[admin]</a>
	<a href="{{{UrlPrefixes::$item_opac}}}{{{$r['id']}}}" class='expres_edit'>[opac]</a>
	<br/>


@elseif ($r['obj_type'] == 'periodic')
	<b><a href="{{{UrlPrefixes::$item_opac}}}{{{$r['id']}}}" class="expres_link" >{{{ $r['title'] }}}</a></b>
	<!--({{{$r['id']}}})-->
	@if ($edit_link)
	<a href="{{{UrlPrefixes::$item_edit}}}{{{$r['id']}}}" class='expres_edit'>[edit]</a>
	@endif
	<a href="{{{UrlPrefixes::$item_admin}}}{{{$r['id']}}}" class='expres_edit'>[admin]</a>
	<a href="{{{UrlPrefixes::$item_opac}}}{{{$r['id']}}}" class='expres_edit'>[opac]</a>
	@if (!empty($r['issues_num']))
		<br/><b>{{tr('Issues')}}: {{$r['issues_num']}}</b>
	@endif
	<br/>


@else

	<b><a href="{{{UrlPrefixes::$item_opac}}}{{{$r['id']}}}"  class='other_link' >{{{$label}}}</a></b>
	<!--({{{$r['id']}}})-->
	@if ($edit_link)
	<a href="{{{UrlPrefixes::$item_edit}}}{{{$r['id']}}}" class='other_link'>[edit]</a>
	@endif
	<a href="{{{UrlPrefixes::$item_admin}}}{{{$r['id']}}}" class='other_link'>[admin]</a>
	<a href="{{{UrlPrefixes::$item_opac}}}{{{$r['id']}}}" class='other_link'>[opac]</a>
	<br/>


@endif


	<div style="text-align:right;"> {{{$r['user_create']}}} : {{{$r['user_update']}}} :  {{{$r['dt_update']}}} </div>

</td>
</tr>
@endforeach
