<?php

require_once('config.php');
session_start();

mysql_connect(DB_SERVER,DB_USER,DB_PASS);
@mysql_select_db(DB_NAME) or die( "Unable to select database");

// If not logged in, deny access
if (!isset($_SESSION['userID'])) {
	header('Location: /login');
    die();
}

// If picture specified, use it
$picid = '';
if (isset($_GET['pic'])) {
	$picid = $_GET['pic'];
}

$title = "Submit Story";

include('header.php');
        
if (isset($_GET['fail'])) {
   echo( '<p class="failnotice">Sorry!  Something went horribly wrong there.  Maybe you didn\'t choose a picture for this story to belong to?</p>');
}  

echo('<form name="submitstoryform" method="post" action="/submitstorycallback.php">

		<script language="JavaScript">
			function wordCount(){
				var text = document.submitstoryform.text.value;
				text = text.split(/[\s\n]+/);
				document.getElementById(\'wordcount\').innerHTML = "" + text.length + " words";
			}
			function setPictureID(id){
				document.submitstoryform.pictureid.value = id;
			}
		</script>
			
        <table border="0" align="center" cellpadding="5" cellspacing="5" style="margin:50px auto 50px auto;">
        <tr>
        <td>Title</td>
        <td><input name="title" type="text" id="title" style="width:400px"></td>
        </tr>
        <tr>
        <td>Picture</td>
        <td><iframe src="/picturepicker.php?id=' . $picid . '" width="100%" height="145"></iframe>
		<input name="pictureid" type="hidden" id="pictureid" style="width:40px" value="' . $picid . '">
		</td>
        </tr>
        <tr>
        <td>Text</td>
        <td><textarea name="text" id="text" style="width:700px; height:300px;" onKeyDown="wordCount();" onKeyUp="wordCount();"></textarea><br/>
		<p id="wordcount">0 words</p>
		</td>
        </tr>
        <tr>
        <td>&nbsp;</td>
        <td><input type="checkbox" name="critique" value="true"> Request critique of this story</td>
        </tr>
        <tr>
        <td>&nbsp;</td>
        <td><input type="checkbox" name="adult" value="true"> This story contains adult content (sex, violence, etc.)</td>
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

