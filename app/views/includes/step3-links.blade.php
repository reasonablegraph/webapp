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
	</ul>
</div>
