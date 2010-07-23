<?php
/*
   Plugin Name: Buddy Profiles
   Plugin URI: http://fadderuke.cyb.no
   Description: Profile plugin for the buddy system 2010, IFI, Uni. Oslo.
   Version: 0.1
   Author: Marius Næss Olsen, Nikolai Kristiansen
   Author URI: http://cyb.no
   License: GPL2
   */
/*  Copyright 2010 Nikolai Kristiansen, Marius Næss Olsen (email : nikolark@ifi.uio.no, mariusno@ifi.uio.no)

   This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

require_once("buddy_func.php");
require_once("group.php");

function draw_profile($userid) {
	$data = get_userdata($userid);

//    include_once '../simple-facebook-connect/facebook-platform/facebook.php';
  //  $fb=new Facebook($options['api_key'], $options['app_secret']);
//    $fbuid=$fb->get_loggedin_user();

    /* Start */
    $o .= "<div class=\"user_infobox\">";

    /* Legg til bilde */ 
	if( isset($data->bilde) )
        $bilde = "<img src=\"".get_user_meta($userid,'bilde',true)."\" width=50px \>";
    
    else if ($data->fbuid){
        //var_dump($data->fbuid);
//        $test = $fb->api_client->fql_query('SELECT uid, pic_square, first_name FROM user WHERE uid = ' . $data->fbuid);
        //var_dump($test);
        $bilde = get_avatar($userid, 50, "", "bilde"); /* Bare midlertidig, til vi får facebook-bilder opp å gå */
    }
    else{
        $bilde = get_avatar($userid, 50, "", "bilde");
    }
    $o .= "<div class=\"avatar\">".$bilde."</div>";

	/* Legg til navn. */
	$o .= $data->first_name." (".$data->user_login.")<br />";

	/*TODO: Add edit-link if the user is logged in and the profile belongs to the logged in user. */

    /* Legg til info om brukeren */       
    $o .= "<div class=\"user_info\">";
    
	/* Legg til Facebook */
 	if( isset($data->facebook_url) ) {
		$o .= "<a href=\"".$data->facebook_url."\"><img src=\"http://folk.uio.no/nikolark/bilder/facebook_16.png\"></a>";
	}

    /* Legg til Twitter */
	if( isset($data->twitter) ) {
		$o .= "<a href=\"http://twitter.com/".$data->twitter."\"><img src=\"http://folk.uio.no/nikolark/bilder/twitter_16.png\"></a>";# - last tweet";
	}
    /* Legg til Twitter */
	if( isset($data->lastfm_url) ) {
		$o .= "<a href=\"http://www.last.fm/user/".$data->lastfm_url."\"><img src=\"http://folk.uio.no/nikolark/bilder/lastfm_16.png\"></a>";
	}
	
	/* Legg til egen hjemmeside */
 	if( isset($data->user_url) && $data->user_url != "") {
		$o .= "<a href=\"".$data->user_url."\"><img src=\"http://folk.uio.no/nikolark/bilder/wordpress_16.png\"></a>";
	}

	/* Legg til geolocation */
	if( isset($data->fad_geotime) ) {
		$o .= "<a href=\"http://cyb.ifi.uio.no/fadderuke/kart/?type=persons&highlight=".$userid."\"><img src=\"http://folk.uio.no/mariusno/globe-europe.jpg\" height=\"17\"></a>";
	}
	


    $o .= "<br/>";    
 
	if( isset($data->stud_ret) ) {
        $o .= "Studerer ".$data->stud_ret;
    }
    if( isset($data->stud_ar) ) {
        $o .= " på ".$data->stud_ar.". året<br/>";
    }
    /*if( isset($data->description) && $data->description != "" ) {
        $o .= "Annet: ".$data->description;
    }*/

    $o .= "</div>"; 
    /* Avslutt og returner */
    $o .= "</div>"; 
	return $o;
}
function get_study_program($groupnr) {
	global $wpdb;

    $group = $wpdb->get_results("SELECT * FROM fad_gruppe WHERE id=$groupnr LIMIT 1");
	return $group[0]->navn;
}
function draw_study_program($study_program) {
	ob_start();

	echo '<div class"program_name">'.$study_program.'</div>';
	
	$data = ob_get_contents();
	ob_end_clean();
	return $data;
}
function draw_group_infobox($groupnr) {
	ob_start();
	
?>
	<div class="group_infobox">
		<span class="groupname" ><?= "<a href=\"../gruppe/?n=$groupnr\">";?>Gruppe <?= $groupnr; ?></a></span><span class="score"><?php count_group_score($groupnr); ?><img src="http://folk.uio.no/mariusno/trophy.png"/></span>
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
function draw_activity_feed($groupnr) {
	return ;
}
function draw_profiles($settings) {
	global $wpdb;

	$last_gid = 1;
	$last_gname = "";
	ob_start();

	$NUM_BUDDY_GROUPS = 16;
	for($i=1; $i <= $NUM_BUDDY_GROUPS; $i++) {
		$buddys = get_buddys($i);
		$program_name = get_study_program($i);
		if($program_name != $current) {
			echo draw_study_program($program_name);
			$current = $program_name;
		}
		if(count($buddys) > 0) {
			echo '<div class="group_list">';
		}
		$first_in_group = true;
		foreach ($buddys as $buddy) {
			if($first_in_group) {
				echo draw_profile($buddy->user_id);
				echo draw_group_infobox($i);
				$first_in_group = false;
			}
			else {
				echo draw_profile($buddy->user_id);
			}
		}
		if(count($buddys) > 0) {
			echo "</div>";
		}
		if(get_study_program($i) != $current) {
			echo draw_study_program($program_name);
			$current = $program_name;
		}
	}
	$data = ob_get_contents();
	ob_end_clean();
	return $data;
}
function add_stylesheet() {
?>
	<style type="text/css">
		.user_infobox {
			float:left;
			width:52%;
			padding:0px;
		}
		.user_infobox img {
			padding: 2px;
		}
		
		.group_list {
			display: table;
			width: 98%;
			margin: 4px;
			padding: 4px;
			border: solid 2px;
			border-color: #dddddd;
		}
		.group_infobox {
			float:right;
		}
		.group_infobox span.score {
			float:right;
			font-weight:bold;
			font-size:1.2em;
			color:#cf2020;
		}
		.group_infobox div.activityfeed {
			font-size:0.8em;
		}
		.group_infobox div.footer {
			display:inline-block;
			veritcal-algin:bottom;
		}
		.group_infobox span.connected {
			display:inline-block;
			vertical-align:bottom;
		}
		.group_infobox span.groupname {
		}

		div.user_info {
			font-size:0.8em;
			display:inline-block;
			vertical-align:top;
		}
		div.avatar {
			float:left;
		}
		div.buddy_list {
			float:left;
			width:30%;
		}
		div.kids_list {
			width:50%;
			float:right;
		}
		div.group_wrapper {
			width:100%;
		}
		.group_info {
			/*float:left;*/
		}
		.group_info span.score {
			float:right;
			font-weight:bold;
			font-size:1.2em;
			color:#cf2020;
		}
		.group_info div.activityfeed {
			font-size:0.8em;
		}
		.group_info div.footer {
			display:inline-block;
			veritcal-algin:bottom;
		}
		.group_info span.connected {
			display:inline-block;
			vertical-align:bottom;
		}
		div.program_name {
			font-weight:bold;
		}

	</style>

<?php
}
add_action('admin_footer','add_bacon');
add_action('wp_head','add_stylesheet');

add_shortcode('profiles','draw_profiles');
add_shortcode('get_map_info','get_map_info');

add_shortcode('kart','draw_maps');

/*** OPTIONS in the user pages ***/

add_action( 'show_user_profile', 'my_show_extra_profile_fields' );
add_action( 'edit_user_profile', 'my_show_extra_profile_fields' );

function my_show_extra_profile_fields( $user ) { ?>

    <h3>Faddergruppe</h3>

    <table class="form-table">

        <tr>
            <th><label for="faddergroup"><span style="font-weight:bold;">Faddergruppe</span> <span style="color:red;" >*</span></label></th>

            <td>

                <?php make_dropdown_group("faddergroup", $user) ?>
                <span class="description">Vennligst velg gruppe, faddere i parantes</span>
            </td>
        </tr>



      
<?php 
/* Funksjon for å vise "er fadder" boksen, skal etter planen bli borte 14.8.2010 */
if ( current_user_can('manage_options') || time() < gmmktime(0, 0, 0, 8, 14, 2010) ){

	$is_f = get_user_meta( $user->ID, 'isFadder', true);
	//var_dump($is_f);
	//var_dump($user->ID);

print '
         <tr>
            <th><label for="isFadder"><span style="font-weight:bold;">Er du fadder?</span> <span style="color:red;" >*</span></label></th>

            <td>
                <input type="hidden" name="isFadder" id="isFadder" value="0" class="regular-text" />';
		if ($is_f == 1) print '<input type="checkbox" name="isFadder" value="1" checked>';
		else print '<input type="checkbox" name="isFadder" value="1">';
print '		<br/>
                <span class="description">Velg denne hvis du er fadder</span>
            </td>
        </tr>
        
';
}
?>


	</table>
    	<h3>Annen info</h3>

   <table class="form-table">

	<tr>
            <th><label for="bilde">Bilde</label></th>

            <td>
                <input type="text" name="bilde" id="bilde" value="<?php echo esc_attr( get_the_author_meta( 'bilde', $user->ID ) ); ?>" class="regular-text" /><br />
                <span class="description">URL til bilde, hvis blank pr&oslash;ver den &aring; hente facebook, eller gravatar bilde</span>
            </td>
        </tr>
        
        <tr>
            <th><label for="stud_ret">Studieretning</label></th>

            <td>
                <input type="text" name="stud_ret" id="stud_ret" value="<?php echo esc_attr( get_the_author_meta( 'stud_ret', $user->ID ) ); ?>" class="regular-text" /><br />
                <span class="description">Studieretning</span>
            </td>
        </tr>
        <tr>
            <th><label for="stud_ar">Studie&aring;r</label></th>

            <td>
                <input type="text" name="stud_ar" id="stud_ar" value="<?php echo esc_attr( get_the_author_meta( 'stud_ar', $user->ID ) ); ?>" class="regular-text" /><br />
                <span class="description">Studie&aring;r</span>
            </td>
        </tr>





    </table>
    
    
    <h3>Sosial informasjon</h3>

    <table class="form-table">

        <tr>
            <th><label for="twitter">Twitter</label></th>

            <td>
                <input type="text" name="twitter" id="twitter" value="<?php echo esc_attr( get_the_author_meta( 'twitter', $user->ID ) ); ?>" class="regular-text" /><br />
                <span class="description">Please enter your Twitter username.</span>
            </td>
        </tr>
        
         <tr>
            <th><label for="facebook_url">Facebook</label></th>
            <td>
                <input type="text" name="facebook_url" id="facebook_url" value="<?php echo esc_attr( get_the_author_meta( 'facebook_url', $user->ID ) ); ?>" class="regular-text" /><br />
                <span class="description">Facebook url</span>
            </td>
        </tr>
        
        <tr>
            <th><label for="lastfm_url">Last.fm</label></th>
            <td>
                <input type="text" name="lastfm_url" id="lastfm_url" value="<?php echo esc_attr( get_the_author_meta( 'lastfm_url', $user->ID ) ); ?>" class="regular-text" /><br />
                <span class="description">Last.fm brukernavn</span>
            </td>
        </tr>

	<tr>
            <th><label for="fad_lat">Lat</label></th>
            <td>
                <input type="text" name="fad_lat" id="fad_lat" value="<?php echo esc_attr( get_the_author_meta( 'fad_lat', $user->ID ) ); ?>" class="regular-text" /><br />
                <span class="description">Latitude</span>
            </td>
        </tr>


	<tr>
            <th><label for="fad_lon">lon</label></th>
            <td>
                <input type="text" name="fad_lon" id="fad_lon" value="<?php echo esc_attr( get_the_author_meta( 'fad_lon', $user->id ) ); ?>" class="regular-text" /><br />
                <span class="description">lontitude</span>
            </td>
        </tr>

	<tr>
            <th><label for="fad_geotime">Geo time</label></th>
            <td>
                <input type="text" name="fad_geotime" id="fad_geotime" value="<?php echo esc_attr( get_the_author_meta( 'fad_geotime', $user->id ) ); ?>" class="regular-text" /><br />
                <span class="description">Time for geo</span>
            </td>
        </tr>



    </table>

    <?php }

add_action( 'personal_options_update', 'my_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'my_save_extra_profile_fields' );
function my_save_extra_profile_fields( $user_id ) {

    if ( !current_user_can( 'edit_user', $user_id ) )
        return false;

    /* Copy and paste this line for additional fields. Make sure to change 'twitter' to the field ID. */
    update_usermeta( $user_id, 'twitter', $_POST['twitter'] );
    update_usermeta( $user_id, 'faddergroup', $_POST['faddergroup'] );
    update_usermeta( $user_id, 'bilde', $_POST['bilde'] );
    update_usermeta( $user_id, 'stud_ret', $_POST['stud_ret'] );
    update_usermeta( $user_id, 'stud_ar', $_POST['stud_ar'] );
    update_usermeta( $user_id, 'facebook_url', $_POST['facebook_url'] );
    update_usermeta( $user_id, 'lastfm_url', $_POST['lastfm_url'] );
    
 
    if ( current_user_can('manage_options') || time() < gmmktime(0, 0, 0, 8, 14, 2010)){
    	/* Geodata */
	update_usermeta( $user_id, 'fad_lat', $_POST['fad_lat'] );
    	update_usermeta( $user_id, 'fad_lon', $_POST['fad_lon'] );
    	update_usermeta( $user_id, 'fad_geotime', $_POST['fad_geotime'] );
 	/* is fadder */
  	update_usermeta( $user_id, 'isFadder', $_POST['isFadder'] );
    }
}

add_action('admin_menu', 'my_plugin_menu');

function my_plugin_menu() {

  add_options_page('Buddy profile Options', 'Buddy profile', 'manage_options', 'buddy_profile', 'my_plugin_options');

}

function my_plugin_options() {
   include("buddy-admin.php" );
}
    
function draw_maps(){
	print '<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAArmJmbq77m8lzUfEaPb2ZvhRH0MZJ1dk1u0j8Vy0StsBioyX4SBRDkuc0WPtU5ftZOtCDjxUJ7nZPbw"   type="text/javascript"></script>
<script src="../wp-content/plugins/buddy_profile/functions.js" type="text/javascript"></script>
<link href="../wp-content/plugins/buddy_profile/style.css" rel="stylesheet" type="text/css"></link>


<script  type="text/javascript">
	$(function() { initialize("'.$_GET['type'].'","'.$_GET['highlight'].'"); });

</script>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>


<div id="map">
	<div id="mapi">
		<div id="map_canvas"></div>
    		<div id="placeholder"></div>
	</div>
</div>


';

/* Legg til geolocation */
if( isset($data->fad_geotime) ) {
		$o .= "<a href=\"http://cyb.ifi.uio.no/fadderuke/kart/?type=persons&highlight=".$userid."\"><img src=\"http://folk.uio.no/mariusno/globe-europe.jpg\" height=\"17\"></a>";
	}
	

}

function get_map_info(){
die("fikk kart");
}

?>
