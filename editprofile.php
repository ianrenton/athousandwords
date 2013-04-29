<?php

require_once('config.php');
session_start();

mysql_connect(DB_SERVER,DB_USER,DB_PASS);
@mysql_select_db(DB_NAME) or die( "Unable to select database");

// Get the user from the database.
$query = "SELECT * FROM users WHERE (id = " . $_SESSION['userID'] . ")";
$result = mysql_query($query);
$user = mysql_fetch_assoc($result);


// If the user has somehow requested a profile that doesn't exist, redirect to the homepage.
if ($user == false) {
	header('Location: /');
	die();
}

// Get gravatar
$grav_url = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $user['email'] ) ) ) . "?d=&s=50";

$title = "Edit Profile";

include('header.php');

if (isset($_GET['fail'])) {
   echo( '<p class="failnotice">Sorry!  Something went horribly wrong there.</p>');
}

echo('<form name="editprofileform" method="post" action="editprofilecallback.php">
        <table border="0" align="center" cellpadding="5" cellspacing="5" style="margin:50px auto 50px auto;">
        <tr>
        <td>E-mail Address</td>
        <td><input name="email" type="text" id="email" style="width:200px" value="' . $user['email'] . '"><input name="oldemail" type="hidden" id="oldemail" value="' . $user['email'] . '"></td>
        </tr>
        <tr>
        <td>Password</td>
        <td><input name="password1" type="password" id="password1" style="width:100px" value="" autocomplete="off"> Retype: <input name="password2" type="password" id="password2" style="width:100px" value="" autocomplete="off"></td>
        </tr>
        <tr>
        <td>Display Name</td>
        <td><input name="displayname" type="text" id="displayname" style="width:200px" value="' . $user['displayname'] . '"></td>
        </tr>
        <tr>
        <td>Date of Birth (YYYY-MM-DD)</td>
        <td><input name="dob" type="text" id="dob" style="width:200px" value="' . $user['dob'] . '"></td>
        </tr>
        <tr>
        <td>Avatar</td>
        <td><a href="http://en.gravatar.com/site/login/%252F">Log in to Gravatar</a> to set your avatar on "a thousand words".</td>
        </tr>
        <tr>
        <td>Location</td>
        <td><input name="location" type="text" id="location" style="width:200px" value="' . $user['location'] . '"></td>
        </tr>
        <tr>
        <td>Favourite Author</td>
        <td><input name="favauthor" type="text" id="favauthor" style="width:200px" value="' . $user['favauthor'] . '"></td>
        </tr>
        <tr>
        <td>Favourite Artist</td>
        <td><input name="favartist" type="text" id="favartist" style="width:200px" value="' . $user['favartist'] . '"></td>
        </tr>
        <tr>
        <td>&nbsp;</td>
        <td><input type="checkbox" name="delete" value="true"> Delete my account <b>(Be careful, there is NO UNDO!)</b></td>
        </tr>
        <tr>
        <td>&nbsp;</td>
        <td><input type="submit" name="Submit" value="Submit"></td>
        </tr>
        </table>
        </form>');

include('footer.php');

mysql_close();

?>
