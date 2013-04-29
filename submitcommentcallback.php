<?php

/* Start session and load lib */
session_start();
require_once('config.php');

// If not logged in, deny access
if (!isset($_SESSION['userID'])) {
	header('Location: ./login');
    die();
}

if ((isset($_POST['storyid'])) && (isset($_POST['text']))) {
    mysql_connect(DB_SERVER,DB_USER,DB_PASS);
    @mysql_select_db(DB_NAME) or die( "Unable to select database");

    // Add item to the comments table
	$text = strip_tags($_POST['text'], '<em><i><strong><b><u><br><a>');
	$query = "INSERT INTO comments VALUES (0, " . mysql_real_escape_string($_SESSION['userID']) . ", '" . mysql_real_escape_string($_POST['storyid']) . "', '" . date("Y-m-d H:i:s") . "', '" . mysql_real_escape_string($text) . "')";
    mysql_query($query);
    header('Location: ./story/' . $_POST['storyid']);

	die();
    mysql_close();
} else {
    header('Location: ./');
    die();
}

?>
