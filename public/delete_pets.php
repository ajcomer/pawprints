<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php confirm_logged_in(); ?>

<?php
  $pet = find_pet_by_id($_GET["pet_id"]);
  if (!$pet) {
    // user ID was missing or invalid or 
    // user couldn't be found in database
    redirect_to("manage_dogs.php");
  }
  
  $pet_id = $pet["pet_id"];
  $query = "DELETE FROM pets WHERE pet_id = {$pet_id} LIMIT 1";
  $result = mysqli_query($connection, $query);

  if ($result && mysqli_affected_rows($connection) == 1) {
    // Success
    $_SESSION["message"] = "Pet deleted.";
    redirect_to("manage_dogs.php");
  } else {
    // Failure
    $_SESSION["message"] = "Pet deletion of failed.";
    redirect_to("manage_dogs.php");
  }
  
?>