<div class="panel panel-primary">
	<!--<div class="panel-heading">
		<h3 class="panel-title">{{{tr('relations')}}}</h3>
	</div>
	<div class="panel-body"> -->
		<table
			class="table table-bordered table-condensed table-striped table-hover">
			<thead class="a_thead">
				<tr>
					<th colspan="7"><span class="a_shead" >{{{tr('Inferred Relations')}}}</span></th>
				</tr>
			</thead>
			<tbody>
				@foreach ($inferred_relations as $r)
				<tr>

					@if ( isset($r['temporal_status']))
					<td>{{{$r['temporal_status']}}}</td> @endif

					<td>{{{tr($r['obj_type'])}}}</td>
					<td style="width:60px;">@if ( $r['direction'] == 'IN') <span title="<?=tr('εισερχόμενος κόμβος')?>"
						class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
						<span >{{{$r['direction']}}}</span> @endif @if (
						$r['direction'] == 'OUT') <span  title="<?=tr('εξερχόμενος κόμβος')?>"
						class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
						<span >{{{$r['direction']}}}</span> @endif


					</td>
					<td>{{{tr($r['element'])}}}</td>
					<td>{{{$r['label']}}}</td>
					<td>{{{$r['ref_item']}}}</td>
					<td class="nowrap"><a
						href="{{{UrlPrefixes::$item_edit}}}{{{$r['ref_item']}}}"><span
							class="glyphicon glyphicon-edit" aria-hidden="true"></span> <?php echo tr('Edit');?></a>
						&nbsp;&nbsp; <a
						href="{{{UrlPrefixes::$item_edit_step3}}}?i={{{$r['ref_item']}}}"><span
							class="glyphicon glyphicon-th-list" aria-hidden="true"></span> <?php echo tr('Admin');?></a>
						&nbsp;&nbsp; <a
						href="{{{UrlPrefixes::$item_opac}}}{{{$r['ref_item']}}}"><span
							class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> <?php echo tr('Opac');?></a>

						@if ($r['obj_class'] == 'auth-manifestation') &nbsp;&nbsp; <a
						href="{{{UrlPrefixes::$item_edit_step1}}}?aft=1&afti={{{$r['ref_item']}}}"><span
							class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> <?php echo tr('Add Item');?></a>
						@endif</td>

				</tr>
				@endforeach
			</tbody>
		</table>

	<!-- </div> -->
</div>

