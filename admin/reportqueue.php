<?php

require_once('../config.php');
require_once('../common.php');

mysql_connect(DB_SERVER,DB_USER,DB_PASS);
@mysql_select_db(DB_NAME) or die( "Unable to select database");

if (((isset($_POST['markadult'])) || (isset($_POST['marknotadult'])) || (isset($_POST['deleteitem'])) || (isset($_POST['ignore']))) && (isset($_POST['id'])) && (isset($_POST['picid'])) && (isset($_POST['storyid']))) {
    $picid = urldecode($_POST['picid']);
    $storyid = urldecode($_POST['storyid']);
    
	if ($storyid != '') {

		// Get owner
		$query = "SELECT * FROM stories WHERE (id = " . $storyid . ")";
		$result = mysql_query($query);
		$story = mysql_fetch_assoc($result);
		$query = "SELECT * FROM users WHERE (id = " . $story['userid'] . ")";
		$result = mysql_query($query);
		$user = mysql_fetch_assoc($result);
		
		if (isset($_POST['markadult'])) {
		    $query = "UPDATE stories SET adult=1 WHERE id=" . mysql_real_escape_string($storyid);
			mysql_query($query);
			
			$body = "Hi " . $user['displayname'] . ", \n\nYour story, \"" . mysql_real_escape_string($story['title']) . "\", has been reported by a user as containing adult content, and a moderator has agreed.  This story is now tagged as containing adult content, and will not be viewable by users under the age of 18.";
			mail($user['email'], '"a thousand words" Story Moderation', $body);
			
		} elseif (isset($_POST['marknotadult'])) {
		    $query = "UPDATE stories SET adult=0 WHERE id=" . mysql_real_escape_string($storyid);
			mysql_query($query);
			
			$body = "Hi " . $user['displayname'] . ", \n\nYour story, \"" . mysql_real_escape_string($story['title']) . "\", has been reported by a user as not containing adult content despite being marked as such, and a moderator has agreed.  This story is now no longer tagged as containing adult content, and will now be viewable by users under the age of 18.";
			mail($user['email'], '"a thousand words" Story Moderation', $body);
			
		} elseif (isset($_POST['deleteitem'])) {
		    $query = "DELETE FROM stories WHERE id=" . mysql_real_escape_string($storyid);
			mysql_query($query);
		    $query = "DELETE FROM votes WHERE storyid=" . mysql_real_escape_string($storyid);
			mysql_query($query);
			updateStars();
			
			$body = "Hi " . $user['displayname'] . ", \n\nYour story, \"" . mysql_real_escape_string($story['title']) . "\", has been reported to the moderation team, and they have decided that it should be deleted.  If you wish to complain about this decision, please e-mail admin@athousandwords.org.uk.\n\nYour story's content is included below for your records, as it is no longer held in our database.\n\n" . $story['text'];
			mail($user['email'], '"a thousand words" Story Moderation', $body);
		}
	} elseif ($picid != '') {

		// Get owner
		$query = "SELECT * FROM stories WHERE (id = " . $storyid . ")";
		$result = mysql_query($query);
		$pic = mysql_fetch_assoc($result);
		$query = "SELECT * FROM users WHERE (id = " . $pic['userid'] . ")";
		$result = mysql_query($query);
		$user = mysql_fetch_assoc($result);
		
		if (isset($_POST['deleteitem'])) {
		    $query = "DELETE FROM pictures WHERE id=" . mysql_real_escape_string($picid);
			mysql_query($query);
			
			$body = "Hi " . $user['displayname'] . ", \n\nYour picture, \"" . mysql_real_escape_string($story['pic']) . "\", has been reported to the moderation team, likely for copyright infringement, and they have decided that it should be deleted.  If you wish to complain about this decision, please e-mail admin@athousandwords.org.uk.";
			mail($user['email'], '"a thousand words" Picture Moderation', $body);
		}
	}
	$query = "DELETE FROM modqueue WHERE id=" . mysql_real_escape_string($_POST['id']) . ";";
    mysql_query($query);
}

echo('<h2>Behold the Report Moderation Queue!</h2>');

$query = "SELECT * FROM modqueue LIMIT 9999";
$result = mysql_query($query);
echo('<table border="0">');
while($row = mysql_fetch_assoc($result)) {
	$userquery = "SELECT * FROM users WHERE id='" . $row['userid'] . "'";
    $userresult = mysql_query($userquery);
	$user = mysql_fetch_assoc($userresult);
	
	if ($row['picid'] != "0") {
		$query = "SELECT * FROM pictures WHERE id=" . $row['picid'];
		$picresult = mysql_query($query);
		$pic = mysql_fetch_assoc($picresult);
		$itemlink = "<a href=\"http://athousandwords.org.uk/picture/" . $pic['id'] . "\">" . $pic['title'] . "</a>";
	} else {
		$query = "SELECT * FROM stories WHERE id=" . $row['storyid'];
		$storyresult = mysql_query($query);
		$story = mysql_fetch_assoc($storyresult);
		$itemlink = "<a href=\"http://athousandwords.org.uk/story/" . $story['id'] . "\">" . $story['title'] . "</a>";
	}
	?>
	<tr><td>
	<form id="reportqueueform" name="reportqueueform" method="post" action="reportqueue.php"></td>
	<td><input type="hidden" size="3" name="id" value="<?php echo($row['id']) ?>"></td>
	<td><input type="hidden" size="3" name="picid" value="<?php echo($row['picid']) ?>"></td>
	<td><input type="hidden" size="3" name="storyid" value="<?php echo($row['storyid']) ?>"></td>
	<td><?php echo($itemlink); ?></td>
	<td>Reported by: <?php echo($user['displayname']) ?></td>
	<td>Type: <?php echo($row['type']) ?></td>
	<td style="width:350px; display:block;">Explanation: <?php echo($row['explanation']) ?></td>
	<?php if ($row['picid'] != "0") { ?>
		<td><input type="submit" name="deleteitem" value="Delete Picture" />
	<?php } else { ?>
		<td><input type="submit" name="markadult" value="Mark as Adult" />
		<td><input type="submit" name="marknotadult" value="Mark as Not Adult" />
		<td><input type="submit" name="deleteitem" value="Delete Story" />
	<?php } ?>
	<input type="submit" name="ignore" value="Ignore Report" /></td></tr>
	</form>
	<?php
}
echo("</table>");
echo("The end!");

mysql_close();
  
?>