<?php
  if (!current_user_can('manage_options'))  {
    wp_die( __('You do not have sufficient permissions to access this page.') );
  }

    require_once("../wp-content/plugins/buddy_profile/buddy_func.php");

$save_file = "../wp-content/plugins/buddy_profile/buddy-admin-save.php";
?>



<form name="badge_to_user" action="<?php echo $save_file ?>" method="post">
<input type="hidden" name="badge_to_user" value="1"/>
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
<input class="submit" type="submit" name="submit" value="Add" />
</div>
</fieldset>
	</form>
<hr/>

<form name="badge_to_group" action="../wp-content/plugins/buddy_profile/buddy-admin-save.php" method="post">
<input type="hidden" name="badge_to_group" value="1"/>
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
	<input class="submit" type="submit" name="submit" value="Add" />
</div>
</fieldset>
</form>

<hr/>

<form name="add_konk" action="../wp-content/plugins/buddy_profile/buddy-admin-save.php" method="post">
<input type="hidden" name="add_konk" value="1"/>
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
<input class="submit" type="submit" name="submit" value="Add" />
</div>
</fieldset>
</form>
<hr/>


<form name="group_konk" action="../wp-content/plugins/buddy_profile/buddy-admin-save.php" method="post">
<input type="hidden" name="group_konk" value="1"/>
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
	<input class="submit" type="submit" name="submit" value="Add" />
</div>
</fieldset>
</form>

<hr/>



<?php
?>

