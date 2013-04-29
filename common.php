<?php

require_once('config.php');
session_start();

mysql_connect(DB_SERVER,DB_USER,DB_PASS);
@mysql_select_db(DB_NAME) or die( "Unable to select database");

// Adult check
function adultCheck() {
	$eighteenyearsago = strtotime("today-18 years");
	$isadult = "maybe";
	if (isset($_SESSION['userID'])) {
		$query = "SELECT dob FROM users WHERE id=" . $_SESSION['userID'];
		$result = mysql_query($query);
		$row = mysql_fetch_assoc($result);
		if (strtotime($row['dob']) < $eighteenyearsago) {
			$isadult = "yes";
		} else {
			$isadult = "no";
		}
	}
	return $isadult;
}



// Render a story excerpt for index and user pages.  Returns boolean
// to say whether or not it was displayed due to age restrictions
function renderStoryExcerpt($story, $isadult, $showpicture, $showuser) {
	
	if (($isadult == "no") && ($story['adult'] > 0)) {
		// Don't display it, user isn't an adult
		return false;
	} else {
		// User is or may be adult
		
		echo("<div class=\"storiesstory\">");
		
		if ($showpicture == true) {
			$query = "SELECT * FROM pictures WHERE (id = " . $story['pictureid'] . ")";
			$result = mysql_query($query);
			$picture = mysql_fetch_assoc($result);
			echo("<a href=/picture/" . $picture['id'] . "><img id=\"insetpicture\" style=\"height:60px; margin-top:10px; margin-left:20px;\" src=\"" . $picture['url'] . "\" title=\"" . $picture['title'] . "\" /></a>");
		}
		
		echo("<div class=\"stars\">");
		if ($story['stars'] == -1) {
			echo("<span class=\"novotes\">no votes yet</span>");
		} else {
			for ($i=0; $i<$story['stars']; $i++)  echo("<img src=\"/images/star.gif\"/>");
			for ($i=0; $i<(5-$story['stars']); $i++)  echo("<img src=\"/images/nostar.gif\"/>");
		}
		echo("</div>");
		echo("<span class=\"storiesstorytitle\"><a href=\"/story/" . $story['id'] . "\">");
		if (strlen($story['title']) > 40) {
			echo(substr($story['title'],0,35) . "[...]");
		} else {
			echo($story['title']);
		}
		echo("</a></span> <span class=\"storiesstoryauthor\">");
		
		if ($showuser == true) {
			$query = "SELECT displayname FROM users WHERE (id = " . $story['userid'] . ")";
			$result = mysql_query($query);
			$displayname = mysql_fetch_assoc($result);
			echo("<a href=\"/user/" . $story['userid'] . "\">" . $displayname['displayname'] . "</a> &#8212; ");
		}
		
		echo(date("jS F Y", strtotime($story['submitdate'])) . "</span>");
		
		if (($isadult == "yes") || ($story['adult'] == 0)) {
			echo("<div class=\"storiesstorycontent\"><a href=\"/story/" . $story['id'] . "\">" . substr($story['text'],0,300) . "[...]</a></div>");
		} else {
			echo("<div class=\"storiesstorycontent\"><p class=\"adultcontent\"><a href=\"/story/" . $story['id'] . "\">This story is marked as containing adult content.  Click here to read it if you're okay with that.</a></p></div>");
		}
		echo("<div class=\"clear\"></div></div>");
	}
	return true;
}


// Update star counts
function updateStars() {
	// Recalculate story stars
	$query = "SELECT * FROM stories";
	$stories = mysql_query($query);
	while ($story = mysql_fetch_assoc($stories)) {
		$stars = 0;
		$query = "SELECT * FROM votes WHERE (storyid = " . $story['id'] . ")";
		$votes = mysql_query($query);
		$numvotes = mysql_num_rows($votes);
		while ($vote = mysql_fetch_assoc($votes)) {
			$stars = $stars + $vote['stars'];
		}
		if ($numvotes > 0) {
			$stars = round($stars / $numvotes);
		} else {
			$stars = -1;
		}
		$query = "UPDATE stories SET stars=" . $stars . " WHERE id=" . $story['id'];
		mysql_query($query);
	}

	// Recalculate user stars
	$query = "SELECT * FROM users";
	$users = mysql_query($query);
	while ($user = mysql_fetch_assoc($users)) {
		$stars = 0;
		$query = "SELECT * FROM votes WHERE (targetuserid = " . $user['id'] . ")";
		$votes = mysql_query($query);
		while ($vote = mysql_fetch_assoc($votes)) {
			$stars = $stars + $vote['stars'];
		}
		$query = "UPDATE users SET stars=" . $stars . " WHERE id=" . $user['id'];
		mysql_query($query);
	}
}

mysql_close();

?>