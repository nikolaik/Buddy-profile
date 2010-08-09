<?php
function draw_profile($userid) {
	global $current_user;

	ob_start();
	$data = get_userdata($userid);

    /* Start */
    echo '<li class="user_infobox">';
	echo '<div class="user_info clearfix">';

    /* Legg til bilde */ 
	if( isset($data->bilde) ) {
        $bilde = '<img src="' . get_user_meta($userid, "bilde", true) . '" width="50px" \>';
	} 
    else {
        $bilde = get_avatar($userid, 50, "", "bilde");
    }
    echo $bilde;

    /* Legg til info om brukeren */
	echo '<div class="inner">';
    
	/* Legg til navn. */
	/*$o .= $data->first_name." (".$data->nickname.")<br />";*/
	echo '<h5 class="user_name">'.$data->first_name.' ('.$data->nickname.')';

	if(is_user_logged_in() && ($userid == $current_user->ID)) {
		echo ' <span class="color_red"><a href="'.get_bloginfo('url').'/wp-admin/profile.php">(edit)</a></span>';
	}
	echo "</h5>";

	echo '<ul class="horizontal clearfix">';
	/* Legg til Facebook */
 	if( isset($data->facebook_url) ) {
		echo '<li><a href="'.$data->facebook_url.'"><img src="http://folk.uio.no/nikolark/bilder/facebook_16.png"></a></li>';
	}

    /* Legg til Twitter */
	if( isset($data->twitter) ) {
		echo '<li><a href="http://twitter.com/'.$data->twitter.'"><img src="http://folk.uio.no/nikolark/bilder/twitter_16.png"></a></li>';
		/*TODO: Last tweet */
	}
    /* Legg til Last.FM */
	if( isset($data->lastfm_url) ) {
		echo '<li><a href="http://www.last.fm/user/'.$data->lastfm_url.'"><img src="http://folk.uio.no/nikolark/bilder/lastfm_16.png"></a></li>';
	}
	/* Legg til egen hjemmeside */
 	if( isset($data->user_url) && $data->user_url != "") {
		echo '<li><a href="'.$data->user_url.'"><img src="http://folk.uio.no/nikolark/bilder/wordpress_16.png"></a></li>';
	}
	/* Legg til geolocation */
	if( isset($data->fad_geotime) ) {
		echo '<li><a href="http://cyb.ifi.uio.no/fadderuke/kart/?type=persons&highlight='.$userid.'"><img src="http://folk.uio.no/mariusno/globe-europe.jpg" height="17"></a></li>';
	}
	echo '</ul>';
 
	echo '<div class="studie">';
	if( isset($data->stud_ret) ) {
        echo 'Studerer '.$data->stud_ret;
    }
    if( isset($data->stud_ret) && isset($data->stud_ar) ) {
        echo ' på '.$data->stud_ar.'. året';
    }
	echo '</div>';

    /* Avslutt og returner */
	echo "</div>";
	echo "</div>";
	echo "</li>"; 

	$data = ob_get_contents();
	ob_end_clean();
	return $data;
}


function draw_study_program($study_program) {
	ob_start();

	echo '<h4>'.$study_program.'</h4>';
	
	$data = ob_get_contents();
	ob_end_clean();
	return $data;
}

function draw_group_name($groupnr) {
	ob_start();

	?>
		<div class="group_name" >
			<?= "<a href=\"../gruppe/?n=$groupnr\">";?><?= $groupnr; ?></a>
		</div>
	<?php

	$data = ob_get_contents();
	ob_end_clean();
	return $data;
}

function draw_group_infobox($groupnr) {
	ob_start();
	
?>
	<div class="group_infobox">
		<?= draw_group_name($groupnr); ?>
		<span class="score" title="Poengsum i IFI-Olympiaden"><?= count_group_score($groupnr); ?><img src="http://folk.uio.no/mariusno/trophy.png"/></span>
		<div class="activityfeed">
			<span class="feedtitle">Feed:</span><br />
			<?= draw_activity_feed($groupnr); ?>
		</div>
		<div class="footer">
			<span class="connected">Tilkoblede fadderbarn: <?= "<a href=\"../gruppe/?n=$groupnr\">".num_kids($groupnr)."</a></span>"; ?> <img src="http://folk.uio.no/nikolark/bilder/add_user.png" title="Connect to group with facebook." />
		</div>
	</div>
<?php
	$data = ob_get_contents();
	ob_end_clean();
	return $data;
}

function draw_group_info($groupnr) {
	ob_start();
	
?>
	<div class="group_info">
		<span class="groupname" ><?= "<a href=\"../gruppe/?n=$groupnr\">";?>Gruppe <?= $groupnr; ?></a></span><span class="score"><?= count_group_score($groupnr); ?><img src="http://folk.uio.no/mariusno/trophy.png"/></span>
		<div class="activityfeed">
			<span class="feedtitle">Feed:</span><br />
			<?= draw_activity_feed($groupnr); ?>
		</div>
		<div class="footer">
			<span class="connected">Tilkoblede fadderbarn: <?= "<a href=\"../gruppe/?n=$groupnr\">".num_kids($groupnr)."</a></span>"; ?> <img src="http://folk.uio.no/nikolark/bilder/add_user.png" title="Connect to group with facebook." />
		</div>
	</div>
<?php
	$data = ob_get_contents();
	ob_end_clean();
	return $data;
}

/* Returns a string describing the activity.
   f.ex: '<name> join the group', 'Scored <num> points from a <contest-link>', 'Got the <award-name> award!'
 */
function draw_activity($activity) {
	ob_start();
	echo "&raquo; ";
	if( isset( $activity->u_id ) && isset( $activity->b_id)  ) {
		/* User on group got a badge */
		$user = get_userdata($activity->u_id);
		$badge = get_badge($activity->b_id);
		echo "Medlem <a href=\"#fancyboxinfo\">$user->first_name</a> fikk badgen <a href=\"#fancyboxinfo\">$badge->navn</a> <img src=\"$badge->icon\" title=\"$badge->besk\" />.";
	}
	else if( isset( $activity->u_id ) ) {
		$user = get_userdata($activity->u_id);
		/* New member */
		echo "<a href=\"#fancyboxinfo\">$user->first_name</a> koblet til gruppen.";
	}
	else if( isset( $activity->k_id ) ) {
		/* New score */
		$contest = get_contest($activity->k_id);
		echo "Scoret $contest->score poeng i <a href=\"#fancyboxinfo\">$contest->navn</a>.";
	}
	else if( isset( $activity->b_id ) ) {
		$badge = get_badge($activity->b_id);
		/* New badge */
		echo "Fikk badgen <a href=\"#fancyboxinfo\">$badge->navn</a> <img src=\"$badge->icon\" title=\"$badge->besk\" />.";
	}
	else {
		echo "Invalid acitivity ID.";
	}

	$data = ob_get_contents();
	ob_end_clean();
	return $data;
}
function draw_activity_feed($groupnr) {
	ob_start();
	$activities = get_activities($groupnr);
	$num_act = 0;

	foreach( $activities as $activity ) {
		echo '<div class="activity_item" title="'.strftime("%c", $activity->tid).'">';
		echo draw_activity($activity);
		echo '</div>';
		/* Draw a 'more'-link on the buddy-page if there are more than 3 act. tied to group. */
		$num_act++;
		/* Note: Check for the page-name. */
		if( wp_title(NULL, false) == "  Fadderne" && $num_act >= 3) {
			echo "<a href=\"gruppe/?n=$groupnr\">mer</a>...";
			break;
		}
	}

	$data = ob_get_contents();
	ob_end_clean();
	return $data;
}

function draw_profiles($settings) {
	global $wpdb;

	$current = "";
	ob_start();

	$NUM_BUDDY_GROUPS = 16;
	for($i=1; $i <= $NUM_BUDDY_GROUPS; $i++) {
		$buddys = get_buddys($i);
		$program_name = get_study_program($i);
		if($program_name != $current) {
			echo draw_study_program($program_name);
			$current = $program_name;
		}
		echo '<div class="group_list clearfix">';
		echo '<ul class="group_list_buddys">';
		foreach ($buddys as $buddy) {
			echo draw_profile($buddy->user_id);
		}
		echo "</ul>\n";
		echo draw_group_infobox($i);
		echo "</div>";
		if(get_study_program($i) != $current) {
			echo draw_study_program($program_name);
			$current = $program_name;
		}
	}
	$data = ob_get_contents();
	ob_end_clean();
	return $data;
}
function draw_small_profile($userid) {
	$data = get_userdata($userid);
	ob_start();

    /* Start */
	echo '<div class="user_smallinfobox">';

    /* Legg til bilde */ 
	if( isset($data->bilde) ) {
        $bilde = '<img src="'.get_user_meta($userid,'bilde',true).'" width=50px \>';
	}
	else {
        $bilde = get_avatar($userid, 50, "", "bilde");
    }
    echo '<div class="avatar">'.$bilde.'</div>';

	/* Legg til navn. */
	echo $data->first_name.' ('.$data->nickname.')<br />';

    /* Avslutt og returner */
	echo "</div>";
	$data = ob_get_contents();
	ob_end_clean();
	return $data;
}
?>
