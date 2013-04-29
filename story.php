<?php

require_once('config.php');
require_once('common.php');
session_start();

mysql_connect(DB_SERVER,DB_USER,DB_PASS);
@mysql_select_db(DB_NAME) or die( "Unable to select database");

// If id not specified, go to the home page.
if(!isset($_GET['id'])) {
	header('Location: /');
	die();
} else {
	$id = $_GET['id'];
}

// Get the story from the database.
$query = "SELECT * FROM stories WHERE (id = " . $id . ")";
$result = mysql_query($query);
$story = mysql_fetch_assoc($result);


// If the user has asked for a story that doesn't exist, redirect to the homepage.
if ($story == false) {
	header('Location: /');
	die();
}


// Get author's display name
$query = "SELECT * FROM users WHERE (id = " . $story['userid'] . ")";
$result = mysql_query($query);
$displayname = mysql_fetch_assoc($result);

// Get picture's URL
$query = "SELECT * FROM pictures WHERE (id = " . $story['pictureid'] . ")";
$result = mysql_query($query);
$picture = mysql_fetch_assoc($result);

// Get gravatar
$grav_url = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $displayname['email'] ) ) ) . "?d=identicon&s=50";

// Get adult check
$isadult = adultCheck();

$title = "\"" . $story['title'] . "\" by " . $displayname['displayname'];

include('header.php');
?>
			<div id="singlestory">
				<a href="<?php echo("/picture/" . $picture['id']); ?>"><img id="insetpicture" src="<?php echo($picture['url']); ?>" title="<?php echo($picture['title']); ?>" /></a>
				<div id="singlestorytitle">
					<?php foreach(explode(" ", strip_tags($story['title'])) as $key => $line) {
						if (strlen($line) > 30) $story['title'] = str_replace($line, wordwrap($line, 25, "- ", 1), $story['title']);
					}
					echo($story['title']); ?>
				</div>
				<div id="singlestoryauthor">
                    <div class="avatar"><img src="<?php echo $grav_url; ?>" /></div>
					Written by <a href="/user/<?php echo($story['userid']) ?>"><?php echo($displayname['displayname'] . "</a>");
					if ($story['userid'] == $_SESSION['userID']) {
						echo(" (<a href=\"/editstory/" . $story['id'] . "\">Edit this story</a>)");
					}
					echo("<br>Posted on " . date("jS F Y", strtotime($story['submitdate'])) . "<br>");
					
					if ($story['adult'] > 0) {
						echo("This story contains adult content.<br/>");
					}
					
					if ($story['stars'] == -1) {
						echo("<span class=\"novotes\">No votes yet!</span><br/>");
					} else {
						for ($i=0; $i<$story['stars']; $i++)  echo("<img src=\"/images/star.gif\"/>");
						for ($i=0; $i<(5-$story['stars']); $i++)  echo("<img src=\"/images/nostar.gif\"/>");
					}
				
if ((isset($_SESSION['userID'])) && ($story['userid'] != $_SESSION['userID'])) {
	// Get user's old vote, if it exists
	$query = "SELECT * FROM votes WHERE (userid = " . $_SESSION['userID'] . ") AND (storyid = " . $story['id'] . ")";
	$result = mysql_query($query);
	$voterow = mysql_fetch_assoc($result);
	if ($voterow == false) {
		$default = "";
	} else {
		$default = $voterow['stars'];
	}

?>

			<form name="voteform" method="post" action="/votecallback.php">
		        My opinion: 
				<select name="stars" onchange="this.form.submit()">
					<option <?php if ($default == "") { echo('selected'); } ?>></option>
					<option <?php if ($default == "0") { echo('selected'); } ?>>0</option>
					<option <?php if ($default == "1") { echo('selected'); } ?>>1</option>
					<option <?php if ($default == "2") { echo('selected'); } ?>>2</option>
					<option <?php if ($default == "3") { echo('selected'); } ?>>3</option>
					<option <?php if ($default == "4") { echo('selected'); } ?>>4</option>
					<option <?php if ($default == "5") { echo('selected'); } ?>>5</option>
				</select> stars
				<input type="hidden" name="storyid" value="<?php echo($story['id']); ?>">
		     </form>

<?php } ?>
		
	<a href="/report.php?storyid=<?php echo($story['id']); ?>">Report this story?</a><br/>

				</div>
				<div id="singlestorycontent">
					<?php 
					if (($story['adult'] > 0) && ($isadult == "no")) {
						echo("<p class=\"helptext\" style=\"margin-top:40px;\">Sorry, but your date of birth restricts you from viewing adult content.</p>");
					} else {
						echo("<p>" . str_replace("\n", "</p><p>", $story['text']) . "</p>");
					}
					 ?>
				</div>

			
<?php

// Get comments
$query = "SELECT * FROM comments WHERE (storyid = " . $story['id'] . ")";
$comments = mysql_query($query);

// Only print the comments header if there are some, or if the user is logged in (so we're displaying a comment form).
if ((mysql_num_rows($comments) > 0) || (isset($_SESSION['userID']))) {
    echo("<h3>Comments</h3>");
    if ($story['critique']) {
        echo("<p class=\"critiquetext\">The author has requested critique for this story.  Fire away!</p>");
    } else {
        echo("<p class=\"critiquetext\">The author has <em>not</em> requested critique for this story.  Please be nice!</p>");
    }
}


while ($comment = mysql_fetch_assoc($comments)) {
	
	// Get author's display name
	$query = "SELECT * FROM users WHERE (id = " . $comment['userid'] . ")";
	$result = mysql_query($query);
	$displayname = mysql_fetch_assoc($result);

	// Get gravatar
	$grav_url = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $displayname['email'] ) ) ) . "?d=identicon&s=50";

?>
			<div class="comment">
				<div class="commentauthor">
                    <div class="avatar"><img src="<?php echo $grav_url; ?>" /></div>
					<a href="/user/<?php echo($comment['userid']) ?>"><?php echo($displayname['displayname'] . "</a>");
					echo(" &#8212; " . date("g:ia, jS F Y", strtotime($comment['submitdate'])) . "<br>"); ?>
				</div>
				<div class="commentcontent">
					<?php echo("<p>" . str_replace("\n", "</p><p>", $comment['text']) . "</p>"); ?>
				</div>
			</div>
				

<?php
}
if (isset($_SESSION['userID'])) {
?>
				<div class="submitcomment">
					<form name="submitcommentform" method="post" action="/submitcommentcallback.php">
	        			<textarea name="text" type="text" id="text" style="width:400px; height:200px;"></textarea><br/>
						<input type="hidden" name="storyid" value="<?php echo($story['id']); ?>">
						<input type="submit" name="Submit" value="Submit">
	        		</form>
				</div>

<?php } ?>
</div>
<?php

$displayfootertext = true;
include('footer.php');

mysql_close();

?>
