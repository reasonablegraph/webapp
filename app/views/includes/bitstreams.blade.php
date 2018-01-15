@if (!empty($bitstreams))
	<div class="panel panel-primary">
		<!--  <div class="panel-heading">
			<h3 class="panel-title">{{{tr('bitstreams')}}}</h3>
		</div>
		<div class="panel-body"> -->
			<table
					class="table table-bordered table-condensed table-striped table-hover">
				<thead class="a_thead">
					<tr>
						<th colspan="8"><span class="a_shead" >{{{tr('Bitstreams')}}}</span></th>
					</tr>
				</thead>
				<tbody>
					@foreach($bitstreams as $v)

					<tr style="vertical-align: top;">
						@if ($edit_link)
						<td><a href="/prepo/edit_bitstream?bid={{{$v[6]}}}">[{{{$v[6]}}}]</a></td>
						@endif
						<td>{{{$v['artifact_id']}}}</td>
						<td>{{{$v[5]}}} ({{{$v[4]}}})</td>
						<td>@if (! empty($v['replaces'])) <a
							href="/prepo/edit_bitstream?bid={{{$v['replaces']}}}">[{{{$v['replaces']}}}]</a>
							@endif
						</td>

						<td>{{{$v[3]}}}</td>
						<td><a href="/archive/download?i={{{$item_id}}}&d={{{$v[1]}}}">{{{$v[0]}}}</a>
							&nbsp; ({{{ceil($v[2]/1000)}}}K) <a href="/archive/download?i={{{$item_id}}}&d={{{$v[1]}}}&ds=0"><span
								class="glyphicon glyphicon-download-alt" aria-hidden="true"></span><span
								class="sr-only">{{{tr('Download w/out DigitalSignature')}}}</span></a> @if (!
							empty($v['download_fname'])) <br />download&nbsp;file&nbsp;name:&nbsp;{{{$v['download_fname']}}}
							@endif @if (! empty($v['description'])) <br />description:&nbsp;{{{$v['description']}}}
							@endif @if (! empty($v['redirect_url'])) <br />redirect_url:&nbsp;{{{$v['redirect_url']}}}
							@endif</td>
						<td>{{{$v[8]}}}</td>
						@if ($edit_link)
						<td style="text-align: right;">
							<a href="/prepo/edit_bitstream?bid={{{$v[6]}}}"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span><span class="sr-only">{{{tr('Edit')}}}</span></a>
							<!--  	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a
								href="/prepo/edit_step2?i={{{$v[9]}}}"><span
									class="glyphicon glyphicon-file" aria-hidden="true"></span><span
									class="sr-only">{{{tr('Item')}}}</span></a>-->
						</td>
						@endif
					</tr>
					@endforeach
				</tbody>
			</table>
		<!-- </div> -->
	</div>
@endif


