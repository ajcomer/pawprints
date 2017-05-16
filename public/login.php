<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php require_once("../includes/validation_functions.php"); ?>

<?php
$username = ""; // set username to SOMEthing because we display it
                // in the HTML below. so if you get the username/password
                // combo incorrect, it redisplays your username on the
                // redirect so you don't have to type it AGAIN.

if (isset($_POST['submit'])) {
  // Process the form
  
  // validations
  $required_fields = array("username", "password");
  validate_presences($required_fields);
  
  if (empty($errors)) {
    // Attempt Login

		$username = $_POST["username"];
		$password = $_POST["password"];
		
		$found_user = attempt_login($username, $password);

    if ($found_user) {
      // Success
			// Mark user as logged in
			$_SESSION["user_id"] = $found_user["user_id"]; // we use SESSIONs, not COOKIEs, since users can see COOKIEs. SESSIONs are server-side.
			$_SESSION["username"] = $found_user["username"];
      $_SESSION["admin_access"] = $found_user["admin_access"];
      
      if ($found_user["admin_access"] == 1)
        redirect_to("aindex.php");
      else
        redirect_to("uindex.php");
    }
    else {
      // Failure
      $_SESSION["message"] = "Sorry, username/password not found.";
    }
  }
} else {
    // This is probably a GET request
  
} // end: if (isset($_POST['submit']))

?>

<?php include("../includes/layouts/header.php"); ?>

    <?php echo message(); ?>
    <?php echo form_errors($errors); ?>
    
    <h2>Login</h2>
    <form action="login.php" method="post">
      <p>Username:
        <input type="text" name="username" value="<?php echo htmlentities($username); ?>" />
      </p>
      <p>Password:
        <input type="password" name="password" value="" />
      </p>
      <input type="submit" name="submit" value="Submit" />
    </form>

<?php include("../includes/layouts/footer.php"); ?>
