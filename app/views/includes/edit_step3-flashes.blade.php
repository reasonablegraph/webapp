<?php
$msg = array();
$msg['1'] = 'Your submission for the new record is being processed in the background. The new record will be available in a few moments.';
$msg['2'] = 'Your submission for the updated record is being processed in the background. The updated record will be refreshed in a few moments.';
$s_pend = 'Records are being processed in the background, you may experience delays adding or editing records. Number of records in queue: ';
?>

@if (isset($submit_status) && isset($msg[$submit_status]))
<div class="alert alert-success alert-dismissible" role="alert">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>

	<?= tr($msg[$submit_status]) ?>
</div>
@endif

@if (isset($submits_pending) && intval($submits_pending) > 1)
	<div class="alert alert-success alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>

        <?= tr($s_pend) ?> <?= intval($submits_pending) ?>
	</div>
@endif


@if (isset($inprocess_reset) && $inprocess_reset>0)
<div class="alert alert-success alert-dismissible" role="alert">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	{{tr("Warning")}}: {{tr("reset graph in process... please wait..!")}}
</div>
@endif



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
