<div class="row">



	<ul id="admin_area" class="nav nav-pills">
		@if ($edit_link)
		<li><a href="{{{UrlPrefixes::$item_edit}}}{{{$item_id}}}"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span> <?php echo tr('Edit');?></a></li>
		@endif
		<li><a href="{{{UrlPrefixes::$item_edit_step2}}}?i={{{$item_id}}}"><span class="glyphicon glyphicon-th" aria-hidden="true"></span> <?php echo tr('Detail admin');?></a></li>
		<li><a href="{{{UrlPrefixes::$item_opac}}}{{{$item_id}}}"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> <?php echo tr('Opac');?></a></li>
		<li><a href="{{{UrlPrefixes::$cataloging}}}"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span> <?php echo tr('Cataloging');?></a></li>



		@if ($item['obj_type'] == 'digital-item')
		<li><a href="{{{UrlPrefixes::$spool}}}"><span class="glyphicon glyphicon-inbox" aria-hidden="true"></span> <?php echo tr('Spool');?></a></li>
		@endif

		@if ($edit_link && $status == 'finish')
			<li style="float:right;">
				<form id="error-item" method="post" action="/prepo/edit_step2?i={{{$item_id}}}">
				<button class="delete error" type="submit" name="error" value="error" onClick="return confirm('<?php echo tr('Are you sure you want to change item status from finish to error?');?>')">
					<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>&nbsp;Error
				</button>
				</form>
			</li>
		@endif

		{{-- //DRYLL --}}
		@if ($is_book)
			<li style="float:right;">
				<form id="confirm-item" method="post" action="/prepo/edit_step3?i={{{$item_id}}}">
				<button class="send confirm" type="submit" name="book_send_email" value="Send Email" onClick="return confirm('<?php echo tr('email_alert_secretary_confirmation');?>')">
					<span class="glyphicon glyphicon-send" aria-hidden="true"></span>&nbsp;<?php echo tr('Send Email');?>
				</button>
				</form>
			</li>
		@elseif ($is_issue)
			<li style="float:right;">
				<form id="confirm-item" method="post" action="/prepo/edit_step3?i={{{$item_id}}}">
				<button class="send confirm" type="submit" name="issue_send_email" value="Send Email" onClick="return confirm('<?php echo tr('email_alert_issue_confirmation');?>')">
					<span class="glyphicon glyphicon-send" aria-hidden="true"></span>&nbsp;<?php echo tr('Send Email');?>
				</button>
				</form>
			</li>
		@elseif ($is_periodic)
			<li style="float:right;">
				<form id="error-item" method="post" action="/prepo/edit_step3?i={{{$item_id}}}">
				<button class="send confirm" type="submit" name="periodic_send_email" value="Send Email" onClick="return confirm('<?php echo tr('email_alert_secretary_confirmation');?>')">
					<span class="glyphicon glyphicon-send" aria-hidden="true"></span>&nbsp;<?php echo tr('Send Email');?>
				</button>
				</form>
			</li>
		@endif
		{{--  //** --}}


	</ul>
</div>
