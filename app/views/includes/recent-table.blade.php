@foreach ($members as $row)
	<?php

	$obj_type = $row['obj_type'];
	$thumb = $row['thumb'];
	$pages = $row['pages'];
	$folder_flag = $row['folder'];
	$folders = $row['folders'];

	if (! PUtil::isEmpty($folders)) {
		$folders = sprintf('(%s)', $folders);
	}

	if (! empty($pages)) {
		$pagesStr = sprintf('<br/> %s: %s', tr('σελιδες'), $pages);
	} else {
		$pagesStr = "";
	}

	if ($folder_flag) {
		$txt = ($obj_type == Config::get('arc.DB_OBJ_TYPE_WEBSITE')) ? tr('σελίδες') : ($obj_type == Config::get('arc.DB_OBJ_TYPE_SILOGI')) ? tr('τεκμήρια') : tr('τεύχη');
		$tefxiStr = sprintf('<br/>%s:%s', $txt, PUtil::coalesce($row['issue_cnt'], '1'));
	}else{
		$tefxiStr = "";
	}

	if ($edit_flag){
		$tefxiStr .= sprintf('<br>id: %s 	&#160; 	&#160; status: <a href="/archive/recent?s=%s">%s</a>  ', $row['item_id'], $row['status'], $row['status']);
		if (! empty($row['user_create'])) {
			$tefxiStr .= sprintf('&#160; &#160;  create: %s', $row['user_create']);
		}
		if (! empty($row['user_update'])) {
			$tefxiStr .= sprintf('&#160; &#160;  update: %s', $row['user_update']);
		}
		$dt = PUtil::coalesce($row['dt_update'], $row['dt_create']);
		$phpdate = strtotime($dt);
		$tefxiStr .= sprintf('&#160; &#160; %s', date('d/m/Y', strtotime($dt)));
	}

	$opac = isset($row['jdata']) ? new OpacHelper($row['jdata']) : new OpacHelper(null);

// 	FLAG ORG:ID PROVIDER
// 	if ($opac->hasOpac1('flags')) {
// 		$flags = $opac->opac1('flags');
// 		$matches  = preg_grep ('/ORG:/', $flags);
// 		$organizations = array_values($matches);
// 		if(!empty($organizations)){
// 			$tefxiStr .= '&#160; 	&#160; provider: '. $organizations[0];
// 		}
// 	}

// 	NAME PROVIDER
		if ($opac->hasOpac1('organization')) {
			$organizations_name = $opac->opac1('organization');
			if(!empty($organizations_name)){
				$tefxiStr .= '&#160; 	&#160; provider: <b><i>'. $organizations_name . '</i></b>';
			}
		}

	if ($opac->hasOpac1('label')) {
		$title = $opac->opac1('label');
	}else{
		$title = $row['title'];
	}

	if ($opac->hasOpac1('public_title')){
		$public_title_data = $opac->opac1('public_title');
		$t = $public_title_data['title'];
		$id = $public_title_data['id'];
	}

	if ($opac->hasOpac1('public_lines')){
		$public_lines = $opac->opac1('public_lines');
	}

	$obj_type_name = tr($obj_type_names[$obj_type]);
	if ($obj_type == Config::get('arc.DB_OBJ_TYPE_AUTH-WORK')){
		$bg_img= 'work.jpg';
	}elseif($obj_type == Config::get('arc.DB_OBJ_TYPE_AUTH-EXPRESSION')){
		$bg_img= 'expression.jpg';
	}elseif($obj_type == Config::get('arc.DB_OBJ_TYPE_AUTH-MANIFESTATION')){
		$bg_img= 'manif.jpg';
	}elseif($obj_type == Config::get('arc.DB_OBJ_TYPE_AUTH-PERSON')){
		$bg_img= 'person.jpg';
	}elseif($obj_type == Config::get('arc.DB_OBJ_TYPE_AUTH-FAMILY')){
		$bg_img= 'family.jpg';
	}elseif($obj_type == Config::get('arc.DB_OBJ_TYPE_AUTH-ORGANIZATION')){
		$bg_img= 'organ.jpg';
	}elseif($obj_type == Config::get('arc.DB_OBJ_TYPE_AUTH-PLACE')){
		$bg_img= 'map.jpg';
	}else{
		$bg_img= 'subject.jpg';
	}
	?>

	<tr>
			<td class="std1">
			   	@if ($folder_flag)
				   	<a href="/archive/item/{{$row[5]}}?lang={{$lang}}"><img class="mimeico {{$img_class2}}" alt="folder" src="/_assets/img/items/folder.png"/></a>
			 	  	<br/>{{$txt}}:&nbsp;{{PUtil::coalesce($row['issue_cnt'],'1')}}
			 		@else
			 			<span class="obj_type" style="background-image:url(/_assets/img/items/{{$bg_img}});">{{$obj_type_name}}</span>
		 			@endif
			</td>
			<td style="width:100%">
					@if (!empty($thumb))
						<div class="col-md-11">
					@else
						<div class="col-md-12">
					@endif
					@if ($opac->hasOpac1('public_title'))
						<a href="/archive/item/{{$id}}">{{{$t}}}</a>
									@if ($opac->hasOpac1('public_lines'))
											<dl class="opac_list">
											<dt>{{tr('Available versions')}}</dt>
											@foreach ($public_lines as $line)
											<dd> <a href="/archive/item/{{$line['id']}}">{{$line['title']}}</a></dd>
											@endforeach
											</dl>
									@else
									<br/>
									@endif
						{{$row[3]}} {{$row[2]}} {{$folders}} {{$pagesStr}} {{$tefxiStr}}
					@else
						<a href="/archive/item/{{$row[5]}}?lang={{$lang}}">{{{$title}}}</a><br/> {{$row[3]}} {{$row[2]}} {{$folders}} {{$pagesStr}} {{$tefxiStr}}
					@endif
					</div>
					@if (!empty($thumb))
						<div class="col-md-1"><span  aria-hidden="true" class="thumb_s_bg_img" style="background-image:url(/media/{{$thumb}});"></span></div>
					@else
						@if ($obj_type == 'silogi')
							<a href="/archive/item/{{$row[5]}}?lang={{$lang}}" title="{{htmlspecialchars($row[1])}}"><img class="{{$img_class1}}" src="/_assets/img/books4_64.png" alt="{{htmlspecialchars($row[1])}}"/></a>
						@endif
					@endif
			</td>
	</tr>

@endforeach