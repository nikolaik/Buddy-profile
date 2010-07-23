<?php
function add_buddy($userid) {
	$result = update_user_meta($userid,"isFadder",true);
	return $result;
}
function is_buddy($userid) {
	return get_user_meta($userid,'isFadder',true) == 1;
}
function get_lasttweet($twittername) {
	return;
}
function add_bacon($moo) {
	echo '<b>Expecting bacon down here?</b>';
	return;
}


function num_kids($groupnr){
    global $wpdb;

    $n = $wpdb->get_var($wpdb->prepare("SELECT count(user_id) as antall
        FROM $wpdb->usermeta AS a
        WHERE a.`meta_key` = '%s' 
        AND a.`meta_value` = %d
        AND a.user_id NOT IN (
        SELECT user_id
        FROM $wpdb->usermeta 
        WHERE `meta_key` = %s AND `meta_value` = %d
        )", "faddergroup", $groupnr, "isFadder", 1));
	return $n;
}
function num_buddys($groupnr) {
	global $wpdb;

    $n = $wpdb->get_var($wpdb->prepare("SELECT count(user_id)
        FROM $wpdb->usermeta AS a
        WHERE a.`meta_key` = '%s' 
        AND a.`meta_value` = %d
        AND a.user_id IN (
        SELECT user_id
        FROM $wpdb->usermeta 
        WHERE `meta_key` = '%s' AND `meta_value` = %d
        )", "faddergroup", $groupnr, "isFadder", 1));
	return $n;
}
function get_buddys($groupnr) {
	global $wpdb;

    $buddy_ids = $wpdb->get_results($wpdb->prepare("SELECT user_id
        FROM $wpdb->usermeta AS a
        WHERE a.`meta_key` = '%s' 
        AND a.`meta_value` = %d
        AND a.user_id IN (
        SELECT user_id
        FROM $wpdb->usermeta 
        WHERE `meta_key` = '%s' AND `meta_value` = %d
        ) ", "faddergroup", $groupnr, "isFadder", 1));

	return $buddy_ids;
}
function get_kids($groupnr) {
	global $wpdb;

    $kids_ids = $wpdb->get_results($wpdb->prepare("SELECT user_id
        FROM $wpdb->usermeta AS a
        WHERE a.`meta_key` = '%s' 
        AND a.`meta_value` = %d
        AND a.user_id NOT IN (
        SELECT user_id
        FROM $wpdb->usermeta 
        WHERE `meta_key` = '%s' AND `meta_value` = %d
        ) ", "faddergroup", $groupnr, "isFadder", 1));

	return $kids_ids;
}

function make_dropdown_group($id, $user){
    global $wpdb;
    $groups = $wpdb->get_results("SELECT * FROM fad_gruppe");
    $chosen_gr = esc_attr (get_the_author_meta( 'faddergroup', $user->ID ));
    $fadder = $user->isFadder;
    $return = "";
    $first = 1;
    $current = "";
    print '<select name="'.$id.'">';
    foreach($groups as $group) {
	if ($group->navn != $current){
		if ($first != 1) print '</optgroup>';
		print ' <optgroup label="'.$group->navn.'"> ';
		$first = 1;
		$current = $group->navn;
	}
        $faddere = fadder_in_group($group);
        if ($group->id == $chosen_gr) $tillegg = "selected";
        else $tillegg ="";
	if ($fadder == 1 || time() < gmmktime(0, 0, 0, 8, 14, 2010) ) $info = "Plasser: ". $group->antall." Fadderbarn: ".$group->ant_barn;
        print '<option '.$tillegg.' value="'.$group->id.'">Gruppe: '.$group->id.' ('.$faddere.') '.$info.'</option>';
//        echo var_dump($group);
    }
    print '</optgroup>';
   print '</select><br/>';
}




function fadder_in_group($group){
    global $wpdb;
    $res = $wpdb->get_results("SELECT user_login,user_id FROM fad_users JOIN fad_usermeta ON fad_users.ID = fad_usermeta.user_id WHERE meta_key LIKE 'faddergroup' AND meta_value = {$group->id}");
    $return = "";
    $first = 1;
    foreach($res as $fadder) {
        if (get_user_meta($fadder->user_id,'isFadder',true) == 1){
            if ($first != 1) $return.=", ";
            $return.=$fadder->user_login;
            $first = 0;
        }
    }
    return $return;
}
/*function kids_in_group($group){
    global $wpdb;
    $res = $wpdb->get_results("SELECT user_login,user_id FROM fad_users JOIN fad_usermeta ON fad_users.ID = fad_usermeta.user_id WHERE meta_key LIKE 'faddergroup' AND meta_value = {$group->id}");
    $return = array();
    $first = 1;
    foreach($res as $buddy) {
        if (get_user_meta($buddy->user_id,'isFadder',true) == 0){
            $return[] = $buddy;
        }
    }
    return $return;
}*/



function count_group_score($group){
    global $wpdb;
     $res = $wpdb->get_results("SELECT sum(score) as point FROM fad_gruppe_konk NATURAL JOIN fad_konk WHERE g_id = {$group}");
    foreach($res as $score) { 
        echo $score->point;
        return $score->point;

    }
   return 150;
}

function make_dropdown_badge(){
    global $wpdb;
    $badges = $wpdb->get_results("SELECT * FROM fad_badge");
    $return = "";
    print '<select name="badge">';
    foreach($badges as $badge) {
        print '<option '.$tillegg.' value="'.$badge->b_id.'">'. $badge->b_id.': '.$badge->besk.'</option>';
    }
   print '</select><br/>';
}


function make_dropdown_konk(){
    global $wpdb;
    $badges = $wpdb->get_results("SELECT * FROM fad_konk");
    $return = "";
    print '<select name="k_id">';
    foreach($badges as $badge) {
        print '<option '.$tillegg.' value="'.$badge->k_id.'">'. $badge->k_id.': '.$badge->navn.' score: '.$badge->score.'</option>';
    }
   print '</select><br/>';
}


function make_checkbox_group(){
    global $wpdb;
    $groups = $wpdb->get_results("SELECT * FROM fad_gruppe");
    $return = "";
    $i=1;
    foreach($groups as $group) {
        $faddere = fadder_in_group($group);
        
    print '<INPUT TYPE=CHECKBOX NAME="box[]" value="'.$group->id.'" />Gruppe '.$group->id.' ('.$faddere.') ';
    if ($i == 3){
	$i=1;
	echo "<br/>";
    }
    else $i++;
    }
   print '</select><br/>';
}
function get_groups(){
    global $wpdb;
    $groups = $wpdb->get_results("SELECT * FROM fad_gruppe");
	return $groups;
}

function show_profile(){
	
}
function draw_small_profile($userid) {
	$data = get_userdata($userid);
	ob_start();

    /* Start */
	echo '<div class="user_infobox">';

    /* Legg til bilde */ 
	if( isset($data->bilde) ) {
        $bilde = '<img src="'.get_user_meta($userid,'bilde',true).'" width=50px \>';
	}
	else {
        $bilde = get_avatar($userid, 50, "", "bilde");
    }
    echo '<div class="avatar">'.$bilde.'</div>';

	/* Legg til navn. */
	echo $data->first_name.' ('.$data->user_login.')<br />';

    /* Avslutt og returner */
	echo "</div>";
	$data = ob_get_contents();
	ob_end_clean();
	return $data;
}

?>
