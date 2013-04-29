<?php

/* Start session and load lib */
session_start();
require_once('config.php');

// CAPTCHA
require_once('recaptchalib.php');
$privatekey = "6LeG1LwSAAAAAD8ThriUlKNPVLbQQUgmwLgERGXF";

$resp = recaptcha_check_answer ($privatekey,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);

if (!$resp->is_valid) {
    header('Location: ./register?fail=true');
    die();
}

if ((isset($_POST['email'])) && (isset($_POST['displayname'])) && ($_POST['displayname'] != "") && (isset($_POST['password']))) {
        mysql_connect(DB_SERVER,DB_USER,DB_PASS);
        @mysql_select_db(DB_NAME) or die( "Unable to select database");
        
        // Add the user to the table
        $query = "SELECT * FROM users WHERE email='" . mysql_real_escape_string($_POST['email']) . "'";
        $result = mysql_query($query);
        if (!mysql_num_rows($result) ) {
			$secret = createSecret();
            $query = "INSERT INTO users VALUES (0, '" . mysql_real_escape_string($_POST['email']) . "','" . mysql_real_escape_string(md5($_POST['password'])) . "','" . mysql_real_escape_string($_POST['displayname']) . "', 0, '', '', '', '', '" . mysql_real_escape_string($_POST['dob']) . "', '" . date("Y-m-d H:i:s") . "', '" . $secret . "', 0)";
            mysql_query($query);
			$displayname = strip_tags($_POST['displayname']);
			$body = "Hi " . mysql_real_escape_string($displayname) . ",\nThank you for registering an account on \"a thousand words\".  Please click the following link to activate your account, then log in with the password you have set.\n \nhttp://athousandwords.org.uk/login.php?email=" . mysql_real_escape_string($_POST['email']) . "&secret=" . $secret;
			if (mail($_POST['email'], 'Registration on "a thousand words"', $body)) {
            	header('Location: ./register.php?emailed=true');
			} else {
            	header('Location: ./register.php?fail=true');
			}
			die();
        } else {
            header('Location: ./register.php?fail=true');
    		die();
        }
        mysql_close();
} else {
    header('Location: ./register?fail=true');
    die();
}

function createSecret() {
    $length = 100;
    $characters = '0123456789abcdef';
    $string = '';    

    for ($p = 0; $p < $length; $p++) {
        $string .= $characters[mt_rand(0, strlen($characters))];
    }

    return $string;
}

?>
