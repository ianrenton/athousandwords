<?php 

session_start();

// Log in from cookie if it exists and is correct.
if ((!empty($_COOKIE['userID'])) && (!empty($_COOKIE['secret']))) {
	if (DB_SERVER != '') {
        mysql_connect(DB_SERVER,DB_USER,DB_PASS);
        @mysql_select_db(DB_NAME) or die( "Unable to select database");
        
        // Get the user's row from the table
        $query = "SELECT * FROM users WHERE id='" . mysql_real_escape_string(unserialize($_COOKIE['userID'])) . "'";
        $result = mysql_query($query);
        
        // Username check
        if (mysql_num_rows($result) > 0) {
            // Secret check
            $dbSecret = mysql_result($result, 0, "secret");
            if (strcmp($dbSecret, unserialize($_COOKIE['secret'])) == 0) {
                // Secret matched, so set user id
                $_SESSION['userID'] = mysql_result($result, 0, "id");
            }
        }
    }
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<head>
		<title><?php if (isset($title)) { echo($title . " | "); } ?>a thousand words</title>
		<meta http-equiv="X-UA-Compatible" content="IE=8" />
		<link rel="icon" href="/favicon.ico" type="image/x-icon" />
        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />

		<link type="text/css" rel="stylesheet" media="all" href="/css/style.css" />
	</head>
	<body>
		<div id="wrapper">
			<!--p style="color:red; border: 1px solid red; padding:5px; margin: 20px auto; width: 70%; font-family:sans-serif; font-size:90%;">This site is currently in beta test.  Pictures and stories added to the site during this phase will be deleted when the site launches.  Please report any bugs you find, or features you would like to see added before launch, using <a href="http://www.onlydreaming.net/contact">this contact form</a>.</p-->
			<div id="header">
				<div id="headermenu">
					<?php if (isset($_SESSION['userID'])) { ?>
						<a href="/submitpicture">submit picture</a><a href="/submitstory<?php if ($pic != "") { echo("/" . $pic); } ?>">submit story</a><a href="/user/<?php echo($_SESSION['userID']); ?>">profile</a><a href="/logout">log out</a>
					<?php } else { ?>
						<a href="/login">log in</a><a href="/register">register</a>
					<?php } ?>
				</div>
				<h1 id="sitename">
					<a href="/">a thousand words</a>
				</h1>
			</div>
			
