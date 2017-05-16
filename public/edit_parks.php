<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php require_once("../includes/validation_functions.php"); ?>
<?php confirm_logged_in(); ?>

<?php
  $park = find_park_by_id($_GET["park_id"]);
  
  if (!$park) {
    // park_id was missing or invalid or 
    // park couldn't be found in database
    redirect_to("index.php");
  }
?>

<?php
if (isset($_POST['submit'])) {
  // Process the form
  
  // validations
  $required_fields = array("parkName");
  validate_presences($required_fields);
  
  $fields_with_max_lengths = array("parkName" => 30);
  validate_max_lengths($fields_with_max_lengths);
  
  if (empty($errors)) {
    
    // Perform Update
    $park_id = $park["park_id"];
    $parkName = mysql_prep($_POST["parkName"]);
    $street = mysql_prep($_POST["street"]);
    $city = mysql_prep($_POST["city"]);
    $state = mysql_prep($_POST["state"]);
    $zip_code = mysql_prep($_POST["zip_code"]);
    $country = mysql_prep($_POST["country"]);
    //$sepSections = mysql_prep($_POST["separateSections"]);
    //$water = mysql_prep($_POST["water"]);
    $description = mysql_prep($_POST["parkDescription"]);
  
    $query  = "UPDATE parks SET ";
    $query .= "park_name = '{$parkName}', ";
    $query .= "street = '{$street}', ";
    $query .= "city = '{$city}', ";
    $query .= "state = '{$state}', ";
    $query .= "zip_code = '{$zip_code}', ";
    $query .= "country = '{$country}', ";
    //$query .= "separateSections = {$sepSections}, ";
    //$query .= "waterAvailable = {$water}, ";
    $query .= "parkDescription = '{$description}' ";
    $query .= "WHERE park_id = {$park_id} ";
    $query .= "LIMIT 1";
    $result = mysqli_query($connection, $query);

    if ($result && mysqli_affected_rows($connection) >= 1)
    {
      // Success
      $_SESSION["message"] = "Park profile updated.";
    }
    elseif ($result && mysqli_affected_rows($connection) == 0)
    {
      $_SESSION["message"] = "No changes made.";
    }
    else
    {
      // Failure
      $_SESSION["message"] = "Park profile update failed.";

    }
    redirect_to("manage_parks.php");
  }
} else {
  // This is probably a GET request
  
} // end: if (isset($_POST['submit']))

?>

<?php include("../includes/layouts/header.php"); ?>

    <?php echo message(); ?>
    <?php echo form_errors($errors); ?>
    
    <h2>Edit Park Profile: <?php echo htmlentities($park["park_name"]); ?></h2>
    <form action="edit_parks.php?park_id=<?php echo urlencode($park["park_id"]); ?>" method="post">
    <form action="new_park.php" method="post">
      <table>
        <tr>
          <td><b>Park Name:</b></td>
          <td><input type="text" name="parkName" value="<?php
              if (isset($park["park_name"]))
                echo htmlentities($park["park_name"]);
              else
                echo "";
            ?>" /></td>
        </tr>
        <tr>
          <td><b>Street:</b></td>
          <td><input type="text" name="street" value="<?php
              if (isset($park["street"]))
                echo htmlentities($park["street"]);
              else
                echo "";
            ?>" /></td>
        </tr>
        <tr>
          <td><b>City:</b></td>
          <td><input type="text" name="city" value="<?php
              if (isset($park["city"]))
                echo htmlentities($park["city"]);
              else
                echo "";
            ?>" /></td>
        </tr>
         <tr>
          <td><b>State:</b></td>
          <td><input type="text" name="state" value="<?php
              if (isset($park["state"]))
                echo htmlentities($park["state"]);
              else
                echo "";
            ?>" /></td>
        </tr>
         <tr>
          <td><b>Zip code:</b></td>
          <td><input type="text" name="zip_code" value="<?php
              if (isset($park["zip_code"]))
                echo htmlentities($park["zip_code"]);
              else
                echo "";
            ?>" /></td>
        </tr>
         <tr>
          <td><b>Country:</b></td>
          <td><input type="text" name="country" value="<?php
              if (isset($park["country"]))
                echo htmlentities($park["country"]);
              else
                echo "";
            ?>" /></td>
        </tr>
         <!--<tr>
          <td><b>Small/large dogs are separated</b></td>
            <td><input type="radio" name="separateSections" value="<?php 
              if (!isset($park["separateSections"])) echo "null";
            ?>" /> No&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="separateSections" value="<?php
              if (isset($park["separateSections"])) echo "";
            ?>" <?php if (isset($park["separateSections"])) echo " checked"; ?>/> Yes</td>
        </tr>
        <tr>
          <td><b>Water bowls available:</b></td>
            <td><input type="radio" name="water" value="<?php 
              if (!isset($park["waterAvailable"])) { echo "null"; }
            ?>" /> No&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="water" value="<?php
              if (isset($park["waterAvailable"])) echo "";
            ?>" <?php if (isset($park["waterAvailable"])) echo " checked"; ?>/> Yes</td>
         </tr>
         <tr>-->
          <td><b>Park Description:</b><br/>(Parking instructions, <br/>
          opening/closing times, etc.)</td>
          <td><textarea name="parkDescription" rows="5" cols="25"><?php
                if (isset($park["parkDescription"]))
                  echo htmlentities($park["parkDescription"]);
                else
                  echo "";
              ?>
          </textarea></td>
        </tr>
        </table>
      <input type="submit" class="button" name="submit" value="Edit Park Profile" />
    </form>
    <br />
    <?php
      $url = htmlspecialchars($_SERVER['HTTP_REFERER']);
      echo "<a href='$url'>Cancel</a>"; 
    ?>

<?php include("../includes/layouts/footer.php"); ?>
