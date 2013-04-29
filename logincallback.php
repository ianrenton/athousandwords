<?php

/* Start session and load lib */
session_start();
require_once('config.php');

if ((isset($_POST['email'])) && (isset($_POST['password']))) {
       mysql_connect(DB_SERVER,DB_USER,DB_PASS);
       @mysql_select_db(DB_NAME) or die( "Unable to select database");
       
       // Get the user's row from the table
       $query = "SELECT * FROM users WHERE email='" . mysql_real_escape_string($_POST['email']) . "'";
       $result = mysql_query($query);
       
       // Email check
       if (mysql_num_rows($result) > 0) {
		// Activation check
		if (strcmp(mysql_result($result, 0, "activated"), "1") == 0) {
           	// Password check
            $storedPassword = mysql_result($result, 0, "password");
            if (strcmp($storedPassword, md5($_POST['password'])) == 0) {
               
                /* Password matched, so set user id */
                $_SESSION['userID'] = mysql_result($result, 0, "id");

                // Save cookies here
				if ((isset($_POST['setcookie'])) && ($_POST['setcookie'] == "true")) {
					setcookie('userID', serialize(mysql_result($result, 0, "id")), mktime()+86400*365);
					setcookie('secret', serialize(mysql_result($result, 0, "secret")), mktime()+86400*365);
				}
				
                header('Location: ./');
                die();
               
            } else {
                // Password didn't match
                header('Location: ./login.php?fail=true');
                die();
            }
		} else {
			// Account not activated
           	header('Location: ./login.php?fail=true');
            die();
		}
       } else {
           // Username didn't match
           header('Location: ./login.php?fail=true');
           die();
       }
       
       mysql_close();

} else {
    // Called without username/password POST.
    header('Location: ./login.php');
    die();
}

?>
