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

require_once("functions.php");
require_once("admin_profile.php");
require_once("group.php");
require_once("profiles.php");
require_once("showmap.php");
require_once("movmarker.php"); 

/*** CONFIG ***/
date_default_timezone_set('Europe/Oslo');

/*** Hook our methods to wordpress. ***/

/* Bacon to the admin footer. */
add_action('admin_footer','add_bacon');
/* Adds the stylesheet  */
add_action('wp_head','add_stylesheet');
/* Profiles page */
add_shortcode('profiles','draw_profiles');
/* Group page */
add_shortcode('buddy_group','buddy_group_page');
/* Map page */
add_shortcode('kart','draw_maps');
/* Move marker page */
add_shortcode('movemap','move_maps');

/*** OPTIONS in the user pages ***/
add_action( 'show_user_profile', 'my_show_extra_profile_fields' );
add_action( 'edit_user_profile', 'my_show_extra_profile_fields' );

/* Options in the admin panel. */
add_action('admin_menu', 'my_plugin_menu');

function my_plugin_menu() {
  add_options_page('Buddy profile Options', 'Buddy profile', 'manage_options', 'buddy_profile', 'my_plugin_options');
}
function my_plugin_options() {
	global $wpdb;

	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	if ( isset($_POST['badge_to_user']) ) {
		/* Give the user a badge */
		$data = array(
				'b_id' => $_POST['badge'],
				'u_id' => $_POST['user']
				);
		$wpdb->insert("fad_badge_user", $data);

		/* Insert into activity feed. */
		$g_id = get_user_meta( $_POST['user'], "faddergroup", true );
		$tid = time();
		$data = array(
				'g_id' => $g_id,
				'b_id' => $_POST['badge'],
				'u_id' => $_POST['user'],
				'tid' => $tid
				);
		$wpdb->insert("fad_aktivitet", $data);

		echo "Added badge to user.";
	}
	if ( isset($_POST['badge_to_group']) ) {
		$box = $_POST['box'];
		$badge = $_POST['badge'];
		/* Go through the selected groups */
		while (list ($key,$val) = @each ($box)) {
			$data = array(
				'b_id' => $_POST['badge'],
				'g_id' => $val
			);
			$wpdb->insert('fad_badge_group', $data);

			/* Insert into activity feed. */
			$tid = time();
			$data = array(
					'g_id' => $val,
					'b_id' => $_POST['badge'],
					'tid' => $tid
					);
			$wpdb->insert("fad_aktivitet", $data);
			echo "Added Badge to group $val.<br/>";
		} 
	}
	if (isset( $_POST['add_konk']) ) {
		$score = $_POST["score"];
		$navn = $_POST["navn"];
		$beskrivelse = $_POST["beskrivelse"];
		
		$data = array(
				'score' => $score,
				'navn' => $navn,
				'beskrivelse' => $beskrivelse
				);
		$wpdb->insert("fad_konk", $data);
		echo "La til konkurranse.<br/>";
	}
	if (isset( $_POST['add_badge']) ) {
		$navn = $_POST["navn"];
		$ikon= $_POST["ikon"];
		$beskrivelse = $_POST["beskrivelse"];
		
		$data = array(
				'navn' => $navn,
				'icon' => $ikon,
				'besk' => $beskrivelse
				);
		$wpdb->insert("fad_badge", $data);
		echo "La til badge.<br/>";
	}

	if ( isset($_POST['group_konk']) ) {
		$box = $_POST['box'];
		$k_id = $_POST['k_id'];

		while (list ($key,$val) = @each ($box)) {
			$data = array(
				'k_id' => $k_id,
				'g_id' => $val
				);
			$wpdb->insert("fad_gruppe_konk", $data);

			/* Insert into activity feed. */
			$tid = time();
			$data = array(
					'k_id' => $k_id,
					'g_id' => $val,
					'tid' => $tid
					);
			$wpdb->insert("fad_aktivitet", $data);
			echo "La konkurranseresultat til gruppe $val.<br/>";
		} 
	}
	?>
	<form name="add_badge" method="post">
	<fieldset>
	<legend>Legg til badge</legend>
	<div>
		<label>Navn</label>
		<input type="text" size="12" maxlength="36" name="navn">
	</div>
	<div>
		<label>Ikon (url)</label>
		<input type="text" size="12" maxlength="36" name="ikon">
	<div>
	<div>
		<label>Beskrivelse</label>
		<input type="text" size="12" maxlength="36" name="beskrivelse">
	</div>
	<div>
	<input class="submit" type="submit" name="add_badge" value="Add" />
	</div>
	</fieldset>
	</form>
	<hr/>

	<form name="badge_to_user" method="post">
	<fieldset>
	<legend>Add badge to user</legend>
	<div>
		<label>Bruker</label>
		<?php wp_dropdown_users(); ?>
	<div>
	<div>
		<label>Badge nr</label>
		<?php make_dropdown_badge(); ?>
	</div>
	<div>
	<input class="submit" type="submit" name="badge_to_user" value="Add" />
	</div>
	</fieldset>
		</form>
	<hr/>

	<form name="badge_to_group" method="post">
	<fieldset>
	<legend>Add badge to group</legend>
	<div>
		<?php make_checkbox_group(); ?>
	</div>
	<div>
		<label>Badge nr</label>
		<?php make_dropdown_badge(); ?>
	</div>

	<div>
		<input class="submit" type="submit" name="badge_to_group" value="Add" />
	</div>
	</fieldset>
	</form>

	<hr/>

	<form name="add_konk" method="post">
	<fieldset>
	<legend>Legg til konkurranse</legend>
	<div>
		<label>Score</label>
		<input type="text" size="12" maxlength="36" name="score">
	<div>
	<div>
		<label>Navn</label>
		<input type="text" size="12" maxlength="36" name="navn">
	</div>
	<div>
		<label>Beskrivelse</label>
		<input type="text" size="12" maxlength="36" name="beskrivelse">
	</div>
	<div>
	<input class="submit" type="submit" name="add_konk" value="Add" />
	</div>
	</fieldset>
	</form>
	<hr/>


	<form name="group_konk" method="post">
	<fieldset>
	<legend>Add konkurranse to group</legend>
	<div>
		<?php make_checkbox_group(); ?>
	</div>
	<div>
		<label>Konkurranse nr</label>
		<?php make_dropdown_konk(); ?>
	</div>

	<div>
		<input class="submit" type="submit" name="group_konk" value="Add" />
	</div>
	</fieldset>
	</form>

	<hr/>
<?php
}

/* Our stylesheet */
function add_stylesheet() {
?>
	<style type="text/css">
		.user_infobox {  }
		.user_info > img { float: left; }
		.user_info > .inner { margin-left: 50px; padding: 0 0.5em; }
		.studie { font-size:0.8em; }
		.group_list { border: solid 2px #dddddd; display: block; margin: 1em 0; }
		.group_list_buddys { float: left; margin: 0 !important; width: 50%; }
		.group_list_buddys > li { 
			border: dotted 1px #ddd;
			border-width: 1px 1px 0 0;
			list-style-type: none !important; 
			width: 100%; 
		}
		.group_list_buddys > li:first-child { border-top: 0; }
		div.group_name {
			display:table-cell;
			float:left;
			width:50px;
			height:60px;
			text-align:center;
			vertical-align:middle;
			font-size:2.0em;
			margin-right:4px;
			margin-left:4px;
			padding:0px;
		}
		div.group_name a {
			text-decoration:none;
			margin:0px;
			padding-left:5px;
			padding-right:5px;
		}
		div.group_name a:hover {
			background-color:#pink;
		}

		/* Profiles page */
		.group_infobox {
			width:50%;
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
			float:right;
			padding-top:40px;
			display:inline-block;
			vertical-align:bottom;
		}
		.group_infobox span.connected {
			display:inline-block;
			vertical-align:bottom;
		}
		.group_infobox span.groupname {
		}
		.group_infobox img {
			display:inline-block;
			vertical-align: middle;
		}

		/* Group page */
		.group_info {
			width:50%;
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
		.group_info span.groupname {
		}
		.group_info img {
			display:inline-block;
			vertical-align: middle;
		}

		div.buddy_list {
			float:left;
				  H
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
		.color_red a{
			color:red;
		}

	</style>
<?php
}
?>
