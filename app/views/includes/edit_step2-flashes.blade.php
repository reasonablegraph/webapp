

{{-- //DRYLL --}}
@if (!empty($book_notification))
	<div class="alert alert-success alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
     {{tr('success_book_notification')}}
	</div>
@elseif (!empty($issue_notification))
	<div class="alert alert-success alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
     {{tr('success_issue_notification')}}
	</div>
@elseif (!empty($periodic_notification))
	<div class="alert alert-success alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
     {{tr('success_periodic_notification')}}
	</div>
@endif
{{--  //** --}}