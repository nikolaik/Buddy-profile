<?php
function buddy_group_page($options) {
	ob_start();
	if(isset($_GET['n']) && is_numeric($_GET['n'])) {
		echo draw_group($_GET['n']);
	}
	else { 
		echo "Invalid group.";
	}
	$data = ob_get_contents();
	ob_end_clean();
	return $data;
}

function draw_group($group) {
	$buddys = get_buddys($group);
	$kids = get_kids($group);
	ob_start();
?>
	<div class="group_wrapper">
		<div class="group_info">
			<?=  draw_group_infobox($group); ?>
		</div>
		<br />
		<div class="buddy_list">
			<h3>Faddere</h3>
			<?php
			foreach ($buddys as $buddy) {
				echo draw_small_profile($buddy->user_id);
				echo "</tr>\n";
			}
			?>
		</div>
		<div class="kids_list">
			<h3>Barn</h3>
			<?php
			foreach ($kids as $kid) {
				echo draw_small_profile($kid->user_id);
				echo "</tr>\n";
			}
			?>
		</div>
	</div>
<?php
	$data = ob_get_contents();
	ob_end_clean();
	return $data;
}
add_shortcode('buddy_group','buddy_group_page');
?>
