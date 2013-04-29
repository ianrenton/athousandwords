<?php

require_once('config.php');
session_start();

mysql_connect(DB_SERVER,DB_USER,DB_PASS);
@mysql_select_db(DB_NAME) or die( "Unable to select database");

// If not logged in, deny access
if (!isset($_SESSION['userID'])) {
	header('Location: ./login');
    die();
}

// If id not specified, go to the home page.
if(!isset($_GET['id'])) {
	header('Location: /');
	die();
} else {
	$id = $_GET['id'];
}

// Get the story from the database.
$query = "SELECT * FROM stories WHERE (id = " . $id . ")";
$result = mysql_query($query);
$story = mysql_fetch_assoc($result);

// If the user has asked for a story that doesn't exist, redirect to the homepage.
if ($story == false) {
	header('Location: /');
	die();
}

// Check author ID, redirect to the homepage if this isn't the logged in user's story
if ($story['userid'] != $_SESSION['userID']) {
	header('Location: /');
	die();	
}

mysql_close();

$title = "Edit Story";

include('header.php');
        
if (isset($_GET['fail'])) {
   echo( '<p class="failnotice">Sorry!  Something went horribly wrong there.  Maybe you didn\'t choose a picture for this story to belong to?</p>');
}  

echo('<form name="editstoryform" method="post" action="/editstorycallback.php">
        
		<script language="JavaScript">
			function wordCount(){
				var text = document.editstoryform.text.value;
				text = text.split(/[\s\n]+/);
				document.getElementById(\'wordcount\').innerHTML = "" + text.length + " words";
			}
			function setPictureID(id){
				document.editstoryform.pictureid.value = id;
			}
		</script>

        <table border="0" align="center" cellpadding="5" cellspacing="5" style="margin:50px auto 50px auto;">
        <tr>
        <td>Title</td>
        <td><input name="id" type="hidden" id="id" value="' . $story['id'] . '">
		<input name="title" type="text" id="title" style="width:400px" value="' . $story['title'] . '"></td>
        </tr>
        <tr>
        <td>Picture</td>
        <td><iframe src="/picturepicker.php?id=' . $story['pictureid'] . '" width="100%" height="145"></iframe>
		<input name="pictureid" type="hidden" id="pictureid" style="width:40px" value="' . $story['pictureid'] . '"></td>
        </tr>
        <tr>
        <td>Text</td>
        <td><textarea name="text" id="text" style="width:700px; height:300px;" onKeyDown="wordCount();" onKeyUp="wordCount();">' . $story['text'] . '</textarea><br/>
		<p id="wordcount"></p>
		</td>
        </tr>
        <tr>
        <td>&nbsp;</td>
        <td><input type="checkbox" name="critique" value="true"');
		if ($story['critique'] > 0) { echo(' checked'); }
		echo('> Request critique of this story</td>
        </tr>
        <tr>
        <td>&nbsp;</td>
        <td><input type="checkbox" name="adult" value="true"');
		if ($story['adult'] > 0) { echo(' checked'); }
		echo('> This story contains adult content (sex, violence, etc.)</td>
        </tr>
        <tr>
        <td>&nbsp;</td>
        <td><input type="checkbox" name="delete" value="true"> Delete my story <b>(Be careful, there is NO UNDO!)</b></td>
        </tr>
        <tr>
        <td>&nbsp;</td>
        <td><input type="submit" name="Submit" value="Submit"></td>
        </tr>
        </table>
        </form>');

include('footer.php')
?>

