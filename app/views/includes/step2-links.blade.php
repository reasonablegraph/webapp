<div class="row">
	<ul id="admin_area" class="nav nav-pills">

		@if ($edit_link)
		<li><a href="{{{UrlPrefixes::$item_edit}}}{{{$item_id}}}"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span> <?php echo tr('Edit');?></a></li>
		@endif
		<li><a href="{{{UrlPrefixes::$item_edit_step3}}}?i={{{$item_id}}}"><span class="glyphicon glyphicon-th-large" aria-hidden="true"></span> <?php echo tr('Admin');?></a></li>
		<li><a href="{{{UrlPrefixes::$item_opac}}}{{{$item_id}}}"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> <?php echo tr('Opac');?></a></li>
		<?php
		$print_flag =  variable_get('arc_display_artifacts', 0);
		if ($print_flag):
		printf('<li><a href="/prepo/artifacts?i=%s"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span> '. tr('Artifacts'). '</a></li>',$item_id);
		endif;
		?>
		@if  ($edit_link)
		<li><a href="{{{UrlPrefixes::$thumbs}}}{{{$item_id}}}"><span class="glyphicon glyphicon-picture" aria-hidden="true"></span> <?php echo tr('Thumbnails');?></a></li>
		@endif
		@if  ($edit_link && !$bitstream_flag)
			<li><a href="{{{UrlPrefixes::$bitstreams}}}{{{$item_id}}}"><span class="glyphicon glyphicon-th-list" aria-hidden="true"></span> <?php echo tr('Bitstreams');?></a></li>
		<?php
			$print_flag =  variable_get('arc_display_notes', 0);
			if ($print_flag):
			printf('<li><a  href="/prepo/contents?i=%s"><span class="glyphicon glyphicon-copy" aria-hidden="true"></span> '. tr('Content'). '</a></li>',$item_id);
			endif;
		?>
		@endif
		<li ><a href="{{{UrlPrefixes::$cataloging}}}"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span> <?php echo tr('Cataloging');?></a></li>

		@if ($edit_link && ($status == 'error' || $obj_class = 'artifact') )
		<li style="float:right;"><a class="delete" onClick="return confirm('Are you sure you want to delete this?')" href="/prepo/delete_item?i=<?=$item_id?>"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> Delete</a></li>
		@endif

		@if ($edit_link)
		<li style="float:right;"><a href="/prepo/export_item?i=<?=$item_id?>"><span class="glyphicon glyphicon-export" aria-hidden="true"></span> Export</a></li>
		@endif
	</ul>
	<br>
</div>





