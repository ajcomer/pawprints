<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php confirm_logged_in(); ?>

<?php
  $park = find_park_by_id($_GET["park_id"]);
  if (!$park) {
    redirect_to("manage_parks.php");
  }

  // Reassign all dogs currently registered at the park to a "Unassigned" park
  $dog_park_reset = "Unassigned";
  $query = "UPDATE pets SET ";
  $query .= "dog_park = '{$dog_park_reset}' ";
  $query .= "WHERE dog_park = '{$park[park_name]}'";
  $result_update = mysqli_query($connection, $query);

  if ($result_update && mysqli_affected_rows($connection) >= 1) {
    // Success
    $_SESSION["message"] = "Pets assigned to dog park, \"{$park[park_name]}\" have had their dog park reassigned to \"{$dog_park_reset}\". ";
  } 
  elseif ($result_update && mysqli_affected_rows($connection) == 0) {
    $_SESSION["message"] = "No park reassignments necessary. ";
  }
  else {
    // Failure
    $_SESSION["message"] = "Park reassignment failed. ";
  }

  // Delete the park
  $park_id = $park["park_id"];
  $query = "DELETE FROM parks WHERE park_id = {$park_id} LIMIT 1";
  $result = mysqli_query($connection, $query);

  if ($result && mysqli_affected_rows($connection) == 1) {
    // Success
    $_SESSION["message"] .= "Park deleted.";
    redirect_to("manage_parks.php");
  } else {
    // Failure
    $_SESSION["message"] .= "Park deletion of failed.";
    redirect_to("manage_parks.php");
  }
  
?>
