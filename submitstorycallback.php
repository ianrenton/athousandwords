<?php

/* Start session and load lib */
session_start();
require_once('config.php');

// If not logged in, deny access
if (!isset($_SESSION['userID'])) {
	header('Location: ./login');
    die();
}

if ((isset($_POST['title'])) && ($_POST['title'] != "") && (isset($_POST['pictureid'])) && ($_POST['pictureid'] != '') && (isset($_POST['text'])) && ($_POST['text'] != "")) {
    if (DB_SERVER != '') {
        mysql_connect(DB_SERVER,DB_USER,DB_PASS);
        @mysql_select_db(DB_NAME) or die( "Unable to select database");
        
        // Add the item to the stories table
		$title = strip_tags($_POST['title']);
		$text = strip_tags($_POST['text'], '<em><i><strong><b><u><br>');
		if ((isset($_POST['critique'])) && ($_POST['critique'] == "true")) {
		    $critique = 1;
		} else {
		    $critique = 0;
		}
		if ((isset($_POST['adult'])) && ($_POST['adult'] == "true")) {
		    $adult = 1;
		} else {
		    $adult = 0;
		}
        $query = "INSERT INTO stories VALUES (0, " . mysql_real_escape_string($_SESSION['userID']) . ", '" . mysql_real_escape_string($_POST['pictureid']) . "', '" . date("Y-m-d H:i:s") . "', '" . mysql_real_escape_string($title) . "', '" . mysql_real_escape_string($text) . "', -1, " . $critique . ", " . $adult . ")";
        mysql_query($query);
        header('Location: ./story/' . mysql_insert_id());
		die();
        mysql_close();
    }
} else {
    header('Location: ./submitstory?fail=true');
    die();
}

?>
