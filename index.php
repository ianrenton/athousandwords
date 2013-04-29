<?php

require_once('config.php');
require_once('common.php');
session_start();

mysql_connect(DB_SERVER,DB_USER,DB_PASS);
@mysql_select_db(DB_NAME) or die( "Unable to select database");

// If pic not specified, go to the latest.
$query = "SELECT * FROM pictures";
$result = mysql_query($query);
$numpics = mysql_num_rows($result);
if(!isset($_GET['pic'])) {
	$pic = $numpics;
} else {
	$pic = $_GET['pic'];
}

// If sort not specified, sort by newest
if(!isset($_GET['sort'])) {
	$sort = "newest";
} else {
	$sort = $_GET['sort'];
}

// Get the picture from the database, and the next one so we can work out
// when the picture *stopped* being featured.
$query = "SELECT * FROM pictures ORDER BY id ASC LIMIT 2 OFFSET " . ($pic-1);
$result = mysql_query($query);
$latestpic = mysql_fetch_assoc($result);
$nextpic = mysql_fetch_assoc($result);


// If the user has asked for a pic that doesn't exist, redirect to the homepage.
if ($latestpic == false) {
	header('Location: /');
	die();
}


// Work out the date range for which the pic was featured.
if ($nextpic == false) {
	$nextpicdate = "Now";
} else {
	$nextpicdate = date("jS F", strtotime($nextpic['featuredate']));
}
$featureddaterange = date("jS F", strtotime($latestpic['featuredate'])) . " - " . $nextpicdate;


// Work out the picture submitter
$query = "SELECT * FROM users WHERE (id = " . $latestpic['userid'] . ")";
$result = mysql_query($query);
$displayname = mysql_fetch_assoc($result);


// Work out the left/right links
if ($pic == 1) {
	$leftlink = "";
} else {
	$leftlink = "<a href=\"/picture/" . ($pic-1) . "/" . $sort . "\"></a>";
}
if ($pic == $numpics) {
	$rightlink = "";
} else {
	$rightlink = "<a href=\"/picture/" . ($pic+1) . "/" . $sort . "\"></a>";
}


// Get stories for this picture in chosen sort order
if ($sort == 'oldest') {
	$orderby = "submitdate ASC";
} else if ($sort == 'newest') {
	$orderby = "submitdate DESC";
} else {	
	$orderby = "stars DESC";
}
$query = "SELECT * FROM stories WHERE (pictureID = " . $latestpic['id'] . ") ORDER BY " . $orderby;
$stories = mysql_query($query);

// Get adult check
$isadult = adultCheck();

include('header.php');
?>

			<?php if (!isset($_SESSION['userID'])) { ?>
				<p class="introbig">Which thousand words does this picture paint for you?</p>
				<p class="introsmall">"a thousand words" is a fiction community where users write short stories inspired by pictures chosen by its own members.  <a href="/about">Learn More »</a></p>
			<?php } ?>
					
			<div id="picture">
				<div id="picturescrollleft" <?php if ($leftlink != "") { echo(' style="background: #eeeeee url(/images/scrollimageleft.gif);"'); } ?>><?php echo($leftlink); ?></div>
				<div id="picturescrollright" <?php if ($rightlink != "") { echo(' style="background: #eeeeee url(/images/scrollimageright.gif);"'); } ?>><?php echo($rightlink); ?></div>
				<div style="text-align:center;"><img id="mainpicture" src="<?php echo($latestpic['url']); ?>" /></div>
				<div id="picturemetadata">
					<div id="picturetitle">"<?php echo($latestpic['title']); ?>"
						<span class="picturesubmitter"> by <a href="<?php echo($latestpic['sourceurl']); ?>" target="_new"><?php echo($latestpic['artist']); ?></a>, <?php echo($latestpic['licence']); ?> licence, submitted by <a href="/user/<?php echo($latestpic['userid']) ?>"><?php echo($displayname['displayname']); ?></a>
							(<a href="/report.php?picid=<?php echo($latestpic['id']); ?>">Report?</a>)
						</span>
					</div>
					<div id="pictureweek"><?php echo($featureddaterange); ?></div>
					<div class="clear"></div>
				</div>
			</div>
			<div class="stories">
				<div class="storiesheader">
					<div class="sortstories">Sort by:<a href="/picture/<?php echo($pic); ?>/oldest">oldest</a> <a href="/picture/<?php echo($pic); ?>/newest">newest</a> <a href="/picture/<?php echo($pic); ?>/best">best</a></div>
					<div class="submitstory"><a href="/submitstory/<?php echo($pic); ?>"><img src="/images/plus-icon.png"/> Submit New Story</a></div>

				</div>
				<?php
				$numstoriesshown = 0;
				
				while ($story = mysql_fetch_assoc($stories)) {
					$shown = renderStoryExcerpt($story, $isadult, false, true);
					if ($shown == true) { $numstoriesshown++; }
				}
				
				if ($numstoriesshown == 0) {
					echo("<div class=\"storiesstory\"><p class=\"helptext\">There are no stories yet for this picture.  <a href=\"/submitstory/" . $pic . "\">Write your thousand words</a>, and be the first!</a></p></div>");
				}
				
				?>
			</div>
				
<?php
$displayfootertext = true;
include('footer.php');

mysql_close();

?>
