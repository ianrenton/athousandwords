<?php

require_once('../config.php');

mysql_connect(DB_SERVER,DB_USER,DB_PASS);
@mysql_select_db(DB_NAME) or die( "Unable to select database");

if (((isset($_POST['approve'])) || (isset($_POST['delete']))) && (isset($_POST['id'])) && (isset($_POST['userid'])) && (isset($_POST['submitdate'])) && (isset($_POST['title'])) && (isset($_POST['url']))) {
    $userid = urldecode($_POST['userid']);
    $submitdate = urldecode($_POST['submitdate']);
    $title = urldecode($_POST['title']);
    $url = urldecode($_POST['url']);
    $sourceurl = urldecode($_POST['sourceurl']);
    $artist = urldecode($_POST['artist']);
    $licence = urldecode($_POST['licence']);
    
	if ($_POST['approve']) {
    	$query = "INSERT INTO pictures VALUES (0, " . mysql_real_escape_string($userid) . ",'" . mysql_real_escape_string($submitdate) . "','" . date("Y-m-d H:i:s") . "', '" . mysql_real_escape_string($_POST['title']) . "', '" . mysql_real_escape_string($_POST['url']) . "', '" . mysql_real_escape_string($_POST['sourceurl']) . "', '" . mysql_real_escape_string($_POST['artist']) . "', '" . mysql_real_escape_string($_POST['licence']) . "')";
        mysql_query($query);

		// Get submitter
		$query = "SELECT * FROM users WHERE (id = " . $userid . ")";
		$result = mysql_query($query);
		$user = mysql_fetch_assoc($result);

		$body = "Congratulations, " . $user['displayname'] . "! \n\nYour picture, \"" . mysql_real_escape_string($_POST['title']) . "\", has been selected as the new picture of the week on \"a thousand words\"!  You can find it at http://athousandwords.org.uk.\n\nThanks for using the site!";
		mail($user['email'], 'Your "a thousand words" Picture Submission', $body);
		
	}
	$query = "DELETE FROM picturequeue WHERE id=" . mysql_real_escape_string($_POST['id']) . ";";
    mysql_query($query);
}

echo('<h2>Behold the Picture Submission Queue!</h2>');

$query = "SELECT * FROM picturequeue LIMIT 9999";
$result = mysql_query($query);
echo('<table border="0">');
while($row = mysql_fetch_assoc($result)) {
	$userquery = "SELECT * FROM users WHERE id='" . $row['userid'] . "'";
    $userresult = mysql_query($userquery);
	$user = mysql_fetch_assoc($userresult);
	?>
	<tr><td>
	<form id="picturequeueform" name="picturequeueform" method="post" action="picturequeue.php"></td>
	<td><input type="hidden" size="3" name="id" value="<?php echo($row['id']) ?>"></td>
	<td>From: <?php echo($user['displayname']) ?><input type="hidden" size="3" name="userid" value="<?php echo($row['userid']) ?>"></td>
	<td>At: <?php echo($row['submitdate']) ?><input type="hidden" size="20" name="submitdate" value="<?php echo($row['submitdate']) ?>"></td>
	<td><input type="text" size="30" name="title" value="<?php echo(strip_tags($row['title'])) ?>"></td>
	<td>URL@1kW: <input type="text" size="40" name="url" value="<?php if ($row['url'] != '') { echo($row['url']); } else { echo('http://athousandwords.org.uk/pictures/'); } ?>"></td>
	<td>Source: <input type="text" size="40" name="sourceurl" value="<?php echo($row['sourceurl']) ?>"></td>
	<td>Artist name: <input type="text" size="20" name="artist" value="<?php echo($row['artist']) ?>"></td>
	<td>Licence: <input type="text" size="10" name="licence" value="<?php echo($row['licence']) ?>"></td>
	<td><input type="submit" name="approve" value="Approve" />
	<input type="submit" name="delete" value="Delete" /></td></tr>
	</form>
	<?php
}
echo("</table>");
echo("The end!");

mysql_close();
  
?>