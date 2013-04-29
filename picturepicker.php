<?php

require_once('config.php');
session_start();

mysql_connect(DB_SERVER,DB_USER,DB_PASS);
@mysql_select_db(DB_NAME) or die( "Unable to select database");

// Get pictures
$query = "SELECT * FROM pictures ORDER BY id ASC";
$pictures = mysql_query($query);

// Get max picture ID
$query = "SELECT max(id) FROM pictures";
$result = mysql_query($query);
$maxidrow = mysql_fetch_assoc($result);
$maxid = $maxidrow['max(id)'];

// Get initial selection
if (isset($_GET['id'])) {
	$id = $_GET['id'];
} else {
	$id = -1;
}
?>
<script type="text/javascript" > 
function selectPicture(i) { 
	for(var q=1;q<=<?php echo($maxid); ?>;q++) {
		document.getElementById("pic"+q).style.borderStyle="solid";
		document.getElementById("pic"+q).style.borderWidth="3px";
		document.getElementById("pic"+q).style.borderColor="white";	
	}
	document.getElementById("pic"+i).style.borderColor="#bb6666";
	parent.setPictureID(i);
} 
</script>
<style type="text/css">
img {border-style:solid; border-width:3px; border-color:white; }
</style>

<?php

while($picture = mysql_fetch_assoc($pictures)) {
	echo('<img id="pic' . $picture['id'] . '" src="' . $picture['url'] . '" alt="' . $picture['name'] . '" title="' . $picture['name'] . '" height="90" onclick="selectPicture(\'' . $picture['id'] . '\')"');
	if ($picture['id'] == $id) {
		echo(' style="border-color: #bb6666"');
	}
	echo('/>');
}

mysql_close();

?>