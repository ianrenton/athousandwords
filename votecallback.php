<?php

/* Start session and load lib */
session_start();
require_once('config.php');
require_once('common.php');

// If not logged in, deny access
if (!isset($_SESSION['userID'])) {
	header('Location: ./login');
    die();
}

if ((isset($_POST['stars'])) && (isset($_POST['storyid']))) {
	
    mysql_connect(DB_SERVER,DB_USER,DB_PASS);
    @mysql_select_db(DB_NAME) or die( "Unable to select database");

    // Delete user's old vote, if it exists
	$query = "DELETE FROM votes WHERE (userid = " . mysql_real_escape_string($_SESSION['userID']) . ") AND (storyid = " . mysql_real_escape_string($_POST['storyid']) . ")";
	mysql_query($query);
	
	// Get story's owner
	$query = "SELECT * FROM stories WHERE (id = " . $_POST['storyid'] . ")";
	$result = mysql_query($query);
	$story = mysql_fetch_assoc($result);
	$storyowner = $story['userid'];
	
	// Can't vote for your own stories
	if ($storyowner == $_SESSION['userID']) {
		mysql_close();
		header('Location: ./story/' . $_POST['storyid']);
		die();
	}
	
	// Add new vote
	$query = "INSERT INTO votes VALUES (" . mysql_real_escape_string($_SESSION['userID']) . ", " . mysql_real_escape_string($_POST['storyid']) . ", " . mysql_real_escape_string($storyowner) . "," . mysql_real_escape_string($_POST['stars']) . ")";
    mysql_query($query);

	updateStars();
	
    mysql_close();
	header('Location: ./story/' . $_POST['storyid']);
	die();

} else {
    header('Location: ./');
    die();
}

?>
