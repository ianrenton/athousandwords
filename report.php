<?php

require_once('config.php');
session_start();

mysql_connect(DB_SERVER,DB_USER,DB_PASS);
@mysql_select_db(DB_NAME) or die( "Unable to select database");

// If picture/story specified, use them
$picid = '';
$storyid = '';
if (isset($_GET['picid'])) {
	$picid = $_GET['picid'];
}
if (isset($_GET['storyid'])) {
	$storyid = $_GET['storyid'];
}

$title = "Report";

include('header.php');

if ($picid != "") {
	$query = "SELECT * FROM pictures WHERE id=" . $picid;
	$result = mysql_query($query);
	$pic = mysql_fetch_assoc($result);
	
	if ($pic == false) {
		header('Location: /');
		die();
	}
	
	$query = "SELECT * FROM users WHERE (id = " . $pic['userid'] . ")";
	$result = mysql_query($query);
	$displayname = mysql_fetch_assoc($result);
	
	echo("<p class=\"helptext\" style=\"margin-top:40px\">You are reporting the picture \"" . $pic['title'] . "\", submitted by \"" . $displayname['displayname'] . "\".</p>");
} else {
	$query = "SELECT * FROM stories WHERE id=" . $storyid;
	$result = mysql_query($query);
	$story = mysql_fetch_assoc($result);
	
	if ($story == false) {
		header('Location: /');
		die();
	}
	
	$query = "SELECT * FROM users WHERE (id = " . $story['userid'] . ")";
	$result = mysql_query($query);
	$displayname = mysql_fetch_assoc($result);
	
	echo("<p class=\"helptext\" style=\"margin-top:40px\">You are reporting the story \"" . $story['title'] . "\", submitted by \"" . $displayname['displayname'] . "\".</p>");
}

if (isset($_GET['fail'])) {
   echo( '<p class="failnotice">Sorry!  Something went horribly wrong there.</p>');
}  
        
if (isset($_GET['success'])) {
   echo( '<p class="successnotice">Thank you, your report has been submitted and will now be handled by the moderation team.</p>');
} 

echo('<form name="reportform" method="post" action="/reportcallback.php">
			
        <table border="0" align="center" cellpadding="5" cellspacing="5" style="margin:50px auto 50px auto;">
        <tr>
        <td>Reason</td>
        <td><input type="hidden" name="picid" value="' . $picid . '" />
		<input type="hidden" name="storyid" value="' . $storyid . '" />
		<select name="reason">
		<option value="">Please choose an option</option>
		<option value="adult">Contains adult content, but not tagged as such</option>
		<option value="notadult">No adult content, but tagged as containing it</option>
		<option value="copyright">Copyright/licence infringement</option>
		<option value="other">Other (please state)</option>
		</select>
		</td>
        </tr>
        <tr>
        <td>Explanation</td>
        <td><textarea name="text" id="text" style="width:400px; height:200px;" onKeyDown="wordCount();" onKeyUp="wordCount();"></textarea><br/>
		<p id="wordcount" style="width:400px">Please describe why you have reported this item.  If it is due to copyright infringement, please provide details of the original creator of the work (e.g. link to the picture/story on the creator\'s website).</p>
		</td>
        </tr>
        <tr>
        <td>&nbsp;</td>
        <td><input type="submit" name="Submit" value="Submit"></td>
        </tr>
        </table>
        </form>'); 

mysql_close();

include('footer.php')
?>

