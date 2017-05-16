<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/functions.php"); ?>

<?php
	// v1: simple logout
	// session_start(); (this is done in the session.php included above)
	// sufficiently erases their admin_id which is required
	// in confirm_logged_in(); at the top of all admin pages.
	//$_SESSION["user_id"] = null;
	//$_SESSION["username"] = null;
	//$_SESSION["admin_access"] = null;
	//redirect_to("login.php");
?>

<?php
	// v2: destroy session
	// assumes nothing else in session to keep
	//session_start();
	 $_SESSION = array(); // tell session to be set to an EMPTY array (clears it)
	 if (isset($_COOKIE[session_name()])) { // if the cookie for the session name still there, then...
	   setcookie(session_name(), '', time()-42000, '/'); // make the cookie expire.
	 }
	 session_destroy(); 
	 redirect_to("index.php");
?>
