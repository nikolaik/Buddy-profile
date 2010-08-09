<?php
function my_show_extra_profile_fields( $user ) {
	?>
    <h3>Faddergruppe</h3>
    <table class="form-table">
        <tr>
            <th><label for="faddergroup"><span style="font-weight:bold;">Faddergruppe</span> <span style="color:red;" >*</span></label></th>
            <td>
                <?php make_dropdown_group("faddergroup", $user) ?>
                <span class="description">Vennligst velg gruppe, faddere i parantes. Om alle grupper er full, bruk buffer gruppa intill videre</span>
            </td>
        </tr>
	<?php 
	/* Funksjon for å vise "er fadder" boksen, skal etter planen bli borte 14.8.2010 */
	if ( current_user_can('manage_options') || time() < gmmktime(0, 0, 0, 8, 14, 2010) ) {
		$is_f = get_user_meta( $user->ID, 'isFadder', true);
		?>
		<tr>
			<th><label for="isFadder"><span style="font-weight:bold;">Er du fadder?</span> <span style="color:red;" >*</span></label></th>
			<td>
				<input type="hidden" name="isFadder" id="isFadder" value="0" class="regular-text" />
				<?php if ($is_f == 1) { ?>
					<input type="checkbox" name="isFadder" value="1" checked>
				<?php } else { ?>
					<input type="checkbox" name="isFadder" value="1">
				<?php } ?>
				<br/>
				<span class="description">Velg denne hvis du er fadder</span>
			</td>
		</tr>
	<?php
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
            <th><label for="lastfm_url">IRC</label></th>
            <td>
                <input type="text" name="irc" id="irc" value="<?php echo esc_attr( get_the_author_meta( 'irc', $user->ID ) ); ?>" class="regular-text" /><br />
                <span class="description">#kanal@server, #kanal2@server</span>
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
                <span class="description">Time for geo (Tøm dette feltet for å fjerne marker)</span>
            </td>
        </tr>
	<tr>
            <th><label for="fad_geotime">Android hash</label></th>
            <td>
                <input type="text" DISABLED name="fad_geohash" id="fad_geohash" value="<?php $var = base64_encode($user->user_nicename); $var[0]="A"; echo $var ?>" class="regular-text" /><br />
                <span class="description">Hashkode for å bruke med android oppdateringsprogram</span>
            </td>
        </tr>

    </table>
	<?php
}

add_action( 'personal_options_update', 'my_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'my_save_extra_profile_fields' );
function my_save_extra_profile_fields( $user_id ) {
    if ( !current_user_can( 'edit_user', $user_id ) ) {
        return false;
	}

    /* Copy and paste this line for additional fields. Make sure to change 'twitter' to the field ID. */
    update_usermeta( $user_id, 'twitter', $_POST['twitter'] );
    if ($_POST['faddergroup'] != "")    update_usermeta( $user_id, 'faddergroup', $_POST['faddergroup'] );
    update_usermeta( $user_id, 'bilde', $_POST['bilde'] );
    update_usermeta( $user_id, 'stud_ret', $_POST['stud_ret'] );
    update_usermeta( $user_id, 'stud_ar', $_POST['stud_ar'] );
    update_usermeta( $user_id, 'facebook_url', $_POST['facebook_url'] );
    update_usermeta( $user_id, 'lastfm_url', $_POST['lastfm_url'] );
    update_usermeta( $user_id, 'irc', $_POST['irc'] );
    
	/* Geodata */
	update_usermeta( $user_id, 'fad_lat', $_POST['fad_lat'] );
    	update_usermeta( $user_id, 'fad_lon', $_POST['fad_lon'] );
    	update_usermeta( $user_id, 'fad_geotime', $_POST['fad_geotime'] );
    	update_usermeta( $user_id, 'fad_geohash', $_POST['fad_geohash'] );


    if ( current_user_can('manage_options') || time() < gmmktime(0, 0, 0, 8, 14, 2010)){
    	/* is fadder */
	update_usermeta( $user_id, 'isFadder', $_POST['isFadder'] );
    }
}
?>
