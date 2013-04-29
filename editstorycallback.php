<?php

/* Start session and load lib */
session_start();
require_once('config.php');

// If not logged in, deny access
if (!isset($_SESSION['userID'])) {
	header('Location: ./login');
    die();
}

if ((isset($_POST['id'])) && (isset($_POST['title'])) && ($_POST['title'] != "") && (isset($_POST['pictureid'])) && ($_POST['pictureid'] != '') && (isset($_POST['text'])) && ($_POST['text'] != "")) {
    mysql_connect(DB_SERVER,DB_USER,DB_PASS);
    @mysql_select_db(DB_NAME) or die( "Unable to select database");
    
	if ((isset($_POST['delete'])) && ($_POST['delete'] == "true")) {
		// Delete item
		$query = "DELETE FROM stories WHERE id=" . mysql_real_escape_string($_POST['id']);
		mysql_query($query);
	    header('Location: ./picture/' . $_POST['pictureid']);
	} else {
	    // Update the item in the stories table
		$title = strip_tags($_POST['title']);
	    $query = "UPDATE stories SET title='" . mysql_real_escape_string($title) . "' WHERE id=" . mysql_real_escape_string($_POST['id']);
	    mysql_query($query);
	    $query = "UPDATE stories SET pictureid='" . mysql_real_escape_string($_POST['pictureid']) . "' WHERE id=" . mysql_real_escape_string($_POST['id']);
	    mysql_query($query);
		$text = strip_tags($_POST['text'], '<em><i><strong><b><u><br>');
	    $query = "UPDATE stories SET text='" . mysql_real_escape_string($text) . "' WHERE id=" . mysql_real_escape_string($_POST['id']);
		mysql_query($query);
		if ((isset($_POST['critique'])) && ($_POST['critique'] == "true")) {
		    $query = "UPDATE stories SET critique=1 WHERE id=" . mysql_real_escape_string($_POST['id']);
		} else {
		    $query = "UPDATE stories SET critique=0 WHERE id=" . mysql_real_escape_string($_POST['id']);
		}
		mysql_query($query);
		if ((isset($_POST['adult'])) && ($_POST['adult'] == "true")) {
		    $query = "UPDATE stories SET adult=1 WHERE id=" . mysql_real_escape_string($_POST['id']);
		} else {
		    $query = "UPDATE stories SET adult=0 WHERE id=" . mysql_real_escape_string($_POST['id']);
		}
		mysql_query($query);
	    header('Location: ./story/' . $_POST['id']);
	}
	die();
    mysql_close();
} else {
    header('Location: ./editstory.php?id=' . $_POST['id'] . '&fail=true');
    die();
}

?>
