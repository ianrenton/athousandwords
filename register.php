<?php

$title = "Register";

include('header.php');
require_once('recaptchalib.php');
$publickey = "6LeG1LwSAAAAADsQfXoCfeTCH6JuxAGyImxoIjux";

?>
 <script type="text/javascript">
 var RecaptchaOptions = {
    theme : 'custom',
    custom_theme_widget: 'recaptcha_widget'
 };
 </script>
<?php

if (isset($_GET['emailed'])) {
   echo( '<p class="successnotice">Registration was successful.  An e-mail with activation instructions has been sent to the address you registered with.</p>');
} else {
	  
	if (isset($_GET['fail'])) {
	   echo( '<p class="failnotice">Sorry!  Registration failed.  Either that e-mail address is already registered, it wasn\'t a valid e-mail address, or you entered the CAPTCHA words incorrectly.</p>');
	}
?>
<form name="registerform" method="post" action="registercallback.php">
        <table border="0" align="center" cellpadding="5" cellspacing="5" style="margin:50px auto 50px auto;">
        <tr>
        <td>E-mail address<br>(never made public)</td>
        <td><input name="email" type="text" id="email" style="width:200px"></td>
        </tr>
        <tr>
        <td>Display Name</td>
        <td><input name="displayname" type="text" id="displayname" style="width:200px"></td>
        </tr>
        <tr>
        <td>Password</td>
        <td><input name="password" type="password" id="password" style="width:200px"></td>
        </tr>
        <tr>
        <td>Date of Birth (YYYY-MM-DD)<br>(never made public)</td>
        <td><input name="dob" type="text" id="dob" style="width:200px"></td>
        </tr>
        <tr>
        <td>CAPTCHA</td>
        <td>


 <div id="recaptcha_widget">

   <div id="recaptcha_image"></div>
   <div class="recaptcha_only_if_incorrect_sol" style="color:red">Incorrect please try again</div>

   <span class="recaptcha_only_if_image">Please enter the words above:</span>
   <span class="recaptcha_only_if_audio">Please enter the numbers you hear:</span>

   <input type="text" id="recaptcha_response_field" name="recaptcha_response_field" />

   <div><a href="javascript:Recaptcha.reload()">Hard to read? Get new words</a></div>
   <div class="recaptcha_only_if_image"><a href="javascript:Recaptcha.switch_type('audio')">Get an audio CAPTCHA</a></div>
   <div class="recaptcha_only_if_audio"><a href="javascript:Recaptcha.switch_type('image')">Get an image CAPTCHA</a></div>

   <div>Powered by reCAPTCHA.  <a href="javascript:Recaptcha.showhelp()">Get help</a>

 </div>

 <script type="text/javascript"
    src="http://www.google.com/recaptcha/api/challenge?k=6LeG1LwSAAAAADsQfXoCfeTCH6JuxAGyImxoIjux">
 </script>
 <noscript>
   <iframe src="http://www.google.com/recaptcha/api/noscript?k=6LeG1LwSAAAAADsQfXoCfeTCH6JuxAGyImxoIjux"
        height="300" width="500" frameborder="0"></iframe><br>
   <textarea name="recaptcha_challenge_field" rows="3" cols="40">
   </textarea>
   <input type="hidden" name="recaptcha_response_field"
        value="manual_challenge">
 </noscript></td>
        </tr>
        <tr>
        <td>&nbsp;</td>
        <td><input type="submit" name="Submit" value="Register"></td>
        </tr>
        </table>
        </form>

<?php
}

include('footer.php')
?>

