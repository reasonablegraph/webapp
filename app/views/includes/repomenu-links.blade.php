<?php $item_id= 3;?>

<div class="row">
	<ul id="admin_area" class="nav nav-pills">
		<li><a href="/prepo/reset-graph"><span class="glyphicon glyphicon-refresh" aria-hidden="true"></span> <?php echo tr('Reset graph');?></a></li>
		<li><a href="/prepo/reset-lock"><span class="glyphicon glyphicon-lock" aria-hidden="true"></span> <?php echo tr('Reset Lock Transaction');?></a></li>
		<li><a href="/prepo/marc-export"><span class="glyphicon glyphicon-export" aria-hidden="true"></span> <?php echo tr('Marc Export');?></a></li>
		<li><a href="/prepo/marc-import"><span class="glyphicon glyphicon-import" aria-hidden="true"></span> <?php echo tr('Marc Import');?></a></li>

		<li><a href="/prepo/update_folder_thumbs"><span class="glyphicon glyphicon-picture" aria-hidden="true"></span> <?php echo tr('update folder thumbnails');?></a></li>
		<li style="float:right;"><a href="{{{UrlPrefixes::$cataloging}}}"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span> <?php echo tr('Cataloging');?></a></li>

	<!--	<li style="float:right;"><a href="/prepo/export_item?i="><span class="glyphicon glyphicon-export" aria-hidden="true"></span> Export</a></li> -->

	</ul>
	<br>
</div>
