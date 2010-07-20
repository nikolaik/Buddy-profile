<?php
function group_page() {
	if(isset($_GET['group']) && is_numeric($_GET['group']) {
		draw_group($_GET['group']);
	}
	else { 
		echo "invalid group";

	}
}

function draw_groups($group) {
	$groups = get_groups();
	foreach($groups as $group) {
	ob_start();
?>
	<td class="group_infobox" rowspan="<?= num_buddys($groupnr); ?>">
	<span class="groupname" >Gruppe <?= $groupnr; ?></span><span class="score"><?php count_group_score($groupnr); ?><img src="http://folk.uio.no/mariusno/trophy.png"/></span>
	<div class="activityfeed">
		<span class="feedtitle">Feed:</span><br />
		<?= draw_activity_feed($groupnr); ?>
	</div>
	<div class="footer">
		<span class="connected">Tilkoblede fadderbarn: <?= "<a href=\"./group/$groupnr/kids/\">".num_kids($groupnr)."</a></span>"; ?> <img src="http://folk.uio.no/nikolark/bilder/add_user.png" title="Connect to group with facebook." />
	</div>
	</td>
<?php
	$data = ob_get_contents();
	ob_end_clean();
	return $data;
	}
}
add_shortcode('group','group_page');
?>
