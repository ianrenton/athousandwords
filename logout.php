<?php

/* Load and clear sessions */
session_start();
session_destroy();

// Destroy cookies
setcookie('userID', '', mktime()-1);
setcookie('secret', '', mktime()-1);
 
/* Redirect to homepage. */
header('Location: ./');
die();

?>