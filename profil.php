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

function draw_profile($userid) {
	$data = get_userdata($userid);

//    include_once '../simple-facebook-connect/facebook-platform/facebook.php';
  //  $fb=new Facebook($options['api_key'], $options['app_secret']);
//    $fbuid=$fb->get_loggedin_user();

    /* Start */
	$o = "<tr>\n<td class=\"user_infobox\">";
    $o .= "<div class=\"container\">";

    /* Legg til bilde */ 
	if( isset($data->bilde) )
        $bilde = "<img src=\"".get_user_meta($userid,'bilde',true)."\" width=50px \>";
    
    else if ($data->fbuid){
        //var_dump($data->fbuid);
//        $test = $fb->api_client->fql_query('SELECT uid, pic_square, first_name FROM user WHERE uid = ' . $data->fbuid);
        //var_dump($test);
        $bilde = $test;
        $bilde = get_avatar($userid, 50, "", "bilde"); /* Bare midlertidig, til vi får facebook-bilder opp å gå */
    }
    else{
        $bilde = get_avatar($userid, 50, "", "bilde");
    }
    $o .= "<div class=\"avatar\">".$bilde."</div>";

	/* Legg til navn. */
	$o .= $data->first_name." (".$data->user_login.")<br />";

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


    $o .= "<br/>";    
 
	if( isset($data->stud_ret) ) {
        $o .= "Studieretning: ".$data->stud_ret."<br/>";
    }
    if( isset($data->stud_ar) ) {
        $o .= "Studie&aring;r: ".$data->stud_ar."<br/>";
    }
    /*if( isset($data->description) && $data->description != "" ) {
        $o .= "Annet: ".$data->description;
    }*/

    $o .= "</div>"; 
    $o .= "</div>"; 
    /* Avslutt og returner */
	$o .= "</td>";
	return $o;
}
function draw_group_infobox($groupnr) {
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
function draw_activity_feed($groupnr) {
	return ;
}
function draw_profiles($settings) {
	global $wpdb;

	$last_gid = 1;
	ob_start();

	//$users = $wpdb->get_results("SELECT user_id,meta_value FROM $wpdb->users JOIN $wpdb->usermeta ON {$wpdb->users}.ID = {$wpdb->usermeta}.user_id WHERE meta_key LIKE 'faddergroup' ORDER BY meta_value,user_login ASC");

	$NUM_BUDDY_GROUPS = 15;
	for($i=1; $i <= $NUM_BUDDY_GROUPS; $i++) {
		$buddys = get_buddys($i);
		if(count($buddys) > 0) {
			echo "<table>";
		}
		$first_in_group = true;
		foreach ($buddys as $buddy) {
			echo draw_profile($buddy->user_id);
			if($first_in_group) {
				echo draw_group_infobox($i);
				$first_in_group = false;
			}
			echo "</tr>\n";
		}
		if(count($buddys) > 0) {
			echo "</table>";
		}
	}
	$data = ob_get_contents();
	ob_end_clean();
	return $data;
	//add_custom_usermetafields();
	//var_dump($settings);
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

		div.user_info {
			font-size:0.8em;
			display:inline-block;
			vertical-align:top;
		}
		div.avatar {
			float:left;
		}
	</style>

<?php
}
add_action('admin_footer','add_bacon');
add_action('wp_head','add_stylesheet');

add_shortcode('profiles','draw_profiles');

/*** OPTIONS in the user pages ***/

add_action( 'show_user_profile', 'my_show_extra_profile_fields' );
add_action( 'edit_user_profile', 'my_show_extra_profile_fields' );

function my_show_extra_profile_fields( $user ) { ?>

    <h3>Faddergruppe</h3>

    <table class="form-table">

        <tr>
            <th><label for="faddergroup">Faddergruppe</label></th>

            <td>

                <?php make_dropdown_group("faddergroup", $user) ?>
                <span class="description">Vennligst velg gruppe, faddere i parantes</span>
            </td>
        </tr>


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




        
<?php if ( current_user_can('manage_options') ){

$is_f = get_user_meta( $user->ID, 'isFadder', true);
//var_dump($is_f);
//var_dump($user->ID);

print '
         <tr>
            <th><label for="isFadder">Er fadder?</label></th>

            <td>
                <input type="text" name="isFadder" id="isFadder" value="'.$is_f.'" class="regular-text" /><br />
                <span class="description">1 hvis ja, 0 hvis nei</span>
            </td>
        </tr>
        
';
}
?>


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
    
 
    if ( current_user_can('manage_options') ){
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
    


?>
