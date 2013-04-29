<?php

/* Start session and load lib */
session_start();
require_once('config.php');

if ((isset($_POST['reason'])) && ($_POST['reason'] != "") && (isset($_POST['text'])) && ($_POST['text'] != '') && (isset($_POST['picid'])) && (isset($_POST['storyid']))) {
        
		$picid = $_POST['picid'];
		$storyid = $_POST['storyid'];

		mysql_connect(DB_SERVER,DB_USER,DB_PASS);
        @mysql_select_db(DB_NAME) or die( "Unable to select database");

		if ($picid != "") {
			$query = "SELECT * FROM pictures WHERE id=" . $picid;
			$result = mysql_query($query);
			$thing = mysql_fetch_assoc($result);
	
			if ($thing == false) {
				header('Location: /');
				die();
			}
		} else {
			$query = "SELECT * FROM stories WHERE id=" . $storyid;
			$result = mysql_query($query);
			$thing = mysql_fetch_assoc($result);
	
			if ($thing == false) {
				header('Location: /');
				die();
			}
		}

        // Add the item to the report table
        $query = "INSERT INTO modqueue VALUES (0, '" . mysql_real_escape_string($_SESSION['userID']) . "', '" . mysql_real_escape_string($_POST['storyid']) . "', '" . mysql_real_escape_string($_POST['picid']) . "', '" . mysql_real_escape_string($thing['userid']) . "', '" . mysql_real_escape_string($_POST['reason']) . "', '" . mysql_real_escape_string($_POST['text']) . "')";
        mysql_query($query);

        mysql_close();

		$body = "An item has been reported on A Thousand Words.\n\nPlease check the queue at http://athousandwords.org.uk/admin/reportqueue.php, username 1kwadmin, password 1kwadminpassword.";
		mail('admin@athousandwords.org.uk', 'a thousand words Item Reported', $body);
			
		$idstring = "";
		if ($_POST['picid'] != '') {
			$idstring = "&picid=" . $_POST['picid'];
		} elseif ($_POST['storyid'] != '') {
			$idstring = "&storyid=" . $_POST['storyid'];
		}
        header('Location: /report.php?success=true' . $idstring);
		die();
} else {
	$idstring = "";
	if ($_POST['picid'] != '') {
		$idstring = "&picid=" . $_POST['picid'];
	} elseif ($_POST['storyid'] != '') {
		$idstring = "&storyid=" . $_POST['storyid'];
	}
    header('Location: /report.php?fail=true' . $idstring);
    die();
}

?>
