@section('content')
<?php auth_check_mentainer(); ?>

<?php
$user = ArcApp::user();
//print_r($user);
?>




<?php //echo "<pre>"; print_r($results); echo "</pre>"; ?>



		<div class="log_report">
		<h1 class='admin item-title spool'>Researchers Report</h1>

		<div class="panel panel-primary">
			<table class="table table-bordered table-condensed table-striped table-hover">
				<thead class="a_thead">
					<tr>
						<th colspan="1">
							<span class="a_shead">Author</span>
						</th>
						<th colspan="1">
							<span class="a_shead">Number of Books lemmas</span>
						</th>
							<th colspan="1">
							<span class="a_shead">Number of Others lemmas</span>
						</th>
						<th colspan="1">
							<span class="a_shead">Number of Actors lemmas</span>
						</th>
							<th colspan="1">
							<span class="a_shead">Total</span>
						</th>
					  </th>
							<th colspan="1">
							<span class="a_shead">Score</span>
						</th>


					</tr>
				</thead>
					<tbody>
							@foreach ($results as $k => $v)
								@if ($v['total'] >0)
										<tr>
					  					<td> <a href="{{UrlPrefixes::$item_opac}}{{$v['item_id']}}" >&#160;{{$v['label']}}</a></td>
					  					<td style="text-align:right" >&#160;{{$v['lemma_book_count']}}</td>
					  					<td style="text-align:right" >&#160;{{$v['lemma_other_count']}}</td>
					  					<td style="text-align:right">&#160;{{$v['bio_count']}}</td>
					  					<td style="text-align:right">&#160;{{$v['total']}}</td>
					  					<td style="text-align:right;font-weight:bold;">&#160;{{$v['score']}}</td>
										</tr>
									@endif
							@endforeach
					</tbody>
			</table>
		</div>

	</div>


@stop