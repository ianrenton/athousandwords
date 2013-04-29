<?php

require_once('config.php');
session_start();

$email = '';

// If following link in e-mail...
if ((isset($_GET['email'])) && (isset($_GET['secret']))) {
	
	mysql_connect(DB_SERVER,DB_USER,DB_PASS);
	@mysql_select_db(DB_NAME) or die( "Unable to select database");
	
	// Get the user's row from the table
    $query = "SELECT * FROM users WHERE email='" . mysql_real_escape_string($_GET['email']) . "'";
    $result = mysql_query($query);

    
    // Email check
    if (mysql_num_rows($result) > 0) {
        // Secret check
        $storedSecret = mysql_result($result, 0, "secret");
        if (strcmp($storedSecret, $_GET['secret']) == 0) {
           
            /* Secret matched, so set activated */
            $query = "UPDATE users SET activated=1 WHERE email='" . mysql_real_escape_string($_GET['email']) . "'";
		    $result = mysql_query($query);
		
			// Fill in email box
			$email = mysql_real_escape_string($_GET['email']);
		}
	}
    
}

$title = "Log In";

include('header.php');
      
if (isset($_GET['fail'])) {
   echo( '<p class="failnotice">Sorry!  Either that user does not exist, or the password you entered was incorrect.</p>');
}

echo('<form name="loginform" method="post" action="logincallback.php">
        <table border="0" align="center" cellpadding="5" cellspacing="5" style="margin:50px auto 50px auto;">
        <tr>
        <td>E-mail address</td>
        <td><input name="email" type="text" id="email" style="width:200px" value="'.$email.'"></td>
        </tr>
        <tr>
        <td>Password</td>
        <td><input name="password" type="password" id="password" style="width:200px"></td>
        </tr>
        <tr>
        <td>&nbsp;</td>
        <td><input type="checkbox" name="setcookie" value="true" checked> Remember me</td>
        </tr>
        <tr>
        <td>&nbsp;</td>
        <td><input type="submit" name="Submit" value="Log In"></td>
        </tr>
        </table>
        </form>
		<p class="helptext" style="margin-top:30px;">Not got an account?  <a href="/register">Create one here!</a></p>');
  
include('footer.php');
?>

