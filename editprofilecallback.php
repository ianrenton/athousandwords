<?php

/* Start session and load lib */
session_start();
require_once('config.php');

// If not logged in, deny access
if (!isset($_SESSION['userID'])) {
	header('Location: ./login');
    die();
}

if ((isset($_POST['email'])) && ($_POST['email'] != "") && (isset($_POST['oldemail'])) && (isset($_POST['displayname'])) && ($_POST['displayname'] != "") && (isset($_POST['location'])) && (isset($_POST['favauthor'])) && (isset($_POST['favartist']))) {
    mysql_connect(DB_SERVER,DB_USER,DB_PASS);
    @mysql_select_db(DB_NAME) or die( "Unable to select database");
    
	if ((isset($_POST['delete'])) && ($_POST['delete'] == "true")) {
		// Delete user
		$query = "DELETE FROM users WHERE id=" . $_SESSION['userID'];
		mysql_query($query);
		$query = "DELETE FROM stories WHERE userid=" . $_SESSION['userID'];
		mysql_query($query);
		$query = "DELETE FROM comments WHERE userid=" . $_SESSION['userID'];
		mysql_query($query);
		$query = "DELETE FROM votes WHERE userid=" . $_SESSION['userID'];
		mysql_query($query);
	    header('Location: ./');
	} else {
	    // Update the item in the users table
	
		// Updating the e-mail address requires re-sending activation e-mail
		if (strcmp($_POST['oldemail'], $_POST['email']) != 0){
			$email = strip_tags($_POST['email']);
		    $query = "UPDATE users SET email='" . $email . "' WHERE id=" . mysql_real_escape_string($_SESSION['userID']);
		    mysql_query($query);
			$query = "UPDATE users SET activated=0 WHERE id=" . mysql_real_escape_string($_SESSION['userID']);
		    mysql_query($query);
			$query = "SELECT * FROM users WHERE id=" . mysql_real_escape_string($_SESSION['userID']);
    		$result = mysql_query($query);
        	$secret = mysql_result($result, 0, "secret");
			$displayname = strip_tags($_POST['displayname']);
			$body = "Hi " . mysql_real_escape_string($displayname) . ",\nThank you for updating your stored e-mail address on \"a thousand words\".  Please click the following link to reactivate your account, then log in with the password you have set.\n \nhttp://athousandwords.org.uk/login.php?email=" . mysql_real_escape_string($_POST['email']) . "&secret=" . $secret;
			if (!mail($_POST['email'], 'Registration on "a thousand words"', $body)) {
				header('Location: ./editprofile?fail=true');
    			die();
			}
		}
		$displayname = strip_tags($_POST['displayname']);
	    $query = "UPDATE users SET displayname='" . $displayname . "' WHERE id=" . mysql_real_escape_string($_SESSION['userID']);
	    mysql_query($query);
		$dob = strip_tags($_POST['dob']);
	    $query = "UPDATE users SET dob='" . $dob . "' WHERE id=" . mysql_real_escape_string($_SESSION['userID']);
	    mysql_query($query);
		$location = strip_tags($_POST['location']);
	    $query = "UPDATE users SET location='" . $location . "' WHERE id=" . mysql_real_escape_string($_SESSION['userID']);
	    mysql_query($query);
		$favauthor = strip_tags($_POST['favauthor']);
	    $query = "UPDATE users SET favauthor='" . $favauthor . "' WHERE id=" . mysql_real_escape_string($_SESSION['userID']);
	    mysql_query($query);
		$favartist = strip_tags($_POST['favartist']);
	    $query = "UPDATE users SET favartist='" . $favartist . "' WHERE id=" . mysql_real_escape_string($_SESSION['userID']);
	    mysql_query($query);
	
		// Password change
		if ((isset($_POST['password1'])) && (isset($_POST['password2']))) {
			if (strcmp($_POST['password1'], "" != 0)) {
				if (strcmp($_POST['password1'], $_POST['password2']) == 0) {
					$query = "UPDATE users SET password='" . md5($_POST['password1']) . "' WHERE id=" . mysql_real_escape_string($_SESSION['userID']);
		    		mysql_query($query);
				}
			}
		}
		
	    header('Location: ./user/' . $_SESSION['userID']);
	}
	die();
    mysql_close();
} else {
    header('Location: ./editprofile?fail=true');
    die();
}

?>
