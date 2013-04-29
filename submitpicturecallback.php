<?php

/* Start session and load lib */
session_start();
require_once('config.php');

// If not logged in, deny access
if (!isset($_SESSION['userID'])) {
	header('Location: ./login');
    die();
}

if ((isset($_POST['title'])) && (isset($_POST['url']))) {
    if (DB_SERVER != '') {
        mysql_connect(DB_SERVER,DB_USER,DB_PASS);
        @mysql_select_db(DB_NAME) or die( "Unable to select database");
        
        // Add the item to the picture queue
        $query = "SELECT * FROM picturequeue WHERE url='" . mysql_real_escape_string($_POST['url']) . "'";
        $result = mysql_query($query);
        if (!mysql_num_rows($result) ) {
            $query = "INSERT INTO picturequeue VALUES (0, " . mysql_real_escape_string($_SESSION['userID']) . ",'" . date("Y-m-d H:i:s") . "','" . date("Y-m-d H:i:s", 0) . "', '" . mysql_real_escape_string($_POST['title']) . "', '', '" . mysql_real_escape_string($_POST['url']) . "')";
            mysql_query($query);

			$body = "A new picture has been submitted to A Thousand Words.\n\nPlease check the queue at http://athousandwords.org.uk/admin/picturequeue.php, username 1kwadmin, password 1kwadminpassword.\n\nIf you want a picture to become live on the site, download the picture from the Source URL, upload it to 1kW via FTP, then set the URL@1kW field to point to the actual image URL on 1kW.  Make sure you set the URL@1kW section to do this before clicking 'Approve'.";
			mail('admin@athousandwords.org.uk', 'a thousand words Picture Submission', $body);
			
            header('Location: ./submitpicture.php?done=true');
			die();
        } else {
            header('Location: ./submitpicture.php?fail=true');
    		die();
        }
        mysql_close();
    }
} else {
    header('Location: ./submitpicture?fail=true');
    die();
}

?>
