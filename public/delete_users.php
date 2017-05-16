<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php confirm_admin(); ?>

<?php
  $user = find_user_by_id($_GET["user_id"]);
  if (!$user) {
    // user ID was missing or invalid or 
    // user couldn't be found in database
    redirect_to("manage_users.php");
  }
  
  $user_id = $user["user_id"];
  $query = "DELETE FROM users WHERE user_id = {$user_id} LIMIT 1";
  $result = mysqli_query($connection, $query);

  if ($result && mysqli_affected_rows($connection) == 1) {
    // Success
    $_SESSION["message"] = "User deleted.";
    redirect_to("manage_users.php");
  } else {
    // Failure
    $_SESSION["message"] = "User deletion of failed.";
    redirect_to("manage_users.php");
  }
  
?>
