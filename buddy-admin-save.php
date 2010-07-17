<?php 

require_once('../../../wp-config.php');
echo DB_USER;

$connection = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die(mysql_error());
mysql_select_db("cyb") or die(mysql_error());


if (array_key_exists('badge_to_user', $_POST)) {
    echo "Add badge to user: ";
    $badge =  $_POST["badge"];
    echo " to user: ";
    $user =  $_POST["user"];
	mysql_query("INSERT IGNORE INTO `fad_badge_user` ( `b_id` , `u_id` ) VALUES ('{$badge}', '{$user}')") or die(mysql_error());
}
 
if (array_key_exists('badge_to_group', $_POST)) {
    echo "Badge to group<br/>";

	echo "grupper valgt<br/>";

	echo "Legger Gruppe badgeil <br/>";

	echo "<br/>grupper valgt<br/>";

    	$box=$_POST['box'];
    	$badge = $_POST['badge'];

    	while (list ($key,$val) = @each ($box)) {
    	echo "$val,";

	mysql_query("INSERT IGNORE INTO `fad_badge_group` ( `b_id` , `g_id` ) VALUES ('{$badge}', '{$val}')") or die(mysql_error());


    } 

}



if (array_key_exists('add_konk', $_POST)) {
    echo "Legger til konkurranse<br/>";
    echo $_POST["score"];
    echo " ";
    echo $_POST["navn"];
    echo " ";
    echo $_POST["beskrivelse"];
    
    $score 		= $_POST["score"];
    $navn  		= $_POST["navn"];
    $beskrivelse	= $_POST["beskrivelse"];
	
	mysql_query("INSERT IGNORE INTO `fad_konk` ( `k_id` , `score` , `navn`, `beskrivelse` ) VALUES (NULL  , '{$score}' , '{$navn}', '{$beskrivelse}')") or die(mysql_error());
}


if (array_key_exists('group_konk', $_POST)) {
	echo "Legger Gruppe til konkurranseresultat<br/>";
    	echo $_POST["k_id"];

	echo "<br/>grupper valgt<br/>";

    	$box=$_POST['box'];
    	$k_id=$_POST['k_id'];

    	while (list ($key,$val) = @each ($box)) {
    	echo "$val,";

	mysql_query("INSERT IGNORE INTO `fad_gruppe_konk` ( `k_id` , `g_id` ) VALUES ('{$k_id}', '{$val}')") or die(mysql_error());


    } 
}

mysql_close($connection);
?>
