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

// Get the user from the database.
$query = "SELECT * FROM users WHERE (id = " . $id . ")";
$result = mysql_query($query);
$user = mysql_fetch_assoc($result);


// If the user has asked for a profile that doesn't exist, redirect to the homepage.
if ($user == false) {
	header('Location: /');
	die();
}

// Get gravatar
$grav_url = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $user['email'] ) ) ) . "?d=identicon&s=50";


// Get the user's stories from the database.
$query = "SELECT * FROM stories WHERE (userid = " . $id . ")";
$stories = mysql_query($query);

// Get adult check
$isadult = adultCheck();

$title = $user['displayname'] . "'s Profile";

include('header.php');
?>
			
                    <div class="stories">
						<div class="storiesheader">
							<div class="sortstories">
								<?php if ($user['id'] == $_SESSION['userID']) {
									echo("<a href=\"/editprofile\">Edit my Profile</a>");
								} ?>
							</div>
							<div class="submitstory">Information</div>
						</div>

					<div class="avatar"><img src="<?php echo $grav_url; ?>" /></div>
					<h1 class="profiledisplayname"><?php echo($user['displayname']); ?></h1>
					<div class="profileinfo">
					<p>Registered on: <?php echo(date("l, jS F Y", strtotime($user['regdate']))); ?></p>
					<?php foreach(explode(" ", strip_tags($user['location'])) as $key => $line) {
						if (strlen($line) > 70) $user['location'] = str_replace($line, wordwrap($line, 68, "- ", 1), $user['location']);
					}?>
					<p>Location: <?php echo($user['location']); ?></p>
					<p>Total Stars: <?php echo($user['stars']); ?></p>
					<?php foreach(explode(" ", strip_tags($user['favauthor'])) as $key => $line) {
						if (strlen($line) > 70) $user['favauthor'] = str_replace($line, wordwrap($line, 68, "- ", 1), $user['favauthor']);
					}?>
					<p>Favourite Author: <?php echo($user['favauthor']); ?></p>
					<?php foreach(explode(" ", strip_tags($user['favartist'])) as $key => $line) {
						if (strlen($line) > 70) $user['favartist'] = str_replace($line, wordwrap($line, 68, "- ", 1), $user['favartist']);
					}?>
					<p>Favourite Artist: <?php echo($user['favartist']); ?></p>
					</div>
					</div>

                    <div class="stories">
						<div class="storiesheader"><div class="submitstory">Stories</div></div>
<?php

                $numstoriesshown = 0;
				
				while ($story = mysql_fetch_assoc($stories)) {
					$shown = renderStoryExcerpt($story, $isadult, true, false);
					if ($shown == true) { $numstoriesshown++; }
				}
				
				if ($numstoriesshown == 0) {
					echo("<div class=\"storiesstory\"><p class=\"helptext\">This user has not yet submitted any stories.</p></div>");
				}
				
				echo('</div>');

$displayfootertext = true;
include('footer.php');

mysql_close();

?>
