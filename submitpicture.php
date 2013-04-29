
<?php

require_once('config.php');
session_start();

// If not logged in, deny access
if (!isset($_SESSION['userID'])) {
	header('Location: ./login');
    die();
}

$title = "Submit Picture";

include('header.php');
        
if (isset($_GET['fail'])) {
   echo( '<p class="failnotice">Sorry!  Something went horribly wrong there.</p>');
}  
    
if (isset($_GET['done'])) {
   echo( '<p class="successnotice">Thanks!  Your picture has been submitted and if it is approved by the moderation team, may become the basis of a forthcoming "thousand words" competition.</p>');
}

echo('<form name="submitpictureform" method="post" action="submitpicturecallback.php">
        <table border="0" align="center" cellpadding="5" cellspacing="5" style="margin:50px auto 50px auto;">
        <tr>
        <td>Title</td>
        <td><input name="title" type="text" id="title" style="width:400px"></td>
        </tr>
        <tr>
        <td>URL</td>
        <td width="410px"><input name="url" type="text" id="url" style="width:400px"><br/>If the picture isn\'t your own, please make use the URL something that identifies the owner (e.g. link to their Flickr photo\'s page rather than directly to the photo itself). This is required so that we can properly attribute the photo and check its licence.</td>
        </tr>
        <tr>
        <td>&nbsp;</td>
        <td><input type="submit" name="Submit" value="Submit"></td>
        </tr>
        </table>
        </form>');

include('footer.php')
?>

